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
 * Class MatukioControllerEventlist
 *
 * @since  1.0.0
 */
class MatukioControllerEventlist extends JControllerLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   bool  $cachable   - Is it cachable
	 * @param   bool  $urlparams  - The url params
	 *
	 * @return JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		MatukioHelperUtilsBasic::loginUser();

		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'eventlist');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Eventlist', 'MatukioModel');
		$view->setModel($model, true);

		$tmpl = MatukioHelperSettings::getSettings("event_template", "default");

		$params = JComponentHelper::getParams('com_matukio');
		$menuitemid = JFactory::getApplication()->input->getInt('Itemid');

		if ($menuitemid)
		{
			$site = new JSite;
			$menu = $site->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			$params->merge($menuparams);
		}

		$ptmpl = $params->get('template', '');

		if (!empty($ptmpl))
		{
			$tmpl = $ptmpl;
		}

		$view->setLayout($tmpl);
		$view->display();
	}
}
