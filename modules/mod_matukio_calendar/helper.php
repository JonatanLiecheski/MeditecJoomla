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

class ModMatukioCalendarHelper {

    private static $instance;

    public static function getEvents($catid, $limit = 3, $orderby = "begin ASC") {
        $db = JFactory::getDbo();

        $groups	= implode(',', JFactory::getUser()->getAuthorisedViewLevels());

        if(!empty($catid[0])){
            $cids = implode(',', $catid);

            $query = "SELECT a.*, cat.title AS category FROM #__matukio AS a LEFT JOIN #__categories AS cat ON cat.id = a.catid WHERE a.catid IN ("
                . $cids . ") AND a.published = 1 AND cat.access in (" . $groups . ") AND a.begin > '" . JFactory::getDate()->toSql() . "' ORDER BY a." . $orderby;
        } else {
            $query = "SELECT a.*, cat.title AS category FROM #__matukio AS a LEFT JOIN #__categories AS cat ON cat.id = a.catid WHERE a.published = 1 AND cat.access in (" . $groups . ") AND a.begin > '"
                . JFactory::getDate()->toSql() . "' ORDER BY " . $orderby;
        }
        $db->setQuery($query,0, $limit);
        return $db->loadObjectList();
    }

}
