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
 * Class MatukioControllerImport
 *
 * @since  2.2.0
 */
class MatukioControllerImport extends JControllerLegacy
{
	/**
	 * Construct
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
	}

	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'Import');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Import', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * $cattable->id = null;
	 * $cattable->asset_id = 0;
	 * $cattable->parent_id = 0;
	 * $cattable->lft = 0;
	 * $cattable->rgt = 0;
	 * $cattable->level = 1;
	 * $cattable->path = "test";
	 * $cattable->extension = "com_matukio";
	 * $cattable->title = "";
	 * $cattable->alias = "";
	 * $cattable->note = "";
	 * $cattable->description = "";
	 * $cattable->published = "";
	 * $cattable->checked_out = 0;
	 * $cattable->checked_out_time = "0000-00-00 00:00:00";
	 * $cattable->access = "";
	 * $cattable->params = "";
	 *
	 * $cattable->metadesc = "";
	 * $cattable->metakey = "";
	 * $cattable->metadata = "";
	 * $cattable->created_user_id = "";
	 * $cattable->created_time = "";
	 * $cattable->modified_user_id = "";
	 * $cattable->hits = "";
	 * $cattable->language = "";
	 */

	public function importseminar()
	{
		$input = JFactory::getApplication()->input;
		$db = JFactory::getDbo();

		$seminar_table = $input->get('seminar_table', '');
		$seminar_category_table = $input->get('seminar_category_table', '');
		$seminar_booking_table = $input->get('seminar_booking_table', '');
		$seminar_number_table = $input->get('seminar_number_table', '');

		// Load old categories
		$query = $db->getQuery(true);
		$query->select("*")->from($seminar_category_table)->where("section = " . $db->quote("com_seminar"));
		$db->setQuery($query);

		$cats = $db->loadObjectList();

		$insert_id = null;
		$relationsDb = array();
		$user = JFactory::getUser();
		$i = 0;
		$table = JTable::getInstance('Category', 'JTable');
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');

		foreach ($cats as $cat)
		{
			// Import category into Joomla 2.5 #__categories table

			$old_id = $cat->id;

			$cat->name = html_entity_decode($cat->title);
			$cat->path = $cat->name;
			$cat->alias = $cat->alias;
			$cat->parent = 1;
			$cat->author = $user->id;

			$new_id = $this->insertCategory($cat);

			if ($new_id == -1) break;

			$dispatcher->trigger('onContentAfterSave', array('com_content.category.' . $insert_id, &$table, true));

			$relationsDb[] = $db->quote($new_id) . ',' . $i . ',' . $old_id;

			// Get the events for the category
			$query = $db->getQuery(true);
			$query->select("*")->from($seminar_table)->where("catid = " . $old_id);
			$db->setQuery($query);

			$events = $db->loadObjectList();

			foreach ($events as $event)
			{
				$mattab = JTable::getInstance('Matukio', 'Table');

				$old_event_id = $event->id;

				// Reset event id
				$event->id = null;
				$event->created_by = $user->id;
				$event->catid = $new_id;

				if (!$mattab->bind($event))
				{
					JError::raiseError(500, $mattab->getError());
				}

				// Zuweisung der Startzeit
				$mattab->begin = JFactory::getDate($event->begin, MatukioHelperUtilsBasic::getTimeZone())->format('Y-m-d H:i:s', false, false);

				// Zuweisung der Endzeit
				$mattab->end = JFactory::getDate($event->end, MatukioHelperUtilsBasic::getTimeZone())->format('Y-m-d H:i:s', false, false);

				// Zuweisung der Buchungszeit
				$mattab->booked = JFactory::getDate($event->booked, MatukioHelperUtilsBasic::getTimeZone())->format('Y-m-d H:i:s', false, false);

				if (!$mattab->check())
				{
					JError::raiseError(500, $db->stderr());
				}

				if (!$mattab->store())
				{
					JError::raiseError(500, $db->stderr());
				}

				$mattab->checkin();

				// Add recurring event date
				$rid = MatukioHelperRecurring::saveRecurringDateForEvent($mattab);

				// Get the event bookings for this event
				$query = $db->getQuery(true);
				$query->select("*")->from($seminar_booking_table)->where("semid = " . $old_event_id);
				$db->setQuery($query);

				$bookings = $db->loadObjectList();

				foreach ($bookings as $booking)
				{
					// Reset
					$booking->id = null;

					// Update 3.1 - we need the recurring id not the event id
					$booking->semid = $rid;

					$booking->uuid = MatukioHelperPayment::getUuid(true);

					// Calculating payment
					$booking->payment_brutto = $mattab->fees * $booking->nrbooked;

					// No taxes here -> user is goign to add them later
					$booking->payment_netto = $booking->payment_brutto;
					$booking->payment_tax = 0.00;

					$booktable = JTable::getInstance('Bookings', 'Table');

					if (!$booktable->bind($booking))
					{
						JError::raiseError(500, $booktable->getError());
					}

					if (!$booktable->check())
					{
						JError::raiseError(500, $db->stderr());
					}

					if (!$booktable->store())
					{
						JError::raiseError(500, $db->stderr());
					}
				}
			}

			$i++;
		}

		// Import Numbers
		$query = $db->getQuery(true);
		$query->select("*")->from($seminar_number_table);
		$db->setQuery($query);

		$numbers = $db->loadObjectList();

		foreach ($numbers as $number)
		{
			$numtable = JTable::getInstance("Number", "Table");

			if (!$numtable->bind($number))
			{
				JError::raiseError(500, $numtable->getError());
			}

			if (!$numtable->check())
			{
				JError::raiseError(500, $db->stderr());
			}

			if (!$numtable->store())
			{
				JError::raiseError(500, $db->stderr());
			}
		}

		$msg = JText::_("COM_MATUKIO_IMPORT_SUCCESSFULLY");
		$link = 'index.php?option=com_matukio&view=import';
		$this->setRedirect($link, $msg);

	}

	/**
	 * Inserts a category
	 *
	 * @param   object  $category  - The cat object
	 *
	 * @return  int
	 */
	public function insertCategory($category)
	{
		$ret = -1;
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);
		$query->insert('#__categories');
		$query->set('`title`=' . $db->quote($category->name));
		$query->set('`alias`=' . $db->quote($category->alias));
		$query->set('`parent_id`=' . $db->quote($category->parent));
		$query->set('`extension`= "com_matukio"');
		$query->set('`published`= 1');
		$query->set('`created_user_id`=' . $db->quote($category->author));
		$query->set('`access`=1');
		$query->set('`language`="*"');
		$db->setQuery($query);

		if ($db->execute())
		{
			$ret = $db->insertId();
		}
		else
		{
			$ret = -1;
			echo('Category with an title ' . $db->quote($category->name) . 'could not be added into your database!\n' . $db->getErrorMsg());
		}

		return $ret;
	}

	/**
	 * Imports events from a ics file
	 *
	 * @throws  Exception
	 *
	 * @return  void
	 */
	public function importics()
	{
		$input = JFactory::getApplication()->input;

		// Let's start uploading the file
		$file = JRequest::getVar('ics_file', null, 'files', 'array');
		$catid = $input->getInt("caid", 0);

		if (empty($file))
		{
			throw new Exception("COM_MATUKIO_NO_FILE");
		}

		if (empty($catid))
		{
			throw new Exception("COM_MATUKIO_PLEASE_SELECT_A_CATEGORY");
		}

		jimport('joomla.filesystem.file');

		$count = 0;

		if (!strtolower(JFile::getExt($file['name'])) == 'ics')
		{
			throw new Exception("COM_MATUKIO_NO_ICS_FILE");
		}

		$ar = $this->icsToArray($file['tmp_name']);
		var_dump($ar);

		$user_id = JFactory::getUser()->id;

		// Go through the events saved in the file
		foreach ($ar as $e)
		{
			if ($e['BEGIN'] == 'VEVENT')
			{
				$new = MatukioHelperUtilsEvents::getEventEditTemplate();

				// Our real events
				$new->publisher = $user_id;
				$new->title = $e['SUMMARY'];
				$new->shortdesc = $e['SUMMARY'];
				$new->place = $e['LOCATION'];
				$new->description = $e['DESCRIPTION'];
				$new->begin = date("Y-m-d H:i:s", strtotime($e['DTSTART']));
				$new->end = date("Y-m-d H:i:s", strtotime($e['DTEND']));
				$new->booked = $new->begin;
				$new->catid = $catid;

				$new->updated = date("Y-m-d H:i:s");
				$new->publishdate = date("Y-m-d H:i:s");

				// Save new event
				$tab = JTable::getInstance('Matukio', 'Table');

				if (!$tab->bind($new))
				{
					throw new Exception($tab->getError(), 42);
				}

				if (!$tab->check())
				{
					throw new Exception($tab->getError(), 42);
				}

				if (!$tab->store())
				{
					throw new Exception($tab->getError(), 42);
				}

				$tab->checkin();

				// Save date in rec dates
				MatukioHelperRecurring::saveRecurringDateForEvent($tab);
				$count++;
			}
		}

		$msg = JText::_("COM_MATUKIO_ICS_IMPORT_SUCCESSFULLY") . " " . $count;
		$link = 'index.php?option=com_matukio&view=import';
		$this->setRedirect($link, $msg);
	}

	/**
	 * Converts an ics file to array
	 *
	 * @param   string  $paramUrl  - The saved file on the server
	 *
	 * @return mixed
	 */
	private function icsToArray($paramUrl)
	{
		$icsFile = file_get_contents($paramUrl);

		$icsData = explode("BEGIN:", $icsFile);

		foreach ($icsData as $key => $value)
		{
			$icsDatesMeta[$key] = explode("\n", $value);
		}

		foreach ($icsDatesMeta as $key => $value)
		{
			foreach ($value as $subKey => $subValue)
			{
				if ($subValue != "")
				{
					if ($key != 0 && $subKey == 0)
					{
						$icsDates[$key]["BEGIN"] = $subValue;
					}
					else
					{
						$subValueArr = explode(":", $subValue, 2);
						$icsDates[$key][$subValueArr[0]] = $subValueArr[1];
					}
				}
			}
		}

		return $icsDates;
	}

	/**
	 * Cancels the import
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio';
		$this->setRedirect($link);
	}
}
