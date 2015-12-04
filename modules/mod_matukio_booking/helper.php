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

class ModMatukioBookingHelper
{

	private static $instance;

	/**
	 * @param        $catid
	 * @param string $orderby
	 *
	 * @return mixed
	 */
	public static function getEvents($catid, $orderby = "begin ASC")
	{
		$db = JFactory::getDbo();

		$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());

		if (!empty($catid[0]))
		{
			$cids = implode(',', $catid);

			$query = "SELECT a.*, cat.title AS category FROM #__matukio AS a LEFT JOIN #__categories AS cat ON cat.id = a.catid WHERE a.catid IN ("
				. $cids . ") AND a.published = 1 AND cat.access in (" . $groups . ") AND a.begin > '" . JFactory::getDate()->toSql() . "' ORDER BY a." . $orderby;
		}
		else
		{
			$query = "SELECT a.*, cat.title AS category FROM #__matukio AS a LEFT JOIN #__categories AS cat ON cat.id = a.catid WHERE a.published = 1 AND cat.access in (" . $groups . ") AND a.begin > '"
				. JFactory::getDate()->toSql() . "' ORDER BY " . $orderby;
		}
		$db->setQuery($query);
		return $db->loadObjectList();
	}

}
