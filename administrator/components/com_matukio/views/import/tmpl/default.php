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

echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'import', 'COM_MATUKIO_IMPORT', 'COM_MATUKIO_SLOGAN_IMPORT');

JHTML::_('stylesheet', '../media/com_matukio/backend/css/matukio.css');

JFactory::getDocument()->addStyleDeclaration('
		.form-horizontal .control-label {padding-top: 7px;}
		.table td {vertical-align: middle;}
		label {display: inline;}
');
?>
<div class="box-info full">
	<div class="mat_content_holder">
		<div id="mat_import" class="row-fluid form-horizontal">

			<!-- List of tabs -->
			<ul id="tabs" class="nav nav-tabs nav-justified">
				<li class="active">
					<a href="#seminar" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_IMPORT_SEMINAR'); ?></a>
				</li>
				<li>
					<a href="#ics" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_IMPORT_ICS'); ?></a>
				</li>
			</ul>

			<!-- Tab content -->
			<div class="tab-content">

				<div class="tab-pane active" id="seminar">
					<?php
					require dirname(__FILE__) . "/seminar.php";
					?>
				</div>

				<div class="tab-pane" id="ics">
					<?php
					require dirname(__FILE__) . "/ics.php";
					?>
				</div>

			</div>

		</div>
	</div>
</div>
<?php
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
