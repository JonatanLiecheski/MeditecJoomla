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
 * Class MatukioControllerBookevent
 *
 * @since  1.0
 */
class MatukioControllerBookevent extends JControllerLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   bool  $cachable   - Cachable
	 * @param   bool  $urlparams  - Params
	 *
	 * @return  JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'BookEvent');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('BookEvent', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * NEW Booking method for old and new form
	 *
	 * @throws  Exception - if db query fails
	 * @return  mixed
	 */
	public function book()
	{
		$payment_brutto = 0;

		$database = JFactory::getDBO();
		$post = JRequest::get('post');
		$my = JFactory::getUser();

		$input = JFactory::getApplication()->input;

		$event_id = $input->getInt('event_id', 0);
		$uid = $input->getInt('uid', 0);
		$steps = $input->getInt('steps', 3);
		$uuid = $input->get('uuid', 0, 'string');

		$id = $input->getInt('id', 0);
		$booking = null;

		if (!empty($id))
		{
			// check if there is really such a booking
			$model = JModelLegacy::getInstance('Booking', 'MatukioModel');
			$booking = $model->getBooking($uuid);

			if (empty($booking))
			{
				throw new Exception(JText::_("COM_MATUKIO_NO_BOOKING_FOUND"), 404);
			}

			if ($booking->id != $id)
			{
				throw new Exception(JText::_("COM_MATUKIO_NO_BOOKING_FOUND"), 404);
			}
		}

		$nrbooked = $input->getInt('nrbooked', 1);
		$catid = $input->getInt('catid', 0);
		$payment_method = $input->get('payment', '', 'string');
		$agb = $input->get('agb', '', 'string');

		$isWaitlist = false;

		$dispatcher = JDispatcher::getInstance();

		if (empty($event_id))
		{
			throw new Exception(JText::_("COM_MATUKIO_NO_ID"), 404);
		}

		// Load event (use model function)
		$emodel = JModelLegacy::getInstance('Event', 'MatukioModel');
		$event = $emodel->getItem($event_id);

		$userid = $my->id;

		// Different fees @since 3.0
		$different_fees = $event->different_fees;

		$reason = "";
		$art = 2;

		$temp = null;

		$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($event);
		$gebucht = $gebucht->booked;

		$allesok = 1;
		$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_SUCCESSFULL');

		$pflichtfeld = false;

		$fields = MatukioHelperUtilsEvents::getAdditionalFieldsFrontend($event);

		// Checking old required fields - backward compatibilty
		for ($i = 0; $i < 20; $i++)
		{
			$test = $fields[0][$i];

			if (!empty($test))
			{
				$res = explode("|", $test);

				if (trim($res[1]) == "1")
				{
					$value = $input->get(("zusatz" . ($i + 1)), '', 'string');

					if (empty($value))
					{
						$pflichtfeld = true;
					}
				}
			}
		}

		if (MatukioHelperSettings::getSettings("captcha", 0) == 1)
		{
			$ccval = $input->get("ccval", '', 'string');
			$captcha = $input->get("captcha", '', 'string');

			if (empty($captcha))
			{
				$allesok = 0;
				$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
				$reason = JTEXT::_('COM_MATUKIO_CAPTCHA_WRONG');
			}
			elseif (md5($captcha) != $ccval)
			{
				$allesok = 0;
				$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
				$reason = JTEXT::_('COM_MATUKIO_CAPTCHA_WRONG');
			}
		}

		if (MatukioHelperSettings::getSettings("recaptcha", 0) == 1)
		{
			require_once JPATH_COMPONENT_ADMINISTRATOR . '/include/recaptcha/recaptchalib.php';

			$key = MatukioHelperSettings::getSettings("recaptcha_private_key", "");

			if (empty($key))
			{
				throw new Exception("COM_MATUKIO_YOU_HAVE_TO_SET_A_RECAPTCHA_KEY", 500);
			}

			$resp = recaptcha_check_answer(
				$key,
				$_SERVER["REMOTE_ADDR"],
				$_POST["recaptcha_challenge_field"],
				$_POST["recaptcha_response_field"]
			);

			if (!$resp->is_valid)
			{
				// What happens when the CAPTCHA was entered incorrectly
				$allesok = 0;
				$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
				$reason = JTEXT::_('COM_MATUKIO_CAPTCHA_WRONG') . $resp->error;
			}
		}

		$agbtext = MatukioHelperSettings::getSettings("agb_text", "");

		if ($pflichtfeld)
		{
			$allesok = 0;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
			$reason = JTEXT::_('COM_MATUKIO_REQUIRED_ADDITIONAL_FIELD_EMPTY');
		}
		elseif (count($temp) > 0)
		{
			$allesok = 0;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
			$reason = JTEXT::_('COM_MATUKIO_REGISTERED_FOR_THIS_EVENT');
		}
		elseif (MatukioHelperUtilsDate::getCurrentDate() > $event->booked)
		{
			echo "current: " . MatukioHelperUtilsDate::getCurrentDate();
			echo " booking: " . $event->booked;
			$allesok = 0;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
			$reason = JTEXT::_('COM_MATUKIO_EXCEEDED');
		}
		elseif ($event->maxpupil - $gebucht - $nrbooked < 0 && $event->stopbooking == 1)
		{
			$allesok = 0;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
			$reason = JTEXT::_('COM_MATUKIO_MAX_PARTICIPANT_NUMBER_REACHED');
		}
		elseif (!empty($agbtext))
		{
			// Has to be on the end
			if (empty($agb))
			{
				$allesok = 0;
				$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
				$reason = JTEXT::_('COM_MATUKIO_AGB_NOT_ACCEPTED');
			}
		}

		if ($event->maxpupil - $gebucht - $nrbooked < 0 && $event->stopbooking == 0)
		{
			$allesok = 2;
			$ueber1 = JTEXT::_('COM_MATUKIO_ADDED_WAITLIST');
			$reason = JTEXT::_('COM_MATUKIO_YOU_ARE_BOOKED_ON_THE_WAITING_LIST');

			if (empty($booking))
			{
				$isWaitlist = true;
			}
			else
			{
				// Prevent switching old booking to waitlist
				if ($booking->status == MatukioHelperUtilsBooking::$WAITLIST)
				{
					$isWaitlist = true;
				}
				else
				{
					$isWaitlist = false;
				}
			}
		}

		if ($art == 4)
		{
			$allesok = 1;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_SUCCESSFULL');
		}

		$results = $dispatcher->trigger('onValidateBooking', $post, $event, $allesok);

		// Alles in Ordnung
		if ($allesok > 0)
		{
			// Buchung eintragen
			$neu = JTable::getInstance('bookings', 'Table');

			if (!$neu->bind($post))
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

				if (!empty($event->fees) && $steps > 2)
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
								->where('code = ' . $db->quote($coupon_code) . ' AND published = 1 AND (published_up < '
									. $db->quote($cdate->format('Y-m-d H:i:s')) . ' OR published_up = ' . $db->quote("0000-00-00 00:00:00") . ') '
									. 'AND (published_down > ' . $db->quote($cdate->format('Y-m-d H:i:s'))
									. ' OR published_down = ' . $db->quote("0000-00-00 00:00:00") . ')'
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
									// Get a real value
									$payment_brutto = $payment_brutto - $coupon->value;
								}

								// Check how often the coupon is used and if used to often set published to 0 (since 3.0.0)
								$coupon->hits++;

								// Check if coupon has to be disabled now
								if (!empty($coupon->max_hits) && $coupon->hits >= $coupon->max_hits)
								{
									$coupon->published = 0;
								}

								$coupontable = JTable::getInstance('coupons', 'Table');

								if (!$coupontable->bind($coupon))
								{
									throw new Exception(42, $database->stderr());
								}

								if (!$coupontable->check())
								{
									throw new Exception(42, $database->stderr());
								}

								if (!$coupontable->store())
								{
									throw new Exception(42, $database->stderr());
								}

								$coupontable->checkin();
							}
							else
							{
								// Perhaps delete this invalid field, or display an error?! Should be validated through js normally
								throw new Exception(JText::_("COM_MATUKIO_INVALID_COUPON_CODE"), 42);
							}
						}

						$neu->payment_brutto = $payment_brutto;
					}
					else
					{
						// Different fees
						$payment_brutto = MatukioHelperFees::getPaymentTotal($event);
						$neu->payment_brutto = $payment_brutto;

						$difarray = array(
							"places" => $input->get("places", array(), 'Array'),
							"types" => $input->get("ticket_fees",  array(), 'Array')
						);

						$neu->different_fees = json_encode($difarray);
					}
				}
				elseif (!empty($event->fees))
				{
					if ($different_fees == 0)
					{
						// We have disabled payment plugins but a fee
						// Only calculate total amount
						$payment_brutto = $event->fees * $neu->nrbooked;
						$neu->payment_brutto = $payment_brutto;
					}
					else
					{
						// Different fees
						$payment_brutto = MatukioHelperFees::getPaymentTotal($event);

						$neu->payment_brutto = $payment_brutto;

						$difarray = array(
							"places" => $input->get("places", array(), 'Array'),
							"types" => $input->get("ticket_fees",  array(), 'Array')
						);

						$neu->different_fees = json_encode($difarray);
					}
				}
			}
			else
			{
				// Only calculate total amount
				$payment_brutto = $event->fees * $neu->nrbooked;
				$neu->payment_brutto = $event->fees * $neu->nrbooked;
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

			$results = $dispatcher->trigger('onBeforeSaveBooking', $neu, $event);

			// Set status since @3.1
			if (!empty($booking))
			{
				// We don't update the status for old bookings
				$neu->status = $booking->status;
			}
			elseif ($isWaitlist)
			{
				// We book to the waitlist, let's set the booking like that then
				$neu->status = MatukioHelperUtilsBooking::$WAITLIST;
			}
			elseif (MatukioHelperSettings::_("booking_always_inactive", 0))
			{
				// We set the status to pending
				$neu->status = MatukioHelperUtilsBooking::$PENDING;
			}
			elseif (empty($event->fees))
			{
				// We set the status to active because no payment is done.. so no reason to not confirm them
				$neu->status = MatukioHelperUtilsBooking::$ACTIVE;
			}
			elseif ($steps == 2)
			{
				// We set the status to active because no payment can be done.. so no reason to not confirm them
				$neu->status = MatukioHelperUtilsBooking::$ACTIVE;
			}
			elseif ($payment_method == "cash")
			{
				// We check if the booking always active setting is set - if yes the booking is always!! active (except waitlist delete etc.)
				$neu->status = MatukioHelperUtilsBooking::$ACTIVE;
			}
			elseif (MatukioHelperSettings::_("booking_always_active", 0))
			{
				// We check if the booking always active setting is set - if yes the booking is always!! active (except waitlist delete etc.)
				$neu->status = MatukioHelperUtilsBooking::$ACTIVE;
			}
			else
			{
				// We set the status to pending - this is going to be overwritten by the payment API (if paid etc.)
				$neu->status = MatukioHelperUtilsBooking::$PENDING;
			}

			if (!$neu->check())
			{
				JError::raiseError(500, $database->stderr());
			}

			if (!$neu->store())
			{
				JError::raiseError(500, $database->stderr());
			}

			$neu->checkin();

			$results = $dispatcher->trigger('onAfterBookingSave', $neu, $event);

			$ueber1 = JText::_("COM_MATUKIO_BOOKING_WAS_SUCCESSFULL");

			$booking_id = $neu->id;
		}

		if ($payment_brutto > 0 && $steps > 2)
		{
			// Link to the payment form
			$link = JRoute::_("index.php?option=com_matukio&view=paymentform&uuid=" . $uuid);
			$this->setRedirect($link, $reason);
		}
		else
		{
			if ($allesok > 0)
			{
				// We need to send the confirmation here.. we don't send it yet if the event has payment processing
				MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $neu->id, 1);
			}

			// Link to the bookingpage
			if (MatukioHelperSettings::getSettings("oldbooking_redirect_after", "bookingpage") == "bookingpage")
			{
				$link = JRoute::_("index.php?option=com_matukio&view=booking&uuid=" . $neu->uuid);
			}
			elseif (MatukioHelperSettings::getSettings("oldbooking_redirect_after", "bookingpage") == "eventpage")
			{
				$link = JRoute::_(MatukioHelperRoute::getEventRoute($event->id, $catid, 0, $booking_id), false);
			}
			else
			{
				// Eventlist overview
				$link = JRoute::_("index.php?option=com_matukio&view=eventlist");
			}

			$this->setRedirect($link, $ueber1 . " " . $reason);
		}
	}

	/**
	 * Cancel booking
	 *
	 * $unbookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&task=cancelBooking&cid=" . $this->id);
	 *
	 * @throws  Exception on DB error
	 * @return  mixed
	 */
	public function cancelBooking()
	{
		$cid = JFactory::getApplication()->input->getInt('cid', 0);
		$uid = JFactory::getApplication()->input->getInt('booking_id', 0);
		$uuid = JFactory::getApplication()->input->get('uuid', 0);

		$booking = null;

		if (!empty($cid))
		{
			$link = JRoute::_('index.php?option=com_matukio&view=event&id=' . $cid);
		}
		else
		{
			$link = JRoute::_('index.php?option=com_matukio&view=eventlist');
		}

		if (empty($cid) && empty($uid) && empty($uuid))
		{
			throw new Exception(JText::_("COM_MATUKIO_NO_ID"), 404);
		}

		$msg = JText::_("COM_MATUKIO_BOOKING_ANNULATION_SUCESSFULL");

		$database = JFactory::getDBO();
		$user = JFactory::getuser();

		$event = MatukioHelperUtilsEvents::getEventRecurring($cid);

		if (!empty($uid))
		{
			if ($user->id == 0)
			{
				throw new Exception("COM_MATUKIO_NO_ACCESS", 403);
			}

			$database->setQuery("UPDATE #__matukio_bookings SET status = '" . MatukioHelperUtilsBooking::$DELETED . "' WHERE id = '" . $uid . "'");
		}
		else if (!empty($uuid))
		{
			// First load booking
			$database->setQuery("SELECT * FROM #__matukio_bookings WHERE uuid = '" . $uuid . "'");
			$booking = $database->loadObject();

			if (empty($booking))
			{
				throw new Exception(JText::_("COM_MATUKIO_NO_ID"), 404);
			}

			$uid = $booking->id;

			$database->setQuery("UPDATE #__matukio_bookings SET status = '" . MatukioHelperUtilsBooking::$DELETED . "' WHERE uuid = '" . $uuid . "'");
		}
		else
		{
			if ($user->id == 0)
			{
				throw new Exception("COM_MATUKIO_NO_ACCESS", 403);
			}
			else
			{
				$uid = $user->id;

				$database->setQuery(
					"UPDATE #__matukio_bookings SET status = '" . MatukioHelperUtilsBooking::$DELETED
					. "' WHERE semid = " . $cid . " AND userid = '" . $user->id . "'"
				);
			}
		}

		if (!$database->execute())
		{
			JError::raiseError(500, $database->getError());
		}

		MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $uid, 2, true, $booking);
		$this->setRedirect($link, $msg);
	}
}
