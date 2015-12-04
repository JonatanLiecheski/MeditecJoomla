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
 * Class MatukioControllerBooking
 *
 * @since  4.1.1
 */
class MatukioControllerBooking extends JControllerLegacy
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
		$viewName = JFactory::getApplication()->input->get('view', 'Booking');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Booking', 'MatukioModel');
		$view->setModel($model, true);
		$view->display();
	}

	/**
	 * Checks a user in
	 *
	 * @throws  Exception
	 * @return  void
	 */
	public function checkin()
	{
		if (MatukioHelperSettings::_("checkin_only_organizer", 1) && !JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
		{
			// Access check for editing this event
			throw new Exception(JText::_("JERROR_ALERTNOAUTHOR"), 403);
		}

		$uuid = JFactory::getApplication()->input->get('uuid', 0);

		if (empty($uuid))
		{
			throw new Exception(JText::_("COM_MATUKIO_NO_ID"), 404);
		}

		$db = JFactory::getDbo();

		// First load booking
		$query = $db->getQuery(true);

		$query->select("*")
			->from("#__matukio_bookings")
			->where("uuid = " . $db->quote($uuid))
			->where("status = " . MatukioHelperUtilsBooking::$ACTIVE);

		$db->setQuery($query);

		$booking = $db->loadObject();

		if (empty($booking))
		{
			throw new Exception(JText::_("COM_MATUKIO_CHECKIN_NO_ACTIVE_BOOKING_FOUND"), 404);
		}

		// Let's check if the user is checked in already
		if ($booking->checked_in == 1)
		{
			throw new Exception(JText::_("COM_MATUKIO_CHECKIN_BOOKING_ALREADY_CHECKED_IN"), 42);
		}

		// Let's checkin the user
		$query = $db->getQuery(true);

		$query->update("#__matukio_bookings")
			->set("checked_in = 1")
			->where("uuid = " . $db->quote($uuid));

		$db->setQuery($query);
		$db->execute();

		// Let's redirect to the booking details page
		$link = JRoute::_("index.php?option=com_matukio&view=booking&uuid=" . $uuid);
		$msg = JText::_("COM_MATUKIO_CHECKIN_SUCCESSFULL");

		$this->setRedirect($link, $msg);
	}
}
