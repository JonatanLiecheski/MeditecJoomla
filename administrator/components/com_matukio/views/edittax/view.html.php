<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       17.10.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewEdittax
 *
 * @since  3.0
 */
class MatukioViewEdittax extends JViewLegacy
{
	/**
	 * Displays the taxes edit form
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();

		$tax = $model->getTax();

		if (!$tax)
		{
			$tax = JTable::getInstance('taxes', 'MatukioTable');
			$tax->value = 0.00;
		}

		$this->select_published = MatukioHelperInput::getRadioButtonBool("published", "published", $tax->published);

		$this->tax = $tax;
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
		JToolBarHelper::title(JText::_('COM_MATUKIO_EDIT_TAX'), 'user');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::help('screen.taxes', true);
	}
}
