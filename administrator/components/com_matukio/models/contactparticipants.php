<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.modeladmin');

/**
 * Class MatukioModelContactpart
 *
 * @since  2.2.4
 */
class MatukioModelContactparticipants extends JModelLegacy
{

	/**
	 * Std. Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Returns a list of participants for the given (GET) event id
	 *
	 * @return  mixed
	 */
	public function getParticipants()
	{
		$event_id = JFactory::getApplication()->input->getInt('event_id', 0);
		$booking_ids = JFactory::getApplication()->input->get('cid', '', 'array');

		if (empty($this->_data))
		{
			$query = $this->_buildQuery($event_id, $booking_ids);

			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObjectList();
		}

		return $this->_data;
	}

	/**
	 * Loads the event
	 *
	 * @param   int  $event_id  - The event id
	 *
	 * @return  mixed
	 */
	public function getEvent($event_id)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("*")->from("#__matukio")->where("id = " . $db->quote($event_id));
		$db->setQuery($query);

		return($db->loadObject());
	}

	/**
	 * Build the query
	 *
	 * @param   int    $event_id     - The event id
	 * @param   array  $booking_ids  - Bookings ids
	 *
	 * @throws  Exception - if query fails
	 * @return  JDatabaseQuery
	 */
	private function _buildQuery($event_id, $booking_ids)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);


		if (!empty($event_id))
		{
			$query->select("*")->from("#__matukio_bookings")->where("semid = " . $event_id, "status = " . MatukioHelperUtilsBooking::$BOOKED);
		}
		elseif (!empty($booking_ids))
		{
			$query->select("*")->from("#__matukio_bookings")->where("id IN (" . implode(",", $booking_ids) . ")");
		}
		else
		{
			throw new Exception("No bookings / event given - can't send to anyone", 50);
		}

		return $query;
	}
}
