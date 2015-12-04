<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       25.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


defined('_JEXEC') or die ('Restricted access');

jimport('joomla.event.dispatcher');

/**
 * Class MatukioHelperTemplates
 *
 * @since  2.2.0
 */
class MatukioHelperTemplates
{
	private static $instance;

	/**
	 * Gets the replacements for the event & (booking)
	 *
	 * @param   object  $event    - The event data
	 * @param   object  $booking  - The booking data
	 * @param   int     $nr       - the number
	 *
	 * @return mixed
	 */
	public static function getReplaces($event, $booking = null, $nr = null)
	{
		$needles = array(
			'event' => (int) $event->id,
			'category' => 0
		);

		$itemId = MatukioHelperRoute::_findItem($needles)->id;

		if ($nr != null)
		{
			$replaces["MAT_NR"] = $nr;
		}

		// Current date
		$replaces["MAT_DATE"] = JHTML::_('date', '', MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'));

		// Signature
		$replaces["MAT_SIGNATURE"] = MatukioHelperSettings::getSettings('mat_signature', 'Please do not answer this E-Mail.');

		// Event data
		$replaces["MAT_EVENT_SEMNUM"] = $event->semnum;

		// Alias
		$replaces["MAT_EVENT_NUMBER"] = $event->semnum;

		$replaces["MAT_EVENT_CATID"] = $event->catid;

		$replaces["MAT_EVENT_CATEGORY"] = JText::_($event->category);

		$replaces["MAT_EVENT_TITLE"] = JText::_($event->title);
		$replaces["MAT_EVENT_TARGET"] = JText::_($event->target);
		$replaces["MAT_EVENT_SHORTDESC"] = JText::_($event->shortdesc);

		// TODO change image path
		$replaces["MAT_EVENT_DESCRIPTION"] = JHTML::_('content.prepare', $event->description);

		$location = $event->place;

		// Locations
		if ($event->place_id > 0)
		{
			$locobj = MatukioHelperUtilsEvents::getLocation($event->place_id);
			$location = $locobj->location;
		}

		$replaces["MAT_EVENT_PLACE"] = $location;
		$replaces["MAT_EVENT_LOCATION"] = $location;

		$replaces["MAT_EVENT_TEACHER"] = $event->teacher;

		// Alias
		$replaces["MAT_EVENT_TUTOR"] = $event->teacher;

		if ($event->fees > 0)
		{
			$replaces["MAT_EVENT_FEES"] = MatukioHelperUtilsEvents::getFormatedCurrency($event->fees, MatukioHelperSettings::_('currency_symbol', '$'));
		}
		else
		{
			$replaces["MAT_EVENT_FEES"] = JText::_("COM_MATUKIO_FREE");
		}

		$replaces["MAT_EVENT_ORGANIZER_ID"] = $event->publisher;
		$replaces["MAT_EVENT_ORGANIZER"] = JFactory::getUser($event->publisher)->name;

		$replaces["MAT_EVENT_MAXPUPIL"] = $event->maxpupil;
		$replaces["MAT_EVENT_BOOKEDPUPIL"] = $event->bookedpupil;

		$replaces["MAT_EVENT_BEGIN"] = JHTML::_('date', $event->begin, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i'));
		$replaces["MAT_EVENT_END"] = JHTML::_('date', $event->end, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i'));
		$replaces["MAT_EVENT_BOOKED"] = JHTML::_('date', $event->booked, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i'));

		$replaces["MAT_EVENT_GMAPLOC"] = $event->gmaploc;
		$replaces["MAT_EVENT_NRBOOKED"] = $event->nrbooked;


		/* Alias */

		$custom10 = explode("|", $event->zusatz10);
		$custom11 = explode("|", $event->zusatz11);
		$custom12 = explode("|", $event->zusatz12);
		$custom13 = explode("|", $event->zusatz13);
		$custom14 = explode("|", $event->zusatz14);
		$custom15 = explode("|", $event->zusatz15);
		$custom16 = explode("|", $event->zusatz16);
		$custom17 = explode("|", $event->zusatz17);
		$custom18 = explode("|", $event->zusatz18);
		$custom19 = explode("|", $event->zusatz19);
		$custom20 = explode("|", $event->zusatz20);
		$custom1 = explode("|", $event->zusatz1);
		$custom2 = explode("|", $event->zusatz2);
		$custom3 = explode("|", $event->zusatz3);
		$custom4 = explode("|", $event->zusatz4);
		$custom5 = explode("|", $event->zusatz5);
		$custom6 = explode("|", $event->zusatz6);
		$custom7 = explode("|", $event->zusatz7);
		$custom8 = explode("|", $event->zusatz8);
		$custom9 = explode("|", $event->zusatz9);

		$replaces["MAT_EVENT_CUSTOM10"] = $custom10[0];
		$replaces["MAT_EVENT_CUSTOM11"] = $custom11[0];
		$replaces["MAT_EVENT_CUSTOM12"] = $custom12[0];
		$replaces["MAT_EVENT_CUSTOM13"] = $custom13[0];
		$replaces["MAT_EVENT_CUSTOM14"] = $custom14[0];
		$replaces["MAT_EVENT_CUSTOM15"] = $custom15[0];
		$replaces["MAT_EVENT_CUSTOM16"] = $custom16[0];
		$replaces["MAT_EVENT_CUSTOM17"] = $custom17[0];
		$replaces["MAT_EVENT_CUSTOM18"] = $custom18[0];
		$replaces["MAT_EVENT_CUSTOM19"] = $custom19[0];
		$replaces["MAT_EVENT_CUSTOM20"] = $custom20[0];
		$replaces["MAT_EVENT_CUSTOM1"] = $custom1[0];
		$replaces["MAT_EVENT_CUSTOM2"] = $custom2[0];
		$replaces["MAT_EVENT_CUSTOM3"] = $custom3[0];
		$replaces["MAT_EVENT_CUSTOM4"] = $custom4[0];
		$replaces["MAT_EVENT_CUSTOM5"] = $custom5[0];
		$replaces["MAT_EVENT_CUSTOM6"] = $custom6[0];
		$replaces["MAT_EVENT_CUSTOM7"] = $custom7[0];
		$replaces["MAT_EVENT_CUSTOM8"] = $custom8[0];
		$replaces["MAT_EVENT_CUSTOM9"] = $custom9[0];

		$replaces["MAT_EVENT_ZUSATZ10"] = $replaces["MAT_EVENT_CUSTOM10"];
		$replaces["MAT_EVENT_ZUSATZ11"] = $replaces["MAT_EVENT_CUSTOM11"];
		$replaces["MAT_EVENT_ZUSATZ12"] = $replaces["MAT_EVENT_CUSTOM12"];
		$replaces["MAT_EVENT_ZUSATZ13"] = $replaces["MAT_EVENT_CUSTOM13"];
		$replaces["MAT_EVENT_ZUSATZ14"] = $replaces["MAT_EVENT_CUSTOM14"];
		$replaces["MAT_EVENT_ZUSATZ15"] = $replaces["MAT_EVENT_CUSTOM15"];
		$replaces["MAT_EVENT_ZUSATZ16"] = $replaces["MAT_EVENT_CUSTOM16"];
		$replaces["MAT_EVENT_ZUSATZ17"] = $replaces["MAT_EVENT_CUSTOM17"];
		$replaces["MAT_EVENT_ZUSATZ18"] = $replaces["MAT_EVENT_CUSTOM18"];
		$replaces["MAT_EVENT_ZUSATZ19"] = $replaces["MAT_EVENT_CUSTOM19"];
		$replaces["MAT_EVENT_ZUSATZ20"] = $replaces["MAT_EVENT_CUSTOM20"];
		$replaces["MAT_EVENT_ZUSATZ1"] = $replaces["MAT_EVENT_CUSTOM1"];
		$replaces["MAT_EVENT_ZUSATZ2"] = $replaces["MAT_EVENT_CUSTOM2"];
		$replaces["MAT_EVENT_ZUSATZ3"] = $replaces["MAT_EVENT_CUSTOM3"];
		$replaces["MAT_EVENT_ZUSATZ4"] = $replaces["MAT_EVENT_CUSTOM4"];
		$replaces["MAT_EVENT_ZUSATZ5"] = $replaces["MAT_EVENT_CUSTOM5"];
		$replaces["MAT_EVENT_ZUSATZ6"] = $replaces["MAT_EVENT_CUSTOM6"];
		$replaces["MAT_EVENT_ZUSATZ7"] = $replaces["MAT_EVENT_CUSTOM7"];
		$replaces["MAT_EVENT_ZUSATZ8"] = $replaces["MAT_EVENT_CUSTOM8"];
		$replaces["MAT_EVENT_ZUSATZ9"] = $replaces["MAT_EVENT_CUSTOM9"];

		$replaces["MAT_EVENT_ZUSATZ10HINT"] = $event->zusatz10hint;
		$replaces["MAT_EVENT_ZUSATZ11HINT"] = $event->zusatz11hint;
		$replaces["MAT_EVENT_ZUSATZ12HINT"] = $event->zusatz12hint;
		$replaces["MAT_EVENT_ZUSATZ13HINT"] = $event->zusatz13hint;
		$replaces["MAT_EVENT_ZUSATZ14HINT"] = $event->zusatz14hint;
		$replaces["MAT_EVENT_ZUSATZ15HINT"] = $event->zusatz15hint;
		$replaces["MAT_EVENT_ZUSATZ16HINT"] = $event->zusatz16hint;
		$replaces["MAT_EVENT_ZUSATZ17HINT"] = $event->zusatz17hint;
		$replaces["MAT_EVENT_ZUSATZ18HINT"] = $event->zusatz18hint;
		$replaces["MAT_EVENT_ZUSATZ19HINT"] = $event->zusatz19hint;
		$replaces["MAT_EVENT_ZUSATZ20HINT"] = $event->zusatz20hint;
		$replaces["MAT_EVENT_ZUSATZ1HINT"] = $event->zusatz1hint;
		$replaces["MAT_EVENT_ZUSATZ2HINT"] = $event->zusatz2hint;
		$replaces["MAT_EVENT_ZUSATZ3HINT"] = $event->zusatz3hint;
		$replaces["MAT_EVENT_ZUSATZ4HINT"] = $event->zusatz4hint;
		$replaces["MAT_EVENT_ZUSATZ5HINT"] = $event->zusatz5hint;
		$replaces["MAT_EVENT_ZUSATZ6HINT"] = $event->zusatz6hint;
		$replaces["MAT_EVENT_ZUSATZ7HINT"] = $event->zusatz7hint;
		$replaces["MAT_EVENT_ZUSATZ8HINT"] = $event->zusatz8hint;
		$replaces["MAT_EVENT_ZUSATZ9HINT"] = $event->zusatz9hint;

		/* ALIAS */
		$replaces["MAT_EVENT_CUSTOM10HINT"] = $event->zusatz10hint;
		$replaces["MAT_EVENT_CUSTOM11HINT"] = $event->zusatz11hint;
		$replaces["MAT_EVENT_CUSTOM12HINT"] = $event->zusatz12hint;
		$replaces["MAT_EVENT_CUSTOM13HINT"] = $event->zusatz13hint;
		$replaces["MAT_EVENT_CUSTOM14HINT"] = $event->zusatz14hint;
		$replaces["MAT_EVENT_CUSTOM15HINT"] = $event->zusatz15hint;
		$replaces["MAT_EVENT_CUSTOM16HINT"] = $event->zusatz16hint;
		$replaces["MAT_EVENT_CUSTOM17HINT"] = $event->zusatz17hint;
		$replaces["MAT_EVENT_CUSTOM18HINT"] = $event->zusatz18hint;
		$replaces["MAT_EVENT_CUSTOM19HINT"] = $event->zusatz19hint;
		$replaces["MAT_EVENT_CUSTOM20HINT"] = $event->zusatz20hint;
		$replaces["MAT_EVENT_CUSTOM1HINT"] = $event->zusatz1hint;
		$replaces["MAT_EVENT_CUSTOM2HINT"] = $event->zusatz2hint;
		$replaces["MAT_EVENT_CUSTOM3HINT"] = $event->zusatz3hint;
		$replaces["MAT_EVENT_CUSTOM4HINT"] = $event->zusatz4hint;
		$replaces["MAT_EVENT_CUSTOM5HINT"] = $event->zusatz5hint;
		$replaces["MAT_EVENT_CUSTOM6HINT"] = $event->zusatz6hint;
		$replaces["MAT_EVENT_CUSTOM7HINT"] = $event->zusatz7hint;
		$replaces["MAT_EVENT_CUSTOM8HINT"] = $event->zusatz8hint;
		$replaces["MAT_EVENT_CUSTOM9HINT"] = $event->zusatz9hint;

		$replaces["MAT_EVENT_CREATED_BY"] = $event->created_by;
		$replaces["MAT_EVENT_MODIFIED_BY"] = $event->modified_by;

		$replaces["MAT_EVENT_CREATED"] = JHTML::_('date', $event->created, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i'));

		$replaces["MAT_EVENT_WEBINAR"] = $event->webinar;

		if ($booking != null)
		{
			if (empty($booking->sid))
			{
				$booking->sid = $booking->id;
			}

			// Booking data
			$replaces["MAT_BOOKING_ID"] = $booking->sid;
			$replaces["MAT_BOOKING_NUMBER"] = MatukioHelperUtilsBooking::getBookingId($booking->sid);
			$replaces["MAT_SIGN"] = "<span> </span>";
			$user = JFactory::getUser($booking->userid);

			$replaces["MAT_BOOKING_USERNAME"] = "";

			if ($booking->userid > 0)
			{
				$replaces["MAT_BOOKING_USERNAME"] = $user->username;
			}

			if (isset($booking->aname))
			{
				$booking->aname = trim($booking->aname);
			}

			if (isset($booking->name))
			{
				$booking->name = trim($booking->name);
			}

			// Old form
			if (isset($booking->aname) && !empty($booking->aname))
			{
				$replaces["MAT_BOOKING_NAME"] = $booking->aname;
			}
			elseif (isset($booking->name) && !empty($booking->name))
			{
				$replaces["MAT_BOOKING_NAME"] = $booking->name;
			}

			if (isset($booking->aemail) && !empty($booking->aemail))
			{
				$replaces["MAT_BOOKING_EMAIL"] = $booking->aemail;
			}
			elseif (isset($booking->email) && !empty($booking->email))
			{
				$replaces["MAT_BOOKING_EMAIL"] = $booking->email;
			}

			// Fix for empty E-Mail
			if (empty($replaces["MAT_BOOKING_EMAIL"]))
			{
				$replaces["MAT_BOOKING_EMAIL"] = $booking->email;
			}

			$replaces["MAT_BOOKING_SID"] = $booking->sid;
			$replaces["MAT_BOOKING_SEMID"] = $booking->semid;
			$replaces["MAT_BOOKING_USERID"] = $booking->userid;
			$replaces["MAT_BOOKING_CERTIFICATED"] = $booking->certificated;

			$replaces["MAT_EVENT_BOOKINGDATE"] = JHTML::_('date', $booking->bookingdate, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i'));
			$replaces["MAT_EVENT_UPDATED"] = JHTML::_('date', $booking->updated, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i'));

			$replaces["MAT_INVOICE_DATE"] = JHTML::_('date', $booking->bookingdate, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'));

			if ($booking->payment_brutto > 0)
			{
				$replaces["MAT_INVOICE_NUMBER"] = MatukioHelperInvoice::getInvoiceNumber($booking->id, JHTML::_('date', $booking->bookingdate, 'Y'));
			}
			else
			{
				$replaces["MAT_INVOICE_NUMBER"] = "";
			}

			$replaces["MAT_BOOKING_PAID"] = MatukioHelperUtilsBooking::getBookingPaidName($booking->paid);
			$replaces["MAT_BOOKING_PAID_NUM"] = $booking->paid;

			$replaces["MAT_BOOKING_PAYMENT_STATUS"] = $booking->payment_status;
			$replaces["MAT_BOOKING_PAYMENT_PLUGIN_DATA"] = $booking->payment_plugin_data;

			$replaces["MAT_BOOKING_NRBOOKED"] = $booking->nrbooked;

			// Alias
			$replaces["MAT_BOOKING_BOOKEDNR"] = $booking->nrbooked;

			$replaces["MAT_BOOKING_ZUSATZ10"] = $booking->zusatz10;
			$replaces["MAT_BOOKING_ZUSATZ11"] = $booking->zusatz11;
			$replaces["MAT_BOOKING_ZUSATZ12"] = $booking->zusatz12;
			$replaces["MAT_BOOKING_ZUSATZ13"] = $booking->zusatz13;
			$replaces["MAT_BOOKING_ZUSATZ14"] = $booking->zusatz14;
			$replaces["MAT_BOOKING_ZUSATZ15"] = $booking->zusatz15;
			$replaces["MAT_BOOKING_ZUSATZ16"] = $booking->zusatz16;
			$replaces["MAT_BOOKING_ZUSATZ17"] = $booking->zusatz17;
			$replaces["MAT_BOOKING_ZUSATZ18"] = $booking->zusatz18;
			$replaces["MAT_BOOKING_ZUSATZ19"] = $booking->zusatz19;
			$replaces["MAT_BOOKING_ZUSATZ20"] = $booking->zusatz20;
			$replaces["MAT_BOOKING_ZUSATZ1"] = $booking->zusatz1;
			$replaces["MAT_BOOKING_ZUSATZ2"] = $booking->zusatz2;
			$replaces["MAT_BOOKING_ZUSATZ3"] = $booking->zusatz3;
			$replaces["MAT_BOOKING_ZUSATZ4"] = $booking->zusatz4;
			$replaces["MAT_BOOKING_ZUSATZ5"] = $booking->zusatz5;
			$replaces["MAT_BOOKING_ZUSATZ6"] = $booking->zusatz6;
			$replaces["MAT_BOOKING_ZUSATZ7"] = $booking->zusatz7;
			$replaces["MAT_BOOKING_ZUSATZ8"] = $booking->zusatz8;
			$replaces["MAT_BOOKING_ZUSATZ9"] = $booking->zusatz9;

			/* Alias */
			$replaces["MAT_BOOKING_CUSTOM10"] = $booking->zusatz10;
			$replaces["MAT_BOOKING_CUSTOM11"] = $booking->zusatz11;
			$replaces["MAT_BOOKING_CUSTOM12"] = $booking->zusatz12;
			$replaces["MAT_BOOKING_CUSTOM13"] = $booking->zusatz13;
			$replaces["MAT_BOOKING_CUSTOM14"] = $booking->zusatz14;
			$replaces["MAT_BOOKING_CUSTOM15"] = $booking->zusatz15;
			$replaces["MAT_BOOKING_CUSTOM16"] = $booking->zusatz16;
			$replaces["MAT_BOOKING_CUSTOM17"] = $booking->zusatz17;
			$replaces["MAT_BOOKING_CUSTOM18"] = $booking->zusatz18;
			$replaces["MAT_BOOKING_CUSTOM19"] = $booking->zusatz19;
			$replaces["MAT_BOOKING_CUSTOM20"] = $booking->zusatz20;
			$replaces["MAT_BOOKING_CUSTOM1"] = $booking->zusatz1;
			$replaces["MAT_BOOKING_CUSTOM2"] = $booking->zusatz2;
			$replaces["MAT_BOOKING_CUSTOM3"] = $booking->zusatz3;
			$replaces["MAT_BOOKING_CUSTOM4"] = $booking->zusatz4;
			$replaces["MAT_BOOKING_CUSTOM5"] = $booking->zusatz5;
			$replaces["MAT_BOOKING_CUSTOM6"] = $booking->zusatz6;
			$replaces["MAT_BOOKING_CUSTOM7"] = $booking->zusatz7;
			$replaces["MAT_BOOKING_CUSTOM8"] = $booking->zusatz8;
			$replaces["MAT_BOOKING_CUSTOM9"] = $booking->zusatz9;

			$replaces["MAT_BOOKING_UUID"] = $booking->uuid;

			if (!empty($booking->payment_method))
			{
				$replaces["MAT_BOOKING_PAYMENT_METHOD"] = self::getPaymentMethodTitle($booking->payment_method);
			}
			else
			{
				$replaces["MAT_BOOKING_PAYMENT_METHOD"] = "";
			}

			$replaces["MAT_BOOKING_PAYMENT_METHOD_RAW"] = $booking->payment_method;
			$replaces["MAT_BOOKING_PAYMENT_NUMBER"] = $booking->payment_number;

			$replaces["MAT_BOOKING_PAYMENT_NETTO"] = MatukioHelperUtilsEvents::getFormatedCurrency($booking->payment_netto, MatukioHelperSettings::_('currency_symbol', '$'));
			$replaces["MAT_BOOKING_PAYMENT_NET"] = $replaces["MAT_BOOKING_PAYMENT_NETTO"];

			$replaces["MAT_BOOKING_PAYMENT_TAX"] = MatukioHelperUtilsEvents::getFormatedCurrency($booking->payment_tax, MatukioHelperSettings::_('currency_symbol', '$'));

			$replaces["MAT_BOOKING_PAYMENT_BRUTTO"] = MatukioHelperUtilsEvents::getFormatedCurrency($booking->payment_brutto, MatukioHelperSettings::_('currency_symbol', '$'));
			$replaces["MAT_BOOKING_PAYMENT_GROSS"] = $replaces["MAT_BOOKING_PAYMENT_BRUTTO"];

			$replaces["MAT_BOOKING_COUPON_CODE"] = $booking->coupon_code;

			$replaces["MAT_BOOKING_STATUS"] = MatukioHelperUtilsBooking::getBookingStatusName($booking->status);

			/* Mark since 4.5 */
			$replaces["MAT_BOOKING_MARK"] = $booking->mark;

			/* QR Codes */
			$replaces["MAT_BOOKING_QRCODE_ID"] = MatukioHelperUtilsBooking::getBookingIdQRCode($booking->sid);
			$replaces["MAT_BOOKING_QRCODE"] = $replaces["MAT_BOOKING_QRCODE_ID"];

			$replaces["MAT_BOOKING_BARCODE"] = MatukioHelperUtilsBooking::getBookingIdBarcode($booking->sid);

			// Checkin URLs and QR-Codes since 4.5
			$checkinurl = JRoute::_("index.php?option=com_matukio&view=booking&task=checkin&uuid=" . $booking->uuid . "&Itemid=" . $itemId, true, -1);

			$replaces["MAT_BOOKING_CHECKIN_URL"] = $checkinurl;
			$replaces["MAT_BOOKING_CHECKIN_LINK"] = '<a href="' . $checkinurl . '">'
				. JText::_("COM_MATUKIO_CHECKIN_BOOKING") . "</a>";

			$replaces["MAT_BOOKING_CHECKIN_QRCODE"] = MatukioHelperUtilsBooking::getBookingCheckinQRCode($checkinurl, $booking->sid);

			if ($event->fees > 0)
			{
				$replaces["MAT_BOOKING_FEES_STATUS"] = MatukioHelperUtilsEvents::getFormatedCurrency($booking->payment_brutto, MatukioHelperSettings::_('currency_symbol', '$'))
					. " (" . MatukioHelperUtilsBooking::getBookingPaidName($booking->paid) . ")";
			}
			else
			{
				$replaces["MAT_BOOKING_FEES_STATUS"] = JText::_("COM_MATUKIO_FREE_EVENT");
			}

			// Booking detail page @since 4.1.1
			$replaces["MAT_BOOKING_DETAILPAGE_URL"] = JRoute::_("index.php?option=com_matukio&view=booking&uuid=" . $booking->uuid . "&Itemid=" . $itemId, true, -1);
			$replaces["MAT_BOOKING_DETAILPAGE"] =
				'<a href="' . JRoute::_("index.php?option=com_matukio&view=booking&uuid=" . $booking->uuid . "&Itemid=" . $itemId, true, -1) . '">'
				. JText::_("COM_MATUKIO_BOOKING_DETAILS") . "</a>";

			$editbookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $booking->semid . "&uuid=" . $booking->uuid . "&Itemid=" . $itemId, true, -1);

			if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1)
			{
				$editbookinglink = JRoute::_(
					MatukioHelperRoute::getEventRoute($event->id, $event->catid, 1, $booking->id, $booking->uuid), true, -1
				);
			}

			$replaces["MAT_BOOKING_EDIT_URL"] = $editbookinglink;

			$replaces["MAT_BOOKING_EDIT"] =
				'<a href="' . $editbookinglink . '">'
				. JText::_("COM_MATUKIO_EDIT_YOUR_BOOKING") . "</a>";

			// TODO Different fees
		}


		// Event info complete no booking here
		$replaces["MAT_EVENT_ALL_DETAILS_HTML"] = self::getEmailEventInfoHTML($event);
		$replaces["MAT_EVENT_ALL_DETAILS_TEXT"] = self::getEmailEventInfoTEXT($event);

		/* Other things */
		$replaces["MAT_BANK_TRANSFER_INFORMATIONS"] = MatukioHelperPayment::getBanktransferInfo(
			MatukioHelperSettings::getSettings("banktransfer_account", ''),
			MatukioHelperSettings::getSettings("banktransfer_blz", ''),
			MatukioHelperSettings::getSettings("banktransfer_bank", ''),
			MatukioHelperSettings::getSettings("banktransfer_accountholder", ''),
			MatukioHelperSettings::getSettings("banktransfer_iban", ''),
			MatukioHelperSettings::getSettings("banktransfer_bic", '')
		);

		// CSV
		$bookingnewname = "";

		if ($booking != null)
		{
			$replaces["MAT_CSV_BOOKING_DETAILS"] = self::getExportCSVBookingDetails(
				$booking, $event, MatukioHelperSettings::getSettings('export_csv_separator', ';')
			);

			if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1)
			{
				// Old booking form
				if ($booking->userid > 0)
				{
					$user = JFactory::getUser($booking->userid);
					$replaces["MAT_BOOKING_NAME"] = $user->name;
					$replaces["MAT_BOOKING_EMAIL"] = $user->email;
				}
			}
			else
			{
				// New booking form fields
				$fields = MatukioHelperUtilsBooking::getBookingFields();
				$fieldvals = explode(";", $booking->newfields);

				$value = array();

				foreach ($fieldvals as $val)
				{
					$tmp = explode("::", $val);

					if (count($tmp) > 1)
					{
						$value[$tmp[0]] = $tmp[1];
					}
					else
					{
						$value[$tmp[0]] = "";
					}
				}

				foreach ($fields as $field)
				{
					if (!empty($value[$field->id]))
					{
						// Not use the Spacer fields
						if ($field->type != "spacer" && $field->type != "spacertext")
						{
							$replaces["MAT_BOOKING_" . strtoupper($field->field_name)] = $value[$field->id];
						}

						if ($field->field_name == "firstname")
						{
							$bookingnewname .= $value[$field->id];
						}

						if ($field->field_name == "lastname")
						{
							$bookingnewname .= " " . $value[$field->id];
						}
					}
					else
					{
						// Not use the Spacer fields
						if ($field->type != "spacer" && $field->type != "spacertext")
						{
							$replaces["MAT_BOOKING_" . strtoupper($field->field_name)] = "";
						}
					}
				}

				$bookingnewname = trim($bookingnewname);
				$replaces["MAT_BOOKING_COMMENT"] = $booking->comment;

				if (empty($replaces["MAT_BOOKING_NAME"]))
				{
					if (!empty($bookingnewname))
					{
						$replaces["MAT_BOOKING_NAME"] = $bookingnewname;
					}
					else
					{
						if ($booking->userid > 0)
						{
							$user = JFactory::getUser($booking->userid);
							$replaces["MAT_BOOKING_NAME"] = $user->name;
						}
					}
				}
			}

			if (empty($replaces["MAT_BOOKING_EMAIL"]))
			{
				if ($booking->userid > 0)
				{
					$user = JFactory::getUser($booking->userid);
					$replaces["MAT_BOOKING_EMAIL"] = $user->email;
				}
			}

			// Booking complete
			$replaces["MAT_BOOKING_ALL_DETAILS_HTML"] = self::getEmailBookingInfoHTML($event, $booking, $bookingnewname);
			$replaces["MAT_BOOKING_ALL_DETAILS_TEXT"] = self::getEmailBookingInfoTEXT($event, $booking, $bookingnewname);
		}

		return $replaces;
	}


	/**
	 * Generates the Header replacements
	 *
	 * @param   object  $event  - The event
	 *
	 * @return  mixed
	 */
	public static function getReplacesHeader($event)
	{
		$replaces["MAT_NR"] = JText::_("COM_MATUKIO_NR");

		$replaces["MAT_SIGN"] = JText::_("COM_MATUKIO_SIGN");

		// Event data
		$replaces["MAT_EVENT_SEMNUM"] = JText::_("COM_MATUKIO_SEMNUM");
		$replaces["MAT_EVENT_CATID"] = JText::_("COM_MATUKIO_CATID");
		$replaces["MAT_EVENT_TITLE"] = JText::_("COM_MATUKIO_EVENT_TITLE");
		$replaces["MAT_EVENT_TARGET"] = JText::_("COM_MATUKIO_TARGET_GROUP");

		$replaces["MAT_EVENT_SHORTDESC"] = JText::_("COM_MATUKIO_BRIEF_DESCRIPTION");
		$replaces["MAT_EVENT_DESCRIPTION"] = JText::_("COM_MATUKIO_DESCRIPTION");
		$replaces["MAT_EVENT_PLACE"] = JText::_("COM_MATUKIO_FIELDS_CITY");
		$replaces["MAT_EVENT_TEACHER"] = JText::_("COM_MATUKIO_TUTOR");
		$replaces["MAT_EVENT_ORGANIZER"] = JText::_("COM_MATUKIO_ORGANIZER");
		$replaces["MAT_EVENT_FEES"] = JText::_("COM_MATUKIO_FEES");
		$replaces["MAT_EVENT_MAXPUPIL"] = JText::_("COM_MATUKIO_MAX_PARTICIPANT");
		$replaces["MAT_EVENT_BOOKEDPUPIL"] = JText::_("COM_MATUKIO_BOOKED_PLACES");

		$replaces["MAT_EVENT_BEGIN"] = JText::_("COM_MATUKIO_BEGIN");
		$replaces["MAT_EVENT_END"] = JText::_("COM_MATUKIO_END");
		$replaces["MAT_EVENT_BOOKED"] = JText::_("COM_MATUKIO_END_BOOKING");

		$replaces["MAT_EVENT_GMAPLOC"] = JText::_("COM_MATUKIO_GMAPS_LOCATION");
		$replaces["MAT_EVENT_NRBOOKED"] = JText::_("COM_MATUKIO_BOOKED_PLACES");
		$replaces["MAT_EVENT_BOOKEDNR"] = JText::_("COM_MATUKIO_BOOKED_PLACES");

		$replaces["MAT_EVENT_ZUSATZ10"] = JText::_("COM_MATUKIO_CUSTOM10");
		$replaces["MAT_EVENT_ZUSATZ11"] = JText::_("COM_MATUKIO_CUSTOM11");
		$replaces["MAT_EVENT_ZUSATZ12"] = JText::_("COM_MATUKIO_CUSTOM12");
		$replaces["MAT_EVENT_ZUSATZ13"] = JText::_("COM_MATUKIO_CUSTOM13");
		$replaces["MAT_EVENT_ZUSATZ14"] = JText::_("COM_MATUKIO_CUSTOM14");
		$replaces["MAT_EVENT_ZUSATZ15"] = JText::_("COM_MATUKIO_CUSTOM15");
		$replaces["MAT_EVENT_ZUSATZ16"] = JText::_("COM_MATUKIO_CUSTOM16");
		$replaces["MAT_EVENT_ZUSATZ17"] = JText::_("COM_MATUKIO_CUSTOM17");
		$replaces["MAT_EVENT_ZUSATZ18"] = JText::_("COM_MATUKIO_CUSTOM18");
		$replaces["MAT_EVENT_ZUSATZ19"] = JText::_("COM_MATUKIO_CUSTOM19");
		$replaces["MAT_EVENT_ZUSATZ20"] = JText::_("COM_MATUKIO_CUSTOM20");
		$replaces["MAT_EVENT_ZUSATZ1"] = JText::_("COM_MATUKIO_CUSTOM");
		$replaces["MAT_EVENT_ZUSATZ2"] = JText::_("COM_MATUKIO_CUSTOM2");
		$replaces["MAT_EVENT_ZUSATZ3"] = JText::_("COM_MATUKIO_CUSTOM3");
		$replaces["MAT_EVENT_ZUSATZ4"] = JText::_("COM_MATUKIO_CUSTOM4");
		$replaces["MAT_EVENT_ZUSATZ5"] = JText::_("COM_MATUKIO_CUSTOM5");
		$replaces["MAT_EVENT_ZUSATZ6"] = JText::_("COM_MATUKIO_CUSTOM6");
		$replaces["MAT_EVENT_ZUSATZ7"] = JText::_("COM_MATUKIO_CUSTOM7");
		$replaces["MAT_EVENT_ZUSATZ8"] = JText::_("COM_MATUKIO_CUSTOM8");
		$replaces["MAT_EVENT_ZUSATZ9"] = JText::_("COM_MATUKIO_CUSTOM9");

		/* Alias */
		$replaces["MAT_EVENT_CUSTOM10"] = JText::_("COM_MATUKIO_CUSTOM10");
		$replaces["MAT_EVENT_CUSTOM11"] = JText::_("COM_MATUKIO_CUSTOM11");
		$replaces["MAT_EVENT_CUSTOM12"] = JText::_("COM_MATUKIO_CUSTOM12");
		$replaces["MAT_EVENT_CUSTOM13"] = JText::_("COM_MATUKIO_CUSTOM13");
		$replaces["MAT_EVENT_CUSTOM14"] = JText::_("COM_MATUKIO_CUSTOM14");
		$replaces["MAT_EVENT_CUSTOM15"] = JText::_("COM_MATUKIO_CUSTOM15");
		$replaces["MAT_EVENT_CUSTOM16"] = JText::_("COM_MATUKIO_CUSTOM16");
		$replaces["MAT_EVENT_CUSTOM17"] = JText::_("COM_MATUKIO_CUSTOM17");
		$replaces["MAT_EVENT_CUSTOM18"] = JText::_("COM_MATUKIO_CUSTOM18");
		$replaces["MAT_EVENT_CUSTOM19"] = JText::_("COM_MATUKIO_CUSTOM19");
		$replaces["MAT_EVENT_CUSTOM20"] = JText::_("COM_MATUKIO_CUSTOM20");
		$replaces["MAT_EVENT_CUSTOM1"] = JText::_("COM_MATUKIO_CUSTOM1");
		$replaces["MAT_EVENT_CUSTOM2"] = JText::_("COM_MATUKIO_CUSTOM2");
		$replaces["MAT_EVENT_CUSTOM3"] = JText::_("COM_MATUKIO_CUSTOM3");
		$replaces["MAT_EVENT_CUSTOM4"] = JText::_("COM_MATUKIO_CUSTOM4");
		$replaces["MAT_EVENT_CUSTOM5"] = JText::_("COM_MATUKIO_CUSTOM5");
		$replaces["MAT_EVENT_CUSTOM6"] = JText::_("COM_MATUKIO_CUSTOM6");
		$replaces["MAT_EVENT_CUSTOM7"] = JText::_("COM_MATUKIO_CUSTOM7");
		$replaces["MAT_EVENT_CUSTOM8"] = JText::_("COM_MATUKIO_CUSTOM8");
		$replaces["MAT_EVENT_CUSTOM9"] = JText::_("COM_MATUKIO_CUSTOM9");

		if (!empty($event))
		{
			$replaces["MAT_EVENT_CUSTOM10"] = self::getCustomFieldHeader($event->zusatz10);
			$replaces["MAT_EVENT_CUSTOM11"] = self::getCustomFieldHeader($event->zusatz11);
			$replaces["MAT_EVENT_CUSTOM12"] = self::getCustomFieldHeader($event->zusatz12);
			$replaces["MAT_EVENT_CUSTOM13"] = self::getCustomFieldHeader($event->zusatz13);
			$replaces["MAT_EVENT_CUSTOM14"] = self::getCustomFieldHeader($event->zusatz14);
			$replaces["MAT_EVENT_CUSTOM15"] = self::getCustomFieldHeader($event->zusatz15);
			$replaces["MAT_EVENT_CUSTOM16"] = self::getCustomFieldHeader($event->zusatz16);
			$replaces["MAT_EVENT_CUSTOM17"] = self::getCustomFieldHeader($event->zusatz17);
			$replaces["MAT_EVENT_CUSTOM18"] = self::getCustomFieldHeader($event->zusatz18);
			$replaces["MAT_EVENT_CUSTOM19"] = self::getCustomFieldHeader($event->zusatz19);
			$replaces["MAT_EVENT_CUSTOM20"] = self::getCustomFieldHeader($event->zusatz20);
			$replaces["MAT_EVENT_CUSTOM1"] = self::getCustomFieldHeader($event->zusatz1);
			$replaces["MAT_EVENT_CUSTOM2"] = self::getCustomFieldHeader($event->zusatz2);
			$replaces["MAT_EVENT_CUSTOM3"] = self::getCustomFieldHeader($event->zusatz3);
			$replaces["MAT_EVENT_CUSTOM4"] = self::getCustomFieldHeader($event->zusatz4);
			$replaces["MAT_EVENT_CUSTOM5"] = self::getCustomFieldHeader($event->zusatz5);
			$replaces["MAT_EVENT_CUSTOM6"] = self::getCustomFieldHeader($event->zusatz6);
			$replaces["MAT_EVENT_CUSTOM7"] = self::getCustomFieldHeader($event->zusatz7);
			$replaces["MAT_EVENT_CUSTOM8"] = self::getCustomFieldHeader($event->zusatz8);
			$replaces["MAT_EVENT_CUSTOM9"] = self::getCustomFieldHeader($event->zusatz9);

			/* Alias */
			$replaces["MAT_EVENT_ZUSATZ10"] = $replaces["MAT_EVENT_CUSTOM10"];
			$replaces["MAT_EVENT_ZUSATZ11"] = $replaces["MAT_EVENT_CUSTOM11"];
			$replaces["MAT_EVENT_ZUSATZ12"] = $replaces["MAT_EVENT_CUSTOM12"];
			$replaces["MAT_EVENT_ZUSATZ13"] = $replaces["MAT_EVENT_CUSTOM13"];
			$replaces["MAT_EVENT_ZUSATZ14"] = $replaces["MAT_EVENT_CUSTOM14"];
			$replaces["MAT_EVENT_ZUSATZ15"] = $replaces["MAT_EVENT_CUSTOM15"];
			$replaces["MAT_EVENT_ZUSATZ16"] = $replaces["MAT_EVENT_CUSTOM16"];
			$replaces["MAT_EVENT_ZUSATZ17"] = $replaces["MAT_EVENT_CUSTOM17"];
			$replaces["MAT_EVENT_ZUSATZ18"] = $replaces["MAT_EVENT_CUSTOM18"];
			$replaces["MAT_EVENT_ZUSATZ19"] = $replaces["MAT_EVENT_CUSTOM19"];
			$replaces["MAT_EVENT_ZUSATZ20"] = $replaces["MAT_EVENT_CUSTOM20"];
			$replaces["MAT_EVENT_ZUSATZ1"] = $replaces["MAT_EVENT_CUSTOM1"];
			$replaces["MAT_EVENT_ZUSATZ2"] = $replaces["MAT_EVENT_CUSTOM2"];
			$replaces["MAT_EVENT_ZUSATZ3"] = $replaces["MAT_EVENT_CUSTOM3"];
			$replaces["MAT_EVENT_ZUSATZ4"] = $replaces["MAT_EVENT_CUSTOM4"];
			$replaces["MAT_EVENT_ZUSATZ5"] = $replaces["MAT_EVENT_CUSTOM5"];
			$replaces["MAT_EVENT_ZUSATZ6"] = $replaces["MAT_EVENT_CUSTOM6"];
			$replaces["MAT_EVENT_ZUSATZ7"] = $replaces["MAT_EVENT_CUSTOM7"];
			$replaces["MAT_EVENT_ZUSATZ8"] = $replaces["MAT_EVENT_CUSTOM8"];
			$replaces["MAT_EVENT_ZUSATZ9"] = $replaces["MAT_EVENT_CUSTOM9"];
		}

		$replaces["MAT_EVENT_ZUSATZ10HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT10");
		$replaces["MAT_EVENT_ZUSATZ11HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT11");
		$replaces["MAT_EVENT_ZUSATZ12HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT12");
		$replaces["MAT_EVENT_ZUSATZ13HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT13");
		$replaces["MAT_EVENT_ZUSATZ14HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT14");
		$replaces["MAT_EVENT_ZUSATZ15HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT15");
		$replaces["MAT_EVENT_ZUSATZ16HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT16");
		$replaces["MAT_EVENT_ZUSATZ17HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT17");
		$replaces["MAT_EVENT_ZUSATZ18HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT18");
		$replaces["MAT_EVENT_ZUSATZ19HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT19");
		$replaces["MAT_EVENT_ZUSATZ20HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT20");
		$replaces["MAT_EVENT_ZUSATZ1HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT1");
		$replaces["MAT_EVENT_ZUSATZ2HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT2");
		$replaces["MAT_EVENT_ZUSATZ3HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT3");
		$replaces["MAT_EVENT_ZUSATZ4HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT4");
		$replaces["MAT_EVENT_ZUSATZ5HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT5");
		$replaces["MAT_EVENT_ZUSATZ6HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT6");
		$replaces["MAT_EVENT_ZUSATZ7HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT7");
		$replaces["MAT_EVENT_ZUSATZ8HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT8");
		$replaces["MAT_EVENT_ZUSATZ9HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT9");

		/* ALIAS */
		$replaces["MAT_EVENT_CUSTOM10HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT10");
		$replaces["MAT_EVENT_CUSTOM11HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT11");
		$replaces["MAT_EVENT_CUSTOM12HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT12");
		$replaces["MAT_EVENT_CUSTOM13HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT13");
		$replaces["MAT_EVENT_CUSTOM14HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT14");
		$replaces["MAT_EVENT_CUSTOM15HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT15");
		$replaces["MAT_EVENT_CUSTOM16HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT16");
		$replaces["MAT_EVENT_CUSTOM17HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT17");
		$replaces["MAT_EVENT_CUSTOM18HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT18");
		$replaces["MAT_EVENT_CUSTOM19HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT19");
		$replaces["MAT_EVENT_CUSTOM20HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT20");
		$replaces["MAT_EVENT_CUSTOM1HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT1");
		$replaces["MAT_EVENT_CUSTOM2HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT2");
		$replaces["MAT_EVENT_CUSTOM3HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT3");
		$replaces["MAT_EVENT_CUSTOM4HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT4");
		$replaces["MAT_EVENT_CUSTOM5HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT5");
		$replaces["MAT_EVENT_CUSTOM6HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT6");
		$replaces["MAT_EVENT_CUSTOM7HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT7");
		$replaces["MAT_EVENT_CUSTOM8HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT8");
		$replaces["MAT_EVENT_CUSTOM9HINT"] = JText::_("COM_MATUKIO_CUSTOMHINT9");

		$replaces["MAT_EVENT_CREATED_BY"] = JText::_("COM_MATUKIO_CREATED_BY");
		$replaces["MAT_EVENT_MODIFIED_BY"] = JText::_("COM_MATUKIO_MODIFIED_BY");
		$replaces["MAT_EVENT_CREATED"] = JText::_("COM_MATUKIO_CREATED_ON");
		$replaces["MAT_EVENT_WEBINAR"] = JText::_("COM_MATUKIO_WEBINAR");

		// Booking data
		$replaces["MAT_BOOKING_ID"] = JText::_("COM_MATUKIO_BOOKING_ID");
		$replaces["MAT_BOOKING_NUMBER"] = JText::_("COM_MATUKIO_BOOKING_NUMBER");

		// Old form
		$replaces["MAT_BOOKING_NAME"] = JText::_("COM_MATUKIO_NAME");
		$replaces["MAT_BOOKING_EMAIL"] = JText::_("COM_MATUKIO_EMAIL");
		$replaces["MAT_BOOKING_USERID"] = JText::_("COM_MATUKIO_USERID");
		$replaces["MAT_BOOKING_CERTIFICATED"] = JText::_("COM_MATUKIO_CERTIFICATED");
		$replaces["MAT_EVENT_BOOKINGDATE"] = JText::_("COM_MATUKIO_BOOKING_DATE");
		$replaces["MAT_EVENT_UPDATED"] = JText::_("COM_MATUKIO_BOOKING_UPDATED");
		$replaces["MAT_BOOKING_COMMENT"] = JText::_("COM_MATUKIO_COMMENT");
		$replaces["MAT_BOOKING_PAID"] = JText::_("COM_MATUKIO_BOOK_PAID");
		$replaces["MAT_BOOKING_NRBOOKED"] = JText::_("COM_MATUKIO_BOOKED_PLACES");
		$replaces["MAT_BOOKING_ZUSATZ10"] = JText::_("COM_MATUKIO_CUSTOM10");
		$replaces["MAT_BOOKING_ZUSATZ11"] = JText::_("COM_MATUKIO_CUSTOM11");
		$replaces["MAT_BOOKING_ZUSATZ12"] = JText::_("COM_MATUKIO_CUSTOM12");
		$replaces["MAT_BOOKING_ZUSATZ13"] = JText::_("COM_MATUKIO_CUSTOM13");
		$replaces["MAT_BOOKING_ZUSATZ14"] = JText::_("COM_MATUKIO_CUSTOM14");
		$replaces["MAT_BOOKING_ZUSATZ15"] = JText::_("COM_MATUKIO_CUSTOM15");
		$replaces["MAT_BOOKING_ZUSATZ16"] = JText::_("COM_MATUKIO_CUSTOM16");
		$replaces["MAT_BOOKING_ZUSATZ17"] = JText::_("COM_MATUKIO_CUSTOM17");
		$replaces["MAT_BOOKING_ZUSATZ18"] = JText::_("COM_MATUKIO_CUSTOM18");
		$replaces["MAT_BOOKING_ZUSATZ19"] = JText::_("COM_MATUKIO_CUSTOM19");
		$replaces["MAT_BOOKING_ZUSATZ20"] = JText::_("COM_MATUKIO_CUSTOM20");
		$replaces["MAT_BOOKING_ZUSATZ1"] = JText::_("COM_MATUKIO_CUSTOM1");
		$replaces["MAT_BOOKING_ZUSATZ2"] = JText::_("COM_MATUKIO_CUSTOM2");
		$replaces["MAT_BOOKING_ZUSATZ3"] = JText::_("COM_MATUKIO_CUSTOM3");
		$replaces["MAT_BOOKING_ZUSATZ4"] = JText::_("COM_MATUKIO_CUSTOM4");
		$replaces["MAT_BOOKING_ZUSATZ5"] = JText::_("COM_MATUKIO_CUSTOM5");
		$replaces["MAT_BOOKING_ZUSATZ6"] = JText::_("COM_MATUKIO_CUSTOM6");
		$replaces["MAT_BOOKING_ZUSATZ7"] = JText::_("COM_MATUKIO_CUSTOM7");
		$replaces["MAT_BOOKING_ZUSATZ8"] = JText::_("COM_MATUKIO_CUSTOM8");
		$replaces["MAT_BOOKING_ZUSATZ9"] = JText::_("COM_MATUKIO_CUSTOM9");

		/* Alias */
		$replaces["MAT_BOOKING_CUSTOM10"] = JText::_("COM_MATUKIO_CUSTOM10");
		$replaces["MAT_BOOKING_CUSTOM11"] = JText::_("COM_MATUKIO_CUSTOM11");
		$replaces["MAT_BOOKING_CUSTOM12"] = JText::_("COM_MATUKIO_CUSTOM12");
		$replaces["MAT_BOOKING_CUSTOM13"] = JText::_("COM_MATUKIO_CUSTOM13");
		$replaces["MAT_BOOKING_CUSTOM14"] = JText::_("COM_MATUKIO_CUSTOM14");
		$replaces["MAT_BOOKING_CUSTOM15"] = JText::_("COM_MATUKIO_CUSTOM15");
		$replaces["MAT_BOOKING_CUSTOM16"] = JText::_("COM_MATUKIO_CUSTOM16");
		$replaces["MAT_BOOKING_CUSTOM17"] = JText::_("COM_MATUKIO_CUSTOM17");
		$replaces["MAT_BOOKING_CUSTOM18"] = JText::_("COM_MATUKIO_CUSTOM18");
		$replaces["MAT_BOOKING_CUSTOM19"] = JText::_("COM_MATUKIO_CUSTOM19");
		$replaces["MAT_BOOKING_CUSTOM20"] = JText::_("COM_MATUKIO_CUSTOM20");
		$replaces["MAT_BOOKING_CUSTOM1"] = JText::_("COM_MATUKIO_CUSTOM1");
		$replaces["MAT_BOOKING_CUSTOM2"] = JText::_("COM_MATUKIO_CUSTOM2");
		$replaces["MAT_BOOKING_CUSTOM3"] = JText::_("COM_MATUKIO_CUSTOM3");
		$replaces["MAT_BOOKING_CUSTOM4"] = JText::_("COM_MATUKIO_CUSTOM4");
		$replaces["MAT_BOOKING_CUSTOM5"] = JText::_("COM_MATUKIO_CUSTOM5");
		$replaces["MAT_BOOKING_CUSTOM6"] = JText::_("COM_MATUKIO_CUSTOM6");
		$replaces["MAT_BOOKING_CUSTOM7"] = JText::_("COM_MATUKIO_CUSTOM7");
		$replaces["MAT_BOOKING_CUSTOM8"] = JText::_("COM_MATUKIO_CUSTOM8");
		$replaces["MAT_BOOKING_CUSTOM9"] = JText::_("COM_MATUKIO_CUSTOM9");

		$replaces["MAT_BOOKING_UUID"] = JText::_("COM_MATUKIO_UUID");
		$replaces["MAT_BOOKING_PAYMENT_METHOD"] = JText::_("COM_MATUKIO_FIELD_PAYMENT_METHOD");
		$replaces["MAT_BOOKING_PAYMENT_NUMBER"] = JText::_("COM_MATUKIO_FIELD_PAYMENT_NUMBER");
		$replaces["MAT_BOOKING_PAYMENT_NETTO"] = JText::_("COM_MATUKIO_FIELD_PAYMENT_NETTO");

		// Alias
		$replaces["MAT_BOOKING_PAYMENT_NET"] = $replaces["MAT_BOOKING_PAYMENT_NETTO"];

		$replaces["MAT_BOOKING_PAYMENT_TAX"] = JText::_("COM_MATUKIO_FIELD_PAYMENT_TAX");
		$replaces["MAT_BOOKING_PAYMENT_BRUTTO"] = JText::_("COM_MATUKIO_FIELD_PAYMENT_BRUTTO");

		// Alias
		$replaces["MAT_BOOKING_PAYMENT_GROSS"] = $replaces["MAT_BOOKING_PAYMENT_BRUTTO"];

		$replaces["MAT_BOOKING_COUPON_CODE"] = JText::_("COM_MATUKIO_FIELD_COUPON");

		$replaces["MAT_BOOKING_FEES_STATUS"] = JText::_("COM_MATUKIO_FEES_STATUS");

		$replaces["MAT_BOOKING_STATUS"] = JText::_("COM_MATUKIO_STATUS");
		$replaces["MAT_BOOKING_UUID"] = JText::_("COM_MATUKIO_UUID");

		$replaces["MAT_BOOKING_QRCODE"] = JText::_("COM_MATUKIO_QRCODE");
		$replaces["MAT_BOOKING_BARCODE"] = JText::_("COM_MATUKIO_BARCODE");

		$replaces["MAT_CSV_BOOKING_DETAILS"] = self::getExportCSVBookingDetailsHeader(
			MatukioHelperSettings::getSettings('export_csv_separator', ';')
		);

		if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1)
		{
			// Old booking form

		}
		else
		{
			// New booking form fields
			$fields = MatukioHelperUtilsBooking::getBookingFields();

			foreach ($fields as $field)
			{
				if ($field->type != "spacer" && $field->type != "spacertext")
				{
					$replaces["MAT_BOOKING_" . strtoupper($field->field_name)] = JText::_($field->label);
				}
			}
		}

		return $replaces;
	}

	/**
	 * Gets the custom field header
	 *
	 * @param   string  $field  - The field data
	 *
	 * @return string
	 */
	public static function getCustomFieldHeader($field)
	{
		if (!empty($field))
		{
			$res = explode("|", $field);

			if (!empty($res[0]))
			{
				return $res[0];
			}
		}

		return "";
	}

	/**
	 * Just returns the template row with the given name
	 *
	 * @param   string  $tmpl_name  - The name of the template
	 *
	 * @return  template
	 */

	public static function getTemplate($tmpl_name)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("*")->from("#__matukio_templates")->where("tmpl_name = " . $db->quote($tmpl_name), "published = 1");

		$tmpl = $db->setQuery($query)->loadObject();

		return $tmpl;
	}

	/**
	 * Gets the E-Mail template name on the given id
	 *
	 * @param   int  $art  - The type
	 *
	 * @return  string
	 */

	public static function getEmailTemplateName($art)
	{
		switch ($art)
		{
			case 1:
			default:
				return "mail_booking";
				break;

			case 2:
				return "mail_booking_canceled";
				break;

			case 3:
				return "mail_booking_canceled_admin";
				break;
		}
	}

	/**
	 * Generates the E-Mail body
	 *
	 * @param   string  $tmpl_name  - The name of the Template
	 * @param   object  $event      - The event row
	 * @param   object  $booking    - The booking row
	 *
	 * @return  mixed
	 *
	 * @throws  Exception
	 */

	public static function getEmailBody($tmpl_name, $event, $booking = null)
	{
		$tmpl = self::getTemplate($tmpl_name);

		if ($tmpl_name == "mail_booking")
		{
			if (!empty($event->booking_mail))
			{
				$tmpl->value = $event->booking_mail;
				$tmpl->value_text = $event->booking_mail;
			}
		}

		if (empty($tmpl))
		{
			throw new Exception('COM_MATUKIO_NO_TEMPLATE');
		}

		$tmpl = self::replaceLanguage($tmpl);

		$tmpl = self::replaceConstants($tmpl, $event, $booking);

		return $tmpl;
	}

	/**
	 * Gets the header cell for this event
	 *
	 * @param   object  $tmpl   -The not parsed template
	 * @param   object  $event  - The event
	 *
	 * @return mixed
	 */
	public static function getParsedExportTemplateHeadding($tmpl, $event)
	{
		$tmpl = self::replaceLanguage($tmpl);

		// Only event constants!
		if (!empty($event))
		{
			$tmpl = self::replaceConstants($tmpl, $event);
		}

		return $tmpl;
	}

	/**
	 * Replaces all language ##XY## place holders
	 *
	 * @param   object  $template  - The template
	 *
	 * @return  mixed
	 */

	public static function replaceLanguage($template)
	{
		$template->value = preg_replace_callback("/##(.*)##/isU", create_function('$matches', 'return JText::_($matches[1]);'), $template->value);
		$template->value_text = preg_replace_callback("/##(.*)##/isU", create_function('$matches', 'return JText::_($matches[1]);'), $template->value_text);
		$template->subject = preg_replace_callback("/##(.*)##/isU", create_function('$matches', 'return JText::_($matches[1]);'), $template->subject);

		return $template;
	}

	/**
	 * Replaces all lanugage strings in the given string
	 *
	 * @param   string  $s  - The string
	 *
	 * @return string
	 */

	public static function replaceLanguageStrings($s)
	{
		return preg_replace_callback("/##(.*)##/isU", create_function('$matches', 'return JText::_($matches[1]);'), $s);
	}

	/**
	 * Generates the CSV Header
	 *
	 * @param   object  $template  - The template
	 * @param   object  $event     - The event
	 *
	 * @return  mixed
	 */

	public static function getCSVHeader($template, $event)
	{
		$header_text = $template->value;

		$replaces = self::getReplacesHeader($event);

		foreach ($replaces as $key => $replace)
		{
			$header_text = str_replace($key, $replace, $header_text);
		}

		$header_text .= "\r\n";

		return $header_text;
	}

	/**
	 * Generates the signature header
	 *
	 * @param   string  $signature_line  - The signature line
	 * @param   object  $event           - The event
	 *
	 * @return  mixed
	 */

	public static function getExportSignatureHeader($signature_line, $event)
	{
		$replaces = self::getReplacesHeader($event);

		foreach ($replaces as $key => $replace)
		{
			if ($key != "MAT_SIGN")
			{
				$signature_line = str_replace($key, "<th>" . $replace . "</th>", $signature_line);
			}
			else
			{
				$signature_line = str_replace($key, "<th width=\"35%\">" . $replace . "</th>", $signature_line);
			}
		}

		return $signature_line;
	}


	/**
	 * Generates the CSV Data
	 *
	 * @param   object  $template  - The template
	 * @param   array   $bookings  - The bookings
	 * @param   object  $event     - The event
	 *
	 * @return string
	 */

	public static function getCSVData($template, $bookings, $event)
	{
		$header_text = $template->value;

		$csvdata = "";

		$mixed = false;

		foreach ($bookings as $booking)
		{
			if (empty($event))
			{
				$event = MatukioHelperUtilsEvents::getEventRecurring($booking->semid);
				$mixed = true;
			}

			$replaces = self::getReplaces($event, $booking);
			$line = $header_text;

			foreach ($replaces as $key => $replace)
			{
				$val = str_replace("\n", " ", $replace);
				$val = str_replace("\r", " ", $val);
				$line = str_replace($key, $val, $line);
			}

			$csvdata .= $line;
			$csvdata .= "\r\n";

			if ($mixed)
			{
				$event = null;
			}
		}

		return $csvdata;
	}


	/**
	 * Replaces the constants of the template
	 *
	 * @param   object  $template  - The template (mostly text) with the not replaced place holders
	 * @param   object  $event     - The event (mandatory)
	 * @param   object  $booking   - The booking (optional)
	 *
	 * @return mixed
	 */
	public static function replaceConstants($template, $event, $booking = null)
	{
		$replaces = self::getReplaces($event, $booking);

		// Replacing all strings here
		foreach ($replaces as $key => $replace)
		{
			$template->value = str_replace($key, $replace, $template->value);
			$template->value_text = str_replace($key, $replace, $template->value_text);
			$template->subject = str_replace($key, $replace, $template->subject);
		}

		return $template;
	}


	/**
	 * Generates the E-Mail booking informations
	 *
	 * @param   object  $event    - The event object
	 * @param   object  $booking  - The booking
	 * @param   string  $name     - The name
	 *
	 * @return  string
	 */
	public static function getEmailBookingInfoHTML($event, $booking, $name)
	{
		$html = '<p><table cellpadding="2" border="0" width="100%">';
		$html .= "\n<tr>
                        <td style=\"width: 180px\"><strong>" . JTEXT::_('COM_MATUKIO_BOOKING_NUMBER') . "</strong>: </td>
                        <td>" . MatukioHelperUtilsBooking::getBookingId($booking->id) . "</td>
                    </tr>";

		if ($booking->nrbooked > 1)
		{
			$html .= '<tr>';
			$html .= '<td><strong>' . JText::_('COM_MATUKIO_BOOKED_PLACES') . '</strong></td>';
			$html .= '<td>' . $booking->nrbooked . '</td>';
			$html .= '</tr>';
		}


		if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1)
		{
			if ($booking->userid == 0)
			{
				$user = JFactory::getUser(0);

				if (!empty($booking->name))
				{
					$user->name = $booking->name;
				}
				elseif(!empty($booking->aname))
				{
					$user->name = $booking->aname;
				}

				if (!empty($booking->email))
				{
					$user->email = $booking->email;
				}
				elseif(!empty($booking->aemail))
				{
					$user->email = $booking->aemail;
				}
			}
			else
			{
				$user = JFactory::getuser($booking->userid);
			}


			$html .= "\n<tr><td><strong>" . JTEXT::_('COM_MATUKIO_NAME') . "</strong>: </td><td>" . $name . " (" . $user->name . ")" . "</td></tr>";
			$html .= "\n<tr><td><strong>" . JTEXT::_('COM_MATUKIO_EMAIL') . "</strong>: </td><td>" . $user->email . "</td></tr>";
		}
		else
		{
			// New booking form fields
			$fields = MatukioHelperUtilsBooking::getBookingFields();
			$fieldvals = explode(";", $booking->newfields);

			$value = array();

			foreach ($fieldvals as $val)
			{
				$tmp = explode("::", $val);

				if (count($tmp) > 1)
				{
					$value[$tmp[0]] = $tmp[1];
				}
				else
				{
					$value[$tmp[0]] = "";
				}
			}

			foreach ($fields as $field)
			{
				if ($field->type != "spacer" && $field->type != "spacertext")
				{
					if (!empty($value[$field->id]))
					{
						$html .= "<tr><td>" . JTEXT::_($field->label) . ": </td><td>" . $value[$field->id] . "</td></tr>";
					}
					else
					{
						$html .= "<tr><td>" . JTEXT::_($field->label) . ": </td><td> </td></tr>";
					}
				}
			}
		}

		if ($booking->payment_brutto > 0 && $booking->payment_brutto != 0.00)
		{
			$html .= '</table></p>';

			$html .= '<p><table cellpadding="2" border="0" width="100%">';

			$html .= "\n<tr><td style=\"width: 180px\">" . JTEXT::_('COM_MATUKIO_FIELD_PAYMENT_METHOD') . ": </td><td>"
				. self::getPaymentMethodTitle($booking->payment_method) . "</td></tr>";

			if ($booking->payment_brutto > 0)
			{
				$html .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_FEES') . ": </td><td>"
					. MatukioHelperUtilsEvents::getFormatedCurrency($booking->payment_brutto, MatukioHelperSettings::_('currency_symbol', '$'))
					. "</td></tr>";
			}
		}

		$html .= '</table></p>';

		return $html;
	}


	/**
	 * Generates the E-Mail booking info text
	 *
	 * @param   object  $event    - The event
	 * @param   object  $booking  - The booking
	 * @param   string  $name     - The name
	 *
	 * @return  string
	 */

	public static function getEmailBookingInfoTEXT($event, $booking, $name)
	{
		$html = '\n';
		$html .= JTEXT::_('COM_MATUKIO_BOOKING_NUMBER') . ":\t\t" . MatukioHelperUtilsBooking::getBookingId($booking->id) . "\n";

		if ($booking->nrbooked > 1)
		{
			$html .= JTEXT::_('COM_MATUKIO_BOOKED_PLACES') . ":\t\t" . $booking->nrbooked . "\n";
		}

		if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1)
		{
			if ($booking->userid == 0)
			{
				$user = JFactory::getUser(0);
				$user->name = $booking->name;
				$user->email = $booking->email;
			}
			else
			{
				$user = JFactory::getuser($booking->userid);
			}

			$html .= JTEXT::_('COM_MATUKIO_NAME') . ":\t\t" . $name . " (" . $user->name . ")" . "\n";
			$html .= JTEXT::_('COM_MATUKIO_EMAIL') . ":\t\t" . $booking->email . "\n";
		}
		else
		{
			// New booking form fields
			$fields = MatukioHelperUtilsBooking::getBookingFields();
			$fieldvals = explode(";", $booking->newfields);

			$value = array();

			foreach ($fieldvals as $val)
			{
				$tmp = explode("::", $val);

				if (count($tmp) > 1)
				{
					$value[$tmp[0]] = $tmp[1];
				}
				else
				{
					$value[$tmp[0]] = "";
				}
			}

			foreach ($fields as $field)
			{
				if ($field->type != "spacer" && $field->type != "spacertext")
				{
					if (!empty($value[$field->id]))
					{
						$html .= JTEXT::_($field->label) . ":\t\t" . $value[$field->id] . "\n";
					}
					else
					{
						$html .= JTEXT::_($field->label) . ":\t\t" . "\n";
					}
				}
			}
		}

		$html .= '</table></p>';

		return $html;
	}

	/**
	 * Generates The Event info HTML Code
	 *
	 * @param   object  $event  - The event
	 *
	 * @return  string
	 */
	public static function getEmailEventInfoHTML($event)
	{
		$html = '<p><table cellpadding="2" border="0" width="100%">';
		$html .= "\n<tr><td colspan=\"2\"><b>" . $event->title . "</b></td></tr>";

		if ($event->showbegin > 0)
		{
			$html .= "\n<tr><td style=\"width: 180px\">" . JTEXT::_('COM_MATUKIO_BEGIN') . ": </td><td>" . JHTML::_('date', $event->begin,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
				) . "</td></tr>";
		}

		if ($event->showend > 0)
		{
			$html .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_END') . ": </td><td>" . JHTML::_(
					'date', $event->end,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
				) . "</td></tr>";
		}

		if ($event->showbooked > 0)
		{
			$html .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ": </td><td>" . JHTML::_(
					'date', $event->booked,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
				) . "</td></tr>";
		}

		if ($event->teacher != "")
		{
			$html .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_TUTOR') . ": </td><td>" . $event->teacher . "</td></tr>";
		}

		$html .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_CITY') . ": </td><td>" . $event->place . "</td></tr>";

		$html .= '</table></p>';

		return $html;
	}


	/**
	 * Generates the E-Mail Info TEXT
	 *
	 * @param   object  $event  - The event
	 *
	 * @return  string
	 */

	public static function getEmailEventInfoTEXT($event)
	{
		$html = '\n';
		$html .= $event->title . "\n";

		if ($event->showbegin > 0)
		{
			$html .= JTEXT::_('COM_MATUKIO_BEGIN') . ":\t\t" . JHTML::_(
					'date', $event->begin,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
				) . "\n";
		}

		if ($event->showend > 0)
		{
			$html .= JTEXT::_('COM_MATUKIO_END') . ":\t\t" . JHTML::_(
					'date', $event->end,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
				) . "\n";
		}

		if ($event->showbooked > 0)
		{
			$html .= JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ":\t\t" . JHTML::_(
					'date', $event->booked,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
				) . "\n";
		}

		if ($event->teacher != "")
		{
			$html .= JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ":\t\t" . $event->teacher . "\n";
		}

		$html .= JTEXT::_('COM_MATUKIO_CITY') . ":\t\t" . $event->place . "\n";

		return $html;
	}

	/**
	 * Generates the csv booking detail header
	 *
	 * @param   string  $separator  - The Separator char
	 *
	 * @return  string
	 */

	public static function getExportCSVBookingDetailsHeader($separator = ";")
	{
		$html = "";

		$html .= "'" . JTEXT::_('COM_MATUKIO_BOOKED_PLACES') . "';";

		if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1)
		{
			// Old booking form
			$html .= "'" . JText::_("COM_MATUKIO_NAME") . "';";
			$html .= "'" . JText::_("COM_MATUKIO_EMAIL") . "';";
		}
		else
		{
			// New booking form fields
			$fields = MatukioHelperUtilsBooking::getBookingFields();

			foreach ($fields as $field)
			{
				if ($field->type != "spacer" && $field->type != "spacertext")
				{
					$html .= "'" . JText::_($field->label) . "';";
				}
			}
		}

		return $html;
	}


	/**
	 * Generates the csv booking details
	 *
	 * @param   object  $booking    - The booking
	 * @param   object  $event      - The event
	 * @param   string  $separator  - The separator
	 *
	 * @return  string
	 */

	public static function getExportCSVBookingDetails($booking, $event, $separator = ";")
	{
		$html = "";

		$html .= "'" . $booking->nrbooked . "';";

		if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1)
		{
			// Old booking form
			if ($booking->userid < 1)
			{
				if (isset($booking->aname))
				{
					$html .= "'" . $booking->aname . "';";
				}
				else
				{
					$html .= "'" . $booking->name . "';";
				}

				if (isset($booking->aemail))
				{
					$html .= "'" . $booking->aemail . "';";
				}
				else
				{
					$html .= "'" . $booking->email . "';";
				}
			}
			else
			{
				$user = JFactory::getUser($booking->userid);
				$html .= "'" . $user->name . "';";
				$html .= "'" . $user->email . "';";
			}
		}
		else
		{
			// New booking form fields
			$fields = MatukioHelperUtilsBooking::getBookingFields();

			$fields = MatukioHelperUtilsBooking::getBookingFields();
			$fieldvals = explode(";", $booking->newfields);

			$value = array();

			foreach ($fieldvals as $val)
			{
				$tmp = explode("::", $val);

				if (count($tmp) > 1)
				{
					$value[$tmp[0]] = $tmp[1];
				}
				else
				{
					$value[$tmp[0]] = "";
				}
			}

			foreach ($fields as $field)
			{
				if ($field->type != "spacer" && $field->type != "spacertext")
				{
					if (!empty($value[$field->id]))
					{
						$html .= "'" . str_replace($separator, " ", $value[$field->id]) . "'" . $separator;
					}
					else
					{
						$html .= "''" . $separator;
					}
				}
			}
		}

		return $html;
	}

	/**
	 * Gets the payment method title out of the plugin!
	 *
	 * @param   string  $pm  - The payment method
	 *
	 * @return  string
	 */
	public static function getPaymentMethodTitle($pm)
	{
		// Get the plugin name
		$dispatcher = JDispatcher::getInstance();

		JPluginHelper::importPlugin("payment");
		$gateways = $dispatcher->trigger('onTP_GetInfo', array(array($pm)));

		if (count($gateways))
		{
			$gway = $gateways[0];

			return JText::_($gway->name);
		}
		else
		{
			// Fallback.. should not happen :)
			return $pm;
		}
	}
}
