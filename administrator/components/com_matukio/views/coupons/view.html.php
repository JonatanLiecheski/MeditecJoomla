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
 * Class MatukioViewCoupons
 *
 * @since  2.0.0
 */
class MatukioViewCoupons extends JViewLegacy
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

		$filter_state2 = JFactory::getApplication()->getUserStateFromRequest('com_matukio.coupons.list.' . 'filter_state', 'filter_state', '', 'word');
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
		JToolBarHelper::title(JText::_('COM_MATUKIO_COUPONS'), 'user');
		JToolBarHelper::addNew('coupons.editCoupon');
		JToolBarHelper::deleteList(JText::_('COM_MATUKIO_DO_YOU_REALLY_WANT_TO_DELETE_THIS_COUPON'), 'coupons.remove');
		JToolBarHelper::custom('coupons.duplicate', 'copy.png', 'copy_f2.png', JText::_('COM_MATUKIO_DUPLICATE'));
		JToolBarHelper::publishList('coupons.publish');
		JToolBarHelper::unpublishList('coupons.unpublish');
		JToolBarHelper::help('screen.matukio', true);
	}
}
