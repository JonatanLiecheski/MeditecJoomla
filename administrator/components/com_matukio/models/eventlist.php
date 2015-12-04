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
class MatukioModelEventlist extends JModelList
{
	/**
	 * the constructor Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'e.id',
				'title', 'e.title',
				'semnum', 'e.semnum',
				'begin', 'e.begin',
				'end', 'e.end',
				'target', 'e.target',
				'teacher', 'e.teacher',
				'fees', 'e.fees',
				'maxpupil', 'e.maxpupil',
				'shortdesc', 'e.shortdesc',
				'catid', 'e.catid',
				'place', 'e.place',
				'gmaploc', 'e.gmaploc',
				'hot_event', 'e.hot_event',
				'top_event', 'e.top_event'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
		$this->setState('filter.status', $status);

		$categories = $this->getUserStateFromRequest($this->context . '.filter.categories', 'filter_categories');
		$this->setState('filter.categories', $categories);

		parent::populateState('e.begin', 'asc');
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
	 * Method to get a JDatabaseQuery object for retrieving the data set from a database.
	 *
	 * @return  JDatabaseQuery   A JDatabaseQuery object to retrieve the data set.
	 */
	public function getListQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery('true');

		$curdate = MatukioHelperUtilsDate::getCurrentDate();

		// Spaces: event = e, cc = category, u = checkout
		$query->select('e.*, cc.title AS category, u.name AS editor')->from('#__matukio AS e')
			->leftJoin("#__categories AS cc ON cc.id = e.catid")
			->leftJoin("#__users AS u ON u.id = e.checked_out");

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$searchfields = array(
				'e.title',
				'e.semnum',
				'e.begin',
				'e.end',
				'e.target',
				'e.teacher',
				'e.fees',
				'e.shortdesc',
				'e.catid',
				'e.place',
				'e.zusatz1',
				'e.gmaploc',
				'e.zusatz2',
				'e.description'
			);

			if (stripos($search, 'id:') === 0)
			{
				$query->where('e.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where(
					implode(" LIKE " . $search . " OR ", $searchfields)
					. " LIKE " . $search
				);
			}
		}

		$status = $this->getState('filter.status');

		if (!empty($status))
		{
			switch ($status)
			{
				case "published":
					$query->where("e.published = '1'");
					break;

				case "unpublished":
					$query->where("e.published = '0'");
					break;

				case "current":
					$query->where("e.end > " . $db->quote($curdate));
					break;

				case "old":
					$query->where("e.end <= " . $db->quote($curdate));
					break;

				case "all":
					break;
			}
		}
		else
		{
			$this->setState('filter.status', 'current');
			$query->where("e.end > " . $db->quote($curdate));
		}

		$category = $this->getState('filter.categories');

		if (!empty($category))
		{
			$query->where('e.catid = ' . $db->q($category));
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		$query->order($db->escape($orderCol . ' ' . $orderDirn));

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
