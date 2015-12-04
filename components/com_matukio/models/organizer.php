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
 * Class MatukioModelOrganizer
 *
 * @since  2.2.0
 */
class MatukioModelOrganizer extends JModelLegacy
{
	/**
	 * Loads the organizer
	 *
	 * @param   int  $id  - The organizer id
	 *
	 * @return  mixed
	 */
	public function getOrganizer($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__matukio_organizers')->where('id=' . $db->quote($id));
		$db->setQuery($query, 0, 1);

		return $db->loadObject();
	}

	/**
	 * Gets the upcoming events
	 *
	 * @param   int     $orga_id  - The organizer id
	 * @param   string  $limit    - The limit (default 5)
	 * @param   string  $orderby  - The order by (begin)
	 *
	 * @return mixed
	 */
	public function getUpcomingEvents($orga_id, $limit = "5", $orderby = "r.begin ASC")
	{
		$db = JFactory::getDbo();

		$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());

		$query = "SELECT a.*, r.*, cat.title AS category, cat.alias as catalias, cat.access FROM #__matukio_recurring AS r
			LEFT JOIN #__matukio AS a ON r.event_id = a.id
			LEFT JOIN #__categories AS cat ON cat.id = a.catid
			WHERE a.publisher = " . $db->quote($orga_id) . " AND r.published = 1 AND cat.access in (" . $groups . ")
			AND r.begin > '" . JFactory::getDate()->toSql() . "' ORDER BY " . $orderby . " LIMIT " . $limit;

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
