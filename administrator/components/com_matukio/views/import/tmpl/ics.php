<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       02.03.14
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      3.1.0
 */

defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="AdminForm table" id="ICSForm" enctype="multipart/form-data">
	<div class="mat_email">
		<div id="mat_email">
			<fieldset class="adminform">
				<table class="admintable table">
					<tr>
						<td class="key" width="200px">
							<label for="ics_file" title="<?php echo JText::_('COM_MATUKIO_IMPORT_ICS_FILE'); ?>">
								<?php echo JText::_('COM_MATUKIO_IMPORT_ICS_FILE'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_IMPORT_ICS_FILE"); ?>
							</label>
						</td>
						<td>
							<input type="file" id="ics_file" name="ics_file" class="input" />
						</td>
					</tr>
					<tr>
						<td class="key" width="200px">
							<label for="catid" title="<?php echo JText::_('COM_MATUKIO_CATEGORY'); ?>">
								<?php echo JText::_('COM_MATUKIO_CATEGORY'); ?>:
							</label>
						</td>
						<td>
							<?php
							$catlist = MatukioHelperUtilsEvents::getCategoryListArray(0);
							echo $catlist[0];
							?>
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
	<input type="hidden" name="task" value="importics"/>
	<input type="hidden" name="view" value="import"/>
	<input type="hidden" name="controller" value="import"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
