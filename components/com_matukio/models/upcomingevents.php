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
 * Class MatukioModelUpcomingevents
 *
 * @since  1.0.0
 */
class MatukioModelUpcomingevents extends JModelLegacy
{
	/**
	 * Loads the events
	 *
	 * @param   int     $catid    - The category id
	 * @param   int     $limit    - The limit (nr events)
	 * @param   string  $orderby  - Order by (default r.begin)
	 *
	 * @return mixed
	 */
	public function getEvents($catid, $limit = 10, $orderby = "r.begin ASC")
	{
		$db = JFactory::getDbo();
		$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());

		// Quick fix for missing prefix @Update 3.1
		if (strpos($orderby, 'begin') !== false && strpos($orderby, 'r.') === false)
		{
			// Add r.
			$orderby = "r." . $orderby;
		}

		if (!empty($catid[0]))
		{
			$cids = implode(',', $catid);

			$query = "SELECT a.*, r.*, cat.title AS category, cat.alias as catalias, cat.access FROM #__matukio_recurring AS r
				LEFT JOIN #__matukio AS a ON r.event_id = a.id
				LEFT JOIN #__categories AS cat ON cat.id = a.catid
				WHERE a.catid IN ("
				. $cids . ") AND r.published = 1 AND cat.access in (" . $groups . ") AND r.begin > '"
				. JFactory::getDate()->toSql() . "' ORDER BY a." . $orderby;
		}
		else
		{
			$query = "SELECT a.*, r.*, cat.title AS category, cat.alias as catalias, cat.access FROM #__matukio_recurring AS r
			LEFT JOIN #__matukio AS a ON r.event_id = a.id
			LEFT JOIN #__categories AS cat ON cat.id = a.catid
			WHERE r.published = 1 AND cat.access in (" . $groups . ") AND r.begin > '"
			. JFactory::getDate()->toSql() . "' ORDER BY " . $orderby;
		}

		$db->setQuery($query, 0, $limit);

		return $db->loadObjectList();
	}
}
