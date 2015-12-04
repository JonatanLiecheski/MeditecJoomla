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

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$filterStatus = $this->escape($this->state->get('filter.status'));

MatukioHelperUtilsBasic::bootstrap();
?>
<script type="text/javascript">

</script>
<div class="compojoom-bootstrap">
	<form action="<?php echo JRoute::_("index.php?option=com_matukio&view=eventlist"); ?>" method="post"
	      name="adminForm" id="adminForm">

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
						<?php echo JText::_('COM_MATUKIO_ALL_EVENTS'); ?>
					</option>
					<option value="published" <?php echo $subpub; ?>>
						<?php echo JText::_('COM_MATUKIO_PUBLISHED'); ?>
					</option>
					<option value="unpublished" <?php echo $subunp; ?>>
						<?php echo JText::_('COM_MATUKIO_UNPUBLISHED'); ?>
					</option>
					<option value="current" <?php echo $subcur; ?>>
						<?php echo JText::_('COM_MATUKIO_CURRENT_EVENTS'); ?>
					</option>
					<option value="old" <?php echo $subold; ?>>
						<?php echo JText::_('COM_MATUKIO_OLD_EVENTS'); ?>
					</option>
				</select>
			</div>
		</div>
		<div class="clr"></div>

		<table class="adminlist table table-hover">
			<thead>
			<tr>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_TITLE', 'e.title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_NR', 'e.semnum', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_CATEGORY', 'category', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_BEGIN', 'e.begin', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_MATUKIO_BOOKINGS'); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_HITS', 'e.hits', $listDirn, $listOrder); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JText::_('COM_MATUKIO_STATUS'); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JText::_('COM_MATUKIO_AVAILABILITY'); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JText::_('COM_MATUKIO_BOOK'); ?>
				</th>
				<th width="5%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_ID', 'e.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="16"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			foreach ($this->items as $i => $l)
			{
				$checked = JHTML::_('grid.id', $i, $l->id);
				$published = JHTML::_('grid.published', $l, $i);

				$link = JRoute::_('index.php?option=com_matukio&controller=eventlist&task=editEvent&id=' . $l->id);

				$curdate = MatukioHelperUtilsDate::getCurrentDate();

				$booked = MatukioHelperUtilsEvents::calculateBookedPlaces($l)->booked;

				$status_img = MatukioHelperUtilsBasic::getStatusImage($l);
				$available_img = MatukioHelperUtilsBasic::getAvailableImage($l, $booked);
				$bookable_image = MatukioHelperUtilsBasic::getBookableImage($l);
				$rating_image = MatukioHelperUtilsBasic::getRatingImage($l);

				// Triggers publish / unpublish
				$published_image = MatukioHelperUtilsBasic::getPublishedImage($l, $i);

				// Triggers canceld / active
				$cancel_image = MatukioHelperUtilsBasic::getCancelImage($l, $i);

				$title = (strlen($l->title) < 70) ? $l->title : substr($l->title, 0, 67) . "...";
				$category = (strlen($l->category) < 25) ? $l->title : substr($l->category, 0, 22) . "...";

				$begin = JHTML::_('date', $l->begin, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'))
					. ", " . JHTML::_('date', $l->begin, MatukioHelperSettings::getSettings('time_format', 'H:i'));

				$end = JHTML::_('date', $l->end, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'))
					. ", " . JHTML::_('date', $l->end, MatukioHelperSettings::getSettings('time_format', 'H:i'));

				$bplaces = '<a href="index.php?option=com_matukio&view=bookings&event_id=' . $l->id . '" value="'
					. $booked . '">' . $booked . "</a>";
				?>
				<tr class="<?php echo "row" . $i % 2; ?>">
					<td>
						<a onclick="window.parent.selectEvent('<?php echo $l->id; ?>',
							'<?php echo str_replace(array("'", "\""), array("\\'", ""), $title
						); ?>', '<?php echo JRequest::getVar('object'); ?>');">
							<?php echo $title; ?>
						</a>
					</td>
					<td>
						<?php echo $l->semnum; ?>
					</td>
					<td>
						<?php echo $l->category; ?>
					</td>
					<td align="center">
						<?php echo $begin; ?>
					</td>
					<td align="center">
						<?php echo $published_image; ?>
					</td>
					<td align="center">
						<?php echo $bplaces; ?>
					</td>
					<td align="center">
						<?php echo $l->hits; ?>
					</td>
					<td align="center">
						<?php echo $status_img; ?>
					</td>
					<td align="center">
						<?php echo $available_img; ?>
					</td>
					<td align="center">
						<?php echo $bookable_image; ?>
					</td>
					<td align="center">
						<?php echo $l->id; ?>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</tbody>
		</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn ?>"/>

	<?php echo JHTML::_('form.token'); ?>
	</form>
</div>

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

<div class="clear"></div>
<?php
// Show Footer
echo MatukioHelperUtilsBasic::getCopyright(false);