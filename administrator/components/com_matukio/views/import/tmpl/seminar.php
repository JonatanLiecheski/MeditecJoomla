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
<form action="index.php" method="post" name="AdminForm table" id="AdminForm" enctype="multipart/form-data">
	<div class="mat_email">
		<div id="mat_email">
			<fieldset class="adminform">
				<table class="admintable table">
					<tr>
						<td class="key" width="200px">
							<label for="seminar_table" title="<?php echo JText::_('COM_MATUKIO_IMPORT_SEMINAR_TABLE_NAME'); ?>">
								<?php echo JText::_('COM_MATUKIO_IMPORT_SEMINAR_TABLE_NAME'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_IMPORT_SEMINAR_TABLE_NAME"); ?>
							</label>
						</td>
						<td>
							<input type="text" maxlength="255" id="seminar_table" name="seminar_table" size="60" class="input"
							       value="jos_seminar" />
						</td>
					</tr>
					<tr>
						<td class="key" width="200px">
							<label for="seminar_booking_table" title="<?php echo JText::_('COM_MATUKIO_IMPORT_SEMINAR_CATEGORIES_TABLE'); ?>">
								<?php echo JText::_('COM_MATUKIO_IMPORT_SEMINAR_CATEGORIES_TABLE'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_IMPORT_SEMINAR_CATEGORIES_TABLE"); ?>
							</label>
						</td>
						<td>
							<input type="text" maxlength="255" id="seminar_category_table" name="seminar_category_table"
							       size="60" class="input"
							       value="jos_categories" />
						</td>
					</tr>
					<tr>
						<td class="key" width="200px">
							<label for="seminar_booking_table" title="<?php echo JText::_('COM_MATUKIO_IMPORT_SEMINAR_BOOKING_TABLE'); ?>">
								<?php echo JText::_('COM_MATUKIO_IMPORT_SEMINAR_BOOKING_TABLE'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_IMPORT_SEMINAR_BOOKING_TABLE"); ?>
							</label>
						</td>
						<td>
							<input type="text" maxlength="255" id="seminar_booking_table" name="seminar_booking_table"
							       size="60" class="input"
							       value="jos_sembookings" />
						</td>
					</tr>
					<tr>
						<td class="key" width="200px">
								<label for="seminar_table" title="<?php echo JText::_('COM_MATUKIO_IMPORT_SEMINAR_NUMBER_TABLE'); ?>">
								<?php echo JText::_('COM_MATUKIO_IMPORT_SEMINAR_NUMBER_TABLE'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_IMPORT_SEMINAR_NUMBER_TABLE"); ?>
							</label>
						</td>
						<td>
							<input type="text" maxlength="255" id="seminar_number_table" name="seminar_number_table"
							       size="60" class="input"
							       value="jos_semnumber" />
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<div align="right">
								<input type="submit" class="mat_button btn btn-primary"
								       value="<?php echo JText::_("COM_MATUKIO_IMPORT"); ?>" />
							</div>
						</td>
					</tr>

				</table>
			</fieldset>
		</div>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="option" value="com_matukio"/>
	<input type="hidden" name="task" value="importseminar"/>
	<input type="hidden" name="view" value="import"/>
	<input type="hidden" name="controller" value="import"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
