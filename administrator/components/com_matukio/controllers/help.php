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
 * Class MatukioControllerHelp
 *
 * @since  3.0.0
 */
class MatukioControllerHelp extends JControllerLegacy
{
	/**
	 * The constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
	}

	/**
	 * Displays the form
	 *
	 * @param   bool  $cachable   - Is the site cachable
	 * @param   bool  $urlparams  - The url params
	 *
	 * @return  JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'Help');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Help', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Cancel function
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio';
		$this->setRedirect($link);
	}
}
