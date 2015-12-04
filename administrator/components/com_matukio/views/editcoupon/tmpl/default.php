<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       29.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      2.0.0
 */

defined('_JEXEC') or die('Restricted access');

$editor = JFactory::getEditor();

if (JVERSION > 3)
{
	JHTML::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');
}

JHTML::_('behavior.calendar');

// Load formvalidator!
JHtml::_('behavior.formvalidation');

echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'coupons', 'COM_MATUKIO_EDIT_COUPON', 'COM_MATUKIO_SLOGAN_EDIT_COUPON');

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

// Small css fixes
JFactory::getDocument()->addStyleDeclaration('
		.form-horizontal .control-label {padding-top: 7px;}
		label {display: inline;}
');
?>
	<script type="text/javascript">
		Joomla.submitbutton = function (task) {
			if (task == 'cancel' || document.formvalidator.isValid(document.id('adminForm'))) {
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		}
	</script>
	<div class="box-info full">
		<div id="matukio" class="matukio">
			<div class="row">
				<div class="col-lg-9 col-md-8 col-xs-12 col-sm-12">
					<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate"
					      enctype="multipart/form-data">

						<div class="table-responsive">
							<table class="table table-bordered">
								<tr>
									<td width="200" align="left" class="key">
										<?php echo JText::_('COM_MATUKIO_COUPON_CODE'); ?>:
										<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_COUPON_CODE"); ?>
									</td>
									<td>
										<input class="input-xlarge required" type="text" name="code" id="code"
										       maxlength="250" required="required"
										       value="<?php echo $this->coupon->code; ?>"/>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('COM_MATUKIO_VALUE'); ?>:
										<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_COUPON_VALUE"); ?>
									</td>
									<td>
										<input class="input-medium required validate-numeric" type="text"
										       maxlength="15" name="value" id="value" required="required"
										       value="<?php echo $this->coupon->value; ?>"/>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('COM_MATUKIO_HITS_LIMIT'); ?>:
										<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_HITS_LIMIT"); ?>
									</td>
									<td>
										<input class="input-medium required validate-numeric" type="text"
										       maxlength="15" name="max_hits" id="max_hits" required="required"
										       value="<?php echo $this->coupon->max_hits; ?>"/>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('COM_MATUKIO_PERCENT'); ?>:
										<?php echo MatukioHelperInput::getTooltip("COM_MATUKIO_TOOLTIP_PERCENT"); ?>
									</td>
									<td>
										<?php echo $this->select_procent; ?>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('COM_MATUKIO_PUBLISHED_UP'); ?>:
									</td>
									<td>
										<?php echo JHTML::_('calendar', $this->coupon->published_up, 'published_up', 'published_up'); ?>
									</td>
								</tr>
								<tr>
									<td class="key">
										<?php echo JText::_('COM_MATUKIO_PUBLISHED_DOWN'); ?>:
									</td>
									<td>
										<?php echo JHTML::_('calendar', $this->coupon->published_down, 'published_down', 'published_down'); ?>
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
						<input type="hidden" name="id" value="<?php echo $this->coupon->id; ?>"/>

						<input type="hidden" name="option" value="com_matukio" />
						<input type="hidden" name="controller" value="coupons" />
						<input type="hidden" name="view" value="editcoupon" />
						<input type="hidden" name="model" value="editcoupon" />
						<input type="hidden" name="task" value="editcoupon" />
						<?php echo JHTML::_('form.token'); ?>
					</form>
				</div>
				<!-- Informations @since 3.0 -->
				<div class="col-lg-3 col-md-4 col-xs-12 col-sm-12">
					<table class="table table-bordered table-hover">
						<tr class="success">
							<td colspan="2"><?php echo JText::_("COM_MATUKIO_INFORMATIONS"); ?></td>
						</tr>
						<tr>
							<td width="100" align="left" class="key">
								<?php echo JText::_('COM_MATUKIO_ID'); ?>:
							</td>
							<td>
								<?php
								if (!empty($this->coupon->id))
								{
									echo $this->coupon->id;
								}
								?>
							</td>
						</tr>
						<tr>
							<td align="left" class="key">
								<?php echo JText::_('COM_MATUKIO_COUPON_HITS'); ?>:
							</td>
							<td>
								<?php echo $this->coupon->hits; ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div class="clr"></div>
<?php
// Footer
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
