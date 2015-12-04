<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die();

/**
 * Class MatukioHelperPlugins
 *
 * @since  2.0.0
 */
class MatukioHelperPlugins
{
	/**
	 * Triggers the plugin
	 *
	 * @param   object  $event  - The event
	 * @param   array   &$data  - The data
	 *
	 * @return  array
	 */
	public static function triggerPlugin($event, array &$data = array())
	{
		static $dispatcher = null;

		if ($dispatcher === null)
		{
			$dispatcher = JDispatcher::getInstance();
		}

		return $dispatcher->trigger($event, $data);
	}
}
