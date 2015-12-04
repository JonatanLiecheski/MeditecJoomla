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
jimport('joomla.application.component.model');

/**
 * Class MatukioModelEditBooking
 *
 * @since  1.0.0
 */
class MatukioModelEditBooking extends JModelLegacy
{
	/**
	 * Contructor
	 */
	public function __construct()
	{
		parent::__construct();
		$uuid = JFactory::getApplication()->input->get('booking_id', '');
		$this->setUuid = $uuid;
	}

	/**
	 * Loads the booking out of the database
	 *
	 * @return  mixed
	 */
	public function getBooking()
	{
		$uuid = JFactory::getApplication()->input->get('booking_id', 0);

		if (empty($this->_data))
		{
			$query = $this->_buildQuery($uuid);
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
		}

		return $this->_data;
	}

	/**
	 * Builds the query
	 *
	 * @param   string  $uuid  - The booking uuid
	 *
	 * @return  string
	 */
	private function _buildQuery($uuid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("*")
			->from("#__matukio_bookings")
			->where("uuid = " . $db->quote($uuid));

		return $query;
	}
}
