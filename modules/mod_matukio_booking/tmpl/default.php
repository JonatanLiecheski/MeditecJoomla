<?php
/**
 * Matukio
 * @package  Joomla!
 * @Copyright (C) 2013 - Yves Hoppe - compojoom.com
 * @All      rights reserved
 * @Joomla   ! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version  $Revision: 2.2.0 $
 **/
defined('_JEXEC') or die('Restricted access');

JHTML::_('stylesheet', 'media/mod_matukio_upcoming/tmpl/default/css/tmpl.css');

$catids = $params->get("catid", 0);
$orderby = $params->get("orderby", "begin ASC");

$events = ModMatukioBookingHelper::getEvents($catids, $orderby);

$link_type_options = array(
	JHTML::_('select.option', 'absolute', JText::_('COM_TILES_LINK_TYPE_ABSOLUTE_LINK')),
	JHTML::_('select.option', 'buttons', JText::_('COM_TILES_LINK_TYPE_BUTTONS')),
	JHTML::_('select.option', 'none', JText::_('COM_TILES_LINK_TYPE_NONE'))
);

$event_options = array();

foreach ($events as $event)
{
	$event_options[] = JHTML::_('select.option', $event->id, $event->title);
}

$select_event = JHTML::_('select.genericlist', $event_options, 'type', null, 'value', 'text', 0);

$option = JFactory::getApplication()->input->get("option", '');
$view = JFactory::getApplication()->input->get("view", '');

// Don't display if we are in the booking form
if ($option == "com_matukio" && $view == "bookevent")
{
	echo JText::_("MOD_MATUKIO_BOOKING_USE_BOOKINGFORM");

	return;
}
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form">
	<table class="mmat_table" width="100%">
		<tr>
			<td align="left" class="key">
				<?php echo JText::_('COM_MATUKIO_EVENT'); ?>:
			</td>
			<td>
				<?php echo $select_event; ?>
			</td>
		</tr>
		<?php if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1): ?>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_('COM_MATUKIO_NAME'); ?>:
				</td>
				<td>
					<input type="text" class="sem_inputbox" id="name" name="name" value="" size="20"/>
				</td>
			</tr>
			<tr>
				<td align="left" class="key">
					<?php echo JText::_('COM_MATUKIO_EMAIL'); ?>:
				</td>
				<td>
					<input type="text" class="sem_inputbox" id="email" name="email" value="" size="20"/>
				</td>
			</tr>
		<?php
		else:
			$fields = MatukioHelperUtilsBooking::getBookingFields();

			foreach ($fields as $field)
			{
				if ($field->type != 'spacer')
				{
					MatukioHelperUtilsBooking::printFieldElement($field, false, -1, "small");
				}
			}
			?>
		<?php endif; ?>
		<tr>
			<td colspan="2">
				<div align="right">
					<input type="submit" value="<?php echo JText::_("COM_MATUKIO_BOOK"); ?>" class="mmat_button"/>
				</div>
			</td>
		</tr>
	</table>
	<input type="hidden" name="nrbooked" value="1"/>
	<?php if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 0): ?>
		<input type="hidden" name="oldform" value="0"/>
		<input type="hidden" name="uuid" value="<?php echo MatukioHelperPayment::getUuid(true); ?>"/>
		<input type="hidden" name="option" value="com_matukio"/>
		<input type="hidden" name="view" value="bookings"/>
		<input type="hidden" name="controller" value="bookings"/>
		<input type="hidden" name="task" value="editBooking"/>
	<?php else: ?>
		<input type="hidden" name="option" value="com_matukio"/>
		<input type="hidden" name="uuid" value="<?php echo MatukioHelperPayment::getUuid(true); ?>"/>
		<input type="hidden" name="view" value="bookings"/>
		<input type="hidden" name="controller" value="bookings"/>
		<input type="hidden" name="task" value="editBooking"/>
		<input type="hidden" name="oldform" value="1"/>
	<?php endif; ?>
</form>
