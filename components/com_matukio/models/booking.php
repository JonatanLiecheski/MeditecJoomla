<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       08.06.14
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

/**
 * Class MatukioModelBooking
 *
 * @since  4.1.1
 */
class MatukioModelBooking extends JModelLegacy
{
	/**
	 * Loads the booking
	 *
	 * @param   string  $uuid  - The booking uuid
	 *
	 * @return  mixed
	 */
	public function getBooking($uuid)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__matukio_bookings')->where('uuid=' . $db->quote($uuid));
		$db->setQuery($query, 0, 1);

		return $db->loadObject();
	}
}
