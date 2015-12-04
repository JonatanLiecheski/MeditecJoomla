<?php
/**
 * Matukio
 * @package Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 0.9.0 beta $
 **/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

/**
 * Class MatukioControllerEditbooking
 *
 * @since  2.0
 */
class MatukioControllerEditbooking extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('addBooking', 'editBooking');
		$this->registerTask('apply', 'save');
		$this->registerTask('contact', 'contactParticipants');
	}

	/**
	 * Displays the form
	 *
	 * @param   bool  $cachable   - Cache
	 * @param   bool  $urlparams  - Params
	 *
	 * @return  JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'bookings');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Bookings', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Removes the bookings
	 *
	 * @throws  Exception - if query fails
	 * @return  void
	 */
	public function remove()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
		$db = JFactory::getDBO();

		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__matukio_bookings where id IN ( $cids )";
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}
		}

		$this->setRedirect('index.php?option=com_matukio&view=bookings&uid=');
	}

	/**
	 * Publishs / unpublishs the event
	 *
	 * @return  void
	 */

	public function publish()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');

		if ($this->task == 'publish')
		{
			$publish = 1;
		}
		else
		{
			$publish = 0;
		}

		$msg = "";
		$tilesTable = JTable::getInstance('bookings', 'Table');
		$tilesTable->publish($cid, $publish);

		$link = 'index.php?option=com_matukio&view=bookings';

		$this->setRedirect($link, $msg);
	}

	/**
	 * Edit the booking
	 *
	 * @return  void
	 */
	public function editBooking()
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
	 * Saves the form
	 *
	 * @throws  Exception
	 * @return  object
	 */
	public function save()
	{
		$input = JFactory::getApplication()->input;

		if ($input->getInt("oldform", 0) == 1)
		{
			$this->saveOld();

			return;
		}

		$database = JFactory::getDBO();
		$dispatcher = JDispatcher::getInstance();

		// Backend
		$art = 4;

		$event_id = $input->getInt('event_id', 0);
		$uid = $input->getInt('uid', 0);
		$userid = $input->getInt('userid', 0);
		$id = $input->getInt("id", 0);

		$notify_participant = $input->getInt("notify_participant", 0);
		$notify_participant_invoice = $input->getInt("notify_participant_invoice", 0);

		$payment_method = $input->get('payment', '', 'string');

		if (empty($event_id))
		{
			JError::raiseError(404, 'COM_MATUKIO_NO_ID');
		}

		// Load event (use events helper function)
		$event = MatukioHelperUtilsEvents::getEventRecurring($event_id);

		// Different fees @since 3.0
		$different_fees = $event->different_fees;

		$reason = "";

		if (!empty($uid))
		{
			if ($uid < 0)
			{
				// Setting booking to changed booking
				$userid = $uid; // uid = Negativ
			}
		}

		// Checking old required fields - backward compatibilty - only frontend - we allow everything here..
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

		if (empty($id))
		{
			$neu->bookingdate = MatukioHelperUtilsDate::getCurrentDate();
		}

		$neu->name = trim($firstname . " " . $lastname);
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

				if ($different_fees == 0)
				{
					$payment_brutto = $event->fees * $neu->nrbooked;
					$coupon_code = $neu->coupon_code;

					if (!empty($coupon_code))
					{
						$cdate = new DateTime;

						$db = JFactory::getDBO();
						$query = $db->getQuery(true);
						$query->select('*')->from('#__matukio_booking_coupons')
							->where('code = ' . $db->quote($coupon_code));

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
				else
				{
					// Different fees @since 3.0
					$payment_brutto = MatukioHelperFees::getPaymentTotal($event);
					$neu->payment_brutto = $payment_brutto;

					$difarray = array(
						"places" => $input->get("places", array(), 'Array'),
						"types" => $input->get("ticket_fees",  array(), 'Array')
					);

					$neu->different_fees = json_encode($difarray);
				}

				// Taxes
				if ($neu->payment_brutto > 0)
				{
					// Lets check if there are any
					if ($event->tax_id == 0)
					{
						// No taxes
						$neu->payment_netto = $neu->payment_brutto;
						$neu->payment_tax = 0.00;
					}
					else
					{
						$db = JFactory::getDbo();
						$query = $db->getQuery(true);
						$query->select("*")->from("#__matukio_taxes")->where("id = " . $db->quote($event->tax_id) . " AND published = 1");
						$db->setQuery($query);

						$tax = $db->loadObject();

						if (empty($tax))
						{
							// Houston we have a problem
							throw new Exception("Invalid tax value! Please select the correct tax in the event edit form.");
						}
						else
						{
							// Calculate netto
							$minfac = 100 / (100 + $tax->value);

							$neu->payment_netto = $neu->payment_brutto * $minfac;
							$neu->payment_tax = $neu->payment_brutto - $neu->payment_netto;
						}
					}
				}
			}
		}

		$results = $dispatcher->trigger('onBeforeSaveBooking', $neu, $event);

		if (!$neu->check())
		{
			return JError::raiseError(500, $database->stderr());
		}

		if (!$neu->store())
		{
			return JError::raiseError(500, $database->stderr());
		}

		$neu->checkin();

		$results = $dispatcher->trigger('onAfterBooking', $neu, $event);

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

		switch ($this->task)
		{
			case 'apply':
				$msg = JText::_('COM_MATUKIO_BOOKING_APPLY');
				$link = 'index.php?option=com_matukio&controller=editbooking&task=editBooking&booking_id=' . $neu->id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_BOOKING_SAVE');
				$link = 'index.php?option=com_matukio&view=bookings';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * OLD booking form
	 *
	 * @return  object
	 */
	public function saveOld()
	{
		$database = JFactory::getDBO();
		$input = JFactory::getApplication()->input;

		// Backend
		$art = 4;

		$id = $input->getInt("id", 0);
		$event_id = $input->getInt('event_id', 0);

		// Hardcoded to get it working, could cause some new bugs
		$uid = 0;
		$uuid = $input->getInt('uuid', 0);
		$nrbooked = $input->getInt('nrbooked', 1);
		$userid = $input->getInt('userid', 0);

		$notify_participant = $input->getInt("notify_participant", 0);
		$notify_participant_invoice = $input->getInt("notify_participant_invoice", 0);

		if (empty($event_id))
		{
			return JError::raiseError(404, 'COM_MATUKIO_NO_ID');
		}

		$event = JTable::getInstance('matukio', 'Table');
		$event->load($event_id);

		$reason = "";

		if (!empty($uid))
		{
			if ($uid < 0)
			{
				// Setting booking to changed booking
				$userid = $uid; // uid = Negativ

				$art = 4;
			}
		}

		// Checking old required fields - backward compatibilty - only frontend
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

		if (empty($id))
		{
			$neu->bookingdate = MatukioHelperUtilsDate::getCurrentDate();
		}

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

			if ($nrbooked > 0)
			{
				$neu->payment_brutto = $event->fees * $nrbooked;
			}
			else
			{
				$neu->payment_brutto = $event->fees;
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


		// Send new confirmation mail
		if ($notify_participant)
		{
			MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $neu->id, 11, false, $neu, $notify_participant_invoice);
		}


		switch ($this->task)
		{
			case 'apply':
				$msg = JText::_('COM_MATUKIO_BOOKING_FIELD_APPLY');
				$link = 'index.php?option=com_matukio&controller=bookings&task=editBooking&booking_id=' . $neu->id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_BOOKING_FIELD_SAVE');
				$link = 'index.php?option=com_matukio&view=bookings';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Redirects back to bookings overview
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=bookings';
		$this->setRedirect($link);
	}
}
