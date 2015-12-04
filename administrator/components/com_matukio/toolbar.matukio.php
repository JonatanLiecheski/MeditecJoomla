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

/**
 * TODO REWRITE!!
 */

$language = JFactory::getLanguage();
$language->load('com_matukio.sys', JPATH_ADMINISTRATOR, null, true);

$view = JFactory::getApplication()->input->get('task');

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
