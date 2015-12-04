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
		<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_TICKET'); ?></legend>
		<table class="table">
			<tr>
				<td class="key" colspan="2">
					<label for="value_8" title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_TICKET'); ?>">
						<?php echo JText::_('COM_MATUKIO_TEMPLATE_TICKET_PDF'); ?>:
						<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_TICKET_TEMPLATE"); ?>
					</label>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php
					$editor = JFactory::getEditor();
					echo $editor->display("value[10]", $this->templates[9]->value, 800, 300, 40, 20, false, "value_10");
					?>
					<input type="hidden" name="subject[10]" value="E" />
					<input type="hidden" name="value_text[10]" value="" />
				</td>
			</tr>
		</table>
	</fieldset>
</div>

<div class="clr"></div>
