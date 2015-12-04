<?php
/**
 * @author Daniel Dimitrov
 * @date: 29.03.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.model');

/**
 * Class MatukioModelEvent
 *
 * @since  1.0.0
 */
class MatukioModelEvent extends JModelLegacy
{
	/**
	 * Gets the event
	 *
	 * @param   int      $id        - The recurring date id
	 * @oaram   boolean  $category  - Left join #__categories
	 *
	 * @return  mixed
	 */
	public function getItem($id, $category = false)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		if ($category)
		{
			$query->select("cc.title as category, e.*, r.*, e.id as eventid")
				->from("#__matukio_recurring AS r")
				->leftJoin("#__matukio AS e ON e.id = r.event_id")
				->leftJoin("#__categories AS cc ON e.catid = cc.id")
				->where("r.id = " . $db->quote($id));
		}
		else
		{
			$query->select("e.*, r.*, e.id as eventid")
				->from("#__matukio_recurring AS r")
				->leftJoin("#__matukio AS e ON e.id = r.event_id")
				->where("r.id = " . $db->quote($id));
		}

		$db->setQuery($query, 0, 1);

		return $db->loadObject();
	}
}
