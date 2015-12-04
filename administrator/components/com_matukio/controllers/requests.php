<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       10.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Class MatukioControllerRequests
 *
 * @since  3.0
 */
class MatukioControllerRequests extends JControllerLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   bool  $cachable   -
	 * @param   bool  $urlparams  -
	 *
	 * @return JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$view = $this->getView('requests', 'raw');
		$view->display();
	}
}
