<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       04.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewDifferentfees
 *
 * @since  3.0.0
 */
class MatukioViewDifferentfees extends JViewLegacy
{
	/**
	 * Displays the different fees overview list
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

		$filter_state2 = JFactory::getApplication()->getUserStateFromRequest('com_matukio.differentfees.list.' . 'filter_state', 'filter_state', '', 'word');
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
		JToolBarHelper::title(JText::_('COM_MATUKIO_DIFFERENT_FEES'), 'list');
		JToolBarHelper::addNew('differentfees.editFee');
		JToolBarHelper::deleteList(JText::_('COM_MATUKIO_DO_YOU_REALLY_WANT_TO_DELETE_THIS_FEE'), 'differentfees.remove');
		JToolBarHelper::publishList('differentfees.publish');
		JToolBarHelper::unpublishList('differentfees.unpublish');
		JToolBarHelper::help('screen.matukio', true);
	}
}
