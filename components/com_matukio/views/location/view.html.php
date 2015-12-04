<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       12.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');

/**
 * Class MatukioViewLocation
 *
 * @since  3.0.0
 */
class MatukioViewLocation extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  - The templates
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		$loc_id = JFactory::getApplication()->input->get('id', 0);

		$model = $this->getModel();

		$params = JComponentHelper::getParams('com_matukio');
		$menuitemid = JFactory::getApplication()->input->get('Itemid');

		if ($menuitemid)
		{
			$site = new JSite;
			$menu = $site->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			$params->merge($menuparams);
		}

		if (empty($loc_id))
		{
			$loc_id = $params->get('locationId', 0);
		}

		// Raise error
		if (empty($loc_id))
		{
			JError::raise(E_ERROR, 403, JText::_("COM_MATUKIO_NO_ID"));
		}

		$loc = $model->getLocation($loc_id);

		$ue_title = $params->get('title', '');

		if (empty($ue_title))
		{
			// Set the title to the name
			$ue_title = $loc->title;
		}

		$title = JFactory::getDocument()->getTitle();
		JFactory::getDocument()->setTitle($title . " - " . JText::_($loc->title));

		// Plugin support
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$this->jevent = new stdClass;
		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_matukio.location', &$loc, &$params, 0));
		$this->jevent->afterDisplayContent = trim(implode("\n", $results));

		$this->location = $loc;
		$this->title = $ue_title;
		$this->user = JFactory::getUser();

		// Upcoming events since 3.1
		if (MatukioHelperSettings::getSettings("locations_show_upcoming", 1))
		{
			$this->upcoming_events = $model->getUpcomingEvents($loc_id);
		}

		parent::display($tpl);
	}
}
