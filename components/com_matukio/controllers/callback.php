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
 * Class MatukioControllerCallback
 *
 * @since  2.0.0
 * @deprecated  use ppayment instead
 */
class MatukioControllerCallback extends JControllerLegacy
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
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'Callback');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Callback', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	public function cancel()
	{
		$uuid = JFactory::getApplication()->input->get('booking_id', '', 'string');

		if (empty($uuid))
		{
			return JError::raiseError('404', "COM_MATUKIO_NO_ID");
		}

		$model = $this->getModel('Callback', 'MatukioModel');
		$booking = $model->getBooking($uuid);
		$uid = $booking->id;
		$link = JRoute::_('index.php?option=com_matukio&view=bookevent&task=cancelBooking&uid=' . $uid . "&return=1");

		$this->setRedirect($link);
	}
}
