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
jimport('joomla.application.component.modeladmin');

/**
 * Class MatukioModelTemplates
 *
 * @since  2.2.0
 */
class MatukioModelTemplates extends JModelLegacy
{
	/**
	 * The constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Gets the templates
	 *
	 * @return  mixed
	 */
	public function getTemplates()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("*")->from("#__matukio_templates")->where("published = 1");

		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
