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
 * Class MatukioViewPPayment
 *
 * @since  2.2.0
 */
class MatukioViewPPayment extends JViewLegacy
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
		$uuid = JFactory::getApplication()->input->get('uuid', 0);

		if (empty($uuid))
		{
			JError::raise(E_ERROR, 404, JText::_("COM_MATUKIO_NO_ID"));
		}

		$model = $this->getModel();

		$booking = $model->getBooking($uuid);
		$event = $model->getEvent($booking->semid);

		if (empty($booking))
		{
			JError::raise(E_ERROR, 404, JText::_("COM_MATUKIO_NO_BOOKING_FOUND"));
		}

		$this->uuid = $uuid;
		$this->booking = $booking;
		$this->event = $event;

		parent::display($tpl);
	}
}
