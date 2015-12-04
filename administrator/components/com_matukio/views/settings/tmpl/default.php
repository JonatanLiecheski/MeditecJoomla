<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       28.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.html.html.tabs');

if (JVERSION > 3)
{
	JHTML::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');
}

JHTML::_('behavior.multiselect');

// Load bootstrap in J 2.5 and strapper.css
echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'settings', 'COM_MATUKIO_SETTINGS', 'COM_MATUKIO_SLOGAN_SETTINGS');

$doc = JFactory::getDocument();
$doc->addStyleSheet('../media/com_matukio/backend/css/settings.css');

// Small css fixes
$doc->addStyleDeclaration('
		.form-horizontal .control-label {padding-top: 7px;}
		.table td {vertical-align: middle;}
		label {display: inline-block !important; }
		.controls > .radio:first-child, .controls > .checkbox:first-child {
		padding-top: 4px;
		}
		fieldset label,
		fieldset span.faux-label {
			float: left;
			clear: none;
			display: inline-block !important;
			margin: 0;
		}
');
?>
<div class="box-info full">
	<form action="<?php JRoute::_("index.php?option=com_matukio&view=settings") ?>" method="post" name="adminForm"
	      id="adminForm">

		<div id="mat_sets" class="mat_settings_holder mat_settings row-fluid form-horizontal">

			<!-- List of tabs -->
			<ul class="nav nav-tabs nav-justified">
				<li class="active">
					<a href="#basic" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_BASIC'); ?></a>
				</li>
				<li>
					<a href="#layout" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_LAYOUT'); ?></a>
				</li>
				<li>
					<a href="#modern" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_MODERN_TEMPLATE'); ?></a>
				</li>
				<li>
					<a href="#payment" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_PAYMENT'); ?></a>
				</li>
				<li>
					<a href="#advanced" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_ADVANCED'); ?></a>
				</li>
				<li>
					<a href="#security" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_SECURITY'); ?></a>
				</li>
				<li>
					<a href="#cronjobs" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_CRONJOBS'); ?></a>
				</li>
				<li>
					<a href="#defaults" data-toggle="tab"><?php echo JText::_('COM_MATUKIO_DEFAULT_VALUES'); ?></a>
				</li>
			</ul>

			<!-- Tab content -->
			<div class="tab-content">
				<div class="tab-pane active" id="basic">
					<div class="control-group">
						<?php echo JText::_("COM_MATUKIO_SETTINGS_BASIC_INTRO"); ?>
					</div>
					<div class="row">
						<div class="col-lg-6 col-md-12 col-sm-12">
							<?php
							// Print the available settings in this category
							echo MatukioHelperSettings::getSettingsBlock($this->items_basic);
							?>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="layout">
					<div class="control-group">
						<?php echo JText::_("COM_MATUKIO_SETTINGS_LAYOUT_INTRO"); ?>
					</div>
					<div class="row">
						<div class="col-lg-6 col-md-12 col-sm-12">
							<?php
							// Print the available settings in this category
							echo MatukioHelperSettings::getSettingsBlock($this->items_layout);
							?>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="modern">
					<div class="control-group">
						<?php echo JText::_("COM_MATUKIO_SETTINGS_MODERN_TEMPLATE_INTRO"); ?>
					</div>
					<div class="row">
						<div class="col-lg-6 col-md-12 col-sm-12">
							<?php
							// Print the available settings in this category
							echo MatukioHelperSettings::getSettingsBlock($this->items_modernlayout);
							?>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="payment">
					<div class="control-group">
						<?php echo JText::_("COM_MATUKIO_SETTINGS_PAYMENT_INTRO"); ?>
					</div>
					<div class="row">
						<div class="col-lg-6 col-md-12 col-sm-12">
							<?php
							// Print the available settings in this category
							echo MatukioHelperSettings::getSettingsBlock($this->items_payment);
							?>
						</div>
						<div class="clearfix visible-xs"></div>
					</div>
				</div>

				<div class="tab-pane" id="advanced">
					<div class="control-group">
						<?php echo JText::_("COM_MATUKIO_SETTINGS_ADVANCED_INTRO"); ?>
					</div>
					<div class="row">
						<div class="col-lg-6 col-md-12 col-sm-12">
							<?php
							// Print the available settings in this category
							echo MatukioHelperSettings::getSettingsBlock($this->items_advanced);
							?>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="security">
					<div class="control-group">
						<?php echo JText::_("COM_MATUKIO_SETTINGS_SECURITY_INTRO"); ?>
					</div>
					<div class="row">
						<div class="col-lg-6 col-md-12 col-sm-12">
							<?php
							// Print the available settings in this category
							echo MatukioHelperSettings::getSettingsBlock($this->items_security);
							?>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="cronjobs">
					<div class="control-group">
						<?php echo JText::_("COM_MATUKIO_SETTINGS_CRONJOBS_INTRO"); ?>
					</div>
					<div class="row">
						<div class="col-lg-6 col-md-12 col-sm-12">
							<?php
							// Print the available settings in this category
							echo MatukioHelperSettings::getSettingsBlock($this->items_cronjobs);
							?>
						</div>
					</div>
				</div>

				<div class="tab-pane" id="defaults">
					<div class="control-group">
						<?php echo JText::_("COM_MATUKIO_SETTINGS_DEFAULTS_INTRO"); ?>
					</div>
					<div class="row">
						<div class="col-lg-6 col-md-12 col-sm-12">
						<?php
						// Print the available settings in this category
						echo MatukioHelperSettings::getSettingsBlock($this->items_defaults);
						?>
						</div>
					</div>
				</div>

			</div>
		</div>

		<input type="hidden" name="option" value="com_matukio"/>
		<input type="hidden" name="view" value="settings"/>
		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="controller" value="settings"/>

		<?php echo JHTML::_('form.token'); ?>

</form>
</div>
<?php
// Show Footer
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
