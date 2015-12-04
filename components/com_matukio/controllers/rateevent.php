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
 * Class MatukioControllerRateEvent
 *
 * @since  1.0.0
 */
class MatukioControllerRateEvent extends JControllerLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   bool  $cachable   - Is it cachable
	 * @param   bool  $urlparams  - The url params
	 *
	 * @return JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'RateEvent');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('RateEvent', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Rates an event
	 *
	 * @throws  Exception if user is not logged in
	 * @return  void / redirect
	 */
	public function rate()
	{
		// Check if user is logged in
		if (JFactory::getUser()->id == 0)
		{
			throw new Exception("COM_MATUKIO_NO_ACCESS");
		}

		$msg = JText::_("COM_MATUKIO_RATING_SUCCESSFULL");
		$mainframe = JFactory::getApplication();

		jimport('joomla.mail.helper');

		$my = JFactory::getuser();
		$database = JFactory::getDBO();
		$cid = JFactory::getApplication()->input->getInt('cid', 0);
		$grade = JFactory::getApplication()->input->getInt('grade', 0);
		$text = JFactory::getApplication()->input->get('text', '');
		$text = str_replace(array("\"", "\'"), "", $text);
		$text = JMailHelper::cleanBody($text);
		$database->setQuery("UPDATE #__matukio_bookings SET grade='" . $grade . "', comment='" . $text . "' WHERE semid='"
		. $cid . "' AND userid='" . $my->id . "'");

		if (!$database->execute())
		{
			JError::raiseError(500, $database->getError());
			exit();
		}

		$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid='" . $cid . "'");
		$rows = $database->loadObjectList();
		$zaehler = 0;
		$wertung = 0;

		foreach ($rows AS $row)
		{
			if ($row->grade > 0)
			{
				$wertung = $wertung + $row->grade;
				$zaehler++;
			}
		}

		if ($zaehler > 0)
		{
			$geswert = round($wertung / $zaehler);
		}
		else
		{
			$geswert = 0;
		}

		$database->setQuery("UPDATE #__matukio SET grade='$geswert' WHERE id='$cid'");

		if (!$database->execute())
		{
			JError::raiseError(500, $database->getError());
			$msg = "COM_MATUKIO_RATING_FAILED " . $database->getError();
		}

		if (MatukioHelperSettings::getSettings('sendmail_owner', 1) > 0)
		{
			$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid='$cid' AND userid='$my->id'");
			$buchung = $database->loadObject();

			// Load event (use model function)
			$emodel = JModelLegacy::getInstance('Event', 'MatukioModel');
			$row = $emodel->getItem($cid);

			$publisher = JFactory::getuser($row->publisher);
			$body = "\n<head>\n<style type=\"text/css\">\n<!--\nbody {\nfont-family: Verdana, Tahoma, Arial;\nfont-size:12pt;\n}\n-->\n</style></head><body>";
			$body .= "<p><div style=\"font-size: 10pt\">" . JTEXT::_('COM_MATUKIO_RECEIVED_RATING') . "</div>";
			$body .= "<p><div style=\"font-size: 10pt\">" . JTEXT::_('COM_MATUKIO_RATING') . ":</div>";
			$htxt = str_replace('SEM_POINTS', $grade, JTEXT::_('COM_MATUKIO_SEM_POINTS_6'));
			$body .= "<div style=\"border: 1px solid #A0A0A0; width: 100%; padding: 5px;\">" . $htxt . "</div>";
			$body .= "<p><div style=\"font-size: 10pt\">" . JTEXT::_('COM_MATUKIO_COMMENT') . ":</div>";
			$body .= "<div style=\"border: 1px solid #A0A0A0; width: 100%; padding: 5px;\">" . htmlspecialchars($text) . "</div>";
			$body .= "<p><div style=\"font-size: 10pt\">" . JTEXT::_('COM_MATUKIO_AVARAGE_SCORE') . ":</div>";
			$htxt = str_replace('SEM_POINTS', $geswert, JTEXT::_('COM_MATUKIO_SEM_POINTS_6'));
			$body .= "<div style=\"border: 1px solid #A0A0A0; width: 100%; padding: 5px;\">" . $htxt . "</div>";
			$body .= "<p>" . MatukioHelperUtilsEvents::getEmailBody($row, $buchung, $my);
			$sender = $mainframe->getCfg('fromname');
			$from = $mainframe->getCfg('mailfrom');
			$replyname = $my->name;
			$replyto = $my->email;
			$email = $publisher->email;
			$subject = JTEXT::_('COM_MATUKIO_EVENT');

			if ($row->semnum != "")
			{
				$subject .= " " . $row->semnum;
			}

			$subject .= ": " . $row->title;
			$subject = JMailHelper::cleanSubject($subject);
			$mailer = JFactory::getMailer();

			$mailer->sendMail($from, $sender, $email, $subject, $body, 1, null, null, null, $replyto, $replyname);
		}

		$link = "index.php?option=com_matukio&tmpl=component&s=" . MatukioHelperUtilsBasic::getRandomChar() . "&view=rateevent&cid=" . $cid;

		$this->setRedirect($link, $msg);
	}
}
