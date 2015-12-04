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

// Load bootstrap in J 2.5 and strapper.css
echo CompojoomHtmlCtemplate::getHead(
	MatukioHelperUtilsBasic::getMenu(), 'recurring', 'COM_MATUKIO_EDIT_RECURRING_EVENT', 'COM_MATUKIO_SLOGAN_EDIT_RECURRING_EVENT'
);

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

// Small css fixes
JFactory::getDocument()->addStyleDeclaration('
		.form-horizontal .control-label {padding-top: 7px;}
		label {display: inline; clear: none !important;}
');
?>
	<script type="text/javascript">
		Joomla.submitbutton = function(task)
		{
			if (task == 'cancel' || document.formvalidator.isValid(document.id('recurring-form')))
			{
				Joomla.submitform(task, document.getElementById('recurring-form'));
			}
		}
	</script>
	<div class="box-info full">
	<div id="matukio" class="matukio">
		<form action="index.php" method="post" name="recurringForm" id="recurring-form" class="form-validate form-horizontal" enctype="multipart/form-data">
			<div class="table-responsive">
				<table class="admintable table">
					<tr>
						<td width="150" align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_RECURRING_ID'); ?>:
						</td>
						<td>
							<?php echo $this->recurring->id; ?>
						</td>
					</tr>
					<tr>
						<td width="150" align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_EVENT'); ?>:
						</td>
						<td>
							<?php
							echo $this->event_select;
							?>
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<label id="semnum-lbl" for="semnum" class="required">
								<?php echo JText::_('COM_MATUKIO_RECURRING_SEMNUM'); ?>:
							</label>
							<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_RECURRING_SEMNUM"); ?>
						</td>
						<td>
							<input class="input-large required" type="text" name="semnum" id="semnum" maxlength="250"
							       value="<?php echo $this->recurring->semnum; ?>" required="required" />
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<label id="_begin_date-lbl" for="_begin_date" class="required">
								<?php echo JText::_('COM_MATUKIO_BEGIN'); ?>:
								<?php echo MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_DATE_TIME_FORMAT')); ?>
							</label>
						</td>
						<td>
							<?php
							echo JHTML::_('calendar', JHtml::_('date', $this->recurring->begin, 'Y-m-d H:i:s'), '_begin_date', '_begin_date',
								'%Y-%m-%d %H:%M:%S', array('class' => 'inputbox required', 'size' => '22', 'required' => 'required', 'aria-required' => 'true')
							);
							?>
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<label id="_end_date-lbl" for="_end_date" class="required">
								<?php echo JText::_('COM_MATUKIO_END'); ?>:
								<?php echo MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_DATE_TIME_FORMAT')); ?>
							</label>
						</td>
						<td>
							<?php
							echo JHTML::_('calendar', JHtml::_('date', $this->recurring->end, 'Y-m-d H:i:s'), '_end_date', '_end_date',
								'%Y-%m-%d %H:%M:%S', array('class' => 'inputbox required', 'size' => '22', 'required' => 'required', 'aria-required' => 'true')
							);
							?>
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<label id="_booked_date-lbl" for="_booked_date" class="required">
								<?php echo JText::_('COM_MATUKIO_CLOSING_DATE'); ?>:
								<?php echo MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_DATE_TIME_FORMAT')); ?>
							</label>
						</td>
						<td>
							<?php
							echo JHTML::_('calendar', JHtml::_('date', $this->recurring->booked, 'Y-m-d H:i:s'), '_booked_date', '_booked_date',
								'%Y-%m-%d %H:%M:%S', array('class' => 'inputbox required', 'size' => '22', 'required' => 'required', 'aria-required' => 'true')
							);
							?>
						</td>
					</tr>
					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_HITS'); ?>:
						</td>
						<td>
							<input class="input-medium" type="text" name="hits" id="hits" maxlength="11"
							       value="<?php echo $this->recurring->hits; ?>"/>
						</td>
					</tr>
					<!--
					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_CANCELLED'); ?>:
							<?php MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_CANCELLED_EVENT_NO_BOOKINGS')); ?>
						</td>
						<td>
							<?php
							echo MatukioHelperInput::getRadioButtonBool(
								"cancel", "cancel", $this->recurring->cancelled
							);
							?>
						</td>
					</tr>
					-->
					<input type="hidden" name="cancelled" value="<?php echo $this->recurring->cancelled; ?>" />
					<tr>
						<td align="left" class="key">
							<?php echo JText::_('COM_MATUKIO_PUBLISHED'); ?>:
						</td>
						<td>
							<?php
								echo MatukioHelperInput::getRadioButtonBool(
									"published", "published", $this->recurring->published
							);
							?>
						</td>
					</tr>
				</table>

			</div>
			<input type="hidden" name="id" value="<?php echo $this->recurring->id; ?>"/>
			<input type="hidden" name="option" value="com_matukio"/>
			<input type="hidden" name="controller" value="recurring"/>
			<input type="hidden" name="view" value="editRecurring"/>
			<input type="hidden" name="model" value="editRecurring"/>
			<input type="hidden" name="task" value="editRecurring"/>

			<?php echo JHTML::_('form.token'); ?>
		</form>
	</div>
</div>

<?php
// Footer
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
