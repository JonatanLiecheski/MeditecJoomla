<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       04.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      3.0.0
 */

defined('_JEXEC') or die('Restricted access');

$editor = JFactory::getEditor();

if (JVERSION >= 3)
{
	JHTML::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');
}

JHTML::_('behavior.calendar');

// Load formvalidator!
JHtml::_('behavior.formvalidation');

// Load bootstrap in Joomla 2.5

echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'differentfees', 'COM_MATUKIO_EDIT_FEE', 'COM_MATUKIO_SLOGAN_EDIT_FEE');

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
								<?php echo JText::_('COM_MATUKIO_FEE_TITLE'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_FEE_TITLE"); ?>
							</td>
							<td>
								<input class="input text_area" type="text" name="title" id="title" size="50" maxlength="250"
								       value="<?php echo $this->fee->title; ?>"/>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('COM_MATUKIO_VALUE'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_FEE_VALUE"); ?>
							</td>
							<td>
								<input class="input text_area" type="text" size="10" maxlength="15" name="value" id="value"
								       value="<?php echo $this->fee->value; ?>"/>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('COM_MATUKIO_PERCENT'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_PERCENT"); ?>
							</td>
							<td>
									<?php echo $this->select_percent; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('COM_MATUKIO_FEE_DISCOUNT'); ?>:
								<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_FEE_DISCOUNT"); ?>
							</td>
							<td>
									<?php echo $this->select_discount; ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('COM_MATUKIO_PUBLISHED_UP'); ?>:
							</td>
							<td>
								<?php echo JHTML::_('calendar', $this->fee->published_up, 'published_up', 'published_up'); ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('COM_MATUKIO_PUBLISHED_DOWN'); ?>:
							</td>
							<td>
								<?php echo JHTML::_('calendar', $this->fee->published_down, 'published_down', 'published_down'); ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('COM_MATUKIO_PUBLISHED'); ?>:
							</td>
							<td>
									<?php echo $this->select_published; ?>
							</td>
						</tr>
					</table>

				</div>
				<input type="hidden" name="id" value="<?php echo $this->fee->id; ?>"/>

				<input type="hidden" name="option" value="com_matukio" />
				<input type="hidden" name="controller" value="differentfees" />
				<input type="hidden" name="view" value="editfee" />
				<input type="hidden" name="model" value="editfee" />
				<input type="hidden" name="task" value="editfee" />
				<?php echo JHTML::_('form.token'); ?>
			</form>
		</div>
	</div>

	<div class="clr"></div>
<?php
// Footer
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
