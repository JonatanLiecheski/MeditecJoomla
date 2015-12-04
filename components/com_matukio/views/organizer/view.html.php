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
 * Class MatukioViewOrganizer
 *
 * @since  2.2.0
 */
class MatukioViewOrganizer extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  - The templates
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$orga_id = JFactory::getApplication()->input->get('id', 0);

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

		if (empty($orga_id))
		{
			$orga_id = $params->get('organizerId', 0);
		}

		// Raise error
		if (empty($orga_id))
		{
			JError::raise(E_ERROR, 403, JText::_("COM_MATUKIO_NO_ID"));
		}

		$organizer = $model->getOrganizer($orga_id);

		if (!empty($organizer))
		{
			// Get the Joomla user obj
			$organizer_user = JFactory::getUser($organizer->userId);
		}
		else
		{
			$organizer_user = null;
		}

		$ue_title = $params->get('title', '');


		if (empty($ue_title))
		{
			// Set the title to the name
			$ue_title = $organizer->name;
		}

		$title = JFactory::getDocument()->getTitle();
		JFactory::getDocument()->setTitle($title . " - " . JText::_($organizer->name));

		// Plugin support
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('content');
		$this->jevent = new stdClass;
		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_matukio.organizer', &$organizer, &$params, 0));
		$this->jevent->afterDisplayContent = trim(implode("\n", $results));

		$this->organizer = $organizer;
		$this->organizer_user = $organizer_user;
		$this->title = $ue_title;

		$this->user = JFactory::getUser();

		// Upcoming events since 3.1
		if (MatukioHelperSettings::getSettings("organizer_show_upcoming", 1) && !empty($organizer_user))
		{
			$this->upcoming_events = $model->getUpcomingEvents($organizer_user->id);
		}

		parent::display($tpl);
	}
}
