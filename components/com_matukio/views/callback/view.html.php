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
 * Class MatukioViewCallback
 *
 * @since       2.0.0
 * @deprecated  Use ppayment instead
 */
class MatukioViewCallback extends JViewLegacy
{
	public function display($tpl = NULL)
	{
		$booking_id = JFactory::getApplication()->input->get('booking_id', 0);
		$user = JFactory::getUser();
		$model = $this->getModel();
		$return = JFactory::getApplication()->input->get('return', 0);

		if (empty($booking_id))
		{
			JError::raiseError('404', "COM_MATUKIO_NO_ID");

			return;
		}

		$booking = $model->getBooking($booking_id);
		$event = $model->getEvent($booking->semid);

		if ($return != 1)
		{
			$dispatcher = JDispatcher::getInstance();
			$results = $dispatcher->trigger('onAfterPaidBooking', $booking, $event);
		}

		MatukioHelperUtilsBasic::expandPathway(JTEXT::_('COM_MATUKIO_EVENTS'), JRoute::_("index.php?option=com_matukio"));
		MatukioHelperUtilsBasic::expandPathway(JTEXT::_('COM_MATUKIO_EVENT_PAYPAL_PAYMENT'), "");

		$this->event = $event;
		$this->user = $user;
		$this->booking = $booking;

		parent::display($tpl);
	}
}
