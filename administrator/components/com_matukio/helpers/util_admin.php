<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die();

/**
 * Class MatukioHelperUtilsAdmin
 *
 * @since  1.0.0
 */
class MatukioHelperUtilsAdmin
{
	private static $instance;

	/**
	 * Gets the backend print window (sem_f038)
	 *
	 * @param   int  $art  - The art
	 * @param   int  $cid  - The cid
	 * @param   int  $uid  - The uid
	 *
	 * @todo    fix and update
	 * @return  string
	 */
	public static function getBackendPrintWindow($art, $cid, $uid = 0)
	{
		$search = trim(strtolower(JFactory::getApplication()->input->get('search', '', 'string')));
		$limit = trim(JFactory::getApplication()->input->getInt('limit', 5));
		$limitstart = trim(JFactory::getApplication()->input->getInt('limitstart', 0));

		if (empty($uid))
		{
			$uid = trim(JFactory::getApplication()->input->get('uid', 0));
		}

		$href = JURI::ROOT() . "index.php?tmpl=component&s=" . 0 . "&option=" . JFactory::getApplication()->input->get('option')
			. "&view=printeventlist&search=" . $search . "&limit=" . $limit . "&limitstart="
			. $limitstart . "&cid=" . $cid . "&uid=" . $uid . "&todo=";

		$x = 550;
		$y = 300;
		$image = "1932";
		$title = JTEXT::_('COM_MATUKIO_PRINT');

		switch ($art)
		{
			case 1:
				// Art 36
				$href .= "print_eventlist";
				break;

			case 2:
				// Teilnehmerliste - 34
				$href .= "print_teilnehmerliste&art=1&cid=" . $cid;
				$image = "1932";
				$title = JTEXT::_('COM_MATUKIO_PRINT_SIGNATURE_LIST');
				break;

			case 3:
				$href .= "certificate&cid=" . $cid;
				$image = "2900";
				$title = JTEXT::_('COM_MATUKIO_PRINT_CERTIFICATE');
				break;

			case 4:
				// Unterschriftliste
				$href .= "print_teilnehmerliste&cid=" . $cid;
				$image = "2032";
				$title = JTEXT::_('COM_MATUKIO_PRINT_PARTICIPANTS_LIST');
				break;

			case 5:
				$href = JURI::ROOT() . "index.php?option=com_matukio&view=printeventlist&format=raw&todo=csvlist&cid=" . $cid;
				$image = "1632";
				$title = JTEXT::_('COM_MATUKIO_DOWNLOAD_CSV_FILE');
				break;

			case 6:
				// Invoice
				$href = JURI::ROOT() . "index.php?option=com_matukio&view=printeventlist&format=raw&todo=invoice&cid=" . $cid . "&uid=" . $uid;
				$image = "invoice";
				$title = JTEXT::_('COM_MATUKIO_PRINT_INVOICE');
				break;
		}

		if ($art != 5 && $art != 6)
		{
			$html = "<a title=\"" . $title . "\" class=\"modal cjmodal\" href=\"" . $href . "\" rel=\"{handler: 'iframe', size: {x: " . $x . ", y: " . $y . "}}\">";
		}
		else
		{
			$html = "<a title=\"" . $title . "\" href=\"" . $href . "\">";
		}

		$html .= "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . $image
			. ".png\" border=\"0\" valign=\"absmiddle\" alt=\"" . $title . "\" /></a>";

		return $html;
	}

	/**
	 * Gets an table line (sem_f024)
	 *
	 * @param   int     $art     - The art
	 * @param   array   $var1    - The var
	 * @param   array   $var2    - The var2
	 * @param   array   $werte   - The values
	 * @param   string  $klasse  - The class
	 *
	 * @return  string
	 */
	public static function getTableLine($art, $var1, $var2, $werte, $klasse)
	{
		$zurueck = "<tr";

		if ($klasse <> "")
		{
			$zurueck .= " class=\"" . $klasse . "\"";
		}

		$zurueck .= ">";

		$n = count($werte);

		for ($l = 0, $n; $l < $n; $l++)
		{
			$format1 = "";

			if (is_array($var1))
			{
				switch ($var1[$l])
				{
					case "c2":
						$format1 .= " colspan=\"2\"";
						break;
					case "nw":
						$format1 .= " nowrap=\"nowrap\"";
						break;
					case "l":
						$format1 .= " style=\"text-align:left;\"";
						break;
					case "r":
						$format1 .= " style=\"text-align:right;\"";
						break;
					case "c":
						$format1 .= " style=\"text-align:center;\"";
						break;
				}
			}

			$format2 = "";

			if (is_array($var2))
			{
				switch ($var2[$l])
				{
					case "c2":
						$format1 .= " colspan=\"2\"";
						break;
					case "nw":
						$format1 .= " nowrap=\"nowrap\"";
						break;
					case "l":
						$format1 .= " style=\"text-align:left;\"";
						break;
					case "r":
						$format1 .= " style=\"text-align:right;\"";
						break;
					case "c":
						$format1 .= " style=\"text-align:center;\"";
						break;
				}
			}

			$zurueck .= "<" . $art . $format1 . $format2 . ">" . $werte[$l] . "</" . $art . ">";
		}

		$zurueck .= "</tr>";

		return $zurueck;
	}
}
