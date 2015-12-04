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

class MatukioViewHelp extends JViewLegacy
{

	function display($tpl = null)
	{
		$model = $this->getModel();

		$this->model = $model;

		$this->addToolbar();
		parent::display($tpl);
	}

	public function addToolbar()
	{
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_MATUKIO_HELP'), 'help_header');
		JToolBarHelper::cancel();
	}
}