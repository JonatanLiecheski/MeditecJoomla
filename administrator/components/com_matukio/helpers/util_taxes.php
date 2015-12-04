<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       16.10.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class MatukioHelperTaxes
 *
 * @since  3.0.0
 */
class MatukioHelperTaxes
{
	private static $instance;

	/**
	 * Gets all taxes in an objectlist out of the database
	 *
	 * @param   mixed  $published  - Published parameter, 0, 1 or anything else for all
	 *
	 * @return  mixed
	 */
	public static function getTaxes($published = 1)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("*")->from("#__matukio_taxes");

		if ($published == 1)
		{
			$query->where("published = 1");
		}
		elseif ($published == 0)
		{
			$query->where("published = 0");
		}

		$query->order("value ASC");

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
