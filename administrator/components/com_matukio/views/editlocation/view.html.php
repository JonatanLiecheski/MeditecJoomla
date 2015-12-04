<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       12.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewEditlocation
 *
 * @since  3.0.0
 */
class MatukioViewEditlocation extends JViewLegacy
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
			$item = JTable::getInstance('Locations', 'MatukioTable');
		}

		$this->location = $item;

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
		JToolBarHelper::title(JText::_('COM_MATUKIO_EDIT_LOCATION'), 'user');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::help('screen.matukio', true);
	}
}
