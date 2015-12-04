<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       11.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      3.0.0
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filter.output');
JHTML::_('behavior.multiselect');

if (JVERSION > 2.5)
{
	JHtml::_('formbehavior.chosen', 'select');
}

// Load bootstrap in Joomla 2.5
// Load the menu including bootstrap etc
echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'recurring', 'COM_MATUKIO_RECURRING_DATES', 'COM_MATUKIO_SLOGAN_RECURRING_DATES');

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$filterStatus = $this->escape($this->state->get('filter.status'));

// Small css fixes
JFactory::getDocument()->addStyleDeclaration('
	.form-horizontal .control-label {padding-top: 7px;}
	label {display: inline;}
');
?>
<div class="box-info full">
	<form name="adminForm" id="adminForm" method="post"
	      action="<?php echo JRoute::_('index.php?option=com_matukio&view=recurring'); ?>">

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
				        onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i>
				</button>
			<?php else : ?>
				<button class="btn" type="submit"
				        style="margin:0"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button class="btn" type="button" style="margin:0"
				        onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			<?php endif; ?>
		</div>

		<div class="filter-select fltrt pull-right">
			<?php echo $this->eventfilter; ?>
		</div>

		<div class="filter-select fltrt pull-right">
			<?php echo $this->categories; ?>
		</div>

		<div class="filter-select fltrt pull-right">
			<select name="filter_status" class="inputbox chzn-single" onchange="this.form.submit()">

				<?php
				$subpub = $subunp = $subcur = $subold = $suball = "";

				if ($filterStatus == "published")
				{
					$subpub = ' selected="selected" ';
				}
				elseif ($filterStatus == "unpublished")
				{
					$subunp = ' selected="selected "';
				}
				elseif ($filterStatus == "current")
				{
					$subcur = ' selected="selected "';
				}
				elseif ($filterStatus == "old")
				{
					$subold = ' selected="selected" ';
				}
				elseif ($filterStatus == "all")
				{
					$suball = ' selected="selected" ';
				}
				?>
				<option value="all" <?php echo $suball; ?>>
					<?php echo JText::_('COM_MATUKIO_ALL_DATES'); ?>
				</option>
				<option value="published" <?php echo $subpub; ?>>
					<?php echo JText::_('COM_MATUKIO_PUBLISHED'); ?>
				</option>
				<option value="unpublished" <?php echo $subunp; ?>>
					<?php echo JText::_('COM_MATUKIO_UNPUBLISHED'); ?>
				</option>
				<option value="current" <?php echo $subcur; ?>>
					<?php echo JText::_('COM_MATUKIO_CURRENT_DATES'); ?>
				</option>
				<option value="old" <?php echo $subold; ?>>
					<?php echo JText::_('COM_MATUKIO_OLD_EVENTS'); ?>
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
		<table class="table table-hover table-striped" data-sortable="" data-sortable-initialized="true">
			<thead>
			<tr>
				<th width="5">
					#
				</th>
				<th width="5">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
					       onclick="Joomla.checkAll(this);" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_EVENT_TITLE', 'eventname', $listDirn, $listOrder); ?>
				</th>
				<th style="text-align: center">
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_EVENT_ID', 'eventid', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_NR', 'r.semnum', $listDirn, $listOrder); ?>
				</th>
				<th width="10">
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_ID', 'r.id', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_BEGIN', 'r.begin', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_END', 'r.end', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" nowrap="nowrap" style="text-align: center">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th width="5%" nowrap="nowrap" style="text-align: center">
					<?php echo JText::_('COM_MATUKIO_CANCELLED'); ?>
				</th>
				<th style="text-align: center">
					<?php echo JText::_('COM_MATUKIO_BOOKINGS'); ?>
				</th>
				<th style="text-align: center">
					<?php echo JText::_('COM_MATUKIO_RATING'); ?>
				</th>
				<th width="5%" nowrap="nowrap" style="text-align: center">
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_HITS', 'r.hits', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" nowrap="nowrap" style="text-align: center">
					<?php echo JText::_('COM_MATUKIO_STATUS'); ?>
				</th>
				<th width="5%" nowrap="nowrap" style="text-align: center">
					<?php echo JText::_('COM_MATUKIO_AVAILABILITY_SHORT'); ?>
				</th>
				<th width="5%" nowrap="nowrap" style="text-align: center">
					<?php echo JText::_('COM_MATUKIO_BOOK'); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php
			foreach ($this->items as $i => $item)
			{
				$link = JRoute::_('index.php?option=com_matukio&controller=recurring&task=edit&id=' . $item->id);
				$link_event = JRoute::_('index.php?option=com_matukio&controller=eventlist&task=editEvent&id=' . $item->eventid);
				$begin = JHTML::_('date', $item->begin, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'))
					. ", " . JHTML::_('date', $item->begin, MatukioHelperSettings::getSettings('time_format', 'H:i'));

				$end = JHTML::_('date', $item->end, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'))
					. ", " . JHTML::_('date', $item->end, MatukioHelperSettings::getSettings('time_format', 'H:i'));

				// Triggers publish / unpublish
				$published_image = MatukioHelperUtilsBasic::getPublishedImageRecurring($item, $i);

				// Triggers canceld / active
				$cancel_image = MatukioHelperUtilsBasic::getCancelImageRecurring($item, $i);

				// Booked places link
				$booked = MatukioHelperUtilsEvents::calculateBookedPlacesRecurring($item)->booked;

				$status_img = MatukioHelperUtilsBasic::getStatusImage($item);
				$available_img = MatukioHelperUtilsBasic::getAvailableImage($item, $booked);
				$bookable_image = MatukioHelperUtilsBasic::getBookableImage($item);
				$rating_image = MatukioHelperUtilsBasic::getRatingImage($item);

				$bplaces = '<a href="index.php?option=com_matukio&view=bookings&event_id=' . $item->id . '" value="'
					. $booked . '">' . $booked . "</a>";
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<?php echo '<a href="' . $link . '">'
								. $item->eventname . '</a>'; ?>
					</td>
					<td style="text-align: center">
						<?php echo '<a href="' . $link_event . '">'
							. $item->eventid . '</a>'; ?>
					</td>
					<td>
						<?php echo $item->semnum; ?>
					</td>
					<td>
						<?php echo  $item->id;?>
					</td>
					<td>
						<?php echo  $begin;?>
					</td>
					<td>
						<?php echo  $end;?>
					</td>
					<td style="text-align: center">
						<?php echo $published_image; ?>
					</td>
					<td style="text-align: center">
						<?php echo $cancel_image; ?>
					</td>
					<td style="text-align: center">
						<?php echo $bplaces; ?>
					</td>
					<td style="text-align: center">
						<?php echo $rating_image; ?>
					</td>
					<td style="text-align: center">
						<?php echo $item->hits; ?>
					</td>
					<td style="text-align: center">
						<?php echo $status_img; ?>
					</td>
					<td style="text-align: center">
						<?php echo $available_img; ?>
					</td>
					<td style="text-align: center">
						<?php echo $bookable_image; ?>
					</td>

				</tr>
			<?php
			}
			?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="16">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>

			<input type="hidden" name="view" value="recurring"/>

			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>

			<?php echo JHTML::_('form.token'); ?>
		</table>
	</form>
</div>
<div class="clear"></div>

	<div class="clear"></div>

<?php
// Description of the icons
$imgpath = MatukioHelperUtilsBasic::getComponentImagePath()
?>
	<table class="admintable table" style="width: 100%; margin-bottom: 20px; text-align: left;">
		<!-- Status -->
		<thead>
		<tr>
			<th width="33%">
				<?php echo JTEXT::_('COM_MATUKIO_STATUS'); ?>
			</th>
			<th width="33%">
				<?php echo JTEXT::_('COM_MATUKIO_AVAILABILITY'); ?>
			</th>
			<th width="33%">
				<?php echo JTEXT::_('COM_MATUKIO_BOOK'); ?>
			</th>
		</tr>
		</thead>
		<tr>
			<th width="33%">
				<?php
				echo "<img src=\"" . $imgpath . "2502.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_EVENT_HAS_NOT_STARTED_YET'
					);
				?>
			</th>
			<th width="33%">
				<?php
				echo "<img src=\"" . $imgpath . "2502.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_BOOKABLE'
					);
				?>
			</th>
			<th width="33%">
				<?php
				echo "<img src=\"" . $imgpath . "2502.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_NOT_EXCEEDED'
					);
				?>
			</th>
		</tr>
		<tr>
			<th width="33%">
				<?php
				echo "<img src=\"" . $imgpath . "2501.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_EVENT_IS_RUNNING'
					);
				?>
			</th>
			<th width="33%">
				<?php
				echo "<img src=\"" . $imgpath . "2501.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_WAITLIST'
					);
				?>
			</th>
			<th width="33%">
				<?php
				echo "<img src=\"" . $imgpath . "2500.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_EXCEEDED'
					);
				?>
			</th>
		</tr>
		<tr>
			<th width="33%">
				<?php
				echo "<img src=\"" . $imgpath . "2500.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_EVENT_HAS_ENDED'
					);
				?>
			</th>
			<th width="33%">
				<?php
				echo "<img src=\"" . $imgpath . "2500.png\" border=\"0\" align=\"absmiddle\" /> "
					. JTEXT::_('COM_MATUKIO_FULLY_BOOKED'
					);
				?>
			</th>
			<th width="33%">
			</th>
		</tr>
	</table>
	</div>
<?php
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
