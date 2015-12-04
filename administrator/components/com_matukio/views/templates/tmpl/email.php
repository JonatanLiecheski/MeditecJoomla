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
<div class="mat_email_templates">
	<div id="mat_email">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING'); ?></legend>
			<table class=" table">
				<tr>
					<td class="key">
						<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_SUBJECT'); ?>:
						<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_BOOKING_SUBJECT"); ?>
					</td>
					<td>
						<input type="text" maxlength="255" id="subject_1" name="subject[1]" class="input"
						       value="<?php echo $this->templates[0]->subject; ?>" style="width: 350px;" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_1" title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_HTMLTEXT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_HTMLTEXT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_BOOKING_HTMLTEXT"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$editor = JFactory::getEditor();
						echo $editor->display("value[1]", $this->templates[0]->value, 800, 300, 40, 20, false, "value_1");
						?>
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_text_1" 
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_TEXT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_TEXT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_BOOKING_TEXT"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea rows="10" cols="80" name="value_text[1]" id="value_text_1"
						          style="width: auto; min-width: 400px;"><?php echo $this->templates[0]->value_text; ?></textarea>
					</td>
				</tr>
			</table>
		</fieldset>

		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_CANCELATION_ADMIN'); ?></legend>
			<table class="table">
				<tr>
					<td class="key">
						<label for="subject_2"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_SUBJECT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_SUBJECT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_CANCEL_SUBJECT"); ?>
						</label>
					</td>
					<td>
						<input type="text" maxlength="255" id="subject_2" name="subject[2]" size="60"
						       value="<?php echo $this->templates[1]->subject; ?>" style="width: 350px;" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_2" title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_CANCEL_HTMLTEXT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_CANCEL_HTMLTEXT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_CANCEL_HTMLTEXT"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$editor = JFactory::getEditor();
						echo $editor->display("value[2]", $this->templates[1]->value, 800, 300, 40, 20, false, "value_2");
						?>
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_text_2"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_CANCEL_TEXT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_CANCEL_TEXT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_CANCEL_TEXT"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea rows="10" cols="80" name="value_text[2]" id="value_text_2"
						          style="min-width: 400px; width: auto;"><?php echo $this->templates[1]->value_text; ?></textarea>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_CANCELATION'); ?></legend>
			<table class="table">
				<tr>
					<td class="key">
						<label for="subject_3" title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_SUBJECT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_SUBJECT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_CANCEL_USER_SUBJECT"); ?>
						</label>
					</td>
					<td>
						<input type="text" maxlength="255" id="subject_3" name="subject[3]" size="60"
						       value="<?php echo $this->templates[2]->subject; ?>" style="width: 350px;" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_3"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_CANCEL_HTMLTEXT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_CANCEL_HTMLTEXT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_CANCEL_USER_HTMLTEXT"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$editor = JFactory::getEditor();
						echo $editor->display("value[3]", $this->templates[2]->value, 800, 300, 40, 20, false, "value_3");
						?>
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_text_3"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_CANCEL_TEXT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_BOOKING_CANCEL_TEXT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_CANCEL_USER_TEXT"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea rows="10" cols="80" name="value_text[3]" id="value_text_3"
						          style="min-width: 400px; width: auto;"><?php echo $this->templates[2]->value_text; ?></textarea>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_NEW_EVENT'); ?></legend>
			<table class="table">
				<tr>
					<td class="key">
						<label for="subject_12" title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_NEW_EVENT_SUBJECT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_NEW_EVENT_SUBJECT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_NEW_EVENT_SUBJECT"); ?>
						</label>
					</td>
					<td>
						<input type="text" maxlength="255" id="subject_12" name="subject[12]" size="60"
						       value="<?php echo $this->templates[11]->subject; ?>" style="width: 350px;" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_12"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_NEW_EVENT_HTMLTEXT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_NEW_EVENT_HTMLTEXT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_NEW_EVENT_HTMLTEXT"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$editor = JFactory::getEditor();
						echo $editor->display("value[12]", $this->templates[11]->value, 800, 300, 40, 20, false, "value_12");
						?>
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_text_12"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_NEW_EVENT_TEXT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_NEW_EVENT_TEXT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_NEW_EVENT_TEXT"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea rows="10" cols="80" name="value_text[12]" id="value_text_12"
						          style="min-width: 400px; width: auto;"><?php echo $this->templates[11]->value_text; ?></textarea>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_CRONJOB_REMINDER'); ?></legend>
			<table class="table">
				<tr>
					<td class="key">
						<label for="subject_11" title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_CRONJOB_REMINDER_SUBJECT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_CRONJOB_REMINDER_SUBJECT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_CRONJOB_REMINDER_SUBJECT"); ?>
						</label>
					</td>
					<td>
						<input type="text" maxlength="255" id="subject_11" name="subject[11]" size="60"
						       value="<?php echo $this->templates[10]->subject; ?>" style="width: 350px;" />
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_11"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_CRONJOB_REMINDER_HTMLTEXT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_CRONJOB_REMINDER_HTMLTEXT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_CRONJOB_REMINDER_HTMLTEXT"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$editor = JFactory::getEditor();
						echo $editor->display("value[11]", $this->templates[10]->value, 800, 300, 40, 20, false, "value_11");
						?>
					</td>
				</tr>
				<tr>
					<td class="key" colspan="2">
						<label for="value_text_11"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_CRONJOB_REMINDER_TEXT_DESC'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_MAIL_CRONJOB_REMINDER_TEXT'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_MAIL_CRONJOB_REMINDER_TEXT"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<textarea rows="10" cols="80" name="value_text[11]" id="value_text_11"
						          style="min-width: 400px; width: auto;"><?php echo $this->templates[10]->value_text; ?></textarea>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
</div>

<div class="clr"></div>
