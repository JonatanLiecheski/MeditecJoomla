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
 * Class MatukioModelEditbooking
 *
 * @since 2.0
 */
class MatukioModelEditbooking extends JModelLegacy
{
	/**
	 * Konstruktor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId = JFactory::getApplication()->input->getInt('booking_id', 0);
	}

	/**
	 * Gets the booking
	 *
	 * @return mixed
	 */
	public function getBooking()
	{
		$id = JFactory::getApplication()->input->getInt('booking_id', 0);

		if (empty($this->_data))
		{
			$query = $this->_buildQuery($id);
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
		}

		return $this->_data;
	}

	private function _buildQuery($id)
	{
		$query = "SELECT * FROM #__matukio_bookings WHERE id = " . $id;

		return $query;
	}
}
