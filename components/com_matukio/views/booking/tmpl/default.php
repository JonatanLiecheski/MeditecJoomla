<?php
/**
 * Matukio
 * @package  Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All      rights reserved
 * @Joomla   ! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version  $Revision: 2.1.0 $
 **/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal');
JHTML::_('stylesheet', 'media/com_matukio/css/modern.css');

MatukioHelperUtilsBasic::bootstrap(true);
?>
<!-- Start Matukio by compojoom.com -->
<div class="componentheading">
	<h2><?php echo JText::_($this->title); ?></h2>
</div>

<div class="compojoom-bootstrap">
<div class="row-fluid">
<table class="table table-hover table-striped">
	<tr>
		<td class="key" width="150px"><?php echo JText::_("COM_MATUKIO_BOOKING_ID"); ?></td>
		<td><?php echo MatukioHelperUtilsBooking::getBookingId($this->booking->id) ?></td>
	</tr>
	<tr>
		<td class="key" width="150px"><?php echo JText::_("COM_MATUKIO_EVENT"); ?></td>
		<td><?php echo JText::_($this->event->title) ?></td>
	</tr>
	<tr>
		<td class="key" width="150px"><?php echo JText::_("COM_MATUKIO_BEGIN"); ?></td>
		<td>
			<?php
			echo JHTML::_('date', $this->event->begin, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'))
				. " " . JHTML::_('date', $this->event->begin, MatukioHelperSettings::getSettings('time_format', 'H:i'));
			?>
		</td>
	</tr>
	<tr>
		<td class="key" width="150px"><?php echo JText::_("COM_MATUKIO_END"); ?></td>
		<td>
			<?php
			echo JHTML::_('date', $this->event->end, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'))
				. " " . JHTML::_('date', $this->event->end, MatukioHelperSettings::getSettings('time_format', 'H:i'));
			?>
		</td>
	</tr>
	<?php if ($this->booking->payment_brutto > 0): ?>
		<tr>
			<td class="key" width="150px"><?php echo JText::_("COM_MATUKIO_YOUR_FEES"); ?></td>
			<td>
				<?php
				echo MatukioHelperUtilsEvents::getFormatedCurrency(
					$this->booking->payment_brutto,
					MatukioHelperSettings::getSettings('currency_symbol', '$')
				);
				?>
			</td>
		</tr>
		<?php if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 0) : ?>
			<tr>
				<td class="key" width="150px"><?php echo JText::_("COM_MATUKIO_FIELD_PAYMENT_METHOD"); ?></td>
				<td>
					<?php
					echo MatukioHelperTemplates::getPaymentMethodTitle($this->booking->payment_method);
					?>
				</td>
			</tr>
		<?php endif; ?>
		<tr>
			<td class="key"><?php echo JText::_("COM_MATUKIO_YOUR_PAYMENT_STATUS"); ?></td>
			<td>
				<?php
				if ($this->booking->paid)
				{
					echo JText::_("COM_MATUKIO_PAID");
				}
				else
				{
					echo JText::_("COM_MATUKIO_NOT_PAID");
				}
				?>
			</td>
		</tr>
	<?php endif; ?>
	<tr>
		<td class="key"><?php echo JText::_("COM_MATUKIO_STATUS"); ?></td>
		<td>
			<?php
			echo MatukioHelperUtilsBooking::getBookingStatusName($this->booking->status);
			?>
		</td>
	</tr>
	<?php if(MatukioHelperSettings::getSettings('participant_grading_system', 0) && $this->booking->mark != 0) :?>
	<tr>
		<td class="key"><?php echo JText::_("COM_MATUKIO_YOUR_MARK"); ?></td>
		<td>
			<?php
			echo $this->booking->mark;
			?>
		</td>
	</tr>
	<?php endif; ?>
</table>

<h3><?php echo JText::_("COM_MATUKIO_YOUR_BOOKING_DATA"); ?></h3>

<table class="table table-hover table-striped">
	<?php if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1) : ?>
		<tr>
			<td width="150px" class="key">
				<?php echo JText::_('COM_MATUKIO_NAME'); ?>
			</td>
			<td>
				<?php echo $this->booking->name; ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<?php echo JText::_('COM_MATUKIO_EMAIL'); ?>
			</td>
			<td>
				<?php echo $this->booking->email; ?>
			</td>
		</tr>
	<?php
	else :
		// New booking form..
		$fields    = MatukioHelperUtilsBooking::getBookingFields();
		$fieldvals = explode(";", $this->booking->newfields);

		$value = array();

		foreach ($fieldvals as $val)
		{
			$tmp = explode("::", $val);

			if (count($tmp) > 1)
			{
				$value[$tmp[0]] = $tmp[1];
			}
			else
			{
				$value[$tmp[0]] = "";
			}
		}

		foreach ($fields as $field)
		{
			// Not use the Spacer fields
			if ($field->type != "spacer" && $field->type != "spacertext")
			{
				echo "<tr>";
				echo "<td class=\"key\" width=\"150px\">" . JText::_($field->label) . "</td>";
				echo "<td>";

				if (!empty($value[$field->id]))
				{
					echo JText::_($value[$field->id]);
				}

				echo "</td>";
				echo "</tr>";
			}
		}
		?>
	<?php endif; ?>
	<?php
	// Old event only fields.. should be removed some time...

	$html = "";
	if (!empty($this->booking->id))
	{
		$buchopt = MatukioHelperUtilsEvents::getEventBookableArray(0, $this->event, $this->booking->userid);
	}
	else
	{
		$buchopt = "";
	}

	$zusreq = 0;
	$zusfeld = MatukioHelperUtilsEvents::getAdditionalFieldsFrontend($this->event);
	$zustemp = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

	$zustemp = MatukioHelperUtilsEvents::getAdditionalFieldsFrontend($this->event, $this->booking);
	$zustemp = $zustemp[0];

	for ($i = 0; $i < count($zusfeld[0]); $i++)
	{
		if ($zusfeld[0][$i] != "")
		{
			$zusart = explode("|", $zusfeld[0][$i]);

			$name        = 'zusatz' . ($i + 1);
			$val         = $this->booking->$name;
			$zustemp[$i] = $val;

			$htxt = $zusart[0];
			$temp = "";
			$html .= "\n<tr>" . MatukioHelperUtilsEvents::getTableCell($htxt, 'd', 'l', '150px', 'sem_rowd');

			$htxt = $zustemp[$i];

			$html .= MatukioHelperUtilsEvents::getTableCell($htxt, 'd', 'l', '', 'sem_rowd') . "</tr>";
		}
	}

	echo $html;
	?>
</table>

<div class="buttons">
	<?php
	echo MatukioHelperUtilsEvents::getPrintWindow(2, $this->event->id, '', 'b', "bootstrap");

	// Calendar
	if (MatukioHelperSettings::getSettings('frontend_usericsdownload', 1) > 0)
	{
		echo MatukioHelperUtilsEvents::getCalendarButton($this->event, "bootstrap");
	}

	// Contact organizer
	if (MatukioHelperSettings::getSettings("sendmail_contact", 1))
	{
		echo MatukioHelperUtilsEvents::getEmailWindow(MatukioHelperUtilsBasic::getComponentImagePath(), $this->event->id, 1, "bootstrap");
	}

	// Invoice
	if (MatukioHelperSettings::getSettings("download_invoice", 1)
		&& ($this->booking->status == 0 || $this->booking->status == 1)
		&& $this->event->fees > 0)
	{
		$href = JURI::ROOT() . "index.php?option=com_matukio&view=printeventlist&format=raw&todo=invoice&cid=" . $this->booking->semid . "&uuid=" . $this->booking->uuid;

		echo " <a border=\"0\" href=\"" . $href
			. "\" ><span class=\"btn\" type=\"button\">" . JTEXT::_('COM_MATUKIO_DOWNLOAD_INVOICE_BUTTON')
			. "</span></a>";
	}

	// Ticket
	if (MatukioHelperSettings::getSettings("download_ticket", 1)
		&& ($this->booking->status == 0 || $this->booking->status == 1))
	{
		$href = JURI::ROOT() . "index.php?option=com_matukio&view=printeventlist&format=raw&todo=ticket&cid=" . $this->booking->semid . "&uuid=" . $this->booking->uuid;

		echo " <a border=\"0\" href=\"" . $href
			. "\" ><span class=\"btn\" type=\"button\">" . JTEXT::_('COM_MATUKIO_DOWNLOAD_TICKET_BUTTON')
			. "</span></a>";
	}

	// Certification
	if (MatukioHelperSettings::getSettings('frontend_certificatesystem', 0) > 0)
	{
		if ($this->booking->certificated == 1 AND $this->event->nrbooked > 0)
		{
			echo MatukioHelperUtilsEvents::getPrintWindow(1, $this->event->sid, $this->booking->id, 'CERT', 'btn');
		}
	}

	// Edit booking
	if (strtotime($this->event->booked) - time() >= (MatukioHelperSettings::getSettings('booking_stornotage', 1) * 24 * 60 * 60)
		&& MatukioHelperSettings::getSettings('booking_edit', 1) == 1
		&& $this->booking->paid == 0
		&& ($this->booking->status == 0 || $this->booking->status == 1)
	)
	{
		$editbookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $this->booking->semid . "&uuid=" . $this->booking->uuid);

		if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1)
		{
			$editbookinglink = JRoute::_(
				MatukioHelperRoute::getEventRoute($this->event->id, $this->event->catid, 1, $this->booking->id, $this->booking->uuid), false
			);
		}

		echo " <a border=\"0\" href=\"" . $editbookinglink
			. "\" ><span class=\"btn btn-success\" type=\"button\">" . JTEXT::_('COM_MATUKIO_EDIT_YOUR_BOOKING')
			. "</span></a>";
	}

	// Cancel booking
	if (strtotime($this->event->booked) - time() >= (MatukioHelperSettings::getSettings('booking_stornotage', 1) * 24 * 60 * 60)
		&& $this->booking->paid == 0
		&& MatukioHelperSettings::getSettings('booking_stornotage', 1) != -1
		&& ($this->booking->status == 0 || $this->booking->status == 1)
	)
	{
		$unbookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&task=cancelBooking&cid=" . $this->booking->semid . "&uuid=" . $this->booking->uuid);

		echo " <a border=\"0\" href=\"" . $unbookinglink
			. "\" ><span class=\"btn btn-danger\" type=\"button\">"
			. JTEXT::_('COM_MATUKIO_BOOKING_CANCELLED') . "</span></a>";
	}


	?>
</div>

<?php
// Footer
echo MatukioHelperUtilsBasic::getCopyright();
?>
</div>
</div>
<!-- End Matukio by compojoom.com -->
