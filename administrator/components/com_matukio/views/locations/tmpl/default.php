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
echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'locations', 'COM_MATUKIO_LOCATIONS', 'COM_MATUKIO_SLOGAN_LOCATIONS');
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
	      action="<?php echo JRoute::_('index.php?option=com_matukio&view=locations'); ?>">

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
			<select name="filter_status" class="inputbox chzn-single" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_MATUKIO_ALL_LOCATIONS'); ?></option>

				<?php
				$subpub = $subunp = "";

				if ($filterStatus == "published")
				{
					$subpub = ' selected="selected" ';
				}
				elseif ($filterStatus == "unpublished")
				{
					$subunp = ' selected="selected "';
				}
				?>
				<option value="published" <?php echo $subpub; ?>>
					<?php echo JText::_('COM_MATUKIO_PUBLISHED'); ?>
				</option>
				<option value="unpublished" <?php echo $subunp; ?>>
					<?php echo JText::_('COM_MATUKIO_UNPUBLISHED'); ?>
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
		<table class="adminlist table table-hover" style="width: 100%;">
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
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_LOCATION_TITLE', 'cc.title', $listDirn, $listOrder); ?>
				</th>
				<th width="10">
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_ID', 'cc.id', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_LOCATION', 'cc.location', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_LOCATION_EMAIL', 'cc.email', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_LOCATION_PHONE', 'cc.phone', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_LOCATION_GMAP', 'cc.gmaploc', $listDirn, $listOrder); ?>
				</th>
				<th style="text-align: center">
					<?php echo JTEXT::_("COM_MATUKIO_PUBLISHED"); ?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->items as $i => $item) :
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td>
						<div><?php echo '<a href="index.php?option=com_matukio&view=editlocation&id=' . $item->id . '">'
								. $item->title . '</a>'; ?></div>
					</td>
					<td>
						<?php echo $item->id; ?>
					</td>
					<td>
						<?php
						echo  $item->location;
						?>
					</td>
					<td>
						<?php echo '<a href="mailto:' . $item->email . '">' . $item->email . '</a>'; ?>
					</td>
					<td>
						<?php echo $item->phone ?>
					</td>
					<td>
						<?php echo $item->gmaploc ?>
					</td>
					<td style="text-align: center">
						<?php echo JHTML::_('grid.published', $item, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix = 'locations.'); ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
			<tfoot>
			<tr>
				<td colspan="9">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			</tfoot>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>

			<input type="hidden" name="view" value="locations"/>

			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>

			<?php echo JHTML::_('form.token'); ?>
		</table>
		</div>
	</form>
</div>
<div class="clear"></div>

<?php
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
