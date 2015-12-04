<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       12.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

/**
 * Class MatukioModelLocation
 *
 * @since  3.0.0
 */
class MatukioModelLocation extends JModelLegacy
{
	/**
	 * Get the location
	 *
	 * @param   int  $id  - The id
	 *
	 * @return  mixed
	 */
	public function getLocation($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__matukio_locations')
			->where('id=' . $db->quote($id) . ' AND published = 1');

		$db->setQuery($query, 0, 1);

		return $db->loadObject();
	}

	/**
	 * Gets the upcoming events
	 *
	 * @param   int     $loc_id   - The location id
	 * @param   string  $limit    - The limit (default 5)
	 * @param   string  $orderby  - The order by (begin)
	 *
	 * @return mixed
	 */
	public function getUpcomingEvents($loc_id, $limit = "5", $orderby = "r.begin ASC")
	{
		$db = JFactory::getDbo();

		$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());

		$query = "SELECT a.*, r.*, cat.title AS category, cat.alias as catalias, cat.access FROM #__matukio_recurring AS r
			LEFT JOIN #__matukio AS a ON r.event_id = a.id
			LEFT JOIN #__categories AS cat ON cat.id = a.catid
			WHERE a.place_id = " . $db->quote($loc_id) . " AND r.published = 1 AND cat.access in (" . $groups . ")
			AND r.begin > '" . JFactory::getDate()->toSql() . "' ORDER BY " . $orderby . " LIMIT " . $limit;

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
