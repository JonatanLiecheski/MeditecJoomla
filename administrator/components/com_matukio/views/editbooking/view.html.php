<?php
/**
 * Matukio
 * @package Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 2.0.0 Stable $
 **/
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewEditbooking
 *
 * @since  2.0
 */
class MatukioViewEditbooking extends JViewLegacy
{
	/**
	 * Displays the participant edit form
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @throws  Exception
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$booking_id = JFactory::getApplication()->input->getInt('booking_id', 0);
		$model = $this->getModel();
		$booking = null;

		if (!empty($booking_id))
		{
			$booking = $model->getBooking();
		}

		if (!$booking)
		{
			$booking = JTable::getInstance('bookings', 'Table');

			$event_id = JFactory::getApplication()->input->getInt("event_id", 0);

			if (!empty ($event_id))
			{
				$booking->semid = $event_id;
			}

			$booking->uuid = MatukioHelperPayment::getUuid(true);
		}

		$db = JFactory::getDbo();
		$db->setQuery("SELECT r.id AS value, CONCAT(a.title, ' ', r.begin) AS text FROM #__matukio_recurring AS r LEFT JOIN #__matukio AS a ON r.event_id = a.id ");
		$events = (array) $db->loadObjectList();

		$this->event_select = JHtml::_('select.genericlist', $events, 'event_id', '',
			'value', 'text', $booking->semid
		);

		$dispatcher = JDispatcher::getInstance();

		JPluginHelper::importPlugin("payment");
		$gateways = $dispatcher->trigger('onTP_GetInfo', array(MatukioHelperPayment::$matukio_payment_plugins));

		$payment = array();

		foreach ($gateways as $gway)
		{
			$payment[] = array("name" => $gway->id, "title" => $gway->name);
		}

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

		$this->booking = $booking;
		$this->payment = $payment;

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Ads the toolbar buttons
	 *
	 * @return void
	 */
	public function addToolbar()
	{
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_MATUKIO_EDIT_BOOKING'), 'user');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::help('screen.booking', true);
	}
}
