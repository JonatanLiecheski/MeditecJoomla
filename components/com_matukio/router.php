<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       12.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die ('Restricted access');

/**
 * Builds the route
 *
 * @param   string  &$query  - The query
 *
 * @return array
 */
function matukioBuildRoute(&$query)
{
	static $items;
	$itemid = null;

	$segments = array();

	if (isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}

	if (isset($query['task']))
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}

	if (isset($query['art']))
	{
		$segments[] = $query['art'];
		unset($query['art']);
	}

	if (isset($query['catid']))
	{
		$segments[] = $query['catid'];
		unset($query['catid']);
	}

	if (isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}

	if (isset($query['cid']))
	{
		$segments[] = $query['cid'];
		unset($query['cid']);
	}

	if (isset($query['tmpl']))
	{
		$segments[] = $query['tmpl'];
		unset($query['tmpl']);
	}

	if (isset($query['format']))
	{
		$segments[] = $query['format'];
		unset($query['format']);
	}

	if (isset($query['event_id']))
	{
		$segments[] = $query['event_id'];
		unset($query['event_id']);
	}

	if (isset($query['uid']))
	{
		$segments[] = $query['uid'];
		unset($query['uid']);
	}

	if (isset($query['booking_id']))
	{
		$segments[] = $query['booking_id'];
		unset($query['booking_id']);
	}

	if (isset($query['return']))
	{
		$segments[] = $query['return'];
		unset($query['return']);
	}

	if (isset($query['search']))
	{
		$segments[] = $query['search'];
		unset($query['search']);
	}

	if (isset($query['limit']))
	{
		$segments[] = $query['limit'];
		unset($query['limit']);
	}

	if (isset($query['dateid']))
	{
		$segments[] = $query['dateid'];
		unset($query['dateid']);
	}

	if (isset($query['routing']))
	{
		$segments[] = $query['routing'];
		unset($query['routing']);
	}

	return $segments;
}

/**
 * Gets a parsed route
 *
 * @param   array  $segments  - The segments
 *
 * @return  array
 */
function matukioParseRoute($segments)
{
	$vars = array();

	$site = new JSite;
	$menu = $site->getMenu();
	$item = $menu->getActive();

	// Count route segments
	$count = count($segments);

	// Default routing
	if (!isset($item))
	{
		if ($count == 4)
		{
			$vars['view'] = $segments[$count - 3];

			// UNDER TESTING
		}
		elseif ($count == 3)
		{
			$vars['view'] = $segments[$count - 2];
			$segments[$count - 2] = $segments[$count - 3];
		}
		else
		{
			$vars['view'] = 'eventlist';
		}

		$vars['catid'] = $segments[$count - 2];
		$vars['id'] = $segments[$count - 1];
	}
	else
	{
		$view = $segments[0];

		switch ($view)
		{
			default:
				if ($count == 1 && is_numeric($view))
				{
					$vars['art'] = $segments[0];
				}
				elseif (empty($view))
				{
					$vars['view'] = "eventlist";
				}
				else
				{
					$vars['view'] = $segments[0];
				}

				if ($count == 2)
				{
					$vars['art'] = $segments[0];
					$catid = explode(':', $segments[1]);
					$vars['catid'] = $catid[0];
				}
				break;


			case 'eventlist' :

				if ($count == 1)
				{
					$vars['view'] = 'eventlist';
				}

				if ($count == 2)
				{
					$vars['view'] = 'eventlist';
					$vars['art'] = $segments[1];
				}

				if ($count == 3)
				{
					$vars['view'] = 'eventlist';
					$vars['art'] = $segments[1];
					$catid = explode(':', $segments[2]);
					$vars['catid'] = $catid[0];
				}

				if ($count == 4)
				{
					$vars['view'] = 'eventlist';
					$vars['catid'] = $segments[2];
					$vars['art'] = $segments[1];
					$vars['limit'] = $segments[3];
				}

				if ($count == 5)
				{
					$vars['view'] = 'eventlist';
					$vars['catid'] = $segments[2];
					$vars['art'] = $segments[1];
					$vars['search'] = $segments[3];
					$vars['limit'] = $segments[4];
				}

				if ($count == 6)
				{
					$vars['view'] = 'eventlist';
					$vars['catid'] = $segments[2];
					$vars['art'] = $segments[1];
					$vars['search'] = "";
					$vars['limit'] = $segments[3];
					$vars['dateid'] = $segments[4];
				}

				break;

			case 'map' :
				$vars['view'] = 'map';
				$vars['tmpl'] = $segments[1];
				$vars['event_id'] = $segments[2];
				if ($count == 4)
				{
					$vars['location_id'] = $segments[3];
				}
				break;

			case 'matukio' :
				$vars['view'] = 'matukio';
				$vars['task'] = $segments[1];
				break;

			case 'ics' :
				$vars['view'] = 'ics';
				$vars['cid'] = $segments[1];
				$vars['tmpl'] = $segments[2];
				$vars['format'] = $segments[3];
				break;

			case 'event'   :
				if ($count == 1)
				{
					$vars['view'] = 'event';
				}

				if ($count == 2)
				{
					$vars['view'] = 'event';
					$id = explode(':', $segments[1]);
					$vars['id'] = $id[0];
				}

				if ($count == 3)
				{
					$vars['view'] = 'event';
					$id = explode(':', $segments[2]);
					$vars['id'] = $id[0];
					$vars['art'] = $segments[1];
				}

				if ($count == 4)
				{
					$vars['view'] = 'event';
					$id = explode(':', $segments[3]);
					$vars['id'] = $id[0];
					$catid = explode(':', $segments[2]);
					$vars['catid'] = $catid[0];
					$vars['art'] = $segments[1];
				}

				if ($count == 5)
				{
					$vars['view'] = 'event';
					$id = explode(':', $segments[3]);
					$vars['id'] = $id[0];
					$catid = explode(':', $segments[2]);
					$vars['catid'] = $catid[0];
					$vars['art'] = $segments[1];
					$vars['uid'] = $segments[4];
				}

				break;

			case 'participants'   :
				if ($count == 1)
				{
					$vars['view'] = 'participants';
				}

				if ($count == 2)
				{
					$vars['view'] = 'participants';
					$vars['cid'] = $segments[$count - 1];
				}

				if ($count == 3)
				{
					$vars['art'] = $segments[$count - 2];
					$vars['cid'] = $segments[$count - 1];
					$vars['view'] = 'participants';
				}

				if ($count == 4)
				{
					$vars['view'] = 'participants';
					$vars['task'] = $segments[1];
					$vars['cid'] = $segments[2];
					$vars['uid'] = $segments[3];
				}
				break;

			case 'createevent'   :
				if ($count == 1)
				{
					$vars['view'] = 'createevent';
				}

				if ($count == 2)
				{
					$vars['view'] = 'createevent';
					$vars['cid'] = $segments[$count - 1];
				}

				if ($count == 3)
				{
					$vars['view'] = 'createevent';
					$vars['cid'] = $segments[2];
					$vars['task'] = $segments[1];
				}
				break;

			case 'bookevent'   :
				if ($count == 1)
				{
					$vars['view'] = 'bookevent';
				}

				if ($count == 2)
				{
					$vars['view'] = 'bookevent';
					$cid = explode(':', $segments[1]);
					$vars['cid'] = $cid[0];
				}

				if ($count == 3)
				{
					$vars['view'] = 'bookevent';
					$vars['task'] = $segments[1];
					$vars['cid'] = $segments[2];
				}

				if ($count == 4)
				{
					$vars['view'] = 'bookevent';
					$vars['task'] = $segments[1];
					$vars['booking_id'] = $segments[2];
					$vars['return'] = $segments[3];
				}

				break;

			case 'paypalpayment' :

				if ($count == 1)
				{
					$vars['view'] = 'paypalpayment';
				}

				if ($count == 2)
				{
					$vars['view'] = 'paypalpayment';
					$vars['booking_id'] = $segments[1];
				}

				if ($count > 2)
				{
					$vars['view'] = 'paypalpayment';
					$vars['booking_id'] = $segments[1];
				}

				break;

			case 'callback' :

				if ($count == 1)
				{
					$vars['view'] = 'callback';
				}

				if ($count == 2)
				{
					$vars['view'] = 'callback';
					$vars['booking_id'] = $segments[1];
				}

				if ($count > 2)
				{
					$vars['view'] = 'callback';
					$vars['task'] = $segments[1];
					$vars['booking_id'] = $segments[2];
				}

				break;

			case 'editbooking' :

				if ($count == 1)
				{
					$vars['view'] = 'editbooking';
				}

				if ($count == 2)
				{
					$vars['view'] = 'editbooking';
					$vars['booking_id'] = $segments[1];
				}

				if ($count == 3)
				{
					$vars['view'] = 'editbooking';
					$vars['task'] = $segments[1];
					$vars['cid'] = $segments[2];
				}

				break;

			case 'rss'   :
				if ($count == 1)
				{
					$vars['view'] = 'rss';
				}

				if ($count == 2)
				{
					$vars['view'] = 'rss';
					$vars['catid'] = $segments[$count - 1];
				}

				break;

			case 'upcomingevents':
				if ($count == 1)
				{
					$vars['view'] = 'upcomingevents';
				}

				if ($count == 2)
				{
					$vars['view'] = 'upcomingevents';
					$vars['catid'] = $segments[$count - 1];
				}

				break;

			case 'calendar':
				if ($count == 1)
				{
					$vars['view'] = 'calendar';
				}

				if ($count == 2)
				{
					$vars['view'] = 'calendar';
					$vars['catid'] = $segments[$count - 1];
				}

				break;

			case 'organizer'   :
				if ($count == 1)
				{
					$vars['view'] = 'organizer';
				}

				if ($count == 2)
				{
					$vars['view'] = 'organizer';
					$id = explode(':', $segments[1]);
					$vars['id'] = $id[0];
				}

				break;

			case 'location'   :
				if ($count == 1)
				{
					$vars['view'] = 'location';
				}

				if ($count == 2)
				{
					$vars['view'] = 'location';
					$id = explode(':', $segments[1]);
					$vars['id'] = $id[0];
				}

				break;

			case 'booking':
				if ($count == 1)
				{
					$vars['view'] = 'booking';
				}

				if ($count == 2)
				{
					$vars['view'] = 'booking';
					$vars['task'] = $segments[$count - 1];
				}

				if ($count == 3)
				{
					$vars['view'] = 'booking';
					$vars['task'] = $segments[$count - 2];
					$vars['uuid'] = $segments[$count - 1];
				}

				break;
		}
	}

	return $vars;
}
