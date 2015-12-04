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
jimport('joomla.application.component.model');

/**
 * Class MatukioModelEditorganizer
 *
 * @since  2.2.0
 */
class MatukioModelEditorganizer extends JModelLegacy
{
	/**
	 * The constructor, just setting id here
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId = JFactory::getApplication()->input->getInt('id', 0);
	}

	/**
	 * Loads the Organizer
	 *
	 * @return  mixed
	 */
	public function getOrganizer()
	{
		$id = JFactory::getApplication()->input->getInt('id', 0);

		if (empty($this->_data))
		{
			$query = $this->_buildQuery($id);
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
		}

		return $this->_data;
	}

	/**
	 * Builds the query
	 *
	 * @param   int  $id  - The id
	 *
	 * @return  JDatabaseQuery
	 */
	private function _buildQuery($id)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("*")->from($db->quoteName("#__matukio_organizers"))->where("id = " . $id);

		return $query;
	}
}
