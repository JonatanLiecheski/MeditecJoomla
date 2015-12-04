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
 * Class MatukioControllerPayPalPayment
 *
 * @since       2.0
 * @deprecated  Has been replaced through ppayment and paymentform
 */
class MatukioControllerPayPalPayment extends JControllerLegacy
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
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'PayPalPayment');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('PayPalPayment', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}
}