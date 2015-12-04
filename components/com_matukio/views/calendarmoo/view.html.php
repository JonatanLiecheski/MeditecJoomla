<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       30.03.14
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');

/**
 * Class MatukioViewCalendarMoo
 *
 * @since  3.1.0
 */
class MatukioViewCalendarMoo extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  - The tmpl
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$catid = JFactory::getApplication()->input->getInt('catid', 0);
		$user = JFactory::getUser();

		$params = JComponentHelper::getParams('com_matukio');

		$menuitemid = JFactory::getApplication()->input->get('Itemid');

		if ($menuitemid)
		{
			$site = new JSite;
			$menu = $site->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			$params->merge($menuparams);
		}

		// Todo integrate category support - in requests tmpl
		if (empty($catid))
		{
			$catid = $params->get('catid', 0);
		}

		$ue_title = $params->get('title', 'COM_MATUKIO_CALENDAR_TITLE');

		$this->catid = $catid;
		$this->user = $user;
		$this->params = $params;
		$this->title = $ue_title;

		parent::display($tpl);
	}
}
