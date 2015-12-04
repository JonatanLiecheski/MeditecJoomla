<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       25.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filter.output');

if (JVERSION > 3)
{
	JHTML::_('bootstrap.tooltip');
	JHtml::_('formbehavior.chosen', 'select');
}
else
{
	JHTML::_('behavior.tooltip');
}

JHTML::_('behavior.multiselect');

// Import for certificate printing
JHTML::_('behavior.modal');

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$filterStatus = $this->escape($this->state->get('filter.status'));
$filtertime = $this->escape($this->state->get('filter.time'));
$filterlimit = $this->escape($this->state->get('filter.limit'));

// Load the menu including bootstrap etc
echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'bookings', 'COM_MATUKIO_BOOKINGS', 'COM_MATUKIO_SLOGAN_BOOKINGS');

JFactory::getDocument()->addStyleDeclaration(
	'.tcenter {text-align: center !important; }'
);
?>
	<form action="<?php echo JRoute::_("index.php?option=com_matukio&view=bookings"); ?>" method="post"
	      name="adminForm" id="adminForm">
	<div class="box-info full">
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search fltlft btn-group pull-left">
			<label class="filter-search-lbl element-invisible"
			       for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search"
			       value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
			       title="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>"
			       placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>"/>
		</div>
		<div class="btn-group pull-left hidden-phone">
			<?php if (JVERSION > 2.5) : ?>
				<button class="btn" type="submit"><i class="icon-search"></i></button>
				<button class="btn" type="button"
				        onclick="document.id('filter_search').value='';this.form.submit();"><i
						class="icon-remove"></i>
				</button>
			<?php else : ?>
				<button class="btn" type="submit"
				        style="margin:0"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button class="btn" type="button" style="margin:0"
				        onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			<?php endif; ?>
		</div>

		<div class="filter-select fltrt pull-right">
			<?php echo $this->events; ?>
		</div>

		<div class="filter-select fltrt pull-right">
			<select name="filter_status" class="inputbox chzn-single" onchange="jQuery('#task').val(''); this.form.submit()">
				<?php
				$subpen = $subact = $subwait = $subarch = $subdel = $subpaid = $subunpaid = $subactpen = $suball = "";

				if ($filterStatus == "active")
				{
					$subact = ' selected="selected" ';
				}
				elseif ($filterStatus == "activeandpending")
				{
					$subactpen = ' selected="selected" ';
				}
				elseif ($filterStatus == "pending")
				{
					$subpen = ' selected="selected "';
				}
				elseif ($filterStatus == "waitlist")
				{
					$subwait = ' selected="selected "';
				}
				elseif ($filterStatus == "archived")
				{
					$subarch = ' selected="selected "';
				}
				elseif ($filterStatus == "deleted")
				{
					$subdel = ' selected="selected "';
				}
				elseif ($filterStatus == "paid")
				{
					$subpaid = ' selected="selected "';
				}
				elseif ($filterStatus == "unpaid")
				{
					$subunpaid = ' selected="selected" ';
				}
				elseif ($filterStatus == "all")
				{
					$suball = ' selected="selected" ';
				}
				?>
				<option value="all" <?php echo $suball; ?>>
					<?php echo JText::_('COM_MATUKIO_ALL_BOOKINGS'); ?>
				</option>
				<option value="activeandpending" <?php echo $subactpen; ?>>
					<?php echo JText::_('COM_MATUKIO_ACTIVE_AND_PENDING'); ?>
				</option>
				<option value="active" <?php echo $subact; ?>>
					<?php echo JText::_('COM_MATUKIO_ACTIVE'); ?>
				</option>
				<option value="pending" <?php echo $subpen; ?>>
					<?php echo JText::_('COM_MATUKIO_PENDING'); ?>
				</option>
				<option value="waitlist" <?php echo $subwait; ?>>
					<?php echo JText::_('COM_MATUKIO_WAITLIST'); ?>
				</option>
				<option value="archived" <?php echo $subarch; ?>>
					<?php echo JText::_('COM_MATUKIO_ARCHIVED'); ?>
				</option>
				<option value="deleted" <?php echo $subdel; ?>>
					<?php echo JText::_('COM_MATUKIO_DELETED'); ?>
				</option>
				<option value="paid" <?php echo $subpaid; ?>>
					<?php echo JText::_('COM_MATUKIO_PAID'); ?>
				</option>
				<option value="unpaid" <?php echo $subunpaid; ?>>
					<?php echo JText::_('COM_MATUKIO_UNPAID'); ?>
				</option>
			</select>
		</div>

		<div class="filter-select fltrt pull-right">
			<select name="filter_time" class="inputbox chzn-single" onchange="jQuery('#task').val(''); this.form.submit()">
				<?php
				$timeday = $timeweek = $timemonth = $timeyear = $timeall = "";

				if ($filtertime == "day")
				{
					$timeday = ' selected="selected" ';
				}
				elseif ($filtertime == "week")
				{
					$timeweek = ' selected="selected "';
				}
				elseif ($filtertime == "month")
				{
					$timemonth = ' selected="selected "';
				}
				elseif ($filtertime == "year")
				{
					$timeyear = ' selected="selected" ';
				}
				elseif ($filtertime == "all")
				{
					$timeall = ' selected="selected" ';
				}
				?>
				<option value="all" <?php echo $timeall; ?>>
					<?php echo JText::_('COM_MATUKIO_ALL_TIME'); ?>
				</option>
				<option value="day" <?php echo $timeday; ?>>
					<?php echo JText::_('COM_MATUKIO_LAST_DAY'); ?>
				</option>
				<option value="week" <?php echo $timeweek; ?>>
					<?php echo JText::_('COM_MATUKIO_LAST_WEEK'); ?>
				</option>
				<option value="month" <?php echo $timemonth; ?>>
					<?php echo JText::_('COM_MATUKIO_LAST_MONTH'); ?>
				</option>
				<option value="year" <?php echo $timeyear; ?>>
					<?php echo JText::_('COM_MATUKIO_LAST_YEAR'); ?>
				</option>
			</select>
		</div>

		<div class="filter-select fltrt pull-right">
			<?php
				echo $this->pagination->getLimitBox();
			?>
		</div>

	</div>
	<div class="clr"></div>

	<div class="table-responsive">
	<table class="table table-hover table-striped">
		<thead>
		<tr>
			<th width="5">#</th>
			<th width="5">
				<input type="checkbox" name="checkall-toggle" value=""
				       title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
				       onclick="Joomla.checkAll(this);"/>
			</th>
			<th class="title">
				<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_NAME', 'e.name', $listDirn, $listOrder); ?>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_EMAIL', 'e.email', $listDirn, $listOrder); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_MATUKIO_EVENT'); ?>
			</th>
			<th>
				<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_DATE_OF_BOOKING', 'e.bookingdate', $listDirn, $listOrder); ?>
			</th>
			<th width="5%" nowrap="nowrap" style="text-align: center">
				<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_BOOKED_PLACES', 'e.nrbooked', $listDirn, $listOrder); ?>
			</th>
			<th width="5%" nowrap="nowrap" style="text-align: center">
				<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_PAID', 'e.paid', $listDirn, $listOrder); ?>
			</th>
			<th width="5%" nowrap="nowrap" style="text-align: center">
				<?php echo JText::_('COM_MATUKIO_RATING'); ?>
			</th>
			<?php if (MatukioHelperSettings::getSettings('participant_grading_system', 0)) : ?>
			<th width="5%" nowrap="nowrap" style="text-align: center">
				<?php echo JText::_('COM_MATUKIO_PARTICIPANT_MARK'); ?>
			</th>
			<?php endif; ?>
			<th width="5%" nowrap="nowrap" style="text-align: center">
				<?php echo JText::_('COM_MATUKIO_CERTIFICATE'); ?>
			</th>
			<th width="8%" nowrap="nowrap">
				<?php echo JText::_('COM_MATUKIO_INVOICE'); ?>
			</th>
			<th>
				<?php echo JText::_('COM_MATUKIO_COMMENT'); ?>
			</th>
			<th width="5%" style="text-align: center">
				<?php echo JText::_('COM_MATUKIO_STATUS'); ?>
			</th>
			<th width="8%">
				<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_ID', 'e.id', $listDirn, $listOrder); ?>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ($this->items as $i => $l)
		{
			if ($l->userid == 0)
			{
				$l->name = $l->aname;
				$l->email = $l->aemail;
			}

			$checked = JHTML::_('grid.id', $i, $l->id);

			$link = JRoute::_('index.php?option=com_matukio&controller=editbooking&task=editBooking&booking_id=' . $l->id);

			$bookingdate = JHTML::_('date', $l->bookingdate, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'))
				. ", " . JHTML::_('date', $l->bookingdate, MatukioHelperSettings::getSettings('time_format', 'H:i'));

			$eventdate = JHTML::_('date', $l->eventbegin, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'))
				. " " . JHTML::_('date', $l->eventbegin, MatukioHelperSettings::getSettings('time_format', 'H:i'));

			$bstatus = MatukioHelperUtilsBasic::getBStatusImage($l);

			$paid_image = MatukioHelperUtilsBasic::getPaidStatus($l);

			$cert_image = MatukioHelperUtilsBasic::getCertificateImage($l);

			$rating_image = MatukioHelperUtilsBasic::getBRatingImage($l);

			$invoice_image = MatukioHelperUtilsBasic::getInvoiceImage($l);
			?>
			<tr class="<?php echo "row" . $i % 2; ?>">
				<td>
					<?php echo $this->pagination->getRowOffset($i); ?>
				</td>
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<a href="<?php echo $link; ?>">
						<?php echo $l->name; ?>
					</a>
				</td>
				<td>
					<a href="mailto:<?php echo $l->email; ?>" title="<?php echo $l->name; ?>">
						<?php echo $l->email; ?>
					</a>
				</td>
				<td>
					<?php echo $l->eventtitle . " " . $eventdate; ?>
				</td>
				<td>
					<?php echo $bookingdate; ?>
				</td>
				<td style="text-align: center">
					<?php echo $l->nrbooked; ?>
				</td>
				<td style="text-align: center">
					<?php echo $paid_image; ?>
				</td>
				<td style="text-align: center">
					<?php echo $rating_image ?>
				</td>
				<?php if (MatukioHelperSettings::getSettings('participant_grading_system', 0)) : ?>
				<td style="text-align: center">
					<?php echo ($l->mark) ? $l->mark : ""; ?>
				</td>
				<?php endif; ?>
				<td style="text-align: center">
					<?php echo $cert_image; ?>
				</td>
				<td>
					<?php echo $invoice_image; ?>
				</td>
				<td>
					<?php echo $l->comment ?>
				</td>
				<td style="text-align: center">
					<?php echo $bstatus; ?>
				</td>
				<td>
					<?php echo MatukioHelperUtilsBooking::getBookingId($l->id) . " (" . $l->id . ")"; ?>
				</td>
			</tr>
			<?php
			$i++;
		}
		?>

	</table>
	</div>
	</div>
	<table>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="14"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
		</tfoot>
	</table>

	<input type="hidden" name="task" id="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn ?>"/>

	<?php echo JHTML::_('form.token'); ?>
	</form>

	<div class="clear"></div>

<?php
// Description of the icons
$imgpath = MatukioHelperUtilsBasic::getComponentImagePath()
?>
	<table class="admintable table tcenter" style="width: 100%; margin-bottom: 20px; ">
		<tr>
			<thead>
			<th width="25%">
				<?php
				echo "<img src=\"" . $imgpath . "pending.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_PENDING');
				?>
			</th>
			<th width="25%">
				<?php
				echo "<img src=\"" . $imgpath . "2502.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_PARTICIPANT_ASSURED');
				?>
			</th>
			<th width="25%">
				<?php
				echo "<img src=\"" . $imgpath . "2501.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_WAITLIST');
				?>
			</th>
			<th width="25%">
				<?php
				echo "<img src=\"" . $imgpath . "2500.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_NO_SPACE_AVAILABLE') . " / " . JTEXT::_('COM_MATUKIO_DELETED_ARCHIVED');
				?>
			</th>
			</thead>
		</tr>
	</table>

	<div class="clear"></div>
<?php
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
