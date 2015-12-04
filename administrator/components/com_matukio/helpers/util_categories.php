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
 * Class MatukioHelperCategories
 *
 * @since  1.0.0
 */
class MatukioHelperCategories
{
	private static $instance;

	/**
	 * Gets the category name
	 *
	 * @param   int  $id  - The id
	 *
	 * @return  mixed
	 */
	public static function getCategoryName($id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("Select id, title FROM #__categories WHERE id = " . $id);

		$cat = $database->loadObject();

		return $cat->title;
	}

	/**
	 * Gets the category alias
	 *
	 * @param   int  $id  - The id
	 *
	 * @return  mixed
	 */
	public static function getCategoryAlias($id)
	{
		$database = JFactory::getDBO();
		$database->setQuery("Select id, alias FROM #__categories WHERE id = " . $id);

		$cat = $database->loadObject();

		return $cat->alias;
	}
}
