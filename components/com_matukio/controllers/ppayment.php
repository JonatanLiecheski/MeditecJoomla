<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       02.04.14
 *
 * @copyright  Copyright (C) 2008 - 2014 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Class MatukioControllerPPayment
 *
 * @since  2.2.0
 */
class MatukioControllerPPayment extends JControllerLegacy
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
		MatukioHelperUtilsBasic::loginUser();
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'PPayment');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('PPayment', 'MatukioModel');
		$view->setModel($model, true);
		$view->display();
	}

	/**
	 * Update Booking status and redirect to event art 1
	 *
	 * @return  void  - Redirects to event view
	 */
	public function status()
	{
		$uuid = JFactory::getApplication()->input->get('uuid', '');
		$pg_plugin = JFactory::getApplication()->input->get('pg_plugin', '');
		$uid = JFactory::getApplication()->input->getInt('uid', 0);

		$dispatcher = JDispatcher::getInstance();

		// Import the right plugin here!
		JPluginHelper::importPlugin('payment', $pg_plugin);

		$data = $dispatcher->trigger('onTP_Processpayment', array(JRequest::get("post")));

		$model = $this->getModel('PPayment', 'MatukioModel');

		$booking = $model->getBooking($uuid);

		if (empty($booking))
		{
			JError::raise(E_ERROR, "500", JText::_("COM_MATUKIO_BOOKING_NOT_FOUND"));
		}

		$event = $model->getEvent($booking->semid);

		$payment_status = $data[0]['status'];

		// Update Payment status
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->update("#__matukio_bookings")
			->where("uuid = " . $db->quote($uuid))
			->set("payment_status = " . $db->quote($payment_status));

		$db->setQuery($query);
		$db->execute();

		$msg = JText::_("COM_MATUKIO_THANK_YOU");

		// Check if there is an error, if yes
		if ($data[0]['status'] == "E")
		{
			$status = MatukioHelperUtilsBooking::$PENDING;

			if (MatukioHelperSettings::_("booking_always_active", 0))
			{
				// We check if the booking always active setting is set - if yes the booking is always!! active (except waitlist delete etc.)
				$status = MatukioHelperUtilsBooking::$ACTIVE;
			}

			// Update status to not paid
			$query = $db->getQuery(true);
			$query->update("#__matukio_bookings")->where("uuid = " . $db->quote($uuid))
				->set("paid = 0")
				->set("status = " . $db->quote($status))
				->set("payment_plugin_data = " . $db->quote($data[0]['raw_data']));
			$db->setQuery($query);
			$db->execute();

			$view = $this->getView("PPayment", "html");
			$model = $this->getModel('PPayment', 'MatukioModel');
			$view->setModel($model, true);
			$view->data = $data;
			$view->setLayout("error");
			$view->display();

			return;
		}
		elseif ($data[0]['status'] == "C")
		{
			$status = MatukioHelperUtilsBooking::$ACTIVE;

			if (MatukioHelperSettings::_("booking_always_inactive", 0))
			{
				// We check if the booking always active setting is set - if yes the booking is always!! active (except waitlist delete etc.)
				$status = MatukioHelperUtilsBooking::$PENDING;
			}

			// Update status to paid and set the booking to active
			$query = $db->getQuery(true);
			$query->update("#__matukio_bookings")->where("uuid = " . $db->quote($uuid))
				->set("paid = 1")
				->set("status = " . $db->quote($status))
				->set("payment_plugin_data = " . $db->quote($data[0]['raw_data']));
			$db->setQuery($query);
			$db->execute();

			$msg = JText::_("COM_MATUKIO_PAYMENT_SUCCESSFULL");
		}
		elseif ($data[0]['status'] == "P")
		{
			$status = MatukioHelperUtilsBooking::$PENDING;

			// Exclusion for cash plugin
			if ($pg_plugin == "cash")
			{
				$status = MatukioHelperUtilsBooking::$ACTIVE;

				if (MatukioHelperSettings::_("booking_always_inactive", 0))
				{
					// We check if the booking always active setting is set - if yes the booking is always!! active (except waitlist delete etc.)
					$status = MatukioHelperUtilsBooking::$PENDING;
				}
			}

			if (MatukioHelperSettings::_("booking_always_active", 0))
			{
				// We check if the booking always active setting is set - if yes the booking is always!! active (except waitlist delete etc.)
				$status = MatukioHelperUtilsBooking::$ACTIVE;
			}

			// Update status to not paid
			$query = $db->getQuery(true);
			$query->update("#__matukio_bookings")->where("uuid = " . $db->quote($uuid))
				->set("paid = 0")
				->set("status = " . $db->quote($status))
				->set("payment_plugin_data = " . $db->quote($data[0]['raw_data']));
			$db->setQuery($query);
			$db->execute();

			$msg = JText::_("COM_MATUKIO_PAYMENT_PENDING");
		}

		// We send the booking confirmation here..
		MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $booking->id, 1);

		// Link to event art = 1
		$eventid_l = $event->id . ':' . JFilterOutput::stringURLSafe($event->title);
		$catid_l = $event->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($event->catid));

		// Link back to the form
		if (MatukioHelperSettings::getSettings("oldbooking_redirect_after", "bookingpage") == "bookingpage")
		{
			$bplink = "index.php?option=com_matukio&view=booking&uuid=" . $booking->uuid;

			$needles = array(
				'category' => 0
			);

			$item = MatukioHelperRoute::_findItem($needles);

			if ($item)
			{
				$bplink .= '&Itemid=' . $item->id;
			}

			$link = JRoute::_($bplink);
		}
		elseif (MatukioHelperSettings::getSettings("oldbooking_redirect_after", "bookingpage") == "eventpage")
		{
			$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l, 0, $uid, $uuid), false);
		}
		else
		{
			// Eventlist overview
			$link = JRoute::_(MatukioHelperRoute::getEventlistRoute(0, 0), false);
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Set booking to canceled?
	 *
	 * @throws  Exception if booking not found
	 * @return  void
	 */
	public function cancelPayment()
	{
		$uuid = JFactory::getApplication()->input->get('uuid', '');
		$pg_plugin = JFactory::getApplication()->input->get('pg_plugin', '');

		$model = $this->getModel('PPayment', 'MatukioModel');

		$booking = $model->getBooking($uuid);

		if (empty($booking))
		{
			throw new Exception(JText::_("COM_MATUKIO_BOOKING_NOT_FOUND"), 404);
		}

		$event = $model->getEvent($booking->semid);

		// Update status
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		// Set status to deleted
		$query->update("#__matukio_bookings")
			->where("uuid = " . $db->quote($uuid))
			->set("payment_status = " . $db->quote("P"))
			->set("status = 4");

		$db->setQuery($query);
		$db->execute();

		// Link to event art = 1
		$eventid_l = $event->id . ':' . JFilterOutput::stringURLSafe($event->title);
		$catid_l = $event->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($event->catid));

		$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l, 1), false);

		$msg = JText::_("COM_MATUKIO_YOUR_BOOKING_HAS_BEEN_DELETED");

		$this->setRedirect($link, $msg);
	}

	/**
	 * Confirms payment
	 *
	 * @param $pg_plugin
	 * @param $oid
	 */
	function confirmpayment($pg_plugin,$oid)
	{
		$post	= JRequest::get('post');
		$vars = array();

		if (!empty($post) && !empty($vars))
		{
			JPluginHelper::importPlugin('payment', $pg_plugin);
			$dispatcher = JDispatcher::getInstance();
			$result = $dispatcher->trigger('onTP_ProcessSubmit', array($post,$vars));
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('SOME_ERROR_OCCURRED'), 'error');
		}
	}
}
