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
 * Class MatukioControllerContactparticipants
 *
 * @since  2.2.4
 */
class MatukioControllerContactparticipants extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Display the contact form
	 *
	 * @param   bool  $cachable   - The cache
	 * @param   bool  $urlparams  - The url params
	 *
	 * @return  JControllerLegacy|void
	 */

	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewName = "ContactParticipants";
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);

		$model = $this->getModel('ContactParticipants', 'MatukioModel');

		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Sends the E-Mail to the participants
	 *
	 * @return  void
	 */

	public function send()
	{
		$event_id = JFactory::getApplication()->input->getInt('event_id', 0);
		$booking_ids = JFactory::getApplication()->input->get('cid', '', 'string');

		if (!empty($booking_ids))
		{
			$booking_ids = explode(",", $booking_ids);
		}

		$model = $this->getModel('ContactParticipants', 'MatukioModel');

		$participants = $model->getParticipants();

		if (!empty($event_id))
		{
			$event = $model->getEvent($event_id);
		}


		jimport('joomla.mail.helper');
		jimport('joomla.mail.mail');

		$mainframe = JFactory::getApplication();

		$sender = $mainframe->getCfg('fromname');
		$from = $mainframe->getCfg('mailfrom');


		$subject = JFactory::getApplication()->input->getString('subject', '');
		$text = JFactory::getApplication()->input->getHtml('text', '');

		if (!empty($event_id))
		{
			$publisher = JFactory::getUser($event->publisher);
		}
		else
		{
			$publisher = JFactory::getUser();
		}

		$replyto = $publisher->email;
		$replyname = $publisher->name;

		foreach ($participants as $p)
		{
			$email = $p->email;

			if (empty($email) && $p->userid > 0)
			{
				$email = JFactory::getUser($p->userid)->email;
			}

			$body = "\n<head>\n<style type=\"text/css\">\n<!--\nbody {\nfont-family: Verdana, Tahoma, Arial;\nfont-size:12pt;\n}\n-->\n</style></head><body>";

			// TODO Add Templates / Booking details
			$body .= $text;
			$body .= "</body></html>";

			$mailer = JFactory::getMailer();

			$success = $mailer->sendMail(
				$from, $sender, $email, $subject, $body, 1,
				null, null, null, $replyto, $replyname
			);

			echo $success . " " . $email;
		}

		if (!empty($event_id))
		{
			$link = 'index.php?option=com_matukio&view=bookings&uid=' . $event_id;
		}
		else
		{
			$link = 'index.php?option=com_matukio&view=bookings';
		}

		$msg = JText::_("COM_MATUKIO_CONTACT_SEND_SUCCESSFULLY");

		$this->setRedirect($link, $msg);
	}

	/**
	 * Cancels the form
	 *
	 * @return  void
	 */

	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=bookings&uid=' . JFactory::getApplication()->input->getInt('event_id', 0);
		$this->setRedirect($link);
	}
}
