<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       11.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

/**
 * Class MatukioControllerEditBooking
 *
 * @since  2.0.0
 */
class MatukioControllerEditBooking extends JControllerLegacy
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
		$viewName = 'editbooking';
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('editbooking');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Save the booking
	 *
	 * @todo    Change and Update to a function (mixing backend and frontend!!!)
	 * @return  object
	 */
	public function save()
	{
		// Check authorization
		if (!JFactory::getUser()->authorise('core.edit', 'com_matukio'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$database = JFactory::getDBO();
		$input = JFactory::getApplication()->input;
		$art = 4;

		$event_id = $input->getInt('event_id', 0);
		$uid = $input->getInt('uid', 0);
		$uuid = $input->getInt('uuid', 0);
		$nrbooked = $input->getInt('nrbooked', 1);
		$userid = $input->getInt('userid', 0);

		$payment_method = $input->get('payment', '', 'string');

		$notify_participant = $input->getInt("notify_participant", 0);
		$notify_participant_invoice = $input->getInt("notify_participant_invoice", 0);

		if (empty($event_id))
		{
			return JError::raiseError(404, 'COM_MATUKIO_NO_ID');
		}

		// Load event (use model function)
		$emodel = JModelLegacy::getInstance('Event', 'MatukioModel');
		$event = $emodel->getItem($event_id);

		$reason = "";

		if (!empty($uid))
		{
			// Setting booking to changed booking
			$userid = $uid; // uid = Negativ

			$art = 4;
		}

		if ($art == 4)
		{
			$allesok = 1;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_SUCCESSFULL');
		}

		// Buchung eintragen
		$neu = JTable::getInstance('bookings', 'Table');

		if (!$neu->bind(JRequest::get('post')))
		{
			return JError::raiseError(500, $database->stderr());
		}

		$neu->semid = $event->id;

		$neu->userid = $userid;

		$firstname = $input->get('firstname', '', 'string');
		$lastname = $input->get('lastname', '', 'string');

		$neu->bookingdate = MatukioHelperUtilsDate::getCurrentDate();
		$neu->name = MatukioHelperUtilsBasic::cleanHTMLfromText($firstname . " " . $lastname);
		$neu->email = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->email);
		$neu->zusatz1 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz1);
		$neu->zusatz2 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz2);
		$neu->zusatz3 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz3);
		$neu->zusatz4 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz4);
		$neu->zusatz5 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz5);
		$neu->zusatz6 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz6);
		$neu->zusatz7 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz7);
		$neu->zusatz8 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz8);
		$neu->zusatz9 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz9);
		$neu->zusatz10 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz10);
		$neu->zusatz11 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz11);
		$neu->zusatz12 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz12);
		$neu->zusatz13 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz13);
		$neu->zusatz14 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz14);
		$neu->zusatz15 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz15);
		$neu->zusatz16 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz16);
		$neu->zusatz17 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz17);
		$neu->zusatz18 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz18);
		$neu->zusatz19 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz19);
		$neu->zusatz20 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz20);

		if (empty($neu->uuid))
		{
			$neu->uuid = MatukioHelperPayment::getUuid(true);
		}

		$fields = MatukioHelperUtilsBooking::getBookingFields();

		if (!empty($fields))
		{
			$newfields = "";

			for ($i = 0; $i < count($fields); $i++)
			{
				$field = $fields[$i];
				$name = $field->field_name;
				$newfields .= $field->id;
				$newfields .= "::";
				$newfields .= $input->get($name, '', 'string');
				$newfields .= ";";
			}

			$neu->newfields = $newfields;

			if (!empty($event->fees))
			{
				$neu->payment_method = $payment_method;
				$payment_brutto = $event->fees * $neu->nrbooked;
				$coupon_code = $neu->coupon_code;

				if (!empty($coupon_code))
				{
					$cdate = new DateTime;

					$db = JFactory::getDBO();
					$query = $db->getQuery(true);
					$query->select('*')->from('#__matukio_booking_coupons')
						->where('code = ' . $db->quote($coupon_code) . ' AND published = 1 AND published_up < '
						. $db->quote($cdate->format('Y-m-d H:i:s')) . " AND published_down > "
							. $db->quote($cdate->format('Y-m-d H:i:s'))
						);

					$db->setQuery($query);
					$coupon = $db->loadObject();


					if (!empty($coupon))
					{
						if ($coupon->procent == 1)
						{
							// Get a procent value
							$payment_brutto = round($payment_brutto * ((100 - $coupon->value) / 100), 2);
						}
						else
						{
							$payment_brutto = $payment_brutto - $coupon->value;
						}
					}
					else
					{
						// Raise an error
						JError::raise(E_ERROR, 500, JText::_("COM_MATUKIO_INVALID_COUPON_CODE"));
					}
				}

				$neu->payment_brutto = $payment_brutto;
			}
		}

		if (!$neu->check())
		{
			return JError::raiseError(500, $database->stderr());
		}

		if (!$neu->store())
		{
			return JError::raiseError(500, $database->stderr());
		}

		$neu->checkin();

		$ueber1 = JText::_("COM_MATUKIO_BOOKING_WAS_SUCCESSFULL");

		if ($userid == 0)
		{
			$userid = $neu->id * -1;
		}

		// Send new confirmation mail
		if ($notify_participant)
		{
			MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $neu->id, 11, false, $neu, $notify_participant_invoice);
		}

		$viewteilnehmerlink = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $event->id . "&art=2");

		$msg = JText::_("COM_MATUKIO_BOOKING_EDITED");

		$this->setRedirect($viewteilnehmerlink, $msg);
	}

	/**
	 * Save old booking form event
	 *
	 * @return object
	 */
	function saveoldevent()
	{
		// Check authorization
		if (!JFactory::getUser()->authorise('core.edit', 'com_matukio'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$database = JFactory::getDBO();
		$input = JFactory::getApplication()->input;

		$art = 4;

		$event_id = $input->getInt('event_id', 0);
		$uid = 0;

		$notify_participant = $input->getInt("notify_participant", 0);
		$notify_participant_invoice = $input->getInt("notify_participant_invoice", 0);

		$uuid = $input->get('uuid', 0, 'string');
		$nrbooked = $input->getInt('nrbooked', 1);
		$userid = $input->getInt('userid', 0);

		if (empty($event_id))
		{
			return JError::raiseError(404, 'COM_MATUKIO_NO_ID');
		}

		// Load event (use model function)
		$emodel = JModelLegacy::getInstance('Event', 'MatukioModel');
		$event = $emodel->getItem($event_id);

		$reason = "";

		if (!empty($uid))
		{
			// Setting booking to changed booking
			$userid = $uid; // uid = Negativ
		}

		if ($art == 4)
		{
			$allesok = 1;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_SUCCESSFULL');
		}

		// Buchung eintragen
		$neu = JTable::getInstance('bookings', 'Table');

		if (!$neu->bind(JRequest::get('post')))
		{
			return JError::raiseError(500, $database->stderr());
		}

		$neu->semid = $event->id;

		$neu->userid = $userid;

		$neu->bookingdate = MatukioHelperUtilsDate::getCurrentDate();
		$neu->name = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->name);
		$neu->email = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->email);
		$neu->zusatz1 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz1);
		$neu->zusatz2 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz2);
		$neu->zusatz3 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz3);
		$neu->zusatz4 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz4);
		$neu->zusatz5 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz5);
		$neu->zusatz6 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz6);
		$neu->zusatz7 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz7);
		$neu->zusatz8 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz8);
		$neu->zusatz9 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz9);
		$neu->zusatz10 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz10);
		$neu->zusatz11 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz11);
		$neu->zusatz12 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz12);
		$neu->zusatz13 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz13);
		$neu->zusatz14 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz14);
		$neu->zusatz15 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz15);
		$neu->zusatz16 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz16);
		$neu->zusatz17 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz17);
		$neu->zusatz18 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz18);
		$neu->zusatz19 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz19);
		$neu->zusatz20 = MatukioHelperUtilsBasic::cleanHTMLfromText($neu->zusatz20);
		$neu->nrbooked = $nrbooked;

		if (!empty($event->fees))
		{
			$neu->payment_method = "cash";

			// TODO Update to diffrent fees
			if ($nrbooked > 0)
			{
				$neu->payment_brutto = $event->fees * $nrbooked;
			}
			else
			{
				$neu->payment_brutto = $event->fees;
			}
		}

		if (empty($neu->uuid))
		{
			$neu->uuid = MatukioHelperPayment::getUuid(true);
		}

		if (!$neu->check())
		{
			return JError::raiseError(500, $database->stderr());
		}

		if (!$neu->store())
		{
			return JError::raiseError(500, $database->stderr());
		}

		$neu->checkin();

		$ueber1 = JText::_("COM_MATUKIO_BOOKING_WAS_SUCCESSFULL");

		if ($userid == 0)
		{
			$userid = $neu->id * -1;
		}

		// Send new confirmation mail
		if ($notify_participant)
		{
			MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $neu->id, 11, false, $neu, false);
		}

		$viewteilnehmerlink = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $event->id . "&art=2");

		$msg = JText::_("COM_MATUKIO_BOOKING_EDITED");

		$this->setRedirect($viewteilnehmerlink, $msg);
	}

	/**
	 * Cancels the edit
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=bookings';
		$this->setRedirect($link);
	}
}
