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
jimport('joomla.application.component.view');

/**
 * Class MatukioViewEventlist
 *
 * @since  2.2.5
 */
class MatukioViewEventlist extends JViewLegacy
{
	/**
	 * Displays the eventlist
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
		JToolBarHelper::title(JText::_('COM_MATUKIO_EVENTS'), 'generic');

		JToolBarHelper::addNew('eventlist.editEvent');

		// Delete events (remove)
		JToolBarHelper::deleteList('COM_MATUKIO_REALLY_DELETE_THIS_EVENTS', 'eventlist.remove');

		// Duplicate an event
		JToolBarHelper::custom('eventlist.duplicate', 'copy.png', 'copy_f2.png', JText::_('COM_MATUKIO_DUPLICATE'));

		JToolBarHelper::unpublishList("eventlist.unpublish");
		JToolBarHelper::publishList("eventlist.publish");
		JToolbarHelper::custom("eventlist.cancelEvent", "cancel", "cancel_f2", JText::_("COM_MATUKIO_CANCEL_EVENT"));
		JToolbarHelper::custom("eventlist.uncancelEvent", "publish", "cancel_f2", JText::_("COM_MATUKIO_REACTIVATE"));

		JToolBarHelper::help('screen.matukio', true);
	}
}
