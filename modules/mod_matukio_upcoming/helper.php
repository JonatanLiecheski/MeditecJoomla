<?php
/**
 * Matukio
 * @package  Joomla!
 * @Copyright (C) 2013 - Yves Hoppe - compojoom.com
 * @All      rights reserved
 * @Joomla   ! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version  $Revision: 2.2.0 $
 **/

defined('_JEXEC') or die('Restricted access');

/**
 * Class ModMatukioUpcomingHelper
 *
 * @since  1.0.0
 */
class ModMatukioUpcomingHelper
{
	private static $instance;

	/**
	 * Loads the events out of the database.. we should use a component helper here
	 *
	 * @param   int     $catid    - catid
	 * @param   int     $limit    - Limit
	 * @param   string  $orderby  - Order by
	 *
	 * @return mixed
	 */
	public static function getEvents($catid, $limit = 3, $orderby = "begin ASC")
	{
		$db = JFactory::getDbo();
		$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());

		if (!empty($catid[0]))
		{
			$cids = implode(',', $catid);

			$query = "SELECT a.*, r.*, cat.title AS category FROM #__matukio_recurring AS r
				LEFT JOIN #__matukio AS a ON r.event_id = a.id
				LEFT JOIN #__categories AS cat ON cat.id = a.catid WHERE a.catid IN ("
				. $cids . ") AND r.published = 1 AND cat.access in (" . $groups . ") AND r.begin > '" . JFactory::getDate()->toSql()
				. "' ORDER BY r." . $orderby;
		}
		else
		{
			$query = "SELECT a.*, r.*, cat.title AS category FROM #__matukio_recurring AS r
				LEFT JOIN #__matukio AS a ON r.event_id = a.id
				LEFT JOIN #__categories AS cat ON cat.id = a.catid
				WHERE r.published = 1 AND cat.access in (" . $groups . ") AND r.begin > '"
				. JFactory::getDate()->toSql() . "' ORDER BY r." . $orderby;
		}

		$db->setQuery($query, 0, $limit);

		return $db->loadObjectList();
	}
}
