<?php
/**
 * Copyright (C) 2010-2012 Yves Hoppe - All rights reserved
 * User: yh
 * E-Mail: info@yves-hoppe.de
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * Class MatukioHelper
 *
 * Shows the menu also in the category view
 *
 * @since  2.0.0
 */
abstract class MatukioHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   object  $submenu  - The submenu Object
	 *
	 * @return  void
	 */
	public static function addSubmenu($submenu)
	{
		// Load Compojoom library
//		require_once JPATH_LIBRARIES . '/compojoom/include.php';
//		require_once JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_basic.php';
//		JLoader::register('MatukioHelperSettings', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/settings.php');
//
//		// Load language
//		CompojoomLanguage::load('com_matukio', JPATH_SITE);
//		CompojoomLanguage::load('com_matukio', JPATH_ADMINISTRATOR);
//
//		echo CompojoomHtmlCtemplate::getHead(
//			MatukioHelperUtilsBasic::getMenu(), 'information', 'COM_MATUKIO_INFORMATIONS', 'COM_MATUKIO_SLOGAN_INFORMATIONS'
//		);


		$language = JFactory::getLanguage();
		$language->load('com_matukio.sys', JPATH_ADMINISTRATOR, null, true);

		$view = JFactory::getApplication()->input->get('task');

		$active2 = ($view == 'controlcenter');
		// JSubMenuHelper::addEntry(JText::_('COM_MATUKIO_CONTROLCENTER'), 'index.php?option=com_matukio&view=controlcenter', $active2);

		$subMenus = array(
			'eventlist' => 'COM_MATUKIO_EVENTS',
			'recurring' => 'COM_MATUKIO_RECURRING_DATES',
			'categories' => 'COM_MATUKIO_CATEGORIES',
			'bookings' => 'COM_MATUKIO_BOOKINGS',
			'bookingfields' => 'COM_MATUKIO_BOOKINGFIELDS',
			'organizers' => 'COM_MATUKIO_ORGANIZERS',
			'locations' => 'COM_MATUKIO_LOCATIONS',
			'coupons' => 'COM_MATUKIO_COUPONS',
			'differentfees' => 'COM_MATUKIO_DIFFERENT_FEES',
			'taxes' => 'COM_MATUKIO_TAXES',
			'templates' => 'COM_MATUKIO_TEMPLATES',
			'settings' => 'COM_MATUKIO_CONFIGURATION',
			'import' => 'COM_MATUKIO_IMPORT',
			'statistics' => 'COM_MATUKIO_STATISTICS',
			'information' => 'COM_MATUKIO_INFORMATIONS'
		);

		foreach ($subMenus as $key => $name)
		{
			$active = ($view == $key);

			if (!is_array($name))
			{
				if ($key == 'categories')
				{
					JSubMenuHelper::addEntry(JText::_($name), 'index.php?option=com_categories&extension=com_matukio', $active);
				}
				else
				{
					JSubMenuHelper::addEntry(JText::_($name), 'index.php?option=com_matukio&view=' . $key, $active);
				}
			}
		}

		$active = ($view == 'liveupdate');

		JSubMenuHelper::addEntry(JText::_('COM_MATUKIO_LIVEUPDATE'), 'index.php?option=com_matukio&view=liveupdate', $active);

		$document = JFactory::getDocument();
		$document->addStyleDeclaration('.icon-48-matukio ' .
		'{background-image: url(../media/com_matukio/backend/images/icon-48.png);}');

		if ($submenu == 'categories')
		{
			$document->setTitle(JText::_('COM_MATUKIO_CATEGORIES'));
		}
	}
}
