<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       29.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      2.2.0
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewImport
 *
 * @since  2.2.0
 */
class MatukioViewImport extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();

		$this->model = $model;

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Adds the toolbar
	 *
	 * @return  void
	 */
	public function addToolbar()
	{
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_MATUKIO_IMPORT'), 'categories');
		JToolBarHelper::help('screen.matukio', true);
	}
}
