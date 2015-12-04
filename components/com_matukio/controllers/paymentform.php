<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       17.10.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * Class MatukioControllerPaymentForm
 *
 * @since  2.2.0
 */
class MatukioControllerPaymentForm extends JControllerLegacy
{
	/**
	 * Displays the payment form
	 *
	 * @param   bool  $cachable   - Is the site cachable (we ignore that)
	 * @param   bool  $urlparams  - The url params
	 *
	 * @return JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		MatukioHelperUtilsBasic::loginUser();
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'PaymentForm');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('PaymentForm', 'MatukioModel');
		$view->setModel($model, true);
		$view->display();
	}
}
