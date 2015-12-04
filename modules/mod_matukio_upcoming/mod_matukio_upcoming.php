<?php
/**
 * Matukio
 * @package  Joomla!
 * @Copyright (C) 2013 - Yves Hoppe - compojoom.com
 * @All      rights reserved
 * @Joomla   ! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version  $Revision: 2.2.0 $
 **/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_matukio/tables');

JLoader::register('MatukioHelperSettings', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/settings.php');
JLoader::register('MatukioHelperUtilsBasic', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_basic.php');
JLoader::register('MatukioHelperUtilsBooking', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_booking.php');
JLoader::register('MatukioHelperUtilsEvents', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_events.php');
JLoader::register('MatukioHelperRoute', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_route.php');
JLoader::register('MatukioHelperCategories', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_categories.php');

require_once JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/defines.php';

require_once dirname(__FILE__) . '/helper.php';

$jlang = JFactory::getLanguage();
$jlang->load('com_matukio', JPATH_SITE, 'en-GB', true);
$jlang->load('com_matukio', JPATH_SITE, $jlang->getDefault(), true);
$jlang->load('com_matukio', JPATH_SITE, null, true);
$jlang->load('com_matukio', JPATH_ADMINISTRATOR, 'en-GB', true);
$jlang->load('com_matukio', JPATH_ADMINISTRATOR, $jlang->getDefault(), true);
$jlang->load('com_matukio', JPATH_ADMINISTRATOR, null, true);

JHTML::_('stylesheet', 'media/mod_matukio_upcoming/css/basic.css');

if (JVERSION >= 3)
{
	JHtmlBehavior::framework();
}
else
{
	JHTML::_('behavior.mootools');
}

// Module class
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

?>
<!-- START Matukio Upcoming module by compojoom.com  -->
<div class="matukioupcoming<?php echo $moduleclass_sfx ?>">
	<?php
	// Params for individual module template
	if ($params->get('template', '0') == '1')
		require JModuleHelper::getLayoutPath('mod_matukio_upcoming', 'simple');
	else if ($params->get('template', '0') == '2')
		require JModuleHelper::getLayoutPath('mod_matukio_upcoming', 'modern');
	else
		require JModuleHelper::getLayoutPath('mod_matukio_upcoming', 'default'); // Fall back to default template 0
	?>
</div>
<!-- END Matukio Upcoming module by compojoom.com  -->