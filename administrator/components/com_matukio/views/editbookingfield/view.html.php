<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       29.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewEditbookingfield
 *
 * @since  2.0.0
 */
class MatukioViewEditbookingfield extends JViewLegacy
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

		$bookingfield = $model->getBookingfield();

		if (!$bookingfield)
		{
			$bookingfield = JTable::getInstance('bookingfields', 'Table');
		}

		// Type
		$field_types = array(
			JHTML::_('select.option', 'text', JText::_('COM_MATUKIO_TYPE_TEXT')),
			JHTML::_('select.option', 'textarea', JText::_('COM_MATUKIO_TYPE_TEXTAREA')),
			JHTML::_('select.option', 'select', JText::_('COM_MATUKIO_TYPE_SELECT')),
			JHTML::_('select.option', 'radio', JText::_('COM_MATUKIO_TYPE_RADIO')),
			JHTML::_('select.option', 'checkbox', JText::_('COM_MATUKIO_TYPE_CHECKBOX')),
			JHTML::_('select.option', 'spacer', JText::_('COM_MATUKIO_TYPE_SPACER')),
			JHTML::_('select.option', 'spacertext', JText::_('COM_MATUKIO_TYPE_SPACER_TEXT')),
		);

		$select_fieldtype = JHTML::_('select.genericlist', $field_types, 'type', 'class="inputbox input select"', 'value', 'text', $bookingfield->type);

		// Page
		$pages = array(
			JHTML::_('select.option', '1', JText::_('COM_MATUKIO_PAGE_ONE')),
			JHTML::_('select.option', '2', JText::_('COM_MATUKIO_PAGE_TWO')),
			JHTML::_('select.option', '3', JText::_('COM_MATUKIO_PAGE_THREE'))
		);

		$select_pages = JHTML::_('select.genericlist', $pages, 'page', 'class="inputbox"', 'value', 'text', $bookingfield->page);

		$this->select_required = MatukioHelperInput::getRadioButtonBool("required", "required", $bookingfield->required);
		$this->select_published = MatukioHelperInput::getRadioButtonBool("published", "published", $bookingfield->published);

		// Data preallocation
		$sources = array(
			JHTML::_('select.option', '0', JText::_('COM_MATUKIO_NONE')),
			JHTML::_('select.option', '1', JText::_('COM_MATUKIO_JOOMLA_USER_PROFILE')),
		);

		$select_source = JHTML::_(
			'select.genericlist', $sources, 'datasource', 'class="input chzn_single"', 'value', 'text', $bookingfield->datasource
		);

		$joomla_fields = array(
			JHTML::_('select.option', 'name', JText::_('COM_MATUKIO_NAME')),
			JHTML::_('select.option', 'username', JText::_('COM_MATUKIO_USERNAME')),
			JHTML::_('select.option', 'email', JText::_('COM_MATUKIO_EMAIL')),
			JHTML::_('select.option', 'address1', 'address1'),
			JHTML::_('select.option', 'address2', JText::_('address2')),
			JHTML::_('select.option', 'city', JText::_('city')),
			JHTML::_('select.option', 'region', JText::_('region')),
			JHTML::_('select.option', 'country', JText::_('country')),
			JHTML::_('select.option', 'postal_code', JText::_('postal_code')),
			JHTML::_('select.option', 'phone', JText::_('phone')),
			JHTML::_('select.option', 'website', JText::_('website')),
			JHTML::_('select.option', 'favoritebook', JText::_('favoritebook')),
			JHTML::_('select.option', 'aboutme', JText::_('aboutme')),
			JHTML::_('select.option', 'dob', JText::_('dob'))
		);

		$select_joomla_data = JHTML::_(
			'select.genericlist', $joomla_fields, 'datasource_map', 'class="input chzn_single"',
			'value', 'text', $bookingfield->datasource_map
		);


		$this->bookingfield = $bookingfield;
		$this->select_type = $select_fieldtype;
		$this->select_page = $select_pages;
		$this->select_source = $select_source;
		$this->select_joomla_data = $select_joomla_data;

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
		JToolBarHelper::title(JText::_('COM_MATUKIO_EDIT_BOOKINGFIELD'), 'module');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
		JToolBarHelper::help('screen.bookingfields', true);
	}
}
