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

echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'eventlist', 'COM_MATUKIO_EVENTS', 'COM_MATUKIO_SLOGAN_EVENTS');
?>
<div class="box-info full">
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

			<div class="filter-select fltrt pull-right">
				<?php
				echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clr"></div>

		<div class="table-responsive">
		<table class="adminlist table table-hover">
			<thead>
			<tr>
				<th width="5">#</th>
				<th width="5">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
					       onclick="Joomla.checkAll(this);"/>
				</th>
				<th class="title">
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_TITLE', 'e.title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_CATEGORY', 'e.catid', $listDirn, $listOrder); ?>
				</th>
				<th  nowrap="nowrap" style="text-align: center">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th nowrap="nowrap" style="text-align: center">
					<?php echo JText::_('COM_MATUKIO_CANCELLED'); ?>
				</th>
				<th nowrap="nowrap" style="text-align: center">
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

				// Triggers publish / unpublish
				$published_image = MatukioHelperUtilsBasic::getPublishedImage($l, $i);

				// Triggers canceld / active
				$cancel_image = MatukioHelperUtilsBasic::getCancelImage($l, $i);

				$title = (strlen($l->title) < 70) ? $l->title : substr($l->title, 0, 67) . "...";
				$category = (strlen($l->category) < 25) ? $l->title : substr($l->category, 0, 22) . "...";
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
							<?php echo $title; ?>
						</a>
					</td>
					<td>
						<?php echo $l->category; ?>
					</td>
					<td style="text-align: center">
						<?php echo $published_image; ?>
					</td>
					<td style="text-align: center">
						<?php echo $cancel_image; ?>
					</td>
					<td style="text-align: center">
						<?php echo $l->id; ?>
					</td>
				</tr>
				<?php
				$i++;
			}
			?>
			</tbody>
		</table>
		</div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn ?>"/>

	<?php echo JHTML::_('form.token'); ?>
	</form>
</div>



<div class="clear"></div>
<?php
// Show Footer
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
