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
 * Class MatukioViewRateEvent
 *
 * @since  1.0.0
 */
class MatukioViewRateEvent extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  - The tpl
	 *
	 * @return  mixed|object
	 */
	public function display($tpl = null)
	{
		$my = JFactory::getuser();

		$art = JFactory::getApplication()->input->getInt('art', 1);
		$cid = JFactory::getApplication()->input->getInt('cid', 0);

		$model = $this->getModel();

		if (empty($cid))
		{
			JError::raiseError('404', "COM_MATUKIO_NO_ID");
		}

		// Only registered users
		if ($my->id == 0)
		{
			JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

		$event = $model->getEvent($cid);

		$database = JFactory::getDBO();

		$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid='" . $cid . "' AND userid='" . $my->id . "'");
		$booking = $database->loadObject();

		$this->event = $event;
		$this->booking = $booking;

		parent::display($tpl);
	}
}
