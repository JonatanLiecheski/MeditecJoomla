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
?>
<div id="mat_export">
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_INVOICE_TEMPLATE'); ?></legend>
		<table class="admintable table">
			<tr>
				<td class="key" colspan="2">
					<label for="value_8" title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_INVOICE_TEMPLATE'); ?>">
						<?php echo JText::_('COM_MATUKIO_TEMPLATE_INVOICE_TEMPLATE'); ?>:
						<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_INVOICE_TEMPLATE"); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php
					$editor = JFactory::getEditor();
					echo $editor->display("value[8]", $this->templates[7]->value, 800, 300, 40, 20, false, "value_8");
					?>
					<input type="hidden" name="subject[8]" value="E"/>
					<input type="hidden" name="value_text[8]" value=""/>
				</td>
			</tr>
		</table>
	</fieldset>
	<br/>
	<fieldset class="adminform">
		<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_INVOICE_EMAIL_TEMPLATE'); ?></legend>
		<table class="admintable table">
			<tr>
				<td class="key">
					<label for="subject_9"
					       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_INVOICE_EMAIL_TEMPLATE_SUBJECT'); ?>">
						<?php echo JText::_('COM_MATUKIO_TEMPLATE_INVOICE_EMAIL_TEMPLATE_SUBJECT'); ?>:
						<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_INVOICE_EMAIL_TEMPLATE_SUBJECT"); ?>
					</label>
				</td>
				<td>
					<input type="text" maxlength="255" id="subject_9" name="subject[9]" class="input"
					       value="<?php echo $this->templates[8]->subject; ?>" style="width: 350px;"/>
				</td>
			</tr>
			<tr>
				<td class="key" colspan="2">
					<label for="value_9"
					       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_INVOICE_EMAIL_TEMPLATE_TEXT'); ?>">
						<?php echo JText::_('COM_MATUKIO_TEMPLATE_INVOICE_EMAIL_TEMPLATE_TEXT'); ?>:
						<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_INVOICE_EMAIL_TEMPLATE_TEXT"); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php
					$editor = JFactory::getEditor();
					echo $editor->display("value[9]", $this->templates[8]->value, 800, 300, 40, 20, false, "value_9");
					?>
					<input type="hidden" name="value_text[4]" value=""/>
				</td>
			</tr>
		</table>
	</fieldset>
</div>

<div class="clr"></div>
