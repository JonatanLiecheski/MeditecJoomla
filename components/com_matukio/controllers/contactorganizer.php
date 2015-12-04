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
 * Class MatukioControllerContactOrganizer
 *
 * @since  2.2.0
 */
class MatukioControllerContactOrganizer extends JControllerLegacy
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
		$viewName = JFactory::getApplication()->input->get('view', 'ContactOrganizer');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('ContactOrganizer', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Sends an email to the organizer
	 *
	 * @throws  exception
	 * @return  object
	 */
	public function sendEmail()
	{
		$mainframe = JFactory::getApplication();
		$msg = JText::_("COM_MATUKIO_MAIL_TO_ORGANIZER_SEND_SUCCESSFULL");
		$msg_type = "message";

		jimport('joomla.mail.helper');

		// Check if sending is allowed
		if (!MatukioHelperSettings::getSettings("sendmail_contact", 1))
		{
			throw new Exception("COM_MATUKIO_CONTACTING_ORGANIZERS_IS_DISABLED");
		}

		$my = JFactory::getuser();
		$database = JFactory::getDBO();
		$cid = JFactory::getApplication()->input->getInt('event_id', 0);
		$organizer_id = JFactory::getApplication()->input->getInt('organizer_id', 0);

		$uid = JFactory::getApplication()->input->get('art', 0);
		$text = JMailHelper::cleanBody(nl2br(JFactory::getApplication()->input->get('text', '', 'string')));

		$name = JFactory::getApplication()->input->get('name', '', 'string');
		$email = JFactory::getApplication()->input->get('email', '', 'string');

		if ($text != "" && $name != "" && $email != "")
		{
			$reason = JTEXT::_('COM_MATUKIO_MESSAGE_SEND');

			// Load event (use model function)
			$emodel = JModelLegacy::getInstance('Event', 'MatukioModel');
			$event = $emodel->getItem($cid);

			$subject = "";

			if ($event->semnum != "")
			{
				$subject .= " " . $event->semnum;
			}

			$subject .= ": " . $event->title;
			$subject = JMailHelper::cleanSubject($subject);
			$sender = $mainframe->getCfg('fromname');
			$from = $mainframe->getCfg('mailfrom');

			if ($my->id == 0)
			{
				$replyname = $name;
				$replyto = $email;

				// Setting it hardcoded for the body function.. dirk you really give me headaches
				$my->name = $name;
				$my->email = $email;
			}
			else
			{
				$replyname = $my->name;
				$replyto = $my->email;
			}

			$body = "\n<head>\n<style type=\"text/css\">\n<!--\nbody {\nfont-family: Verdana, Tahoma, Arial;\nfont-size:12pt;\n}\n-->\n</style></head><body>";

			if ($uid == 1 AND $my->id != 0)
			{
				$body .= "<p><div style=\"font-size: 10pt\">" . JTEXT::_('COM_MATUKIO_QUESTION_ABOUT_EVENT') . "</div><p>";
			}

			$body .= "<div style=\"border: 1px solid #A0A0A0; width: 100%; padding: 5px;\">" . $text . "</div><p>";
			$temp = array();

			// Mail to Organizer
			if ($uid == 1)
			{
				$body .= MatukioHelperUtilsEvents::getEmailBody($event, $temp, $my);
				$publisher = JFactory::getUser($event->publisher);
				$email = $publisher->email;
				$mailer = JFactory::getMailer();

				$mailer->sendMail($from, $sender, $email, $subject, $body, 1, null, null, null, $replyto, $replyname);
			}
			elseif ($uid == "organizer")
			{
				$organizer = MatukioHelperOrganizer::getOrganizerId($organizer_id);
				$publisher = JFactory::getuser($organizer->userId);
				$email = $publisher->email;
				$mailer = JFactory::getMailer();

				$mailer->sendMail($from, $sender, $email, $subject, $body, 1, null, null, null, $replyto, $replyname);
			}
			else
			{
				if (!JFactory::getUser()->authorise('core.create', 'com_matukio'))
				{
					return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
				}

				$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid='" . $event->id . "'");
				$rows = $database->loadObjectList();

				foreach ($rows as $row)
				{
					if ($row->userid == 0)
					{
						$user = JFactory::getUser(0);
						$user->email = $row->email;
						$user->name = $row->name;
					}
					else
					{
						$user = JFactory::getUser($row->userid);
					}

					$text = $body . MatukioHelperUtilsEvents::getEmailBody($event, $row, $user);
					$mailer = JFactory::getMailer();

					$mailer->sendMail($from, $sender, $user->email, $subject, $text, 1, null, null, null, $replyto, $replyname);
				}
			}
		}
		else
		{
			$msg = JTEXT::_('COM_MATUKIO_MESSAGE_NOT_SEND');
			$msg_type = "error";
		}

		$link = MatukioHelperUtilsBasic::getSitePath() . "index.php?tmpl=component&s=" . MatukioHelperUtilsBasic::getRandomChar()
			. "&option=" . JFactory::getApplication()->input->get('option') . "&view=contactorganizer&cid=" . $cid . "&art=" . $uid . "&task=19";

		$this->setRedirect($link, $msg, $msg_type);
	}
}
