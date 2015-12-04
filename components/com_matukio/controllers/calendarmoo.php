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
 * Class MatukioControllerCalendarmoo
 *
 * @since  3.1.0
 */
class MatukioControllerCalendarmoo extends JControllerLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   bool  $cachable   - Cachable
	 * @param   bool  $urlparams  - Params
	 *
	 * @return  JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		MatukioHelperUtilsBasic::loginUser();

		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'Calendarmoo');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Calendarmoo', 'MatukioModel');
		$view->setModel($model, true);
		$view->display();
	}
}
