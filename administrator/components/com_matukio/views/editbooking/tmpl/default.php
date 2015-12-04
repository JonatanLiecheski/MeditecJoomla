<?php
/**
 * Matukio
 * @package Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 2.0.0 Stable $
 **/

defined('_JEXEC') or die('Restricted access');

// Load event (use events helper function)
$event = MatukioHelperUtilsEvents::getEventRecurring($this->booking->semid);

// We need Bootstrap since 3.0
if (JVERSION > 2.5)
{
	JHtml::_('formbehavior.chosen', 'select');
}

// Load formvalidator!
JHtml::_('behavior.formvalidation');

echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'bookings', 'COM_MATUKIO_EDIT_BOOKING', 'COM_MATUKIO_SLOGAN_BOOKINGS');

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');
?>
<div class="box-info full">
	<div id="matukio" class="matukio">
	<form action="<?php echo JRoute::_("index.php?option=com_matukio&controller=editbooking&task=editBooking"); ?>" method="post"
	      name="adminForm" id="adminForm" class="form" enctype="multipart/form-data">
		<div class="row">
		<div class="col-lg-9 col-md-8 col-xs-12 col-sm-12">
		<div class="table-responsive">
		<table class="table table-bordered">
			<tr>
				<td align="left" class="key">
					<?php echo JText::_('COM_MATUKIO_EVENT'); ?>
				</td>
				<td>
					<?php
					echo $this->event_select;
					?>
				</td>
			</tr>

			<tr>
				<td align="left" class="key">
					<?php echo JText::_('COM_MATUKIO_USER'); ?>
				</td>
				<td>
					<?php
					echo JHTML::_('list.users', "userid", $this->booking->userid, true, null, "name", 0);
					?>
				</td>
			</tr>

			<tr>
				<td align="left" class="key">
					<?php echo JText::_('COM_MATUKIO_STATUS'); ?>
				</td>
				<td>
					<?php
					echo $this->status_select;
					?>
				</td>
			</tr>

			<?php
			if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 1)
			{
				?>
				<tr>
					<td width="200" align="left" class="key">
						<?php echo JText::_('COM_MATUKIO_NAME'); ?>
					</td>
					<td>
						<input type="text" class="sem_inputbox" id="name" name="name"
						       value="<?php echo $this->booking->name; ?>" size="50"/>
					</td>
				</tr>
				<tr>
					<td width="200" align="left" class="key">
						<?php echo JText::_('COM_MATUKIO_EMAIL'); ?>
					</td>
					<td>
						<input type="text" class="sem_inputbox" id="email" name="email"
						       value="<?php echo $this->booking->email; ?>" size="50"/>
					</td>
				</tr>
			<?php
			}
			else
			{
				// New booking form..
				$fields = MatukioHelperUtilsBooking::getBookingFields();
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
					if (!empty($value[$field->id]))
					{
						MatukioHelperUtilsBooking::printFieldElement($field, false, $value[$field->id]);
					}
					else
					{
						MatukioHelperUtilsBooking::printFieldElement($field, false, -1);
					}
				}

				if (isset($event) && $event->fees > 0 && isset($this->booking) && $this->booking->semid)
				{
					echo '<tr>';
					echo '<td class="key" width="150px">';
					echo JText::_("COM_MATUKIO_FIELD_PAYMENT_METHOD");
					echo " <span class=\"mat_req\">*</span>";
					echo '</td>';
					echo '<td>';
					echo MatukioHelperPayment::getPaymentSelect($this->payment, $this->booking->payment_method);
					echo '</td>';
					echo '</tr>';

					// Payment Coupon codes
					if (MatukioHelperSettings::getSettings("payment_coupon", 1) == 1)
					{
						?>
						<tr>
							<td class="key" width="150px">
								<?php echo JText::_("COM_MATUKIO_FIELD_COUPON"); ?>
							</td>
							<td>
								<input class="text_area" type="text" name="coupon_code"
								       id="coupon_code" size="50"
								       maxlength="255" style="width: 150px"
								       title="<?php echo JText::_('COM_MATUKIO_FIELD_COUPON_DESC') ?>"
								       value="<?php echo $this->booking->coupon_code; ?>"
									/>
							</td>
						</tr>
					<?php
					}
				}
			}
			?>
		</table>
		</div>
		<?php
		$hidden = "";

		// We only have the old event specific custom fields if we have an event ;)
		if ($this->booking->semid)
		{
			// Old event only fields.. should be removed some time...
			// Zusatzfelder ausgeben
			if (!empty($this->booking->id))
			{
				$buchopt = MatukioHelperUtilsEvents::getEventBookableArray(0, $event, $this->booking->userid);
			}
			else
			{
				$buchopt = "";
			}


			$html = "";
			$tempdis = "";
			$hidden = "";
			$reqfield = " <span class=\"sem_reqfield\">*</span>";
			$reqnow = "\n<tr>" . MatukioHelperUtilsEvents::getTableCell("&nbsp;" . $reqfield . " "
				. JTEXT::_('COM_MATUKIO_REQUIRED_FIELD'), 'd', 'r', '', 'sem_nav', 2
			) . "</tr>";

			$zusreq = 0;
			$zusfeld = MatukioHelperUtilsEvents::getAdditionalFieldsFrontend($event);
			$zustemp = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

			$zustemp = MatukioHelperUtilsEvents::getAdditionalFieldsFrontend($event, $this->booking);
			$zustemp = $zustemp[0];

			for ($i = 0; $i < count($zusfeld[0]); $i++)
			{
				if ($zusfeld[0][$i] != "")
				{
					$zusart = explode("|", $zusfeld[0][$i]);

					if (count($buchopt[2]) == 0)
					{
						// $zustemp[$i] = $zusart[2];
					}

					$name = 'zusatz' . ($i + 1);
					$val = $this->booking->$name;
					$zustemp[$i] = $val;

					$htxt = $zusart[0] . MatukioHelperUtilsBasic::createToolTip($zusfeld[1][$i]);
					$temp = "";
					$html .= "\n<tr>" . MatukioHelperUtilsEvents::getTableCell($htxt, 'd', 'l', '150px', 'sem_rowd');

					if ($tempdis == "")
					{
						if ($zusart[1] == 1)
						{
							$temp = $reqfield;
							$reqtext = $reqnow;
						}
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

								$htxt = JHTML::_('select.genericlist', $optionen, 'zusatz' . ($i + 1), 'class="sem_inputbox" size="1"'
										. $tempdis, 'value', 'text', $zustemp[$i]
									) . $temp;
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

								$htxt = JHTML::_('select.radiolist', $optionen, 'zusatz' . ($i + 1), 'class="sem_inputbox"' . $tempdis, 'value', 'text', $auswahl) . $temp;
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

								$htxt = "<textarea class=\"sem_inputbox\" id=\"zusatz" . ($i + 1) . "\" name=\"zusatz" . ($i + 1) . "\" cols=\""
									. $zusart[4] . "\" rows=\"" . $zusart[5] . "\"" . $tempdis . ">" . $zustemp[$i] . "</textarea>" . $temp;
								break;
							case "email":
								$htxt = "<input type=\"text\" class=\"sem_inputbox\" id=\"emailzusatz" . ($i + 1) . "\" name=\"zusatz" . ($i + 1) . "\" value=\""
									. $zustemp[$i] . "\" size=\"50\"" . $tempdis . " />" . $temp;
								break;
							default:
								$htxt = "<input type=\"text\" class=\"sem_inputbox\" id=\"zusatz" . ($i + 1) . "\" name=\"zusatz" . ($i + 1) . "\" value=\""
									. $zustemp[$i] . "\" size=\"50\"" . $tempdis . " />" . $temp;
								break;
						}
					}
					else
					{
						$htxt = "<input class=\"sem_inputbox\" type=\"text\" id=\"zusatz" . ($i + 1) . "\" name=\"zusatz" . ($i + 1) . "\" value=\"" . $zustemp[$i]
							. "\" size=\"50\"" . $tempdis . ">" . $temp;
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
					$hidden .= "<input type=\"hidden\" id=\"zusatz" . ($i + 1) . "\" name=\"zusatz"
						. ($i + 1) . "\" value=\"\"><input type=\"hidden\" name=\"zusatz" . ($i + 1) . "opt\" value=\"0\">";
				}
			}

			echo "<table class=\"mat_table table table-hover\">\n";
			echo $html;
			echo "</table>";
		}

		if (isset($event) && $event->nrbooked > 1 && MatukioHelperSettings::getSettings('frontend_usermehrereplaetze', 1) > 0)
		{
			if ($event->different_fees == 0)
			{
				echo "<table class=\"mat_table table\">\n";
				$this->limits = array();

				for ($i = 1; $i <= $event->nrbooked; $i++)
				{
					$this->limits[] = JHTML::_('select.option', $i);
				}

				$platzauswahl = JHTML::_('select.genericlist', $this->limits,
					'nrbooked', 'class="sem_inputbox" size="1"' . $tempdis,
					'value', 'text', $this->booking->nrbooked
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
					$htx2 = "<input class=\"sem_inputbox\" type=\"text\" value=\"" . $buchopt[2][0]->nrbooked
						. "\"size=\"1\" style=\"text-align:right;\"" . $tempdis . " />";
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
				echo "<input type=\"hidden\" name=\"nrbooked\" id=\"nrbooked\" value=\"" . $this->booking->nrbooked . "\" />";

				if (!empty($this->booking->different_fees))
				{
					echo MatukioHelperFees::getEditBookingFeesList($this->booking->different_fees, $event, $buchopt);
				}
				else
				{
					echo JText::_("COM_MATUKIO_NO_TICKETS_BOOKED_WITH_DIFFERENT_FEES");
				}
			}
		}
		else
		{
			echo "<table class=\"mat_table table\">\n";
			echo '<tr>';
			echo '<td class="key" width="150px">';
			echo JTEXT::_('COM_MATUKIO_PLACES_TO_BOOK');
			echo " <span class=\"mat_req\">*</span>";
			echo '</td>';
			echo '<td>';
			echo "<input class=\"sem_inputbox\" type=\"text\" name=\"nrbooked\" id=\"nrbooked\" value=\"1\"size=\"1\" style=\"text-align:right;\" />";
			echo '</td>';
			echo '</tr>';
			echo "</table>";
		}

		if(MatukioHelperSettings::getSettings('participant_grading_system', 0))
		{
			echo "<table class=\"mat_table table\">\n";
			echo '<tr>';
			echo '<td class="key" width="150px">';
			echo JTEXT::_('COM_MATUKIO_PARTICIPANT_MARK');
			echo '</td>';
			echo '<td>';
			echo $this->mark_select;
			echo '</td>';
			echo '</tr>';
			echo "</table>";
		}

		echo "<table class=\"mat_table table\">\n";
		echo '<tr>';
		echo '<td class="key" width="150px">';
		echo JTEXT::_('COM_MATUKIO_CHECKED_IN');
		echo '</td>';
		echo '<td>';
		echo $this->select_checkedin;
		echo '</td>';
		echo '</tr>';
		echo "</table>";

		echo $hidden;

		if (MatukioHelperSettings::getSettings('oldbookingform', 0) == 0)
		{
			?>
			<input type="hidden" name="id" value="<?php echo $this->booking->id; ?>"/>
			<input type="hidden" name="booking_id" value="<?php echo $this->booking->id; ?>"/>
			<input type="hidden" name="oldform" value="0"/>
			<input type="hidden" name="uid" value="<?php echo $this->booking->userid; ?>"/>
			<input type="hidden" name="uuid" value="<?php echo $this->booking->uuid; ?>"/>
			<input type="hidden" name="task" value="" />
		<?php
		}
		else
		{
			?>
			<input type="hidden" name="id" value="<?php echo $this->booking->id; ?>"/>
			<input type="hidden" name="booking_id" value="<?php echo $this->booking->id; ?>"/>
			<input type="hidden" name="uid" value="<?php echo $this->booking->userid; ?>"/>
			<input type="hidden" name="uuid" value="<?php echo $this->booking->uuid; ?>"/>
			<input type="hidden" name="task" value="editbooking" />
			<input type="hidden" name="controller" value="editbooking" />
			<input type="hidden" name="view" value="editbooking" />
			<input type="hidden" name="oldform" value="1"/>
		<?php
		}
		?>
		<?php echo JHTML::_('form.token'); ?>
		</div>

		<!-- Settings @since 4.0.7 -->
		<div class="col-lg-3 col-md-4 col-xs-12 col-sm-12">
			<table class="table table-bordered table-hover">
				<tr class="success">
					<td colspan="2"><?php echo JText::_("COM_MATUKIO_SETTINGS"); ?></td>
				</tr>
				<tr>
					<td>
						<div class="checkbox">
							<input type="checkbox" name="notify_participant" checked="checked" value="1" />
							<?php echo JText::_('COM_MATUKIO_LABEL_NOTIFY_PARTICIPANT'); ?>
						</div>
					</td>
				</tr>
				<tr>
					<td>
						<div class="checkbox">
							<input type="checkbox" name="notify_participant_invoice" checked="checked" value="1" />
							<?php echo JText::_('COM_MATUKIO_LABEL_NOTIFY_PARTICIPANT_INVOICE'); ?>
						</div>
					</td>
				</tr>
			</table>
		</div>
		<!-- Informations @since 3.0 -->
		<div class="col-lg-3 col-md-4 col-xs-12 col-sm-12">
			<table class="table table-bordered table-hover">
				<tr class="info">
					<td colspan="2"><?php echo JText::_("COM_MATUKIO_INFORMATIONS"); ?></td>
				</tr>
				<tr>
					<td width="100" align="left" class="key">
						<?php echo JText::_('COM_MATUKIO_BOOKING_ID'); ?>:
					</td>
					<td>
						<?php
						if (!empty($this->booking->id))
						{
							echo MatukioHelperUtilsBooking::getBookingId($this->booking->id) . " (" . $this->booking->id . ")";
						}
						?>
					</td>
				</tr>
				<tr>
					<td align="left" class="key">
						<?php echo JText::_('COM_MATUKIO_BOOKING_DATE'); ?>:
					</td>
					<td>
						<?php echo $this->booking->bookingdate; ?>
					</td>
				</tr>
				<tr>
					<td align="left" class="key">
						<?php echo JText::_('COM_MATUKIO_AMOUNT_NETTO'); ?>:
					</td>
					<td>
						<?php echo MatukioHelperUtilsEvents::getFormatedCurrency(
							$this->booking->payment_netto,
							MatukioHelperSettings::getSettings('currency_symbol', '$')
						); ?>
					</td>
				</tr>
				<tr>
					<td align="left" class="key">
						<?php echo JText::_('COM_MATUKIO_TAX'); ?>:
					</td>
					<td>
						+ <?php echo MatukioHelperUtilsEvents::getFormatedCurrency(
							$this->booking->payment_tax,
							MatukioHelperSettings::getSettings('currency_symbol', '$')
						); ?>
					</td>
				</tr>
				<tr>
					<td align="left" class="key">
						<?php echo JText::_('COM_MATUKIO_TOTAL_AMOUNT'); ?>:
					</td>
					<td>
						= <?php echo MatukioHelperUtilsEvents::getFormatedCurrency(
							$this->booking->payment_brutto,
							MatukioHelperSettings::getSettings('currency_symbol', '$')
						); ?>
					</td>
				</tr>
			</table>
		</div>
		</div>
	</form>
	</div>
</div>

<div class="clr"></div>
<?php
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
