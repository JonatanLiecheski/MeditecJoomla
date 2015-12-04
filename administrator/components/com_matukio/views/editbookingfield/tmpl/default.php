<?php
/**
 * Matukio
 * @package Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 2.0.0 Stable $
 **/

defined('_JEXEC') or die('Restricted access');

$editor = JFactory::getEditor();

if (JVERSION > 3)
{
	JHTML::_('bootstrap.tooltip');
}

// Load formvalidator!
JHtml::_('behavior.formvalidation');

echo CompojoomHtmlCtemplate::getHead(
	MatukioHelperUtilsBasic::getMenu(), 'bookingfields', 'COM_MATUKIO_EDIT_BOOKING_FIELD', 'COM_MATUKIO_SLOGAN_EDIT_BOOKINGFIELD'
);

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

// Small css fixes
JFactory::getDocument()->addStyleDeclaration('
		.form-horizontal .control-label {padding-top: 7px;}
		label {display: inline;}
');
?>
<script type="text/javascript">
	Joomla.submitbutton = function (task) {
		if (task == 'cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>
<div class="box-info full">
	<div id="matukio" class="matukio">

		<form action="index.php" method="post" name="adminForm" id="adminForm" class="form" enctype="multipart/form-data">
				<div class="table-responsive">
				<table class="admintable table">
					<tr>
						<td width="250" align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_FIELD_NAME'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_BOOKINGFIELD_FIELD_NAME"); ?>
						</td>
						<td>
							<input class="input required" type="text" name="field_name" id="field_name" size="50" maxlength="250"
							       value="<?php echo $this->bookingfield->field_name; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_LABEL'); ?>:
						</td>
						<td>
							<input class="input-xlarge required" required="required" aria-required="true" type="text" size="50" maxlength="250" name="label" id="label"
							       value="<?php echo $this->bookingfield->label; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_DEFAULT_VALUE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_BOOKINGFIELD_DEFAULT_VALUE"); ?>
						</td>
						<td>
							<input type="text" name="default" id="default" size="50"
							       value="<?php echo $this->bookingfield->default; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_VALUES'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_BOOKINGFIELD_VALUES"); ?>
						</td>
						<td>
							<textarea class="text_area" cols="20" rows="4" name="values" id="values"
							          style="width: 500px"><?php echo $this->bookingfield->values; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_PAGE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_BOOKINGFIELD_PAGE"); ?>
						</td>
						<td>
							<?php echo $this->select_page; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_TYPE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_BOOKINGFIELD_TYPE"); ?>
						</td>
						<td>
							<?php echo $this->select_type; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_REQUIRED'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_BOOKINGFIELD_REQUIRED"); ?>
						</td>
						<td>
							<?php echo $this->select_required; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_PREALLOCATION_DATA_FROM'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_PREALLOCATION_DATA_FROM"); ?>
						</td>
						<td>
							<?php echo $this->select_source; ?>
						</td>
					</tr>
					<tr id="joomla_mapping" style="display: none;">
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_PREALLOCATION_DATA_MAPPING'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_PREALLOCATION_DATA_MAPPING"); ?>
						</td>
						<td>
							<?php echo $this->select_joomla_data; ?>
						</td>
					</tr>
					<script type="text/javascript">
						(function ($) {
							$("#datasource").change(function(){
								var val = $(this).val();

								if (val == 0) {
									$("#joomla_mapping").hide();
								} else if (val == 1) {
									$("#joomla_mapping").show();
								}
							});

							var dval = $("#datasource").val();

							if (dval == 1) {
								$("#joomla_mapping").show();
							}
						})(jQuery)
					</script>
					<tr>
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_STYLE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_BOOKINGFIELD_STYLE"); ?>
						</td>
						<td>
							<input type="text" name="style" id="style" size="50" class="input"
							       value="<?php echo $this->bookingfield->style; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_ORDERING'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_BOOKINGFIELD_ORDERING"); ?>
						</td>
						<td>
							<input class="required" type="text" name="ordering" id="ordering" class="input input-small"
							       value="<?php echo $this->bookingfield->ordering; ?>" />
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('JPUBLISHED'); ?>:
						</td>
						<td>
							<?php echo $this->select_published; ?>
						</td>
					</tr>
				</table>
				</div>

			<input type="hidden" name="id" value="<?php echo $this->bookingfield->id; ?>" />
			<input type="hidden" name="option" value="com_matukio" />
			<input type="hidden" name="controller" value="bookingfields" />
			<input type="hidden" name="view" value="editbookingfield" />
			<input type="hidden" name="model" value="editbookingfield" />
			<input type="hidden" name="task" value="editbookingfield" />
			<?php echo JHTML::_('form.token'); ?>

		</form>
	</div>
</div>

<div class="clr"></div>
<?php
// Footer
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
