<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       28.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      2.0.0
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.multiselect');

if (JVERSION >= 3)
{
	JHtml::_('formbehavior.chosen', 'select');
}

// Load the menu including bootstrap etc
echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'bookingfields', 'COM_MATUKIO_BOOKINGFIELDS', 'COM_MATUKIO_SLOGAN_BOOKINGFIELDS');

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$filterStatus = $this->escape($this->state->get('filter.status'));
?>
	<form action="<?php echo JRoute::_("index.php?option=com_matukio&view=bookingfields"); ?>" method="post"
	      name="adminForm" id="adminForm">

		<div class="box-info full">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search fltlft btn-group pull-left">
				<label class="filter-search-lbl element-invisible"
					       for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>

					<input type="text" name="filter_search" id="filter_search"
					       value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
					       title="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>"
					       placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" accept=""
					       class="text"/>
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
					<select name="filter_status" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('COM_MATUKIO_ALL_BOOKINGFIELDS'); ?></option>

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
			</div>

			<div class="clr"></div>

			<div class="table-responsive">
			<table class="table table-hover table-striped">
				<thead>
				<tr>
					<th width="5">
						#
					</th>
					<th width="5">
						<input type="checkbox" name="toggle" value=""
						       onclick="Joomla.checkAll(this);"/>
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort', 'COM_MATUKIO_FIELD_NAME', 'cc.field_name', $listDirn, $listOrder); ?>
					</th>
					<th width="5%">
						<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_ID', 'cc.id', $listDirn, $listOrder); ?>
					</th>
					<th width="25%">
						<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_LABEL', 'cc.label', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_MATUKIO_VALUES'); ?>
					</th>
					<th width="7%" style="text-align: center">
						<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_PAGE', 'cc.page', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_TYPE', 'cc.type', $listDirn, $listOrder); ?>
					</th>
					<th width="7%">
						<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_REQUIRED', 'cc.required', $listDirn, $listOrder); ?>
					</th>
					<th width="7%" style="text-align: center">
						<?php echo JHtml::_('grid.sort', 'COM_MATUKIO_ORDERING', 'cc.ordering', $listDirn, $listOrder); ?>
					</th>
					<th width="5%" nowrap="nowrap" style="text-align: center">
						<?php echo JText::_('JPUBLISHED'); ?>
					</th>
				</tr>
				</thead>
				<tfoot>
				<tr>
					<td colspan="11"><?php echo $this->pagination->getListFooter(); ?></td>
				</tr>
				</tfoot>
				<tbody>
				<?php
				foreach ($this->items as $i => $l)
				{
					$checked = JHTML::_('grid.id', $i, $l->id);
					$published = JHTML::_('grid.published', $l, $i, 'tick.png', 'publish_x.png', 'bookingfields.');

					$link = JRoute::_('index.php?option=com_matukio&controller=bookingfields&task=editBookingfield&id=' . $l->id);
					?>
					<tr class="<?php echo "row" . $i % 2; ?>">
						<td><?php echo $this->pagination->getRowOffset($i); ?></td>
						<td>
							<?php echo $checked; ?>
						</td>
						<td>
							<a href="<?php echo $link; ?>"><?php echo $l->field_name; ?></a>
						</td>
						<td>
							<?php echo $l->id; ?>
						</td>
						<td>
							<?php echo $l->label; ?>
						</td>
						<td>
							<?php echo $l->values; ?>
						</td>
						<td style="text-align: center">
							<?php echo $l->page; ?>
						</td>
						<td>
							<?php echo $l->type; ?>
						</td>
						<td>
							<?php echo MatukioHelperInput::getYesNo($l->required); ?>
						</td>
						<td style="text-align: center">
							<?php echo $l->ordering; ?>
						</td>
						<td style="text-align: center">
							<?php echo $published; ?>
						</td>
					</tr>
					<?php
					$i++;
				}
				?>
				</tbody>
			</table>
			</div>

			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn ?>"/>

			<?php echo JHTML::_('form.token'); ?>
		</div>
		</form>
	<div class="clear"></div>

<?php
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
