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
 * Class MatukioControllerRecurring
 *
 * @since  3.1
 */
class MatukioControllerRecurring extends JControllerAdmin
{
	/**
	 * Constructor to register extra tasks
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('uncancelRecurring', 'cancelRecurring');
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
	public function getModel($name = 'Recurring', $prefix = 'MatukioModel', $config = array('ignore_request' => true))
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
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		if ($this->task == 'publish')
		{
			$publish = 1;
		}
		else
		{
			$publish = 0;
		}

		if (count($ids) && MatukioHelperSettings::_("notify_participants_publish", 1))
		{
			$db = JFactory::getDbo();
			$cids = implode(',', $ids);

			$db->setQuery("SELECT * FROM #__matukio_bookings WHERE id IN (" . $cids . ")");
			$rows = $db->loadObjectList();

			if ($db->getErrorNum())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			foreach ($rows AS $row)
			{
				$event = MatukioHelperUtilsEvents::getEventRecurring($row->semid);

				if ($publish == 0)
				{
					MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $row->id, 4);
				}
				else
				{
					MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $row->id, 5);
				}
			}
		}

		$msg = "";
		$table = JTable::getInstance('Recurring', 'MatukioTable');
		$suc = $table->publish($ids, $publish);

		$link = 'index.php?option=com_matukio&view=recurring';
		$this->setRedirect($link, $msg);
	}

	/**
	 * Toogles cancel for the given event ids
	 *
	 * @throws  Exception - If db queries fail
	 * @return  void
	 */
	public function cancelRecurring()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		if ($this->task == 'cancelRecurring')
		{
			$cancelled = 1;
		}
		else
		{
			$cancelled = 0;
		}

		MatukioHelperRecurring::cancelRecurringEvents($ids, $cancelled);

		$link = 'index.php?option=com_matukio&view=recurring';

		$this->setRedirect($link);
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

			$db->setQuery("SELECT * FROM #__matukio_recurring WHERE id IN (" . $cids . ")");
			$rows = $db->loadObjectList();

			if ($db->getErrorNum())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			foreach ($rows as $item)
			{
				$row = JTable::getInstance('Recurring', 'MatukioTable');

				if (!$row->bind($item))
				{
					throw new Exception($db->getErrorMsg(), 42);
				}

				// Reset values
				$row->id = null;
				$row->hits = 0;
				$row->grade = 0;

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
			}
		}

		$msg = JText::_("COM_MATUKIO_DUPLICATE_SUCCESS");
		$link = 'index.php?option=com_matukio&view=recurring';

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

		$db = JFactory::getDBO();

		if (count($cid))
		{
			$cids = implode(',', $cid);

			// Notify users
			if (MatukioHelperSettings::_("notify_participants_delete", 1))
			{
				$db->setQuery("SELECT * FROM #__matukio_bookings WHERE id IN (" . $cids . ")");
				$bookings = $db->loadObjectList();

				if ($db->getErrorNum())
				{
					throw new Exception($db->getErrorMsg(), 42);
				}

				foreach ($bookings AS $b)
				{
					$event = MatukioHelperUtilsEvents::getEventRecurring($b->semid);

					MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $b->id, 4);
				}
			}

			// Delete events
			$query = "DELETE FROM #__matukio_recurring where id IN (" . $cids . ")";
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			// Delete old bookings
			$db->setQuery("DELETE FROM #__matukio_bookings WHERE semid IN (" . $cids . ")");

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}
		}

		$msg = JText::_("COM_MATUKIO_DELETE_SUCCESS");
		$this->setRedirect('index.php?option=com_matukio&view=recurring', $msg);
	}


	/**
	 * Redirects to the participants page
	 *
	 * @return  void
	 */
	public function participants()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		// We can only take one
		$this->setRedirect('index.php?option=com_matukio&view=bookings&id=' . $cid[0]);
	}

	/**
	 * Edit event form
	 *
	 * @return  void
	 */
	public function edit()
	{
		$document = JFactory::getDocument();
		$viewName = 'editrecurring';
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);

		$model = $this->getModel('editrecurring');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Saves the recurring date
	 *
	 * @throws  exception - if query fails
	 * @return  void
	 */
	public function save()
	{
		$id = MatukioHelperRecurring::saveRecurring();

		switch ($this->task)
		{
			case 'apply':
				$msg = JText::_('COM_MATUKIO_RECURRING_APPLY');
				$link = 'index.php?option=com_matukio&controller=recurring&task=edit&id=' . $id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_RECURRING_SAVE');
				$link = 'index.php?option=com_matukio&view=recurring';
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
		$link = 'index.php?option=com_matukio&view=recurring';
		$this->setRedirect($link);
	}
}
