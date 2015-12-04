<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       29.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      2.0.0
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewEditcoupon
 *
 * @since  2.0.0
 */
class MatukioViewEditcoupon extends JViewLegacy
{
	/**
	 * Displays the coupon edit form
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();

		$coupon = $model->getCoupon();

		if (!$coupon)
		{
			$coupon = JTable::getInstance('coupons', 'Table');
		}

		$this->coupon = $coupon;

		$this->select_procent = MatukioHelperInput::getRadioButtonBool("procent", "procent", $coupon->procent);

		$this->select_published = MatukioHelperInput::getRadioButtonBool("published", "published", $coupon->published);

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
		JToolBarHelper::title(JText::_('COM_MATUKIO_EDIT_COUPON'), 'user');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::help('screen.coupons', true);
	}
}
