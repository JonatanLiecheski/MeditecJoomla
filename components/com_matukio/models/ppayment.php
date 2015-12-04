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
jimport('joomla.application.component.model');

/**
 * Class MatukioModelPPayment
 *
 * @since  2.2.0
 */
class MatukioModelPPayment extends JModelLegacy
{
	/**
	 * Gets the booking
	 *
	 * @param   int  $uuid  - The booking uuid
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

	/**
	 * Gets the event (we just reference to the event model -- update sometime)
	 *
	 * @param   int  $id  - The id
	 *
	 * @return  mixed
	 */
	public function getEvent($id)
	{
		$model = JModelLegacy::getInstance('Event', 'MatukioModel');
		$event = $model->getItem($id);

		return $event;
	}
}
