<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       08.06.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');

/**
 * Class MatukioViewBooking
 *
 * @since  4.1.1
 */
class MatukioViewBooking extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  - The templates
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$uuid = JFactory::getApplication()->input->get('uuid', 0);

		$model = $this->getModel();

		$params = JComponentHelper::getParams('com_matukio');
		$menuitemid = JFactory::getApplication()->input->get('Itemid');

		if ($menuitemid)
		{
			$site = new JSite;
			$menu = $site->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			$params->merge($menuparams);
		}

		// Raise error
		if (empty($uuid))
		{
			throw new Exception(JText::_("COM_MATUKIO_NO_ID"), 404);
		}

		$booking = $model->getBooking($uuid);

		if (empty($booking))
		{
			throw new Exception(JText::_("COM_MATUKIO_NO_BOOKING_FOUND"), 404);
		}

		$model = JModelLegacy::getInstance('Event', 'MatukioModel');
		$event = $model->getItem($booking->semid);

		$this->booking = $booking;

		$this->title = JText::_("COM_MATUKIO_BOOKING_DETAILS");

		$title = JFactory::getDocument()->getTitle();
		JFactory::getDocument()->setTitle($title . " - " . $this->title);


		$this->event = $event;
		$this->user = JFactory::getUser();

		parent::display($tpl);
	}
}
