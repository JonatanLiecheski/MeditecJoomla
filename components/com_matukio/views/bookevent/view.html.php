<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');

/**
 * Class MatukioViewBookevent
 *
 * @since  2.0.0
 */
class MatukioViewBookevent extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @throws  Exception
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$cid = JFactory::getApplication()->input->getInt('cid', 0);
		$user = JFactory::getUser();

		// Booking id!!
		$uid = JFactory::getApplication()->input->getInt('uid', 0);
		$uuid = JFactory::getApplication()->input->get('uuid', 0);

		if (empty($cid))
		{
			throw new Exception(JText::_("COM_MATUKIO_NO_ID"), 404);
		}

		// Load event (use model function)
		$emodel = JModelLegacy::getInstance('Event', 'MatukioModel');
		$event = $emodel->getItem($cid);

		$booking = null;

		if (!empty($uuid))
		{
			$model = JModelLegacy::getInstance('Booking', 'MatukioModel');
			$booking = $model->getBooking($uuid);

			if (empty($booking))
			{
				throw new Exception(JText::_("COM_MATUKIO_NO_BOOKING_FOUND"), 404);
			}

			$uid = $booking->id;
		}

		// With Payment Step or without?
		$steps = 3;

		if (empty($event->fees))
		{
			$steps = 2;
		}

		$fields_p1 = MatukioHelperUtilsBooking::getBookingFields(1);
		$fields_p2 = MatukioHelperUtilsBooking::getBookingFields(2);
		$fields_p3 = MatukioHelperUtilsBooking::getBookingFields(3);

		// MatukioHelperUtilsBasic::expandPathway(JTEXT::_('COM_MATUKIO_EVENTS'), JRoute::_("index.php?option=com_matukio&view=eventlist"));

		// Add event to breadcrumb :)
		MatukioHelperUtilsBasic::expandPathway(JTEXT::_($event->title), JRoute::_("index.php?option=com_matukio&view=event&id=" . $cid));
		MatukioHelperUtilsBasic::expandPathway(JTEXT::_('COM_MATUKIO_EVENT_BOOKING'), "");

		$dispatcher = JDispatcher::getInstance();

		JPluginHelper::importPlugin("payment");
		$gateways = $dispatcher->trigger('onTP_GetInfo', array(MatukioHelperPayment::$matukio_payment_plugins));

		$payment = array();

		foreach ($gateways as $gway)
		{
			$payment[] = array("name" => $gway->id, "title" => $gway->name);
		}

		if (empty($payment))
		{
			// If no payment plugins enabled then set Steps to 2 :)
			$steps = 2;
		}

		$this->gateways = $gateways;
		$this->event = $event;
		$this->uid = $uid;
		$this->uuid = $uuid;
		$this->booking = $booking;
		$this->user = $user;
		$this->steps = $steps;
		$this->payment = $payment;
		$this->fields_p1 = $fields_p1;
		$this->fields_p2 = $fields_p2;
		$this->fields_p3 = $fields_p3;

		parent::display($tpl);
	}
}
