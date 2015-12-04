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
 * Class MatukioControllerParticipants
 *
 * @since  1.0.0
 */
class MatukioControllerParticipants extends JControllerLegacy
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
		$viewName = JFactory::getApplication()->input->get('view', 'participants');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('participants', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Toogle paid status..
	 *
	 * @throws  Exception - if access is denied!
	 * @return  void
	 */
	public function toogleStatusPaid()
	{
		if (!JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$database = JFactory::getDBO();
		$uid = JFactory::getApplication()->input->getInt('uid', 0);
		$cid = JFactory::getApplication()->input->getInt('cid', 0);

		$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id='" . $uid . "'");
		$row = $database->loadObject();

		if ($row->paid == 0)
		{
			$paid = 1;
		}
		else
		{
			$paid = 0;
		}

		$database->setQuery("UPDATE #__matukio_bookings SET paid='" . $paid . "' WHERE id='" . $uid . "'");

		if (!$database->execute())
		{
			throw new Exception($database->getError(), 500);
		}

		$msg = JTEXT::_("COM_MATUKIO_PAYMENT_STATUS_CHANGED");
		$link = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $cid . "&art=2");

		$this->setRedirect($link, $msg);
	}

	/**
	 * Cert user
	 *
	 * @throws  Exception - if access is denied!
	 * @return  void
	 */
	public function certificateUser()
	{
		if (!JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$msg = JTEXT::_("COM_MATUKIO_SEND_USER_CERTIFICATE");

		$database = JFactory::getDBO();
		$cid = JFactory::getApplication()->input->getInt('cid', 0);
		$uid = JFactory::getApplication()->input->getInt('uid', 0);
		$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id='" . $uid . "'");

		$row = $database->loadObject();

		if ($row->certificated == 0)
		{
			$cert = 1;
			$certmail = 6;
		}
		else
		{
			$cert = 0;
			$certmail = 7;
		}

		$database->setQuery("UPDATE #__matukio_bookings SET certificated = " . $database->quote($cert) . " WHERE id='" . $uid . "'");

		if (!$database->execute())
		{
			throw new Exception($database->getError(), 500);
		}

		$event = MatukioHelperUtilsEvents::getEventEditTemplate($row->semid);

		MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $uid, $certmail);

		$link = JRoute::_('index.php?option=com_matukio&view=participants&art=2&cid=' . $cid);

		$this->setRedirect($link, $msg);
	}

	/**
	 * Cancels the booking
	 *
	 * @throws  Exception - if access is denied!
	 * @return object
	 */
	public function cancelBookingOrganizer()
	{
		if (!JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$eventid = JFactory::getApplication()->input->getInt('cid', 0);

		$link = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $eventid . "&art=2");
		$msg = JText::_("COM_MATUKIO_CANCEL_BOOKING_SUCCESSFULL");

		$booking_ids = JFactory::getApplication()->input->get('uid', array(), 'array');

		// Set db status to deleted @since 3.1
		MatukioHelperUtilsBooking::deleteBookings($booking_ids);

		$this->setRedirect($link, $msg);
	}


	/**
	 * Cancels the booking
	 *
	 * @throws  Exception - if access is denied!
	 * @return object
	 */
	public function activateBooking()
	{
		if (!JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$eventid = JFactory::getApplication()->input->getInt('cid', 0);

		$link = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $eventid . "&art=2");
		$msg = JText::_("COM_MATUKIO_BOOKING_SET_ACTIVE_SUCCESSFULL");

		$booking_ids = JFactory::getApplication()->input->get('uid', array(), 'array');

		// Set db status to deleted @since 3.1
		MatukioHelperUtilsBooking::changeStatusBooking($booking_ids, MatukioHelperUtilsBooking::$ACTIVE, true);

		$this->setRedirect($link, $msg);
	}

	/**
	 * Changes the organizer
	 *
	 * @throws  Exception - if access is denied!
	 * @return  object
	 */
	public function changeBookingOrganizer()
	{
		if (!JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
		{
			throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		$cid = JFactory::getApplication()->input->getInt('cid', 0);

		$database = JFactory::getDBO();
		$neu = JTable::getInstance("bookings", "Table");

		if (!$neu->bind(JRequest::get('post')))
		{
			throw new Exception($database->getError(), 500);
		}

		$uid = JFactory::getApplication()->input->getInt('uid', 0);

		if ($uid < 0)
		{
			$uid *= -1;
		}

		$neu->id = $uid;

		if (!$neu->check())
		{
			throw new Exception($database->getError(), 500);
		}

		if (!$neu->store())
		{
			throw new Exception($database->getError(), 500);
		}

		$link = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $cid . "&art=2");

		$msg = JTEXT::_("COM_MATUKIO_BOOKING_CHANGED_SUCCESSFULL");

		$this->setRedirect($link, $msg);
	}
}
