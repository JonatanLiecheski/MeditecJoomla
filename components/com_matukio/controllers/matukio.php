<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Class MatukioControllerMatukio
 *
 * @since  1.0.0
 */
class MatukioControllerMatukio extends JControllerLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   bool  $cachable   - Is it cachable
	 * @param   bool  $urlparams  - The url params
	 *
	 * @deprecated  Used for old links - should be moved some time
	 * @return JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$task = JFactory::getApplication()->input->get('task', '');

		if (empty($task))
		{
			MatukioHelperUtilsBasic::loginUser();
			$link = JRoute::_("index.php?option=com_matukio&view=eventlist");
			$this->setRedirect($link);
		}
	}

	/**
	 * Logs out the user
	 *
	 * @return  void
	 */
	public function logoutUser()
	{
		$mainframe = JFactory::getApplication();
		$my = JFactory::getuser();
		$mainframe->logout($my->id);
		$link = JRoute::_("index.php?option=com_matukio&view=eventlist");
		$msg = JText::_("COM_MATUKIO_LOGOUT_SUCCESS");
		$this->setRedirect($link, $msg);
	}

	/**
	 * Generates the file and outputs it as $filetype
	 *
	 * @return  string
	 */
	public function downloadFile()
	{
		$my = JFactory::getUser();

		$daten = trim(JFactory::getApplication()->input->get('a6d5dgdee4cu7eho8e7fc6ed4e76z', ''));
		$cid = substr($daten, 40);
		$dat = substr($daten, 0, 40);

		$kurs = MatukioHelperUtilsEvents::getEventRecurring($cid);
		$datfeld = MatukioHelperUtilsEvents::getEventFileArray($kurs);

		for ($i = 0; $i < count($datfeld[0]); $i++)
		{
			if (sha1(md5($datfeld[0][$i])) == $dat
				AND ($datfeld[2][$i] == 0
				OR ($my->id > 0 AND $datfeld[2][$i] > 0)))
			{
				$datname = $datfeld[0][$i];
				$datcode = "file" . ($i + 1) . "code";
				$daten = base64_decode($kurs->$datcode);
				$datext = array_pop(explode(".", strtolower($datname)));
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Pragma: public");
				header("Content-Type: application/$datext");
				header("Content-Disposition: inline; filename=\"$datname\"");
				header("Content-Length: " . strlen($daten));
				echo $daten;
				exit;
			}
		}
	}

	/**
	 * Generates the barcode and outputs it as header image/jpg
	 *
	 * @return  string
	 */
	public function getBarcode()
	{
		$code = JFactory::getApplication()->input->get("code", '');

		if (empty($code))
		{
			echo "No code given";

			return;
		}

		$text = 1;
		$width = 300;
		$height = 50;

		header("Content-type: image/png");
		$im = ImageCreate($width, $height)
				or die ("Cannot Initialize new GD image stream");

		$White = ImageColorAllocate($im, 255, 255, 255);
		$Black = ImageColorAllocate($im, 0, 0, 0);

		ImageInterLace($im, 1);
		$NarrowRatio = 20;
		$WideRatio = 55;
		$QuietRatio = 35;
		$nChars = (strlen($code) + 2) * ((6 * $NarrowRatio) + (3 * $WideRatio) + ($QuietRatio));
		$Pixels = $width / $nChars;
		$NarrowBar = (int) (20 * $Pixels);
		$WideBar = (int) (55 * $Pixels);
		$QuietBar = (int) (35 * $Pixels);
		$ActualWidth = (($NarrowBar * 6) + ($WideBar * 3) + $QuietBar) * (strlen($code) + 2);

		if (($NarrowBar == 0) || ($NarrowBar == $WideBar) || ($NarrowBar == $QuietBar) || ($WideBar == 0) || ($WideBar == $QuietBar) || ($QuietBar == 0))
		{
			ImageString($im, 1, 0, 0, "Image is too small!", $Black);
			ImagePNG($im);
			exit;
		}

		$CurrentBarX = (int) (($width - $ActualWidth) / 2);
		$Color = $White;
		$BarcodeFull = "*" . strtoupper($code) . "*";
		settype($BarcodeFull, "string");
		$FontNum = 3;
		$FontHeight = ImageFontHeight($FontNum);
		$FontWidth = ImageFontWidth($FontNum);

		if ($text != 0)
		{
			$CenterLoc = (int) (($width - 1) / 2) - (int) (($FontWidth * strlen($BarcodeFull)) / 2);
			ImageString($im, $FontNum, $CenterLoc, $height - $FontHeight, "$BarcodeFull", $Black);
		}
		else
		{
			$FontHeight = -2;
		}

		for ($i = 0; $i < strlen($BarcodeFull); $i++)
		{
			$StripeCode = MatukioHelperUtilsBooking::getCode99($BarcodeFull[$i]);

			for ($n = 0; $n < 9; $n++)
			{
				if ($Color == $White)
				{
					$Color = $Black;
				}
				else
				{
					$Color = $White;
				}

				switch ($StripeCode[$n])
				{
					case '0':
						ImageFilledRectangle($im, $CurrentBarX, 0, $CurrentBarX + $NarrowBar, $height - 1 - $FontHeight - 2, $Color);
						$CurrentBarX += $NarrowBar;
						break;

					case '1':
						ImageFilledRectangle($im, $CurrentBarX, 0, $CurrentBarX + $WideBar, $height - 1 - $FontHeight - 2, $Color);
						$CurrentBarX += $WideBar;
						break;
				}
			}

			$Color = $White;

			ImageFilledRectangle($im, $CurrentBarX, 0, $CurrentBarX + $QuietBar, $height - 1 - $FontHeight - 2, $Color);
			$CurrentBarX += $QuietBar;
		}

		ImagePNG($im);
		imagedestroy($im);
		jexit();
	}
}
