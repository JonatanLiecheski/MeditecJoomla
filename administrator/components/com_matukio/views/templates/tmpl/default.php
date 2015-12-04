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

jimport('joomla.html.html.tabs');

if (JVERSION > 3)
{
	JHTML::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');
}

JHTML::_('behavior.multiselect');

echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'templates', 'COM_MATUKIO_TEMPLATES', 'COM_MATUKIO_SLOGAN_TEMPLATES');

// Small css fixes
JFactory::getDocument()->addStyleDeclaration('
		.form-horizontal .control-label {padding-top: 7px;}
		label {display: inline;}
');
?>
<div class="box-info full">
	<form action="<?php JRoute::_("index.php?option=com_matukio&view=templates") ?>" method="post" name="adminForm" id="adminForm">
		<div id="mat_templates" class="row-fluid form-horizontal">

			<!-- List of tabs -->
			<ul id="tabs" class="nav nav-tabs nav-justified">
				<li class="active">
					<a href="#email" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_TEMPLATE_EMAIL'); ?></a>
				</li>
				<li>
					<a href="#export" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT'); ?></a>
				</li>
				<li>
					<a href="#certificate" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_TEMPLATE_CERTIFICATE'); ?></a>
				</li>
				<li>
					<a href="#invoice" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_TEMPLATE_INVOICE'); ?></a>
				</li>
				<li>
					<a href="#ticket" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_TEMPLATE_TICKET'); ?></a>
				</li>
			</ul>

			<!-- Tab content -->
			<div class="tab-content">

				<div class="tab-pane active" id="email">
					<?php
					require dirname(__FILE__) . "/email.php";
					?>
				</div>

				<div class="tab-pane" id="export">
					<?php
					require dirname(__FILE__) . "/export.php";
					?>
				</div>

				<div class="tab-pane" id="certificate">
					<?php
					require dirname(__FILE__) . "/certificate.php";
					?>
				</div>

				<div class="tab-pane" id="invoice">
					<?php
					require dirname(__FILE__) . "/invoice.php";
					?>
				</div>

				<div class="tab-pane" id="ticket">
					<?php
					require dirname(__FILE__) . "/ticket.php";
					?>
				</div>
			</div>

		</div>

		<input type="hidden" name="option" value="com_matukio" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="view" value="templates" />
		<input type="hidden" name="controller" value="templates" />
		<?php echo JHTML::_('form.token'); ?>
	</form>
</div>
<?php
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
