<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       12.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Class MatukioControllerLocation
 *
 * @since  3.0.0
 */
class MatukioControllerLocation extends JControllerLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   bool  $cachable   - Is the view cachable
	 * @param   bool  $urlparams  - The params
	 *
	 * @return JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		MatukioHelperUtilsBasic::loginUser();
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'location');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Location', 'MatukioModel');
		$view->setModel($model, true);
		$view->display();
	}
}
