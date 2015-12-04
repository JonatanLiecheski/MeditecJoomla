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

// ACL Check
if (!JFactory::getUser()->authorise('core.manage', 'com_matukio'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

require_once JPATH_COMPONENT_ADMINISTRATOR . "/helpers/defines.php";

JLoader::register('MatukioHelperSettings', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/settings.php');
JLoader::register('MatukioHelperUtilsBasic', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_basic.php');
JLoader::register('MatukioHelperUtilsBooking', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_booking.php');
JLoader::register('MatukioHelperUtilsDate', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_date.php');
JLoader::register('MatukioHelperUtilsEvents', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_events.php');
JLoader::register('MatukioHelperUtilsAdmin', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_admin.php');
JLoader::register('MatukioHelperRoute', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_route.php');
JLoader::register('MatukioHelperCategories', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_categories.php');
JLoader::register('MatukioHelperPayment', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_payment.php');
JLoader::register('MatukioHelperTemplates', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_templates.php');
JLoader::register('MatukioHelperInput', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/input.php');
JLoader::register('MatukioHelperChart', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_chart.php');
JLoader::register('MatukioHelperTaxes', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_taxes.php');
JLoader::register('MatukioHelperFees', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/util_fees.php');
JLoader::register('MatukioHelperInvoice', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/invoice.php');
JLoader::register('MatukioHelperPDF', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/pdf.php');
JLoader::register('MatukioHelperRecurring', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/recurring.php');
JLoader::register('MatukioHelperUpcoming', JPATH_COMPONENT_ADMINISTRATOR . '/helpers/upcoming.php');

JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

require_once JPATH_COMPONENT_ADMINISTRATOR . "/toolbar.matukio.php";
require_once JPATH_COMPONENT_ADMINISTRATOR . "/controller.php";

// Load Compojoom library
require_once JPATH_LIBRARIES . '/compojoom/include.php';

// Load language
CompojoomLanguage::load('com_matukio', JPATH_SITE);
CompojoomLanguage::load('com_matukio', JPATH_ADMINISTRATOR);

$input = JFactory::getApplication()->input;

if ($input->get('view', '') == 'liveupdate')
{
	require_once JPATH_COMPONENT_ADMINISTRATOR . '/liveupdate/liveupdate.php';
	JToolBarHelper::preferences('com_matukio');
	LiveUpdate::handleRequest();

	return;
}
elseif ($input->get('view', '') == 'controlcenter')
{
	require_once JPATH_COMPONENT_ADMINISTRATOR . '/controlcenter/controlcenter.php';
	JToolBarHelper::preferences('com_matukio');
	CompojoomControlCenter::handleRequest();

	return;
}
elseif ($input->get('view', '') == 'information')
{
	require_once JPATH_COMPONENT_ADMINISTRATOR . '/controlcenter/controlcenter.php';
	JToolBarHelper::preferences('com_matukio');
	CompojoomControlCenter::handleRequest('information');

	return;
}

if ($input->get('task') != '')
{
	if (count(explode(".", $input->get('task'))) > 1)
	{
		// Ugly hack improve!
		$ar = explode(".", $input->get('task'));
		$contr = $ar[0];
	}
}

if ($input->get('controller', '') != '')
{
	// Require specific controller if requested
	if ($controller = $input->get('controller'))
	{
		$path = JPATH_COMPONENT_ADMINISTRATOR . '/controllers/' . $controller . '.php';

		if (file_exists($path))
		{
			require_once $path;
		}
		else
		{
			$controller = '';
		}
	}


	// Create the controller
	$classname = 'MatukioController' . $controller;
	$controller = new $classname;
	$controller->execute($input->getCmd('task', ''));
	$controller->redirect();

	return;
}

if ($input->get('view', '') != '')
{
	// Get the view and controller from the request, or set to eventlist if they weren't set
	// Black magic: Get controller based on the selected view
	$input->set('controller', $input->get('view', ''));
	$controller = $input->get('controller');

	// Require specific controller if requested
	$path = JPATH_COMPONENT_ADMINISTRATOR . '/controllers/' . $controller . '.php';

	if (file_exists($path))
	{
		require $path;
	}
	else
	{
		$controller = '';
	}

	// Create the controller
	$classname = 'MatukioController' . $controller;
	$controller = JControllerLegacy::getInstance('Matukio');
	$controller->execute($input->getCmd('task', ''));
	$controller->redirect();

	return;
}

// Fallback for invalid routing (maybe send a message?)
$classname = 'MatukioControllerEventlist';
$controller = JControllerLegacy::getInstance('Matukio');
$controller->execute($input->getCmd('task', ''));
$controller->redirect();
