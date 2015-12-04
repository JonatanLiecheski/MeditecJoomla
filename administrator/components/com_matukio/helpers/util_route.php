<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die ('Restricted access');

/**
 * Class MatukioHelperRoute
 *
 * @since  1.0.0
 */
class MatukioHelperRoute
{
	private static $instance;

	/**
	 * Gets the routing to an event
	 *
	 * @param   int     $id     - The event id
	 * @param   int     $catid  - The category id
	 * @param   int     $art    - The art (0 = normal, 1 = my bookings, 2 = my offers)
	 * @param   int     $uid    - The booking id (opt)
	 * @param   string  $uuid   - The unique booking id
	 *
	 * @return string
	 */
	public static function getEventRoute($id, $catid = 0, $art = 0, $uid = 0, $uuid = "")
	{
		$needles = array(
			'event' => (int) $id,
			'category' => (int) $catid
		);

		$link = 'index.php?option=com_matukio&view=event&art=' . $art . '&catid=' . $catid . '&id=' . $id;

		if (!empty($uid))
		{
			$link .= "&uid=" . $uid;
		}

		if (!empty($uuid))
		{
			$link .= "&uuid=" . $uuid;
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item->id;
		}

		return $link;
	}

	/**
	 * Gets the eventlist route
	 *
	 * @param   int  $catid  - The category
	 * @param   int  $art    - The type
	 *
	 * @return  string
	 */
	public static function getEventlistRoute($catid = 0, $art = 0)
	{
		$needles = array(
			'category' => (int) $catid
		);

		$link = 'index.php?option=com_matukio&art=' . $art;

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item->id;
		}

		return $link;
	}

	/**
	 * Finds the matching menu entry
	 *
	 * @param   array  $needles  - The needles (id and catid to find)
	 *
	 * @return  null
	 */
	public static function _findItem($needles)
	{
		$component = JComponentHelper::getComponent('com_matukio');

		$componentId = 'component_id';

		$application = JFactory::getApplication();

		$menus = $application->getMenu('site', array());

		$items = $menus->getItems($componentId, $component->id);

		$match = null;

		foreach ($needles as $needle => $id)
		{
			if (count($items))
			{
				foreach ($items as $item)
				{
					if ($needle == 'event')
					{
						if ((@$item->query['id'] == $id))
						{
							$match = $item;
						}

						// If we don't find a match, try to set a default one
						if (!isset($match))
						{
							if ((@$item->query['view'] == 'eventlist'))
							{
								$match = $item;
							}
						}
					}
					else if ($needle == 'category')
					{
						if ((@$item->query['startcat'] == $id))
						{
							$match = $item;
						}
					}
				}
			}

			if (isset($match))
			{
				break;
			}
		}

		return $match;
	}
}
