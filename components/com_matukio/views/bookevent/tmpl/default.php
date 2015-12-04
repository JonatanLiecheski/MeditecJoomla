<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       24.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      2.0.0
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');

MatukioHelperUtilsBasic::bootstrap(true);
MatukioHelperUtilsBasic::loadValidation();

JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
JHTML::_('stylesheet', 'media/com_matukio/css/modern.css');
JHTML::_('stylesheet', 'media/com_matukio/css/booking.css');
JHTML::_('script', 'media/com_matukio/js/booking.jquery.js');
?>
	<script type="text/javascript">
		(function ($) {
			jQuery( document ).ready(function( $ ) {
				$("#BookingForm").validationEngine();

				$("#BookingForm").mat_booking({
					steps: <?php echo $this->steps; ?>,
					fees: <?php echo $this->event->fees ?>,
					different_fees: <?php echo $this->event->different_fees ?>,
					max_bookings: <?php echo $this->event->nrbooked; ?>,
					event_id: <?php echo $this->event->id; ?>,
					fieldnames: <?php echo json_encode($this->fields_p1); ?>,
					coupon: <?php echo MatukioHelperSettings::getSettings("payment_coupon", 1) == 1 && $this->steps > 2 ? 1 : 0; ?>,
					setting_multiple_places: <?php echo MatukioHelperSettings::getSettings('frontend_usermehrereplaetze', 1) ?>
				},
				{
					error_payment: '<?php echo JTEXT::_("COM_MATUKIO_NO_PAYMENT_SELECTED", true); ?>',
					error_required_fields: '<?php echo JTEXT::_("COM_MATUKIO_PLEASE_FILL_OUT_ALL_REQUIRED_FIELDS", true); ?>',
					error_coupon: '<?php echo JTEXT::_("COM_MATUKIO_INVALID_COUPON_CODE", true); ?>',
					error_max_places: '<?php echo JTEXT::_("COM_MATUKIO_EXCEEDED_MAXIMUM_NUMBER_OF_BOOKABLE_PLACES", true); ?>',
					error_agb: '<?php echo JText::_("COM_MATUKIO_AGB_NOT_ACCEPTED", true); ?>'
				});
			});
		})(jQuery);
		</script>
	<form action="<?php echo JRoute::_("index.php?option=com_matukio&view=bookevent&task=book"); ?>" method="post" name="BookingForm" id="BookingForm">
	<div class="compojoom-bootstrap">
	<div id="mat_booking">
	<div id="mat_heading">
		<?php
		$eventdate = JHTML::_('date', $this->event->begin, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y'))
			. " " . JHTML::_('date', $this->event->begin, MatukioHelperSettings::getSettings('time_format', 'H:i'));

		echo "<div align=\"center\">";
		echo MatukioHelperUtilsBooking::getBookingHeader($this->steps);
		echo "</div>";
		echo "<div id=\"mat_intro\">";
		echo "<h3>" . JText::_($this->event->title) . " " . $eventdate . "</h3>";
		echo "</div>";
		?>
		<noscript>
			<h2><?php echo JText::_("COM_MATUKIO_JAVASCRIPT_REQUIRED"); ?></h2>
		</noscript>

	</div>
	<div id="mat_pageone">
	<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">
		<?php
		$fieldvals = null;
		$value = array();

		if (!empty($this->booking))
		{
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
		}

		foreach ($this->fields_p1 as $field)
		{
			// Prints the field in the table <tr><td>label</td><td>field</td>
			if (empty($this->booking))
			{
				MatukioHelperUtilsBooking::printFieldElement($field, true);
			}
			else
			{
				if (!empty($value[$field->id]))
				{
					MatukioHelperUtilsBooking::printFieldElement($field, true, $value[$field->id]);
				}
				else
				{
					MatukioHelperUtilsBooking::printFieldElement($field, true);
				}
			}
		}
		?>
	</table>
	<?php
	// Old event only fields.. should be removed some time...
	// Zusatzfelder ausgeben
	$buchopt = MatukioHelperUtilsEvents::getEventBookableArray(0, $this->event, $this->user->id, $this->uuid, 1, $this->booking);

	$html = "";
	$tempdis = "";
	$hidden = "";
	$reqfield = " <span class=\"sem_reqfield\">*</span>";
	$reqnow = "\n<tr>" . MatukioHelperUtilsEvents::getTableCell("&nbsp;" . $reqfield . " "
			. JTEXT::_('COM_MATUKIO_REQUIRED_FIELD'), 'd', 'r', '', 'sem_nav', 2
		) . "</tr>";

	$zusreq = 0;
	$zusfeld = MatukioHelperUtilsEvents::getAdditionalFieldsFrontend($this->event, $this->booking);
	$zustemp = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

	if (count($buchopt[2]) > 0)
	{
		$zustemp = MatukioHelperUtilsEvents::getAdditionalFieldsFrontend($buchopt[2][0], $this->booking);
		$zustemp = $zustemp[0];
	}

	for ($i = 0; $i < count($zusfeld[0]); $i++)
	{
		if ($zusfeld[0][$i] != "" AND ($buchopt[0] > 1 OR $this->art == 3))
		{
			$zusart = explode("|", $zusfeld[0][$i]);

			if (count($buchopt[2]) == 0)
			{
				$zustemp[$i] = $zusart[2];
			}

			$htxt = $zusart[0] . MatukioHelperUtilsBasic::createToolTip($zusfeld[1][$i]);

			$temp = "";

			if ($tempdis == "")
			{
				if ($zusart[1] == 1)
				{
					$temp = $reqfield;
					$reqtext = $reqnow;
				}
			}

			$html .= "\n<tr>" . MatukioHelperUtilsEvents::getTableCell($htxt . $temp, 'd', 'l', '150px', 'sem_rowd');
			$reqclass = "";

			if ($zusart[1] == 1)
			{
				$reqclass = " validate[required]";
			}

			if (count($zusart) > 1)
			{
				$optionen = array();

				switch ($zusart[3])
				{
					case "select":
						$optionen[] = JHTML::_('select.option', '', '- ' . JTEXT::_('COM_MATUKIO_PLEASE_SELECT') . ' -');

						for ($z = 4; $z < count($zusart); $z++)
						{
							$hsafe = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $zusart[$z]);
							$optionen[] = JHTML::_('select.option', $hsafe, $zusart[$z]);
						}

						$htxt = JHTML::_(
							'select.genericlist', $optionen, 'zusatz' . ($i + 1), 'class="input' . $reqclass . '" size="1"'
							. $tempdis, 'value', 'text', $zustemp[$i]
						);
						break;

					case "radio":
						for ($z = 4; $z < count($zusart); $z++)
						{
							$hsafe = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $zusart[$z]);
							$optionen[] = JHTML::_('select.option', $hsafe, $zusart[$z]);
						}

						$auswahl = $zustemp[$i];

						if ($zusfeld[2][$i] == 1 AND $auswahl == "")
						{
							$auswahl = $zusart[2];
						}

						$htxt = JHTML::_('select.radiolist', $optionen, 'zusatz' . ($i + 1), 'class="input' . $reqclass . '"'
								. $tempdis, 'value', 'text', $auswahl
							);
						break;

					case "textarea":
						if (count($zusart) > 4)
						{
							if (!is_numeric($zusart[4]))
							{
								$zusart[4] = 30;
							}

							if (!is_numeric($zusart[5]))
							{
								$zusart[5] = 3;
							}
						}
						else
						{
							$zusart[4] = 30;
							$zusart[5] = 3;
						}

						$htxt = "<textarea class=\"input" . $reqclass . "\" id=\"zusatz" . ($i + 1) . "\" name=\"zusatz" . ($i + 1) . "\" cols=\""
							. $zusart[4] . "\" rows=\"" . $zusart[5] . "\"" . $tempdis . ">" . $zustemp[$i] . "</textarea>";
						break;

					case "email":
						$htxt = "<input type=\"text\" class=\"input" . $reqclass . "\" id=\"emailzusatz" . ($i + 1)
							. "\" name=\"zusatz" . ($i + 1) . "\" value=\""
							. $zustemp[$i] . "\" " . $tempdis . " />";
						break;

					default:
						$htxt = "<input type=\"text\" class=\"input" . $reqclass . "\" id=\"zusatz" . ($i + 1) . "\" name=\"zusatz" . ($i + 1) . "\" value=\""
							. $zustemp[$i] . "\" " . $tempdis . " style=\"width: 250px;\" />";
						break;
				}
			}
			else
			{
				$htxt = "<input class=\"input" . $reqclass . "\" type=\"text\" id=\"zusatz" . ($i + 1)
					. "\" name=\"zusatz" . ($i + 1) . "\" value=\"" . $zustemp[$i]
					. "\" " . $tempdis . " style=\"width: 250px;\" />";
			}

			$html .= MatukioHelperUtilsEvents::getTableCell($htxt, 'd', 'l', '', 'sem_rowd') . "</tr>";
			$zwang = 0;

			if ($zusart[1] == 1)
			{
				$zwang = 1;
			}

			$hidden .= "<input type=\"hidden\" id=\"opt" . ($i + 1) . "\" name=\"zusatz" . ($i + 1) . "opt\" value=\"" . $zwang . "\">";
		}
		else
		{
			$hidden .= "<input type=\"hidden\" id=\"zusatz" . ($i + 1) . "\" name=\"zusatz" . ($i + 1)
				. "\" value=\"\"><input type=\"hidden\" name=\"zusatz" . ($i + 1) . "opt\" value=\"0\">";
		}
	}

	echo "<table class=\"mat_table table\">\n";
	echo $html;
	echo "</table>";

	if ($this->event->nrbooked > 1 AND MatukioHelperSettings::getSettings('frontend_usermehrereplaetze', 1) > 0)
	{
		if ($this->event->different_fees == 0)
		{
			echo "<table class=\"mat_table table\">\n";

			$this->limits = array();

			if ($buchopt[4] <= 0) // If booking is on waitlist
			{
				for ($i = 1; $i <= $this->event->nrbooked; $i++)
				{
					// Check how many places are left (to prevent booking more places then allowed)
					$this->limits[] = JHTML::_('select.option', $i);
				}
			}
			else
			{
				for ($i = 1; $i <= $this->event->nrbooked; $i++)
				{
					// Check how many places are left (to prevent booking more places then allowed)
					if ($i <= $buchopt[4])
					{
						$this->limits[] = JHTML::_('select.option', $i);
					}
				}
			}

			$pval = 1;

			if (!empty($this->booking))
			{
				$pval = $this->booking->nrbooked;
			}

			$platzauswahl = JHTML::_('select.genericlist', $this->limits, 'nrbooked', 'class="input" size="1"' . $tempdis,
				'value', 'text', $pval
			);

			if ($buchopt[0] == 3)
			{
				$htx1 = JTEXT::_('COM_MATUKIO_PLACES_TO_BOOK');
			}
			else
			{
				$htx1 = JTEXT::_('COM_MATUKIO_BOOKED_PLACES');
			}

			if ($tempdis == "")
			{
				$htx2 = $platzauswahl;
			}
			else
			{
				$htx2 = "<input class=\"input\" type=\"text\" value=\"" . $buchopt[2][0]->nrbooked
					. "\"size=\"1\" style=\"text-align:right;\"" . $tempdis . " />";
			}

			if ($buchopt[4] <= 0) // If booking is on waitlist
			{
				$htx2 .= " *" . JText::_("COM_MATUKIO_ON_WAITLIST");
			}

			echo '<tr>';
			echo '<td class="key" width="150px">';
			echo $htx1;
			echo " <span class=\"mat_req\">*</span>";
			echo '</td>';
			echo '<td>';
			echo $htx2;
			echo '</td>';
			echo '</tr>';
			echo "</table>";
		}
		else
		{
			// Different Fees with multiple tickets @since 3.0.0
			echo "<input type=\"hidden\" name=\"nrbooked\" id=\"nrbooked\" value=\"1\" />";

			echo '<div id="mat_tickets">';

			if (!empty($this->booking))
			{
				if (!empty($this->booking->different_fees))
				{
					echo MatukioHelperFees::getEditBookingFeesList($this->booking->different_fees, $this->event, $buchopt);
				}
				else
				{
					// This is our fallback
					echo JText::_("COM_MATUKIO_NO_TICKETS_BOOKED_WITH_DIFFERENT_FEES");
					echo MatukioHelperFees::getEditBookingFeesList($this->booking->different_fees, $this->event, $buchopt);
				}
			}
			else
			{
				if (!empty($this->event->different_fees_override))
				{
					// We have an override for this event
					$fees_list = MatukioHelperFees::getOverrideFees($this->event->different_fees_override);
				}
				else
				{
					$fees_list = MatukioHelperFees::getFees();
				}

				echo "<table class=\"mat_table table\">\n";
				echo '<tr>';
				echo '<td class="key" width="150px">';

				echo JText::_("COM_MATUKIO_PLACES_TO_BOOK") . ": ";
				echo MatukioHelperUtilsEvents::getPlaceSelect($buchopt, $this->event, 0);

				echo '</td>';
				echo '<td>';

				echo JText::_("COM_MATUKIO_TICKET_TYPE") . ": ";

				echo '<select id="ticket_fees0" name="ticket_fees[0]" class="input chzn-single ticket_fees" size="1">';
				echo '<option value="0" selected="selected" discvalue="0" discount="1" percent="1">- ' . JText::_("COM_MATUKIO_NORMAL") . ' -</option>';

				foreach ($fees_list as $f)
				{
					$disc_text = ($f->discount) ? '-' : '+';

					if (MatukioHelperSettings::getSettings('different_fees_absolute', 1))
					{
						if (!$f->percent)
						{
							$fval = ($f->discount) ? $this->event->fees - $f->value : $this->event->fees + $f->value;
						}
						else
						{
							// Calculate fees
							$fval = ($f->discount) ? $this->event->fees - ($this->event->fees * ($f->value / 100)) : $this->event->fees +($this->event->fees * ($f->value / 100));
						}

						$fval = MatukioHelperUtilsEvents::getFormatedCurrency($fval, MatukioHelperSettings::getSettings('currency_symbol', '$'));

						echo '<option value="' . $f->id . '" discvalue="' . $f->value . '" percent="' . $f->percent . '" discount="' . $f->discount . '">'
							. JText::_($f->title) . ' (' . $fval . ")" . '</option>';
					}
					else
					{
						if (!$f->percent)
						{
							$fval = MatukioHelperUtilsEvents::getFormatedCurrency($f->value, MatukioHelperSettings::getSettings('currency_symbol', '$'));
						}
						else
						{
							$fval = $f->value . " %";
						}

						echo '<option value="' . $f->id . '" discvalue="' . $f->value . '" percent="' . $f->percent . '" discount="' . $f->discount . '">'
							. JText::_($f->title) . ' (' . $disc_text . $fval . ")" . '</option>';
					}
				}

				echo '</select>';

				if ($buchopt[4] <= 0) // If booking is on waitlist
				{
					echo " *" . JText::_("COM_MATUKIO_ON_WAITLIST");
				}

				echo '</td>';
				echo '<td style="text-align: right;">';

				// Add additional tickets in another category!
				echo " <a id=\"addticket\" class=\"mat_addticket btn btn-success\" border=\"0\" href=\"#\"><span type=\"button\">
					<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
					. "1832.png\" border=\"0\" align=\"absmiddle\" style=\"width: 16px; height: 16px;\">&nbsp;"
					. JTEXT::_('COM_MATUKIO_ADD') . "</span></a>";

				echo '</td>';
				echo '</tr>';
				echo "</table>";
			}

			echo "</div>";
		}
	}
	else
	{
		// Just one single ticket!
		echo "<input type=\"hidden\" name=\"nrbooked\" id=\"nrbooked\" value=\"1\" />";

		// Different Fees @since 3.0.0
		if ($this->event->different_fees)
		{
			$fees_list = MatukioHelperFees::getFees();

			echo "<input type=\"hidden\" name=\"places[0]\" id=\"places0\" value=\"1\" class=\"ticket_places\" />";

			if (!empty($this->event->different_fees_override))
			{
				// We have an override for this event
				$fees_list = MatukioHelperFees::getOverrideFees($this->event->different_fees_override);
			}

			// We have just a single ticket so we just show a drop down list and set ticket_fee to array obj 0
			echo "<table class=\"mat_table table\">\n";
			echo '<tr>';
			echo '<td class="key" width="150px">';

			echo JText::_("COM_MATUKIO_TICKET_TYPE");

			echo " <span class=\"mat_req\">*</span>";
			echo '</td>';
			echo '<td>';

			echo '<select id="ticket_fees0" name="ticket_fees[0]" class="input chzn-single ticket_fees" size="1">';
			echo '<option value="0" selected="selected" discvalue="0" discount="1" percent="1">- ' . JText::_("COM_MATUKIO_NORMAL") . " ("
				. MatukioHelperUtilsEvents::getFormatedCurrency($this->event->fees, MatukioHelperSettings::getSettings('currency_symbol', '$'))
				. ') -</option>';

			$types = null;
			$places = null;

			if (!empty($this->booking))
			{
				$json = json_decode($this->booking->different_fees, true);

				$places = $json["places"];
				$types = $json["types"];

				if (empty($places))
				{
					$places = array(1);
					$types = array(0);
				}
			}

			foreach ($fees_list as $f)
			{
				$disc_text = ($f->discount) ? '-' : '+';

				if (MatukioHelperSettings::getSettings('different_fees_absolute', 1))
				{
					if (!$f->percent)
					{
						$fval = ($f->discount) ? $this->event->fees - $f->value : $this->event->fees + $f->value;
					}
					else
					{
						// Calculate fees
						$fval = ($f->discount) ? $this->event->fees - ($this->event->fees * ($f->value / 100)) : $this->event->fees +($this->event->fees * ($f->value / 100));
					}

					$disc_text = "";

					$fval = MatukioHelperUtilsEvents::getFormatedCurrency($fval, MatukioHelperSettings::getSettings('currency_symbol', '$'));
				}
				else
				{
					if (!$f->percent)
					{
						$fval = MatukioHelperUtilsEvents::getFormatedCurrency($f->value, MatukioHelperSettings::getSettings('currency_symbol', '$'));
					}
					else
					{
						$fval = $f->value . " %";
					}
				}

				$selected = "";

				if (!empty($this->booking))
				{
					if ($f->id == $types[0])
					{
						$selected = ' selected="selected"';
					}
				}

				echo '<option value="' . $f->id . '" discvalue="' . $f->value . '" percent="' . $f->percent
					. '" discount="' . $f->discount . '"' . $selected . '>'
					. JText::_($f->title) . ' (' . $disc_text . $fval . ")" . '</option>';
			}

			echo '</select>';

			if ($buchopt[4] <= 0) // If booking is on waitlist
			{
				echo " *" . JText::_("COM_MATUKIO_ON_WAITLIST");
			}

			echo '</td>';
			echo '</tr>';
			echo "</table>";
		}
	}
	?>
	</div>
	<div id="mat_pagetwo">
		<?php
		if ($this->steps > 2)
		{
			?>
			<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">
				<?php
				echo '<tr>';
				echo '<td class="key" width="150px">';
				echo JText::_("COM_MATUKIO_FIELD_PAYMENT_METHOD");
				echo " <span class=\"mat_req\">*</span>";
				echo '</td>';
				echo '<td>';
				$selected = null;

				if (!empty($this->booking))
				{
					$selected = $this->booking->payment_method;
				}

				echo MatukioHelperPayment::getPaymentSelect($this->payment, $selected);
				echo '</td>';
				echo '</tr>';
				?>
			</table>
			<?php
			// Payment Coupon codes
			if (MatukioHelperSettings::getSettings("payment_coupon", 1) == 1)
			{
				$cval = "";

				if (!empty($this->booking))
				{
					$cval = $this->booking->coupon_code;
				}

				?>
				<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">
					<tr>
						<td class="key" width="150px">
							<?php echo JText::_("COM_MATUKIO_FIELD_COUPON"); ?>
						</td>
						<td>
							<input class="input-large" type="text" name="coupon_code"
							       id="coupon_code" value="<?php echo $cval; ?>"
							       maxlength="255" style="width: 150px"
							       title="<?php echo JText::_('COM_MATUKIO_FIELD_COUPON_DESC') ?>" />
						</td>
					</tr>
				</table>
			<?php
			}
			else
			{
			?>
				<input type="hidden" name="coupon_code" id="coupon_code" value="" />
			<?php
			}

			// Fields on Page 2
			if (!empty($this->fields_p2))
			{
				?>
				<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">
					<?php
					foreach ($this->fields_p2 as $field)
					{
						// Prints the field in the table <tr><td>label</td><td>field</td>
						if (empty($this->booking))
						{
							MatukioHelperUtilsBooking::printFieldElement($field);
						}
						else
						{
							if (!empty($value[$field->id]))
							{
								MatukioHelperUtilsBooking::printFieldElement($field, false, $value[$field->id]);
							}
							else
							{
								MatukioHelperUtilsBooking::printFieldElement($field, false);
							}
						}
					}
					?>
				</table>
			<?php
			}
		}
		else
		{
			echo "Page 2";
		}
		?>
	</div>
	<div id="mat_pagethree">
	<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">
		<?php
		// Confirmation
		// Fields
		foreach ($this->fields_p1 as $field)
		{
			if ($field->type == 'spacer')
			{
				echo "</table>";
				echo MatukioHelperUtilsBooking::getSpacerField();
				echo "<table class=\"mat_table table\">\n";
			}
			elseif ($field->type == 'spacertext')
			{
				// We don't show it on page 3
			}
			else
			{
				echo '<tr>';
				echo '<td class="key" width="150px">';
				echo '<label for="' . $field->field_name . '" width="100" title="' . JText::_($field->label) . '">';
				echo JText::_($field->label);

				if ($field->required == 1)
				{
					echo " <span class=\"mat_req\">*</span>";
				}

				echo '</label>';
				echo '</td>';

				echo '<td>';
				echo MatukioHelperUtilsBooking::getConfirmationfields($field->field_name);
				echo '</td>';
				echo '</tr>';
			}
		}

		// Fields on Page 3
		if (!empty($this->fields_p3))
		{
			?>
			<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">
				<?php
				foreach ($this->fields_p3 as $field)
				{
					// Prints the field in the table <tr><td>label</td><td>field</td>
					if (empty($this->booking))
					{
						MatukioHelperUtilsBooking::printFieldElement($field);
					}
					else
					{
						if (!empty($value[$field->id]))
						{
							MatukioHelperUtilsBooking::printFieldElement($field, false, $value[$field->id]);
						}
						else
						{
							MatukioHelperUtilsBooking::printFieldElement($field, false);
						}
					}
				}
				?>
			</table>
		<?php
		}
		?>
	</table>
	<br />
	<?php
	// Nr Booked
	if ($this->event->nrbooked > 1)
	{
		echo '<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">';
		echo '<tr>';
		echo '<td class="key" width="150px">';
		echo '<label for="conf_nrbooked" width="100" title="' . JText::_("COM_MATUKIO_BOOKED_PLACES") . '">';
		echo JText::_("COM_MATUKIO_BOOKED_PLACES");

		echo '</label>';
		echo '</td>';

		echo '<td >';

		echo "<div id=\"conf_nrbooked\"></div>";

		echo '</td>';
		echo '</tr>';
		echo '</table>';
	}
	else
	{
		echo "<input type=\"hidden\" id=\"conf_nrbooked\" value=\"1\">";
	}

	// Captcha
	if (MatukioHelperSettings::getSettings("captcha", 0))
	{
		echo "<tr>";
		echo '<td class="key" width="150px">';
		echo JTEXT::_("COM_MATUKIO_CAPTCHA");
		echo "</td>";
		echo "<td>";

		/**
		 * Generates a random string.. TODO MOVE
		 *
		 * @param   int $len - Length
		 *
		 * @return string
		 */
		function randomString($len)
		{
			/**
			 * Makes a seed
			 *
			 * @return  float
			 */
			function Make_seed()
			{
				list($usec, $sec) = explode(' ', microtime());

				return (float) $sec + ((float) $usec * 100000);
			}

			srand(Make_seed());
			$possible = "ABCDEFGHJKLMNPRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";
			$str = "";

			while (strlen($str) < $len)
			{
				$str .= substr($possible, (rand() % (strlen($possible))), 1);
			}

			return ($str);
		}

		// IE Problems: header('Content-type: image/png');
		$imagepath = (JPATH_BASE . '/components/com_matukio/captcha/');
		$captchatext = randomString(5);
		$img = ImageCreateFromPNG(JPATH_BASE . '/components/com_matukio/captcha/captcha.PNG');

		// Color
		$color = ImageColorAllocate($img, 0, 0, 0);
		$ttf = (JPATH_BASE . '/components/com_matukio/captcha/XFILES.TTF');
		$ttfsize = 25;
		$angle = rand(0, 5);
		$t_x = rand(5, 30);
		$t_y = 35;
		imagettftext($img, $ttfsize, $angle, $t_x, $t_y, $color, $ttf, $captchatext);

		if (!file_exists($imagepath . md5($captchatext) . '.png'))
		{
			imagepng($img, $imagepath . md5($captchatext) . '.png');
		}
		?>
		<input type="text" name="captcha" id="captcha" size="10"> <img src="<?php echo
		'components/com_matukio/captcha/' . md5($captchatext) . '.png' ?>"
		                                                               border="0" title="Captchacode"
		                                                               style="vertical-align:middle;"/>
		<?php
		echo "</td>";
		echo "</tr>";
		echo "</table>";
	}

	// Recaptcha
	if (MatukioHelperSettings::getSettings("recaptcha", 0))
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR . '/include/recaptcha/recaptchalib.php';

		$key = MatukioHelperSettings::getSettings("recaptcha_public_key", "");

		if (empty($key))
		{
			throw new Exception("COM_MATUKIO_YOU_HAVE_TO_SET_A_RECAPTCHA_KEY", 500);
		}

		echo '<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">';
		echo "<tr>";
		echo '<td class="key" width="150px">';
		echo JTEXT::_("COM_MATUKIO_CAPTCHA");
		echo "</td>";
		echo "<td>";
		echo recaptcha_get_html($key);
		echo "</td>";
		echo "</tr>";
		echo "</table>";
	}

	// Payment
	if ($this->steps == 3)
	{
		echo '<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">';

		// Payment type
		echo '<tr>';
		echo '<td class="key" width="150px">';
		echo '<label for="conf_payment_type" width="100" title="' . JText::_("COM_MATUKIO_FIELD_PAYMENT_METHOD") . '">';
		echo JText::_("COM_MATUKIO_FIELD_PAYMENT_METHOD");

		echo " <span class=\"mat_req\">*</span>";

		echo '</label>';
		echo '</td>';

		echo '<td>';

		echo "<div id=\"conf_payment_type\"></div>";

		echo '</td>';
		echo '</tr>';

		if (MatukioHelperSettings::getSettings("payment_coupon", 1) == 1)
		{
			echo '<tr>';
			echo '<td class="key" width="150px">';
			echo '<label for="conf_coupon_code" width="100" title="' . JText::_("COM_MATUKIO_FIELD_COUPON") . '">';
			echo JText::_("COM_MATUKIO_FIELD_COUPON");

			echo '</label>';
			echo '</td>';

			echo '<td >';

			echo "<div id=\"conf_coupon_code\"></div>";

			echo '</td>';
			echo '</tr>';
		}
		else
		{
			echo "<input type=\"hidden\" id=\"conf_coupon_code\" value=\"1\">";
		}

		echo '<tr>';
		echo '<td class="key" width="150px">';
		echo '<label for="conf_payment_total" width="100" title="' . JText::_("COM_MATUKIO_TOTAL_AMOUNT") . '">';
		echo JText::_("COM_MATUKIO_TOTAL_AMOUNT");

		echo '</label>';
		echo '</td>';

		echo '<td >';

		echo "<div id=\"conf_payment_total\"></div>";

		echo '</td>';
		echo '</tr>';


		echo '</table>';
	}
	elseif ($this->event->fees > 0)
	{
		// Show total amount at the end of the booking
		echo '<table id="mat_payment_table" class="mat_table table" border="0" cellpadding="8" cellspacing="8">';
		echo "<tr>";
		echo '<td class="key" width="150px">';
		echo '<label for="conf_payment_total" width="100" title="' . JText::_("COM_MATUKIO_TOTAL_AMOUNT") . '">';
		echo JText::_("COM_MATUKIO_TOTAL_AMOUNT");
		echo '</label>';
		echo '</td>';
		echo "<td>";
		echo "<div id=\"conf_payment_total\"></div>";
		echo "</tr>";
		echo "</td>";
		echo "</table>";
	}

	// AGB
	echo "<br />";
	$agb = MatukioHelperSettings::getSettings("agb_text", "");

	if (!empty($agb))
	{
		JHTML::_('behavior.modal');

		$link = JURI::ROOT() . "index.php?tmpl=component&s=" . MatukioHelperUtilsBasic::getRandomChar()
			. "&option=" . JFactory::getApplication()->input->get('option') . "&view=agb";
		echo MatukioHelperUtilsBooking::getCheckbox("agb", " ", false);
		echo "<a href=\"" . $link . "\" class=\"modal cjmodal\" rel=\"{handler: 'iframe', size: {x:700, y:500}}\">";
		echo JTEXT::_('COM_MATUKIO_TERMS_AND_CONDITIONS');
		echo "</a>";
	}
	?>
	</div>
	<div id="mat_control">
		<div id="mat_control_inner">
			<button id="btn_back" class="btn"><?php echo JTEXT::_("COM_MATUKIO_BACK") ?></button>
			<button id="btn_next" class="btn"><?php echo JTEXT::_("COM_MATUKIO_NEXT") ?></button>
			<?php if ($this->event->fees > 0): ?>
				<button id="btn_submit" class="btn btn-success"><?php echo JTEXT::_("COM_MATUKIO_BOOK_PAID") ?></button>
			<?php else: ?>
				<button id="btn_submit" class="btn btn-success"><?php echo JTEXT::_("COM_MATUKIO_BOOK") ?></button>
			<?php endif; ?>
		</div>
	</div>
	</div>
	</div>
	<span id="loading"></span>

	<?php
	echo $hidden;
	?>

	<input type="hidden" name="option" value="com_matukio" />
	<input type="hidden" name="view" value="bookevent" />
	<input type="hidden" name="controller" value="bookevent" />
	<input type="hidden" name="task" value="book" />
	<input type="hidden" name="uid" value="<?php echo $this->uid; ?>" />
	<input type="hidden" name="steps" value="<?php echo $this->steps; ?>" />
	<input type="hidden" name="event_id" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="catid" value="<?php echo $this->event->catid; ?>" />
	<input type="hidden" name="semid" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="userid" value="<?php echo $this->user->id; ?>" />
	<input type="hidden" name="uuid" value="<?php echo (empty($this->uuid)) ? MatukioHelperPayment::getUuid(true) : $this->uuid; ?>" />
	<input type="hidden" name="id" value="<?php echo (empty($this->booking)) ? 0 : $this->booking->id; ?>" />
	<input type="hidden" name="ccval" value="<?php
	if (!empty($captchatext))
	{
		echo md5($captchatext);
	}
	?>"/>
	</form>

<?php
echo MatukioHelperUtilsBasic::getCopyright();
