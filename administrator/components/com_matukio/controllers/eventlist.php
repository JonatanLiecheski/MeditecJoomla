<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       24.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');

/**
 * Class MatukioControllerEventlist
 *
 * @since  3.0
 */
class MatukioControllerEventlist extends JControllerAdmin
{
	/**
	 * Constructor to register extra tasks
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('uncancelEvent', 'cancelEvent');
		$this->registerTask('apply', 'save');
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Eventlist', $prefix = 'MatukioModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Toogles publish for the given event ids
	 *
	 * @throws  Exception - If db queries fail
	 * @return  void
	 */
	public function publish()
	{
		$db = JFactory::getDbo();

		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		JPluginHelper::importPlugin('content');
		$dispatcher = JDispatcher::getInstance();

		if ($this->task == 'publish')
		{
			$publish = 1;
		}
		else
		{
			$publish = 0;
		}

		$cids = implode(',', $ids);

		if (count($ids) && MatukioHelperSettings::_("notify_participants_publish", 1))
		{
			foreach ($ids as $id)
			{
				$events = MatukioHelperUtilsEvents::getEventsRecurringOnEventId($id);

				foreach ($events as $e)
				{
					$db->setQuery("SELECT * FROM #__matukio_bookings WHERE semid = " . $db->quote($e->id) . "");
					$rows = $db->loadObjectList();

					if ($db->getErrorNum())
					{
						throw new Exception($db->getErrorMsg(), 500);
					}

					foreach ($rows as $row)
					{
						if ($publish == 0)
						{
							MatukioHelperUtilsEvents::sendBookingConfirmationMail($e, $row->id, 4);
						}
						else
						{
							MatukioHelperUtilsEvents::sendBookingConfirmationMail($e, $row->id, 5);
						}
					}
				}
			}
		}

		// Update recurring events
		$db->setQuery("UPDATE #__matukio_recurring SET published = " . $db->quote($publish) . " WHERE event_id IN (" . $cids . ")");
		$db->execute();

		$msg = "";
		$table = JTable::getInstance('Matukio', 'Table');
		$suc = $table->publish($ids, $publish);

		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger('onEventStateChange', array('com_matukio.event', $ids, $publish));

		$link = 'index.php?option=com_matukio&view=eventlist';
		$this->setRedirect($link, $msg);
	}

	/**
	 * Toogles cancel for the given event ids
	 *
	 * @throws  Exception - If db queries fail
	 * @return  void
	 */
	public function cancelEvent()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		if ($this->task == 'cancelEvent')
		{
			$cancelled = 1;
		}
		else
		{
			$cancelled = 0;
		}

		if (count($ids))
		{
			// First update event
			$db = JFactory::getDbo();
			$cids = implode(',', $ids);

			$db->setQuery("UPDATE #__matukio SET cancelled = '" . $cancelled . "' WHERE id IN (" . $cids . ") ");

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 500);
			}

			// Update recurring events
			$db->setQuery("UPDATE #__matukio_recurring SET cancelled = " . $db->quote($cancelled) . " WHERE event_id IN (" . $cids . ")");

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 500);
			}

			if (MatukioHelperSettings::_("booking_stornoconfirmation", 1))
			{
				foreach ($ids as $id)
				{
					$events = MatukioHelperUtilsEvents::getEventsRecurringOnEventId($id);

					foreach ($events as $e)
					{
						// Notify participants over the change
						$db->setQuery("SELECT * FROM #__matukio_bookings WHERE semid = " . $e->id . "");
						$rows = $db->loadObjectList();

						if ($db->getErrorNum())
						{
							throw new Exception($db->getErrorMsg(), 42);
						}

						foreach ($rows as $row)
						{
							if ($cancelled == 0)
							{
								MatukioHelperUtilsEvents::sendBookingConfirmationMail($e, $row->id, 9);
							}
							else
							{
								MatukioHelperUtilsEvents::sendBookingConfirmationMail($e, $row->id, 10);
							}
						}

						// Delete old bookings
						// Maybe $db->setQuery("UPDATE #__matukio_bookings SET status = 4 WHERE semid = " . $e->id . "");
					}
				}
			}
		}

		$msg = "";
		$link = 'index.php?option=com_matukio&view=eventlist';

		$this->setRedirect($link, $msg);
	}

	/**
	 * Duplicates an or multiple events
	 *
	 * @throws  Exception - If db queries fail
	 * @return  void
	 */
	public function duplicate()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (count($ids))
		{
			$db = JFactory::getDbo();
			$cids = implode(',', $ids);

			$db->setQuery("SELECT * FROM #__matukio WHERE id IN (" . $cids . ")");
			$rows = $db->loadObjectList();

			if ($db->getErrorNum())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			foreach ($rows as $item)
			{
				$row = JTable::getInstance('Matukio', 'Table');

				if (!$row->bind($item))
				{
					throw new Exception($db->getErrorMsg(), 42);
				}

				// Reset values
				$row->id = null;
				$row->hits = 0;
				$row->grade = 0;
				$row->certificated = 0;
				$row->sid = $item->id;

				$unique = MatukioHelperUtilsEvents::createNewEventNumber(date('Y'));

				$row->semnum = $unique;

				if (!$row->check())
				{
					throw new Exception($db->getErrorMsg(), 42);
				}

				if (!$row->store())
				{
					throw new Exception($db->getErrorMsg(), 42);
				}

				$row->checkin();

				// Add recurring date
				MatukioHelperRecurring::saveRecurringDateForEvent($row);
			}
		}

		$msg = JText::_("COM_MATUKIO_DUPLICATE_SUCCESS");
		$link = 'index.php?option=com_matukio&view=eventlist';

		$this->setRedirect($link, $msg);
	}

	/**
	 * Removes an or multiple events
	 *
	 * @throws  Exception - If db queries fail
	 * @return void
	 */

	public function remove()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		JPluginHelper::importPlugin('content');
		$dispatcher = JDispatcher::getInstance();

		$db = JFactory::getDBO();

		if (count($cid))
		{
			// Notify users
			if (MatukioHelperSettings::_("notify_participants_delete", 1))
			{
				foreach ($cid as $id)
				{
					$events = MatukioHelperUtilsEvents::getEventsRecurringOnEventId($id);

					foreach ($events as $e)
					{
						$db->setQuery("SELECT * FROM #__matukio_bookings WHERE semid = " . $db->quote($e->id) . " AND status = 1");
						$bookings = $db->loadObjectList();

						if ($db->getErrorNum())
						{
							throw new Exception($db->getErrorMsg(), 500);
						}

						foreach ($bookings AS $b)
						{
							MatukioHelperUtilsEvents::sendBookingConfirmationMail($e, $b->id, 4);
						}

						// Delete old bookings
						$db->setQuery("UPDATE #__matukio_bookings SET status = 4 WHERE semid = " . $db->quote($e->id));

						if (!$db->execute())
						{
							throw new Exception($db->getErrorMsg(), 42);
						}
					}
				}
			}

			foreach ($cid as $c)
			{
				// Delete recurring events
				MatukioHelperRecurring::deleteRecurringEvents($c);
			}

			// Delete events
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__matukio where id IN (" . $cids . ")";
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 500);
			}
		}

		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger('onEventAfterDelete', array('com_matukio.event', $cid));

		$msg = JText::_("COM_MATUKIO_DELETE_SUCCESS");
		$this->setRedirect('index.php?option=com_matukio&view=eventlist', $msg);
	}


	/**
	 * Redirects to the participants page
	 *
	 * @return  void
	 */
	public function participants()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		$this->setRedirect('index.php?option=com_matukio&view=bookings&event_id=' . $cid[0]);
	}

	/**
	 * Edit event form
	 *
	 * @return  void
	 */
	public function editEvent()
	{
		$document = JFactory::getDocument();
		$viewName = 'event';
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);

		$model = $this->getModel('event');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Saves the event
	 *
	 * @throws  exception - if query fails
	 * @return  void
	 */
	public function save()
	{
		$res = MatukioHelperUtilsEvents::saveEvent();
		$id = $res->id;

		// Ausgabe der Kurse
		switch ($this->task)
		{
			case 'apply':
				$msg = JText::_('COM_MATUKIO_EVENT_APPLY');
				$link = 'index.php?option=com_matukio&controller=eventlist&task=editEvent&id=' . $id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_EVENT_SAVE');
				$link = 'index.php?option=com_matukio&view=eventlist';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Cancels event view
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=eventlist';
		$this->setRedirect($link);
	}
}
