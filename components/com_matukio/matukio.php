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

if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}

require_once JPATH_COMPONENT_ADMINISTRATOR . '/helpers/defines.php';

JLoader::register('MatukioHelperSettings', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/settings.php');
JLoader::register('MatukioHelperUtilsBasic', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_basic.php');
JLoader::register('MatukioHelperUtilsBooking', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_booking.php');
JLoader::register('MatukioHelperUtilsDate', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_date.php');
JLoader::register('MatukioHelperUtilsEvents', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_events.php');
JLoader::register('MatukioHelperRoute', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_route.php');
JLoader::register('MatukioHelperCategories', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_categories.php');
JLoader::register('MatukioHelperPayment', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_payment.php');
JLoader::register('MatukioHelperTemplates', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_templates.php');
JLoader::register('MatukioHelperOrganizer', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_organizer.php');
JLoader::register('MatukioHelperInput', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/input.php');
JLoader::register('MatukioHelperTaxes', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_taxes.php');
JLoader::register('MatukioHelperFees', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_fees.php');
JLoader::register('MatukioHelperPDF', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/pdf.php');
JLoader::register('MatukioHelperInvoice', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/invoice.php');
JLoader::register('MatukioHelperRecurring', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/recurring.php');
JLoader::register('MatukioHelperUpcoming', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/upcoming.php');

// Load Compojoom library
require_once JPATH_LIBRARIES . '/compojoom/include.php';

// Load language
CompojoomLanguage::load('com_matukio', JPATH_SITE);
CompojoomLanguage::load('com_matukio', JPATH_ADMINISTRATOR);

$input = JFactory::getApplication()->input;

JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_matukio/tables');

// Get the view and controller from the request, or set to eventlist if they weren't set
// Black magic: Get controller based on the selected view
$input->set('controller', $input->get('view', 'eventlist'));

// Require specific controller if requested
if ($controller = $input->get('controller'))
{
	$path = JPATH_COMPONENT . '/controllers/' . $controller . '.php';

	if (file_exists($path))
	{
		require_once $path;
	}
	else
	{
		$controller = '';
	}
}

if ($controller == '')
{
	require_once JPATH_COMPONENT . '/controllers/eventlist.php';
	$controller = 'eventlist';
}

// Create the controller
$classname = 'MatukioController' . $controller;
$controller = new $classname;
$controller->execute($input->get('task'));
$controller->redirect();
