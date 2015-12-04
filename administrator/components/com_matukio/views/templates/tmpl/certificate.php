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
<div class="mat_templates">
	<div id="mat_certificate">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_MATUKIO_TEMPLATE_CERTIFICATE'); ?></legend>
			<table class="table">
				<tr>
					<td class="key" colspan="2">
						<label for="value_7" width="100"
						       title="<?php echo JText::_('COM_MATUKIO_TEMPLATE_CERTIFICATE_CODE'); ?>">
							<?php echo JText::_('COM_MATUKIO_TEMPLATE_CERTIFICATE_CODE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_TEMPLATE_CERTIFICATE_CODE"); ?>
						</label>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<?php
						$editor = JFactory::getEditor();
						echo $editor->display("value[7]", $this->templates[6]->value, 800, 400, 40, 20, false, "value_7");
						?>

						<input type="hidden" name="subject[7]" value="E"/>
						<input type="hidden" name="value_text[7]" value=""/>
					</td>
				</tr>
			</table>
		</fieldset>
	</div>
</div>

<div class="clr"></div>
