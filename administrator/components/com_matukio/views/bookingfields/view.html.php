<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       28.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      2.0.0
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewBookingfields
 *
 * @since  2.0.0
 */
class MatukioViewBookingfields extends JViewLegacy
{
	/**
	 * Displays the coupon overview list
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @return  mixed|void
	 */

	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->state = $this->get('state');
		$this->status = $this->get('status');
		$this->pagination = $this->get('Pagination');

		$filter_state2 = JFactory::getApplication()->getUserStateFromRequest(
			'com_matukio.bookingfields.list.' . 'filter_state', 'filter_state', '', 'word'
		);

		$this->filter['state'] = JHTML::_('grid.state', $filter_state2, 'JPUBLISHED', 'JUNPUBLISHED');

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Ads the toolbar buttons
	 *
	 * @return void
	 */
	public function addToolbar()
	{
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_MATUKIO_BOOKINGFIELDS'), 'module');
		JToolBarHelper::addNew('bookingfields.editBookingfield');
		JToolBarHelper::deleteList(JText::_('COM_MATUKIO_DO_YOU_REALLY_WANT_TO_DELETE_THIS_BOOKING_FIELD'), 'bookingfields.remove');
		JToolBarHelper::publishList('bookingfields.publish');
		JToolBarHelper::unpublishList('bookingfields.unpublish');
		JToolBarHelper::custom("bookingfields.reset", "options", "options", JText::_('COM_MATUKIO_SETTINGS_RESET'), false);
		JToolBarHelper::help('screen.matukio', true);
	}
}
