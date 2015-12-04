<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');

/**
 * Class MatukioViewICS
 *
 * @since  1.0.0
 */
class MatukioViewICS extends JViewLegacy
{
	/**
	 * Displays the raw calendar file
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		if (MatukioHelperSettings::getSettings('frontend_usericsdownload', 1) == 0)
		{
			JError::raiseError(403, JText::_("ALERTNOTAUTH"));

			return;
		}

		$cid = JFactory::getApplication()->input->getInt('cid', 0);
		$events = array();

		if (!empty($cid))
		{
			// Just one event
			$model = JModelLegacy::getInstance('Event', 'MatukioModel');
			$event = $model->getItem($cid);
			$events[] = $event;
		}
		else
		{
			$database = JFactory::getDBO();
			$neudatum = MatukioHelperUtilsDate::getCurrentDate();

			// ICS File with all Events
			$where = array();
			$database->setQuery("SELECT id, access FROM #__categories WHERE extension = '" . JFactory::getApplication()->input->get('option') . "'");
			$cats = $database->loadObjectList();
			$allowedcat = array();

			foreach ($cats AS $cat)
			{
				if ($cat->access < 1)
				{
					$allowedcat[] = $cat->id;
				}
			}

			if (count($allowedcat) > 0)
			{
				$allowedcat = implode(',', $allowedcat);
				$where[] = "a.catid IN (" . $allowedcat . ")";
			}

			$where[] = "a.published = '1'";
			$where[] = "a.end > '$neudatum'";
			$where[] = "a.booked > '$neudatum'";

			$database->setQuery("SELECT a.*, r.*, cat.title AS category FROM #__matukio_recurring AS r
			 LEFT JOIN #__matukio AS a ON r.event_id = a.id
			 LEFT JOIN #__categories AS cat ON cat.id = a.catid"
			. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
			. "\nORDER BY r.begin DESC"
			. "\nLIMIT 0, 1000"
			);

			$events = $database->loadObjectList();
		}

		$this->events = $events;

		parent::display($tpl);
	}
}
