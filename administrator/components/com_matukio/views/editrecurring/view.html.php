<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       21.02.14
 *
 * @copyright  Copyright (C) 2008 - 2014 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewEditrecurring
 *
 * @since  3.1.0
 */
class MatukioViewEditrecurring extends JViewLegacy
{
	/**
	 * Displays the organizer form
	 *
	 * @param   string  $tpl  - The layout
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();

		$item = $model->getItem();

		if (!$item)
		{
			$item = JTable::getInstance('Recurring', 'MatukioTable');
		}

		$db = JFactory::getDbo();
		$db->setQuery("SELECT id AS value, CONCAT(title, ' ', begin) AS text FROM #__matukio");
		$events = (array) $db->loadObjectList();

		$this->event_select = JHtml::_('select.genericlist', $events, 'event_id', 'class="input-xxlarge chzn-single"',
			'value', 'text', $item->event_id
		);

		$this->recurring = $item;

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Ads the toolbar buttons
	 *
	 * @return  void
	 */
	public function addToolbar()
	{
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_MATUKIO_EDIT_RECURRING_EVENT'), 'generic');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::help('screen.matukio', true);
	}
}
