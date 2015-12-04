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
 * Class MatukioViewEditfee
 *
 * @since  3.0
 */
class MatukioViewEditfee extends JViewLegacy
{
	/**
	 * Displays the fee edit form
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();

		$fee = $model->getFee();

		if (!$fee)
		{
			$fee = JTable::getInstance('differentfees', 'MatukioTable');
			$fee->value = 0.00;
			$fee->discount = 1;
			$fee->percent = 1;
		}

		$this->select_published = MatukioHelperInput::getRadioButtonBool("published", "published", $fee->published);

		$this->fee = $fee;
		$this->select_percent = MatukioHelperInput::getRadioButtonBool("percent", "percent", $fee->percent);
		$this->select_discount = MatukioHelperInput::getRadioButtonBool("discount", "discount", $fee->discount);

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
		JToolBarHelper::title(JText::_('COM_MATUKIO_EDIT_FEE'), 'user');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::help('screen.matukio', true);
	}
}
