<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Class MatukioControllerCreateEvent
 *
 * @since  2.0
 */
class MatukioControllerCreateEvent extends JControllerLegacy
{
	/**
	 * Gets the view
	 *
	 * @param   bool  $cachable   - Is the view cachable
	 * @param   bool  $urlparams  - The params
	 *
	 * @return JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'createevent');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Createevent', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Unpublishs the event
	 *
	 * @return object
	 */
	public function unpublishEvent()
	{
		$msg = "COM_MATUKIO_EVENT_UNPUBLISH_SUCCESS";

		$database = JFactory::getDBO();
		$my = JFactory::getuser();
		$cid = JFactory::getApplication()->input->getInt('cid', 0);

		if (!JFactory::getUser()->authorise('core.edit', 'com_matukio', $cid))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$vorlage = JFactory::getApplication()->input->getInt('vorlage', 0);
		$database->setQuery("SELECT * FROM #__matukio WHERE id='$cid'");
		$rows = $database->loadObjectList();
		$aktsem = & $rows[0];
		$neudatum = MatukioHelperUtilsDate::getCurrentDate();

		if ($neudatum < $aktsem->begin && $vorlage == 0 && MatukioHelperSettings::_("notify_participants_publish", 1))
		{
			foreach ($rows as $row)
			{
				$events = MatukioHelperUtilsEvents::getEventsRecurringOnEventId($row->semid);

				foreach ($events as $e)
				{
					$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid = " . $database->quote($e->id));
					$bookings = $database->loadObjectList();

					for ($i = 0, $n = count($bookings); $i < $n; $i++)
					{
						MatukioHelperUtilsEvents::sendBookingConfirmationMail($e, $bookings[$i]->id, 4);
					}
				}
			}
		}

		$database->setQuery("UPDATE #__matukio SET published=0 WHERE id='" . $cid . "'");

		// TODO Update recurring
		if (!$database->execute())
		{
			JError::raiseError(500, $database->getError());
			$msg = "COM_MATUKIO_EVENT_UNPUBLISH_FAILURE_" . $database->getError();
			exit();
		}

		$link = JRoute::_("index.php?com_matukio&view=eventlist&art=2");

		$this->setRedirect($link, $msg);
	}

	/**
	 * Duplicate an event
	 *
	 * @return  object
	 */
	public function duplicateEvent()
	{
		$msg = JText::_("COM_MATUKIO_EVENT_DUPLICATE_SUCCESS");

		$database = JFactory::getDBO();
		$cid = JFactory::getApplication()->input->getInt('cid', 0);

		// Check authorization
		if (!JFactory::getUser()->authorise('core.edit', 'com_matukio', $cid))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$database->setQuery("SELECT * FROM #__matukio WHERE id='$cid'");
		$item = $database->loadObject();

		if ($database->getErrorNum())
		{
			JError::raiseError(500, $database->getError());
			$msg = "COM_MATUKIO_EVENT_DUPLICATE_FAILURE_" . $database->getError();
		}

		$item->id = null;

		$row = JTable::getInstance('matukio', 'Table');

		if (!$row->bind($item))
		{
			JError::raiseError(500, $row->getError());
			$msg = "COM_MATUKIO_EVENT_DUPLICATE_FAILURE_" . $row->getError();
		}

		$row->id = null;
		$row->hits = 0;
		$row->grade = 0;
		$row->certificated = 0;
		$row->sid = $item->id;
		$row->publishdate = MatukioHelperUtilsDate::getCurrentDate();
		$row->semnum = MatukioHelperUtilsEvents::createNewEventNumber(date('Y'));

		if (!$row->check())
		{
			JError::raiseError(500, $row->getError());
			$msg = "COM_MATUKIO_EVENT_DUPLICATE_FAILURE_" . $row->getError();
		}

		if (!$row->store())
		{
			JError::raiseError(500, $row->getError());
			$msg = "COM_MATUKIO_EVENT_DUPLICATE_FAILURE_" . $row->getError();
		}

		$row->checkin();

		// Add recurring date
		MatukioHelperRecurring::saveRecurringDateForEvent($row);

		$link = JRoute::_("index.php?option=com_matukio&view=eventlist&art=2");

		$this->setRedirect($link, $msg);
	}

	/**
	 * Saves the vent
	 *
	 * @return bool|object
	 */
	public function saveEvent()
	{
		// Check authorization
		if (!JFactory::getUser()->authorise('core.edit', 'com_matukio'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$msg = JText::_('COM_MATUKIO_EVENT_SAVE');

		$res = MatukioHelperUtilsEvents::saveEvent(true);
		$fehler = $res->error;

		$link = JRoute::_("index.php?option=com_matukio&art=2");
		$link2 = JRoute::_("index.php?option=com_matukio&view=createevent&cid=" . $res->id);

		if (!empty($fehler))
		{
			$tempmsg = implode(",", $fehler);

			// Hack for dirks empty array
			$tempmsg = str_replace(",", "", $tempmsg);

			if (!empty($tempmsg))
			{
				$msg = implode(",", $fehler);
			}
		}

		// Ausgabe der Kurse
		$fehlerzahl = array_unique($fehler);

		if (MatukioHelperUtilsEvents::checkRequiredFieldValues($res->event->pattern, 'leer'))
		{
			$this->setRedirect($link2, $msg);
		}
		elseif (count($fehlerzahl) > 1 AND $res->saved == true)
		{
			$link = JRoute::_("index.php?option=com_matukio&view=eventlist&art=2", $msg);
		}
		else
		{
			$link = JRoute::_("index.php?option=com_matukio&view=eventlist&art=2", $msg);
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Uncancels the event
	 *
	 * @return  void
	 */
	public function uncancel()
	{
		$this->cancel("uncancelEvent");
	}


	/**
	 * Cancels / uncancels the event
	 *
	 * @param   string $task
	 *
	 * @return  object
	 * @throws  Exception
	 */
	public function cancel($task = 'cancelEvent')
	{
		// Check authorization
		if (!JFactory::getUser()->authorise('core.edit', 'com_matukio'))
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		if ($task == 'cancelEvent')
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
		$link = JRoute::_('index.php?option=com_matukio&view=eventlist&art=2');

		$this->setRedirect($link);
	}
}
