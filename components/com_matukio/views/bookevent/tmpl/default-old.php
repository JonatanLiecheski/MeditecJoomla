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

JHTML::_('behavior.modal');
JHTML::_('behavior.tooltip');

JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
JHTML::_('stylesheet', 'media/com_matukio/css/modern.css');
JHTML::_('stylesheet', 'media/com_matukio/css/booking.css');

$usermail = $this->user->email;
?>
	<script type="text/javascript">
	window.addEvent('domready', function () {
		var steps = <?php echo $this->steps; ?>;
		var intro = document.id('mat_intro');
		var current_step = 1;

		var btn_next = document.id('btn_next');
		var btn_back = document.id('btn_back');
		var btn_submit = document.id('btn_submit');

		var page_one = document.id('mat_pageone');
		var page_two = document.id('mat_pagetwo');
		var page_three = document.id('mat_pagethree');
		var payment = document.id('payment');

		var usermail = '<?php echo $usermail; ?>';
		var email = document.id('email');
		var agb = document.id('agb');

		var nrbooked = document.id('nrbooked');
		var coupon_code = document.id('coupon_code');
		var fees = <?php echo $this->event->fees ?>;
		var different_fees = <?php echo $this->event->different_fees ?>;
		var max_bookings = <?php echo $this->event->nrbooked; ?>;

		if (email) {
			email.set('value', usermail);
		}

		<?php
		if (MatukioHelperSettings::getSettings("payment_coupon", 1) == 1 && $this->steps > 2)
		{
			echo "var coupon = true;\n";
		}
		else
		{
			echo "var coupon = false;\n";
		}
		?>

		<?php
		for ($i = 0; $i < count($this->fields_p1); $i++)
		{
			$field = $this->fields_p1[$i];

			if ($field->type != 'spacer' && $field->type != 'spacertext')
			{
				echo "var " . $field->field_name . " = document.id('" . $field->field_name . "');\n";
			}

			// Confirmation fields
			if ($field->type != 'spacer' && $field->type != 'spacertext')
			{
				echo "var conf_" . $field->field_name . " = document.id('conf_" . $field->field_name . "');\n";
			}
		}
		?>

		var mh1, mh2, mh3 = null;

		if (steps == 2) {
			mh1 = document.id('mat_h1');
			mh3 = document.id('mat_h2');
		} else {
			mh1 = document.id('mat_hp1');
			mh2 = document.id('mat_hp2');
			mh3 = document.id('mat_hp3');
		}

		<?php
		// Different fees @since 3.0
		if ($this->event->different_fees == 1 && $this->event->nrbooked > 1
			&& MatukioHelperSettings::getSettings('frontend_usermehrereplaetze', 1) > 0):
		?>

		var count_rows = 0;

		function new_row() {
			count_rows = count_rows + 1;
			var myHTMLRequest = new Request({
				url: 'index.php?option=com_matukio&format=raw&view=requests&task=getnewfeerow&event_id=<?php echo $this->event->id ?>',
				method: 'get',
				autoCancel: true,
				data: {num: count_rows},
				encoding: 'utf-8',
				onRequest: function () {
					$('loading').set('html', '<img src=\"images/ajax-loader.gif\" />');
				},
				onComplete: function (responseText) {

					var new_rows = new Element('div', {
						'html': responseText
					});

					// inject new fields at bottom
					new_rows.inject($('mat_tickets'), 'bottom');

					//    remove loading image
					$('loading').set('text', '');
					//    scroll down to new form fields
					var myFx = new Fx.Scroll(window).toElement('tickets_' + count_rows);

					document.id("delticket" + count_rows).addEvent("click", function (e) {
						e.stop();
						document.id("tickets_" + this.getAttribute("num")).destroy();
					});
				}
			}).send();
		}

		$('addticket').addEvent('click', function (e) {
			e.stop();  // stop the default submission of the form
			new_row();
		});

		<?php endif; ?>

		function validateAGB() {
			if (agb) { // No AGB, so they are always true..
				if (agb.checked == false) {
					return false;
				}
			}

			return true;
		}

		function nextPage(event) {
			event.stop();

			if (current_step == steps) {
				return;
			}

			current_step++;

			if (current_step == 3 && !validatePayment()) {
				alert("<?php echo JTEXT::_("COM_MATUKIO_NO_PAYMENT_SELECTED"); ?>");
				current_step--;
				return;
			}

			// validate input
			if (!validateForm.validate()) {
				alert("<?php echo JTEXT::_("COM_MATUKIO_PLEASE_FILL_OUT_ALL_REQUIRED_FIELDS"); ?>");
				current_step--;
				return;
			}

			<?php if (MatukioHelperSettings::getSettings("payment_coupon", 1) == 1 && $this->steps > 2) : ?>
			if (current_step == 3 && !validateCoupon()) {
				alert("<?php echo JTEXT::_("COM_MATUKIO_INVALID_COUPON_CODE"); ?>");
				current_step--;
				return;
			}
			<?php endif; ?>

			if (different_fees && current_step == 2) {
				var difpl = 0;
				$$(".ticket_places").each(function (num, index) {
					difpl += parseInt(num.get('value'));
				});

				// Set total places
				nrbooked.value = difpl;
			}

			if (nrbooked.value > max_bookings) {
				alert("<?php echo JTEXT::_("COM_MATUKIO_EXCEEDED_MAXIMUM_NUMBER_OF_BOOKABLE_PLACES"); ?> " + max_bookings + ")");
				current_step--;
				return;
			}

			btn_back.setStyle('display', 'inline-block');

			page_one.setStyle('display', 'none');
			mh1.setStyle('display', 'none');


			if (steps == 3 && current_step == 2) {
				page_two.setStyle('display', 'block');

				if (steps != 2) {
					mh2.setStyle('display', 'block');
				}
			}

			if (current_step == steps) {
				page_two.setStyle('display', 'none');
				page_three.setStyle('display', 'block');

				if (steps != 2) {
					mh2.setStyle('display', 'none');
				}
				mh3.setStyle('display', 'block');

				btn_next.setStyle('display', 'none');
				btn_submit.setStyle('display', 'inline-block');

				fillConf();

				if (steps == 3) {
					if (different_fees == 1) {
						calculateDifferentFees();
					} else {
						fillPayment();
					}
				} else {
					if (fees > 0) {
						if (different_fees == 1) {
							calculateDifferentFees();
						} else {
							fillTotal();
						}
					}
				}
			}
		}

		function prevPage(event) {
			event.stop();

			if (current_step == 1) {
				return;
			}

			current_step--;

			if (steps != 2) {
				mh2.setStyle('display', 'none');
			}
			mh3.setStyle('display', 'none');
			page_three.setStyle('display', 'none');
			btn_submit.setStyle('display', 'none');
			btn_next.setStyle('display', 'inline-block');

			if (steps == 3 && current_step == 2) {
				page_two.setStyle('display', 'block');
				if (steps != 2) {
					mh2.setStyle('display', 'block');
				}
			}

			if (current_step == 1) {
				mh1.setStyle('display', 'block');
				btn_back.setStyle('display', 'none');
				page_two.setStyle('display', 'none');
				page_one.setStyle('display', 'block');
			}
		}

		function sendPage(event) {
			event.stop();

			if (!validateAGB()) {
				alert("<?php echo JText::_("COM_MATUKIO_AGB_NOT_ACCEPTED"); ?>");
				return;
			}

			document.id('FrontForm').submit();
		}

		function fillConf() {
			<?php
				// Generate js code for the confirmation fields
				for ($i = 0; $i < count($this->fields_p1); $i++)
				{
					$field = $this->fields_p1[$i];

					if ($field->type != 'spacer' && $field->type != 'spacertext')
					{
						if ($field->type != 'radio')
						{
							if ($field->type == 'select')
							{
								echo "conf_" . $field->field_name . ".set('text', " . $field->field_name . ".getSelected().get('text'));\n";
							}
							else
							{
								echo "conf_" . $field->field_name . ".set('text', " . $field->field_name . ".get('value'));\n";
							}
						}
						else
						{
							echo "conf_" . $field->field_name . ".set('text', document.id(FrontForm).getElement('input[name="
										. $field->field_name . "]:checked').value);\n";
						}
					}
				}
			?>
		}

		<?php
		/* conf_payment_type, conf_nrbooked, conf_coupon_code, conf_payment_total */
		?>
		function fillPayment() {
			var conf_payment_type = document.id("conf_payment_type");
			var conf_nrbooked = document.id("conf_nrbooked");

			var conf_coupon_code = document.id("conf_coupon_code");
			var conf_payment_total = document.id("conf_payment_total");

			if (conf_payment_type) {
				// Not using value here, use name of the plugin
				conf_payment_type.set('text', document.id("payment").getSelected().get('text'));
			}

			if (conf_nrbooked) {
				conf_nrbooked.set('text', document.id("nrbooked").get('value'));
			}

			if (conf_coupon_code) {
				conf_coupon_code.set('text', document.id("coupon_code").get('value'));
			}

			// The tricky part
			if (conf_payment_total) {
				var code = "";

				if (coupon_code) {
					code = coupon_code.get('value');
				}

				var places = 1;

				if (nrbooked) {
					places = nrbooked.get('value');
				}

				var types = new Array();

				if (different_fees == 1) {
					var cnt = 0;

					$$(".ticket_fees").each(function (fee, index) {
						types[cnt] = fee.get('value');
						cnt++;
					});
				}

				var erg = new Request({
					url: 'index.php?option=com_matukio&view=requests&format=raw&task=get_total&code='
						+ code + '&fee=<?php echo $this->event->fees ?>&nrbooked=' + places + '&types=' + types.join(','),
					method: 'get',
					async: false,

					onSuccess: function (responseText) {
						resp = responseText;
						conf_payment_total.set('text', resp);
					}
				});

				erg.send();
			}
		}

		function fillTotal() {
			var places = 1;

			if (nrbooked) {
				places = nrbooked.get('value');
			}

			var conf_payment_total = document.id("conf_payment_total");

			var paytotal = places * fees;

			if (conf_payment_total) {
				conf_payment_total.set('text', paytotal);
			}
		}

		function calculateDifferentFees() {
			var places = 0;
			var code = "";

			if (coupon_code) {
				code = coupon_code.get('value');
			}

			var conf_payment_type = document.id("conf_payment_type");
			var conf_nrbooked = document.id("conf_nrbooked");

			var conf_coupon_code = document.id("conf_coupon_code");

			if (conf_payment_type) {
				// Not using value here, use name of the plugin
				conf_payment_type.set('text', document.id("payment").getSelected().get('text'));
			}

			if (conf_coupon_code) {
				conf_coupon_code.set('text', document.id("coupon_code").get('value'));
			}

			var conf_payment_total = document.id("conf_payment_total");

			var cnt = 0;

			var ticket_places = new Array();
			var ticket_types = new Array();
			var ticket_disc_value = new Array();
			var ticket_percent = new Array();
			var ticket_discount = new Array();

			$$(".ticket_places").each(function (num, index) {
				ticket_places[cnt] = num.get('value');
				places += parseInt(num.get('value'));
				cnt++;
			});

			// Set total places
			nrbooked.value = places;
			if (conf_nrbooked) {
				conf_nrbooked.set('text', places);
			}

			cnt = 0;
			$$(".ticket_fees").each(function (fee, index) {
				ticket_types[cnt] = fee.get('value');
				ticket_disc_value[cnt] = fee.getSelected().get("discvalue");
				ticket_discount[cnt] = fee.getSelected().get("discount");
				ticket_percent[cnt] = fee.getSelected().get("percent");
				cnt++;
			});

			var erg = new Request({
				url: 'index.php?option=com_matukio&view=requests&format=raw&task=get_total_different&code='
					+ code + '&event_id=<?php echo $this->event->id ?>&fee=<?php echo $this->event->fees; ?>&nrbooked=' + places
					+ '&places=' + ticket_places.join(',') + '&types=' + ticket_types.join(',')
					+ '&disc_value=' + ticket_disc_value.join(',') + '&percent=' + ticket_percent.join(',') + '&discount=' + ticket_discount.join(','),
				method: 'get',
				async: false,

				onSuccess: function (responseText) {
					resp = responseText;
					conf_payment_total.set('text', resp);
					nrbooked.value = places;
				}
			});

			erg.send();
		}

		Form.Validator.add('required', {
			errorMsg: '<?php echo JText::_('COM_MATUKIO_FIELD_REQUIRED');?>',
			test: function (element) {
				if (element.value.length == 0) return false;
				else return true;
			}
		});

		var validateForm = new Form.Validator.Inline(document.id('FrontForm'), {
			//useTitles: true
			errorPrefix: '<?php echo JText::_('COM_MATUKIO_ERROR');?>: '
		});

		function validatePayment() {
			if (payment.get('value') == '') {
				return false;
			}
			return true;
		}

		<?php
		if (MatukioHelperSettings::getSettings("payment_coupon", 1) == 1 && $this->steps > 2)
		{
		?>

		function validateCoupon() {
			var response = false;
			var code = coupon_code.get('value');

			if (code == '') {
				return true;
			}

			var erg = new Request({
				url: 'index.php?option=com_matukio&view=requests&format=raw&task=validate_coupon&code=' + code,
				method: 'get',
				async: false,

				onSuccess: function (responseText) {
					response = responseText;
					//alert(response);
				}
			});

			erg.send();
			return (response === 'true');
		}

		<?php
		}
		?>

		btn_next.addEvent('click', function (event) {
			nextPage(event)
		});
		btn_back.addEvent('click', function (event) {
			prevPage(event)
		});
		btn_submit.addEvent('click', function (event) {
			sendPage(event)
		});

	});
	</script>
	<form action="<?php echo JRoute::_("index.php?option=com_matukio&view=bookevent&task=book"); ?>" method="post" name="FrontForm" id="FrontForm">

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
		foreach ($this->fields_p1 as $field)
		{
			// Prints the field in the table <tr><td>label</td><td>field</td>
			MatukioHelperUtilsBooking::printFieldElement($field, true);
		}
		?>
	</table>
	<?php
	// Old event only fields.. should be removed some time...
	// Zusatzfelder ausgeben
	$buchopt = MatukioHelperUtilsEvents::getEventBookableArray(0, $this->event, $this->user->id);
	$html = "";
	$tempdis = "";
	$hidden = "";
	$reqfield = " <span class=\"sem_reqfield\">*</span>";
	$reqnow = "\n<tr>" . MatukioHelperUtilsEvents::getTableCell("&nbsp;" . $reqfield . " "
			. JTEXT::_('COM_MATUKIO_REQUIRED_FIELD'), 'd', 'r', '', 'sem_nav', 2
		) . "</tr>";

	$zusreq = 0;
	$zusfeld = MatukioHelperUtilsEvents::getAdditionalFieldsFrontend($this->event);
	$zustemp = array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');

	if (count($buchopt[2]) > 0)
	{
		$zustemp = MatukioHelperUtilsEvents::getAdditionalFieldsFrontend($buchopt[2][0]);
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
							$optionen[] = JHTML::_('select.option', $zusart[$z], $zusart[$z]);
						}

						$htxt = JHTML::_('select.genericlist', $optionen, 'zusatz' . ($i + 1), 'class="sem_inputbox" size="1"' . $tempdis, 'value', 'text', $zustemp[$i]) . $temp;
						break;

					case "radio":
						for ($z = 4; $z < count($zusart); $z++)
						{
							$optionen[] = JHTML::_('select.option', $zusart[$z], $zusart[$z]);
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
							. $zustemp[$i] . "\" size=\"50\"" . $tempdis . ">" . $temp;
						break;

					default:
						$htxt = "<input type=\"text\" class=\"sem_inputbox\" id=\"zusatz" . ($i + 1) . "\" name=\"zusatz" . ($i + 1) . "\" value=\""
							. $zustemp[$i] . "\" size=\"50\"" . $tempdis . ">" . $temp;
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


			$platzauswahl = JHTML::_('select.genericlist', $this->limits, 'nrbooked', 'class="sem_inputbox" size="1"' . $tempdis,
				'value', 'text', 1
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

			if (!empty($this->event->different_fees_override))
			{
				// We have an override for this event
				$fees_list = MatukioHelperFees::getOverrideFees($this->event->different_fees_override);
			}
			else
			{
				$fees_list = MatukioHelperFees::getFees();
			}

			echo '<div id="mat_tickets">';
			echo "<table class=\"mat_table table\">\n";
			echo '<tr>';
			echo '<td class="key" width="150px">';

			echo JText::_("COM_MATUKIO_PLACES_TO_BOOK") . " ";
			echo MatukioHelperUtilsEvents::getPlaceSelect($buchopt, $this->event, 0);

			echo '</td>';
			echo '<td>';

			echo JText::_("COM_MATUKIO_TICKET_TYPE") . " ";

			echo '<select id="ticket_fees0" name="ticket_fees[0]" class="sem_inputbox chzn-single ticket_fees" size="1">';
			echo '<option value="0" selected="selected" discvalue="0" discount="1" percent="1">- ' . JText::_("COM_MATUKIO_NORMAL") . ' -</option>';

			foreach ($fees_list as $f)
			{
				$disc_text = ($f->discount) ? '-' : '+';

				if (MatukioHelperSettings::getSettings('different_fees_absolute', 1))
				{
					if (!$f->percent)
					{
						$fval = $this->event->fees - $f->value;
					}
					else
					{
						// Calculate fees
						$fval = $this->event->fees - ($this->event->fees * ($f->value / 100));
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
			echo " <a id=\"addticket\" class=\"mat_addticket\" border=\"0\" href=\"#\"><span class=\"mat_add\" type=\"button\">
					<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
				. "1832.png\" border=\"0\" align=\"absmiddle\" style=\"width: 16px; height: 16px;\">&nbsp;"
				. JTEXT::_('COM_MATUKIO_ADD') . "</span></a>";

			echo '</td>';
			echo '</tr>';
			echo "</table>";
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

			echo '<select id="ticket_fees0" name="ticket_fees[0]" class="sem_inputbox chzn-single ticket_fees" size="1">';
			echo '<option value="0" selected="selected" discvalue="0" discount="1" percent="1">- ' . JText::_("COM_MATUKIO_NORMAL") . ' -</option>';

			foreach ($fees_list as $f)
			{
				$disc_text = ($f->discount) ? '-' : '+';

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
				echo MatukioHelperPayment::getPaymentSelect($this->payment);
				echo '</td>';
				echo '</tr>';
				?>
			</table>
			<?php

			// Payment Coupon codes
			if (MatukioHelperSettings::getSettings("payment_coupon", 1) == 1)
			{
				?>
				<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">
					<tr>
						<td class="key" width="150px">
							<?php echo JText::_("COM_MATUKIO_FIELD_COUPON"); ?>
						</td>
						<td>
							<input class="text_area" type="text" name="coupon_code"
							       id="coupon_code" value="" size="50"
							       maxlength="255" style="width: 150px"
							       title="<?php echo JText::_('COM_MATUKIO_FIELD_COUPON_DESC') ?>"/>
						</td>
					</tr>
				</table>
			<?php
			}
			else
			{
				?>
				<input type="hidden" name="coupon_code" id="coupon_code" value=""/>
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
						MatukioHelperUtilsBooking::printFieldElement($field);
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
					MatukioHelperUtilsBooking::printFieldElement($field);
				}
				?>
			</table>
		<?php
		}
		?>
	</table>
	<?php
	echo "<br />";

	// Captcha
	if (MatukioHelperSettings::getSettings("captcha", 0))
	{
		echo '<table class="mat_table table" border="0" cellpadding="8" cellspacing="8">';
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

		// Nr Booked
		if ($this->event->nrbooked > 1 AND MatukioHelperSettings::getSettings('frontend_usermehrereplaetze', 1) > 0)
		{
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
		}
		else
		{
			echo "<input type=\"hidden\" id=\"conf_nrbooked\" value=\"1\">";
		}

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
			<button id="btn_back" class="mat_button"><?php echo JTEXT::_("COM_MATUKIO_BACK") ?></button>
			<button id="btn_next" class="mat_button"><?php echo JTEXT::_("COM_MATUKIO_NEXT") ?></button>
			<?php if ($this->event->fees > 0): ?>
				<button id="btn_submit" class="mat_button"><?php echo JTEXT::_("COM_MATUKIO_BOOK_PAID") ?></button>
			<?php else: ?>
				<button id="btn_submit" class="mat_button"><?php echo JTEXT::_("COM_MATUKIO_BOOK") ?></button>
			<?php endif; ?>
		</div>
	</div>
	</div>
	<span id="loading"></span>

	<?php
	echo $hidden;
	?>

	<input type="hidden" name="option" value="com_matukio"/>
	<input type="hidden" name="view" value="bookevent"/>
	<input type="hidden" name="controller" value="bookevent"/>
	<input type="hidden" name="task" value="book"/>
	<input type="hidden" name="uid" value="<?php echo $this->uid; ?>"/>
	<input type="hidden" name="steps" value="<?php echo $this->steps; ?>"/>
	<input type="hidden" name="event_id" value="<?php echo $this->event->id; ?>"/>
	<input type="hidden" name="catid" value="<?php echo $this->event->catid; ?>"/>
	<input type="hidden" name="semid" value="<?php echo $this->event->id; ?>"/>
	<input type="hidden" name="userid" value="<?php echo $this->user->id; ?>"/>
	<input type="hidden" name="uuid" value="<?php echo MatukioHelperPayment::getUuid(true); ?>"/>
	<input type="hidden" name="ccval" value="<?php
	if (!empty($captchatext))
	{
		echo md5($captchatext);
	}
	?>"/>
	</form>

<?php
echo MatukioHelperUtilsBasic::getCopyright();
