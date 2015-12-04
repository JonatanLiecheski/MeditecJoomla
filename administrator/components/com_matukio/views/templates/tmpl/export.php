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

JImport('joomla.html.editor');

?>
	<div id="mat_export">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_CSV'); ?></legend>
			<table class="admintable table">
				<tr>
					<td class="key" colspan="2">
						<label for="value_4"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_CSV_BOOKING_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_CSV_BOOKING'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_EXPORT_CSV"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<br />
						<textarea rows="10" cols="80" name="value[4]" id="value_4"
						          style="min-width: 400px; width: auto;"><?php echo $this->templates[3]->value; ?></textarea>
						<input type="hidden" name="subject[4]" value="ID" />
						<input type="hidden" name="value_text[4]" value="" />
					</td>
				</tr>
			</table>
		</fieldset>
		<br />
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_SIGNATURE_LIST'); ?></legend>
			<table class="table">
				<tr>
					<td class="key">
						<label for="subject_5" title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_SIGNATURE_LIST_TITLE'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_SIGNATURE_LIST_TITLE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_EXPORT_SIGNATURE_TITLE"); ?>
						</label>
					</td>
					<td>
						<input type="text" maxlength="255" id="subject_5" name="subject[5]" class="input"
						       value="<?php echo $this->templates[4]->subject; ?>" style="width: 350px;" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_text_5" title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_SIGNATURE_HEADING'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_SIGNATURE_HEADING'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_EXPORT_SIGNATURE_HEADING"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$editor = JFactory::getEditor();
						echo $editor->display("value_text[5]", $this->templates[4]->value_text, 800, 300, 40, 20, false, "value_text_5");
						?>
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_5"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_SIGNATURE_LINE'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_SIGNATURE_LINE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TEMPLATE_EXPORT_SIGNATURE_LINE"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$editor = JFactory::getEditor();
						echo $editor->display("value[5]", $this->templates[4]->value, 800, 150, 40, 20, false, "value_5");
						?>
					</td>
				</tr>
			</table>
		</fieldset>
		<br />
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_PARTICIPANTS_LIST'); ?></legend>
			<table class="table">
				<tr>
					<td class="key">
						<label for="subject_6"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_PARTICIPANTS_LIST_TITLE'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_PARTICIPANTS_LIST_TITLE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_EXPORT_PARTLIST_TITLE"); ?>
						</label>
					</td>
					<td>
						<input type="text" maxlength="255" id="subject_6" name="subject[6]" class="input"
						       value="<?php echo $this->templates[5]->subject; ?>" style="width: 350px;" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_text_6"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_PARTICIPANTS_HEADING'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_PARTICIPANTS_HEADING'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_EXPORT_PARTLIST_HEADING"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$editor = JFactory::getEditor();
						echo $editor->display("value_text[6]", $this->templates[5]->value_text, 800, 300, 40, 20, false, "value_text_6");
						?>
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_6" title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_PARTICIPANTS_SINGLE'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_EXPORT_PARTICIPANTS_SINGLE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_EXPORT_PARTLIST_SINGLE"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$editor = JFactory::getEditor();
						echo $editor->display("value[6]", $this->templates[5]->value, 800, 300, 40, 20, false, "value_6");
						?>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>

<div class="clr"></div>
