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
 * Class MatukioViewUpcomingevents
 *
 * @since  2.0.0
 */
class MatukioViewUpcomingevents extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param null $tpl
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		$catid = JFactory::getApplication()->input->get('catid', array(), '', 'array');
		$user = JFactory::getUser();
		$ue_title = "COM_MATUKIO_UPCOMING_EVENTS";
		$dispatcher = JDispatcher::getInstance();

		$params = JComponentHelper::getParams('com_matukio');

		$menuitemid = JFactory::getApplication()->input->get('Itemid');

		if ($menuitemid)
		{
			$site = new JSite;
			$menu = $site->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			$params->merge($menuparams);
		}

		if (empty($catid))
		{
			$catid = $params->get('catid', 0);
		}

		$ue_title = $params->get('title', 'COM_MATUKIO_UPCOMING_EVENTS');
		$number = $params->get('number', 10);
		$orderby = $params->get('orderby', 'begin ASC');

		$model = $this->getModel();
		$events = $model->getEvents($catid, $number, $orderby);

		MatukioHelperUtilsBasic::expandPathway(JTEXT::_('COM_MATUKIO_UPCOMING_EVENTS'), "");

		JPluginHelper::importPlugin('content');

		foreach ($events as $key => $event)
		{
			$events[$key]->jevent = new stdClass;
			$results = $dispatcher->trigger('onContentAfterButton', array('com_matukio.upcomingevent', &$event, &$params, 0));
			$events[$key]->jevent->afterDisplayButton = trim(implode("\n", $results));

			$results = $dispatcher->trigger('onContentAfterDisplay', array('com_matukio.upcomingevent', &$event, &$params, 0));
			$events[$key]->jevent->afterDisplayContent = trim(implode("\n", $results));
		}

		$this->catid = $catid;
		$this->events = $events;
		$this->user = $user;
		$this->title = $ue_title;

		parent::display($tpl);
	}
}