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
}

// Load formvalidator!
JHtml::_('behavior.formvalidation');

JHTML::_('behavior.calendar');
JHTML::_('behavior.tooltip');

// Load select style
JHTML::_('behavior.multiselect');

echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'organizers', 'COM_MATUKIO_EDIT_ORGANIZER', 'COM_MATUKIO_SLOGAN_EDIT_ORGANIZER');

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

// Small css fixes
JFactory::getDocument()->addStyleDeclaration('
		.form-horizontal .control-label {padding-top: 7px;}
		label {display: inline;}
');
?>
<div class="box-info full">
	<div id="matukio" class="matukio">
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="form" enctype="multipart/form-data">
			<div class="table-responsive">
				<table class="admintable table">
					<tr>
						<td width="200" align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_ID'); ?>:
						</td>
						<td>
							<?php echo $this->organizer->id; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('COM_MATUKIO_USER'); ?>* :
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_ORGANIZER_USERID"); ?>
						</td>
						<td>
							<?php
							// Users ($name, $active, $nouser=0, $javascript=NULL, $order= 'name', $reg=1)
							echo JHTML::_('list.users', "userId", $this->organizer->userId, false, null, "name", 0);
							?>
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_OVERRIDE_NAME'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_ORGANIZER_NAME_OVERRIDE"); ?>
						</td>
						<td>
							<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250"
							       value="<?php echo $this->organizer->name; ?>"/>
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_OVERRIDE_EMAIL'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_ORGANIZER_EMAIL_OVERRIDE"); ?>
						</td>
						<td>
							<input class="text_area" type="text" name="email" id="email" size="50" maxlength="250"
							       value="<?php echo $this->organizer->email; ?>"/>
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_WEBSITE'); ?>:
						</td>
						<td>
							<input class="text_area" type="text" name="website" id="website" size="50" maxlength="250"
							       value="<?php echo $this->organizer->website; ?>"/>
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_PHONE'); ?>:
						</td>
						<td>
							<input class="text_area" type="text" name="phone" id="phone" size="50" maxlength="250"
							       value="<?php echo $this->organizer->phone; ?>"/>
						</td>
					</tr>

					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_IMAGE'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_ORGANIZER_IMAGE"); ?>
						</td>
						<td>
							<?php echo JHTML::_('list.images', 'image', $this->organizer->image, null, 'images/'); ?>
						</td>
					</tr>

					<tr>
						<td colspan="2">
							<?php echo JText::_('COM_MATUKIO_DESCRIPTION'); ?>:<br/>
							<?php
							$editor = JFactory::getEditor();
							echo $editor->display("description", $this->organizer->description, 800, 400, 40, 20, 1);
							?>
						</td>
					</tr>

					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_COMMENTS'); ?>:
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_ORGANIZER_COMMENTS"); ?>
						</td>
						<td>
							<textarea class="text_area" cols="20" rows="5" name="comments" id="comments"
							          style="width:550px"><?php echo $this->organizer->comments; ?></textarea>
						</td>
					</tr>
				</table>

			</div>
			<input type="hidden" name="id" value="<?php echo $this->organizer->id; ?>"/>
			<input type="hidden" name="option" value="com_matukio"/>
			<input type="hidden" name="controller" value="organizers"/>
			<input type="hidden" name="view" value="editorganizer"/>
			<input type="hidden" name="model" value="editorganizer"/>
			<input type="hidden" name="task" value="editorganizer"/>

			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div>
</div>

<?php
// Footer
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
