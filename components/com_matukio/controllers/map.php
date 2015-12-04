<?php
/**
 * @author Daniel Dimitrov
 * @date: 29.03.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Class MatukioControllerMap
 *
 * @since  1.0.0
 */
class MatukioControllerMap extends JControllerLegacy
{
	/**
	 * Displays the map
	 *
	 * @param   bool  $cachable   - Cachable
	 * @param   bool  $urlparams  - Params
	 *
	 * @return JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$model = $this->getModel('Event', 'MatukioModel');
		$eventId = JFactory::getApplication()->input->getInt('event_id', 0);
		$locationId = JFactory::getApplication()->input->getInt('location_id', 0);

		$view = $this->getView('Map', 'html', 'MatukioView');

		if (!empty($eventId))
		{
			$event = $model->getItem($eventId);
			$view->event = $event;
		}
		else
		{
			$view->event = null;
		}

		if (!empty($locationId))
		{
			$view->location = MatukioHelperUtilsEvents::getLocation($locationId);
		}
		else
		{
			$view->location = null;
		}

		$view->display();
	}
}
