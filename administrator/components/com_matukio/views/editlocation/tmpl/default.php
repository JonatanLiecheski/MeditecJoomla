<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       06.10.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      2.2.0
 */

defined('_JEXEC') or die('Restricted access');

$editor = JFactory::getEditor();

if (JVERSION > 3)
{
	JHTML::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');

	// Load select style
	JHTML::_('behavior.multiselect');
}

// Load formvalidator!
JHtml::_('behavior.formvalidation');

echo CompojoomHtmlCtemplate::getHead(
	MatukioHelperUtilsBasic::getMenu(), 'locations', 'COM_MATUKIO_EDIT_LOCATION', 'COM_MATUKIO_SLOGAN_EDIT_LOCATION'
);

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
			if (task == 'cancel' || document.formvalidator.isValid(document.id('location-form')))
			{
				Joomla.submitform(task, document.getElementById('location-form'));
			}
		}
	</script>
	<div class="box-info full">
	<div id="matukio" class="matukio">
		<form action="index.php" method="post" name="adminForm" id="location-form" class="form-validate form-horizontal" enctype="multipart/form-data">
			<div class="table-responsive">
				<table class="admintable table">
					<?php if($this->location->id) :?>
					<tr>
						<td width="150" align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_ID'); ?>:
						</td>
						<td>
							<?php echo $this->location->id; ?>
						</td>
					</tr>
					<?php endif; ?>
					<tr>
						<td align="left" class="key">
							<label id="title-lbl" for="title" class="required">
								<?php echo JText::_('COM_MATUKIO_LOCATION_TITLE'); ?>:
							</label>
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_LOCATION_TITLE"); ?>
						</td>
						<td>
							<input class="input-large required" type="text" name="title" id="title" maxlength="250"
							       value="<?php echo $this->location->title; ?>" required="required" />
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<label id="location-lbl" for="location" class="required">
								<?php echo JText::_('COM_MATUKIO_LOCATION'); ?>:
							</label>
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_LOCATION"); ?>
						</td>
						<td>
							<input class="input-xxlarge required" type="text" name="location" id="location" maxlength="250"
							       value="<?php echo $this->location->location; ?>" required="required" />
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_LOCATION_GOOGLE_MAPS'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_LOCATION_GOOGLE_MAPS"); ?>
						</td>
						<td>
							<input class="input-xxlarge" type="text" name="gmaploc" id="gmaploc" maxlength="250"
							       value="<?php echo $this->location->gmaploc; ?>"/>
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_PHONE'); ?>:
						</td>
						<td>
							<input class="input-medium" type="text" name="phone" id="phone" maxlength="250"
							       value="<?php echo $this->location->phone; ?>"/>
						</td>
					</tr>

					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_EMAIL'); ?>:
						</td>
						<td>
							<input class="input-large" type="text" name="email" id="email" size="50" maxlength="250"
							       value="<?php echo $this->location->email; ?>"/>
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_WEBSITE'); ?>:
						</td>
						<td>
							<input class="input-large" type="text" name="website" id="website" size="50" maxlength="250"
							       value="<?php echo $this->location->website; ?>"/>
						</td>
					</tr>


					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_IMAGE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_LOCATION_IMAGE"); ?>
						</td>
						<td>
							<?php echo JHTML::_('list.images', 'image', $this->location->image, null, 'images/'); ?>
						</td>
					</tr>

					<tr>
						<td colspan="2">
							<?php echo JText::_('COM_MATUKIO_DESCRIPTION'); ?>:<br/>
							<?php
							$editor = JFactory::getEditor();
							echo $editor->display("description", $this->location->description, 800, 400, 40, 20, 1);
							?>
						</td>
					</tr>

					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_COMMENTS'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_LOCATION_COMMENTS"); ?>
						</td>
						<td>
							<textarea class="text_area" cols="20" rows="5" name="comments" id="comments"
							          style="width:550px"><?php echo $this->location->comments; ?></textarea>
						</td>
					</tr>
				</table>

			</div>
			<input type="hidden" name="id" value="<?php echo $this->location->id; ?>"/>
			<input type="hidden" name="option" value="com_matukio"/>
			<input type="hidden" name="controller" value="locations"/>
			<input type="hidden" name="view" value="editLocation"/>
			<input type="hidden" name="model" value="editLocation"/>
			<input type="hidden" name="task" value="editLocation"/>

			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div>
</div>

<?php
// Footer
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
