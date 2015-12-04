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
 * Class MatukioModelBookings
 *
 * @since  3.0.0
 */
class MatukioModelBookings extends JModelList
{
	/**
	 * the constructor Constructor.
	 *
	 * @param   array  $config  - An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'e.id',
				'name', 'e.name',
				'email', 'e.email',
				'sid', 'e.sid',
				'semid', 'e.semid',
				'userid', 'e.userid',
				'bookingdate', 'e.bookingdate',
				'updated', 'e.updated',
				'grade', 'e.grade',
				'comment', 'e.comment',
				'paid', 'e.paid',
				'nrbooked', 'e.nrbooked',
				'uuid', 'e.uuid',
				'payment_method', 'e.payment_method',
				'payment_brutto', 'e.payment_brutto',
				'status', 'e.status'
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
	 * @param   string  $ordering   - An optional ordering field.
	 * @param   string  $direction  - An optional direction (asc|desc).
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$status = $this->getUserStateFromRequest($this->context . '.filter.status', 'filter_status');
		$this->setState('filter.status', $status);

		$events = $this->getUserStateFromRequest($this->context . '.filter.events', 'filter_events');
		$this->setState('filter.events', $events);

		$time = $this->getUserStateFromRequest($this->context . '.filter.time', 'filter_time');
		$this->setState('filter.time', $time);

		parent::populateState('e.bookingdate', 'desc');
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
	 * @return  JDatabaseQuery  - A JDatabaseQuery object to retrieve the data set.
	 */
	public function getListQuery()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery('true');

		// Spaces: event = e, cc = user
		$query->select('e.*, cc.name, cc.username, r.id AS eventid, a.title AS eventtitle,
		r.begin as eventbegin, a.fees as eventfees,
		cc.id AS userid, e.id AS sid, e.name AS aname, e.email AS aemail')
			->from('#__matukio_bookings AS e')
			->leftJoin("#__users AS cc ON cc.id = e.userid")
			->leftJoin("#__matukio_recurring AS r ON r.id = e.semid")
			->leftJoin("#__matukio AS a ON a.id = r.event_id");

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			$searchfields = array(
				'e.name',
				'e.email',
				'e.sid',
				'a.title',
				'e.semid',
				'e.userid',
				'e.bookingdate',
				'e.updated',
				'e.grade',
				'e.comment',
				'e.paid',
				'e.nrbooked',
				'e.uuid',
				'e.payment_method',
				'e.payment_brutto'
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
				case "pending":
					// 0 is active in Seminar :(
					$query->where("e.status = '0'");
					break;

				case "active":
					$query->where("e.status = '1'");
					break;

				case 'activeandpending':
					$query->where("(e.status = '1' OR e.status = '0')");
					break;

				case "waitlist":
					$query->where("e.status = '2'");
					break;

				case "archived":
					$query->where("e.status = '3'");
					break;

				case "deleted":
					$query->where("e.status = '4'");
					break;

				case "paid":
					$query->where("e.paid = '1'");
					break;

				case "unpaid":
					$query->where("e.paid = '0'");
					break;

				case "all":
					break;
			}
		}
		else
		{
			// Set the default status to active and pending not to all and add it to the query
			$this->setState('filter.status', 'activeandpending');
			$query->where("(e.status = '1' OR e.status = '0')");
		}

		$timestatus = $this->getState('filter.time');

		if (!empty($timestatus))
		{
			switch ($timestatus)
			{
				case "day":
					$query->where("e.bookingdate > (curdate() - INTERVAL 1 DAY)");
					break;

				case "week":
					$query->where("e.bookingdate > (curdate() - INTERVAL 7 DAY)");
					break;

				case "month":
					$query->where("e.bookingdate > (curdate() - INTERVAL 31 DAY)");
					break;

				case "year":
					$query->where("e.bookingdate > (curdate() - INTERVAL 365 DAY)");
					break;

				case "all":
					break;
			}
		}
		else
		{
			$this->setState('filter.time', 'all');
		}

		$event = $this->getState('filter.events');

		if (!empty($event))
		{
			$query->where($db->qn('e.semid') . ' = ' . $db->q($event));
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
	 * @param   string  $type    - The table type to instantiate
	 * @param   string  $prefix  - A prefix for the table class name. Optional.
	 * @param   array   $config  - Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 */
	public function getTable($type = 'Bookings', $prefix = 'MatukioTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}
}
