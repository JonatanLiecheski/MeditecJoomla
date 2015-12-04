<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       11.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * Class MatukioViewLocations
 *
 * @since  3.0.0
 */
class MatukioViewRecurring extends JViewLegacy
{
	/**
	 * Displays the Locations overview list
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

		$db = JFactory::getDbo();

		$cats[] = JHTML::_('select.option', '0', JTEXT::_('COM_MATUKIO_ALL_CATS'));
		$db->setQuery("SELECT id AS value, title AS text FROM #__categories WHERE extension='com_matukio'");
		$cats = array_merge($cats, (array) $db->loadObjectList());

		$this->categories = JHtml::_('select.genericlist', $cats, 'filter_categories', 'onchange="this.form.submit()"',
			'value', 'text', $this->state->get('filter.categories')
		);

		$events[] = JHTML::_('select.option', '0', JTEXT::_('COM_MATUKIO_ALL_EVENTS'));
		$db->setQuery("SELECT id AS value, title AS text FROM #__matukio");
		$events = array_merge($events, (array) $db->loadObjectList());

		$this->eventfilter = JHtml::_('select.genericlist', $events, 'filter_events', 'onchange="this.form.submit()"',
			'value', 'text', $this->state->get('filter.events')
		);

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
		JToolBarHelper::title(JText::_('COM_MATUKIO_RECURRING_DATES'), 'generic');
		JToolBarHelper::addNew('recurring.edit');

		// Delete events (remove)
		JToolBarHelper::deleteList(JText::_('COM_MATUKIO_DO_YOU_REALLY_WANT_TO_DELETE_THIS_RECURRING_EVENTS'), 'recurring.remove');

		// Duplicate an event
		JToolBarHelper::custom('recurring.duplicate', 'copy', 'copy', JText::_('COM_MATUKIO_DUPLICATE'));

		JToolbarHelper::custom("recurring.participants", "user", "user", JText::_("COM_MATUKIO_PARTICIPANTS"));
		JToolBarHelper::unpublishList('recurring.unpublish');
		JToolBarHelper::publishList('recurring.publish');
		JToolbarHelper::custom("recurring.cancelRecurring", "cancel", "cancel_f2", JText::_("COM_MATUKIO_CANCEL_RECURRING"));
		JToolbarHelper::custom("recurring.uncancelRecurring", "publish", "cancel_f2", JText::_("COM_MATUKIO_REACTIVATE"));

		JToolBarHelper::help('screen.matukio', true);
	}
}
