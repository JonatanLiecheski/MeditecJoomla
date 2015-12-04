<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       29.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      2.2.0
 */

defined('_JEXEC') or die('Restricted access');

if (JVERSION > 3)
{
	JHTML::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');
}
else
{
	JHTML::_('behavior.tooltip');
}

jimport('joomla.html.html.tabs');

JHTML::_('behavior.multiselect');

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

JFactory::getDocument()->addStyleDeclaration('
		.form-horizontal .control-label {padding-top: 7px;}
		.table td {vertical-align: middle;}
		label {display: inline;}
');
?>
<div class="compojoom-bootstrap">
	Calendar
</div>
<?php
echo MatukioHelperUtilsBasic::getCopyright(false);
