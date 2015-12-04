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

jimport('joomla.application.component.view');

/**
 * Class MatukioViewPayPalPayment
 *
 * @since       1.0.0
 * @deprecated  Use ppayment and paymentform instead
 */
class MatukioViewPayPalPayment extends JViewLegacy
{
	/**
	 * Shows the form
	 *
	 * @param   string  $tpl  - The tmpl
	 *
	 * @return  bool|mixed|object
	 */
	public function display($tpl = null)
	{
		$booking_id = JFactory::getApplication()->input->get('booking_id', 0);

		$user = JFactory::getUser();
		$model = $this->getModel();

		if (empty($booking_id))
		{
			JError::raiseError('404', "COM_MATUKIO_NO_ID");
		}

		$booking = $model->getBooking($booking_id);
		$event = $model->getEvent($booking->semid);

		MatukioHelperUtilsBasic::expandPathway(JTEXT::_('COM_MATUKIO_EVENTS'), JRoute::_("index.php?option=com_matukio&view=eventlist"));
		MatukioHelperUtilsBasic::expandPathway(JTEXT::_('COM_MATUKIO_EVENT_PAYPAL_PAYMENT'), "");

		$net_amount = $booking->payment_brutto;
		$tax_amount = 0;

		$successurl = JURI::base() . substr(
				JRoute::_("index.php?option=com_matukio&view=callback&booking_id=" . $booking_id), strlen(JURI::base(true)) + 1
		);

		$cancelreturn = JURI::base() . substr(
				JRoute::_("index.php?option=com_matukio&view=callback&task=cancel&booking_id=" . $booking_id . "&return=1"), strlen(JURI::base(true)) + 1
		);

		$item_number = $booking->nrbooked;

		$this->event = $event;
		$this->user = $user;
		$this->booking = $booking;
		$this->merchant_address = MatukioHelperSettings::getSettings("paypal_address", 'paypal@compjoom.com');
		$this->currency = MatukioHelperSettings::getSettings("paypal_currency", 'EUR');
		$this->success_url = $successurl;
		$this->cancel_url = $cancelreturn;
		$this->item_number = $item_number;
		$this->net_amount = $net_amount;
		$this->tax_amount = $tax_amount;

		parent::display($tpl);
	}
}
