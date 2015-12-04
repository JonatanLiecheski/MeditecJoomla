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
 * Class MatukioControllerEvent
 *
 * @since  1.0.0
 */
class MatukioControllerEvent extends JControllerLegacy
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
		$viewName = JFactory::getApplication()->input->get('view', 'event');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Event', 'MatukioModel');
		$view->setModel($model, true);

		$tmpl = MatukioHelperSettings::getSettings("event_template", "default");

		$params = JComponentHelper::getParams('com_matukio');
		$menuitemid = JFactory::getApplication()->input->getInt('Itemid');

		if ($menuitemid)
		{
			$site = new JSite;
			$menu = $site->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			$params->merge($menuparams);
		}

		$ptmpl = $params->get('event_template', '');

		if (!empty($ptmpl))
		{
			$tmpl = $ptmpl;
		}

		$view->setLayout($tmpl);
		$view->display();
	}

	/**
	 * OLD Booking method for old form
	 *
	 * @todo move into a nice function for both backend / Frontend / old and new form
	 * @return mixed
	 */
	public function bookevent()
	{
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		$id = JFactory::getApplication()->input->getInt('cid', 0);
		$uid = JFactory::getApplication()->input->getInt('uid', 0);
		$catid = JFactory::getApplication()->input->getInt('catid', 0);
		$nrbooked = JFactory::getApplication()->input->getInt('nrbooked', 0);
		$name = JFactory::getApplication()->input->get('name', '', 'string');
		$email = JFactory::getApplication()->input->get('email', '', 'string');

		$dispatcher = JDispatcher::getInstance();

		// Edit own booking
		$booking_id = JFactory::getApplication()->input->getInt('booking_id', 0);

		// AGBs
		$veragb = JFactory::getApplication()->input->get('veragb', 0, 'string');

		$isWaitlist = false;

		$reason = "";

		// Load event (use model function)
		$emodel = JModelLegacy::getInstance('Event', 'MatukioModel');
		$row = $emodel->getItem($id);

		$usrid = $my->id;
		$art = 2;

		if ($uid > 0)
		{
			$usrid = $uid;
			$art = 4;
		}

		$sqlid = $usrid;

		if (($name != "" AND $email != "") OR $usrid == 0)
		{
			$usrid = 0;
			$sqlid = -1;
		}

		// Pruefung ob Buchung erfolgreich durchfuehrbar
		$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid='$id' AND userid='$sqlid'");

		$temp = $database->loadObjectList();

		if (!empty($booking_id))
		{
			$temp = null;
		}

		$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($row);
		$gebucht = $gebucht->booked;

		$allesok = 1;
		$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_SUCCESSFULL');

		$pflichtfeld = false;

		$fields = MatukioHelperUtilsEvents::getAdditionalFieldsFrontend($row);

		for ($i = 0; $i < 20; $i++)
		{
			$test = $fields[0][$i];

			if (!empty($test))
			{
				$res = explode("|", $test);

				if (trim($res[1]) == "1")
				{
					$value = JFactory::getApplication()->input->get(("zusatz" . ($i + 1)), '', 'string');

					if (empty($value))
					{
						$pflichtfeld = true;
					}
				}
			}
		}

		if ($my->id > 0)
		{
			$name = $my->name;
			$email = $my->email;
		}
		// }

		if ((empty($name) || empty($email)))
		{
			$allesok = 0;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
			$reason = JTEXT::_('COM_MATUKIO_NO_NAME_OR_EMAIL');
		}
		elseif ($pflichtfeld)
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
		elseif (MatukioHelperUtilsDate::getCurrentDate() > $row->booked)
		{
			$allesok = 0;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
			$reason = JTEXT::_('COM_MATUKIO_EXCEEDED');
		}
		elseif ($row->maxpupil - $gebucht - $nrbooked < 0 && $row->stopbooking == 1)
		{
			$allesok = 0;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
			$reason = JTEXT::_('COM_MATUKIO_MAX_PARTICIPANT_NUMBER_REACHED');
		}
		elseif ($row->maxpupil - $gebucht - $nrbooked < 0 && $row->stopbooking == 0)
		{
			$allesok = 2;
			$ueber1 = JTEXT::_('COM_MATUKIO_ADDED_WAITLIST');
			$reason = JTEXT::_('COM_MATUKIO_YOU_ARE_BOOKED_ON_THE_WAITING_LIST');
			$isWaitlist = true;
		}
		elseif (MatukioHelperSettings::getSettings('agb_text', '') != "" && $veragb != "1")
		{
			$allesok = 0;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_NOT_SUCCESSFULL');
			$reason = JTEXT::_('COM_MATUKIO_AGB_NOT_ACCEPTED');
		}

		if ($art == 4)
		{
			$allesok = 1;
			$ueber1 = JTEXT::_('COM_MATUKIO_BOOKING_WAS_SUCCESSFULL');
		}

		$link = JRoute::_(MatukioHelperRoute::getEventRoute($row->id, $catid), false);
		$msg = "";
		$neu = "";

		// Alles in Ordnung
		if ($allesok > 0)
		{
			// Buchung eintragen
			$neu = JTable::getInstance('bookings', 'Table');

			if (!$neu->bind(JRequest::get('post')))
			{
				return JError::raiseError(500, $database->stderr());
			}

			if (!empty($booking_id))
			{
				$neu->id = $booking_id;
			}

			$neu->semid = $id;
			$neu->userid = $usrid;

			// Hmm really do that?
			$neu->name = $name;
			$neu->email = $email;

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

			// Set booking status to active @since 3.1
			$neu->status = MatukioHelperUtilsBooking::$ACTIVE;

			if (!empty($row->fees))
			{
				$neu->payment_method = "cash";
				$payment_brutto = $row->fees * $neu->nrbooked;
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
						// Perhaps delete this invalid field, or display an error?! TODO
					}
				}

				$neu->payment_brutto = $payment_brutto;
			}

			$results = $dispatcher->trigger('onBeforeSaveBooking', $neu, $row);

			// Set status since @3.1
			if ($isWaitlist)
			{
				// We book to the waitlist, let's set the booking like that then
				$neu->status = MatukioHelperUtilsBooking::$WAITLIST;
			}
			elseif (empty($row->fees))
			{
				// We set the status to active because no payment is done.. so no reason to not confirm them
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
				exit();
			}

			if (!$neu->store())
			{
				JError::raiseError(500, $database->stderr());
				exit();
			}

			$neu->checkin();

			$ueber1 = JText::_("COM_MATUKIO_BOOKING_WAS_SUCCESSFULL");

			if ($usrid == 0)
			{
				$usrid = $neu->id * -1;
			}

			if (MatukioHelperSettings::getSettings("oldbooking_redirect_after", "bookingpage") == "bookingpage")
			{
				$link = JRoute::_(MatukioHelperRoute::getEventRoute($row->id, $catid, 1, $neu->id), false);
			}
			elseif (MatukioHelperSettings::getSettings("oldbooking_redirect_after", "bookingpage") == "eventpage")
			{
				$link = JRoute::_(MatukioHelperRoute::getEventRoute($row->id, $catid, 0, $neu->id), false);
			}
			else
			{
				// Eventlist overview
				$link = JRoute::_("index.php?option=com_matukio&view=eventlist");
			}

			if ($art == 4)
			{
				MatukioHelperUtilsEvents::sendBookingConfirmationMail($row, $neu->id, 8);
			}
			else
			{
				MatukioHelperUtilsEvents::sendBookingConfirmationMail($row, $neu->id, 1);
			}
		}
		else
		{
			$link = JRoute::_(MatukioHelperRoute::getEventRoute($row->id, $catid), false);
		}

		$this->setRedirect($link, $ueber1 . " " . $reason);
	}
}
