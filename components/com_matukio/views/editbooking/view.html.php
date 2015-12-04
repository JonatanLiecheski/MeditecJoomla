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
 * Class MatukioViewEditbooking
 *
 * @since  2.0.0
 */
class MatukioViewEditbooking extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  The template
	 *
	 * @return  mixed|object
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();

		$booking = $model->getBooking();

		// New booking
		if (!$booking)
		{
			$booking = JTable::getInstance('bookings', 'Table');
			$booking->id = 0;
			$booking->semid = JFactory::getApplication()->input->getInt("cid", 0);
			$booking->userid = 0;
			$booking->uuid = MatukioHelperPayment::getUuid(true);
			$booking->uid = 0;
		}

		// Check authorization
		if (!JFactory::getUser()->authorise('core.edit', 'com_matukio'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$dispatcher = JDispatcher::getInstance();

		JPluginHelper::importPlugin("payment");
		$gateways = $dispatcher->trigger('onTP_GetInfo', array(MatukioHelperPayment::$matukio_payment_plugins));

		$payment = array();

		foreach ($gateways as $gway)
		{
			$payment[] = array("name" => $gway->id, "title" => $gway->name);
		}

		$this->booking = $booking;
		$this->payment = $payment;

		// Booking status
		$options[] = array(
			"value" => MatukioHelperUtilsBooking::$PENDING,
			"text" => MatukioHelperUtilsBooking::getBookingStatusName(MatukioHelperUtilsBooking::$PENDING)
		);

		$options[] = array(
			"value" => MatukioHelperUtilsBooking::$ACTIVE,
			"text" => MatukioHelperUtilsBooking::getBookingStatusName(MatukioHelperUtilsBooking::$ACTIVE)
		);

		$options[] = array(
			"value" => MatukioHelperUtilsBooking::$WAITLIST,
			"text" => MatukioHelperUtilsBooking::getBookingStatusName(MatukioHelperUtilsBooking::$WAITLIST)
		);

		$options[] = array(
			"value" => MatukioHelperUtilsBooking::$ARCHIVED,
			"text" => JText::_("COM_MATUKIO_ARCHIVED")
		);

		$options[] = array(
			"value" => MatukioHelperUtilsBooking::$DELETED,
			"text" => JText::_("COM_MATUKIO_DELETED")
		);

		$this->status_select = JHtml::_(
			'select.genericlist', $options, 'status', '',
			'value', 'text', $booking->status
		);

		$marks[] = array("value" => 0, "text" => JText::_("COM_MATUKIO_NONE"));
		$marks[] = array("value" => 1, "text" => "1");
		$marks[] = array("value" => 2, "text" => "2");
		$marks[] = array("value" => 3, "text" => "3");
		$marks[] = array("value" => 4, "text" => "4");
		$marks[] = array("value" => 5, "text" => "5");
		$marks[] = array("value" => 6, "text" => "6");

		$this->mark_select = JHtml::_(
			'select.genericlist', $marks, 'mark', '',
			'value', 'text', $booking->mark
		);

		$this->select_checkedin = MatukioHelperInput::getRadioButtonBool("checked_in", "checked_in", $booking->checked_in);

		parent::display($tpl);
	}
}
