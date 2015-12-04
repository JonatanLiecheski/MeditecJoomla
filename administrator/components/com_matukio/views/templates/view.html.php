<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       28.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewTemplates
 *
 * @since  2.2.0
 */
class MatukioViewTemplates extends JViewLegacy
{
	/**
	 * Displays the templates view
	 *
	 * @param   string  $tpl  - Differen template
	 *
	 * @return  mixed|void
	 */

	public function display($tpl = null)
	{
		$model = $this->getModel();

		$this->templates = $model->getTemplates();

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Adds the toolbar buttons
	 *
	 * @return  void
	 */
	public function addToolbar()
	{
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_MATUKIO_TEMPLATES'));
		JToolBarHelper::apply();
		JToolBarHelper::cancel();
		JToolBarHelper::custom("reset", "options", "options", JText::_('COM_MATUKIO_SETTINGS_RESET'), false);
		JToolBarHelper::help('screen.matukio', true);
	}
}
