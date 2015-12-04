<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       24.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

/**
 * Class MatukioModelEventlist
 *
 * @since  2.2.5
 */
class MatukioModelEvent extends JModelLegacy
{
	/**
	 * the constructor Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Get the context
	 *
	 * @return null|string
	 */
	public function getContext()
	{
		return $this->context;
	}

	/**
	 * Gets the event if any out of the database
	 *
	 * @return mixed
	 */
	public function getEvent()
	{
		$event_id = JFactory::getApplication()->input->getInt("id", 0);

		$event = MatukioHelperUtilsEvents::getEventEditTemplate($event_id);

		return $event;
	}

	/**
	 * Builds the query
	 *
	 * @param   int  $id  - The event id
	 *
	 * @return string
	 */
	private function _buildQuery($id)
	{
		$query = "SELECT * FROM #__matukio WHERE id = '" . $id . "'";

		return $query;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 */
	public function getTable($type = 'Events', $prefix = 'MatukioTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
}
