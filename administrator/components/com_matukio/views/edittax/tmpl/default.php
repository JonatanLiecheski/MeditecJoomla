<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       17.10.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      3.0.0
 */

defined('_JEXEC') or die('Restricted access');

$editor = JFactory::getEditor();

if (JVERSION > 3)
{
	JHTML::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');
}

JHTML::_('behavior.calendar');

// Load formvalidator!
JHtml::_('behavior.formvalidation');

echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'taxes', 'COM_MATUKIO_EDIT_TAX', 'COM_MATUKIO_SLOGAN_EDIT_TAX');

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

// Small css fixes
JFactory::getDocument()->addStyleDeclaration('
		.form-horizontal .control-label {padding-top: 7px;}
		label {display: inline;}
');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.id('adminForm')))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>
	<div class="box-info full">
		<div id="matukio" class="matukio">
			<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
				<div class="table-responsive">
					<table class="admintable table">
						<tr>
							<td width="200" align="left" class="key">
								<?php echo JText::_('COM_MATUKIO_TAX_TITLE'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TAX_TITLE"); ?>
							</td>
							<td>
								<input class="input required" type="text" name="title" id="title" size="50" maxlength="250" required="required"
								       value="<?php echo $this->tax->title; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('COM_MATUKIO_VALUE'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TAX_VALUE"); ?>
							</td>
							<td>
								<input class="input text_area required validate-numeric" type="text" size="10" maxlength="15" name="value" id="value" required="required"
								       value="<?php echo $this->tax->value; ?>" />
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('COM_MATUKIO_PUBLISHED'); ?>:
							</td>
							<td>
								<fieldset class="radio btn-group">
									<?php echo $this->select_published; ?>
								</fieldset>
							</td>
						</tr>
					</table>

				</div>
				<input type="hidden" name="id" value="<?php echo $this->tax->id; ?>" />

				<input type="hidden" name="option" value="com_matukio" />
				<input type="hidden" name="controller" value="taxes" />
				<input type="hidden" name="view" value="edittax" />
				<input type="hidden" name="model" value="edittax" />
				<input type="hidden" name="task" value="edittax" />
				<?php echo JHTML::_('form.token'); ?>
			</form>
		</div>
	</div>

	<div class="clr"></div>
<?php
// Footer
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
