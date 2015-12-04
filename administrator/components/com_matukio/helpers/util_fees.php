<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       04.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');


/**
 * Class MatukioHelperFees
 *
 * @since  3.0.0
 */
class MatukioHelperFees
{
	private static $instance;

	/**
	 * Gets all fees in an objectlist out of the database
	 *
	 * @param   mixed   $published          - Published parameter, 0, 1 or anything else for all
	 * @param   int     $published_up_down  - bool int
	 * @param   string  $order_by           - order by (default title ASC)
	 *
	 * @return  mixed
	 */
	public static function getFees($published = 1, $published_up_down = 1, $order_by = "title ASC")
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("*")->from("#__matukio_different_fees");

		if ($published == 1)
		{
			$query->where("published = 1");
		}
		elseif ($published === 0)
		{
			$query->where("published = 0");
		}

		if ($published_up_down)
		{
			$cdate = new DateTime;

			$query->where('(published_up < ' . $db->quote($cdate->format('Y-m-d H:i:s'))
				. ' OR published_up = ' . $db->quote("0000-00-00 00:00:00") . ') '
				. 'AND (published_down > ' . $db->quote($cdate->format('Y-m-d H:i:s'))
				. ' OR published_down = ' . $db->quote("0000-00-00 00:00:00") . ')'
			);
		}

		$query->order($order_by);

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Returns the total payment if different fees are used
	 *
	 * @param   object  $event  - The event
	 *
	 * @throws  Exception - if the coupon code is not valid and on database errors
	 * @return  int
	 */
	public static function getPaymentTotal($event)
	{
		$input = JFactory::getApplication()->input;

		// Different fees
		$places = $input->get("places", array(), 'Array');
		$types = $input->get("ticket_fees",  array(), 'Array');

		$total = 0;

		$ofee = null;

		if (!empty($event->different_fees_override))
		{
			$ofee = self::getOverrideFees($event->different_fees_override);
		}

		for ($i = 0; $i < count($places); $i++)
		{
			$p = $places[$i];
			$t = $types[$i];
			$single_fee = $event->fees;

			echo $p . " = " . $t;

			if ($t == 0)
			{
				$total += ($p * $single_fee);
			}
			else
			{
				if (empty($event->different_fees_override))
				{
					// We have no event specific fee overrides
					$cdate = new DateTime;

					$db = JFactory::getDbo();
					$query = $db->getQuery(true);
					$query->select("*")->from("#__matukio_different_fees")->where("id = " . $db->quote($t) . ' AND published = 1 AND (published_up < '
						. $db->quote($cdate->format('Y-m-d H:i:s')) . ' OR published_up = ' . $db->quote("0000-00-00 00:00:00") . ') '
						. 'AND (published_down > ' . $db->quote($cdate->format('Y-m-d H:i:s'))
						. ' OR published_down = ' . $db->quote("0000-00-00 00:00:00") . ')'
					);

					$db->setQuery($query);
					$row = $db->loadObject();

					if (!empty($row))
					{
						if ($row->percent == 0)
						{
							if ($row->discount)
							{
								$single_fee -= $row->value;
							}
							else
							{
								$single_fee += $row->value;
							}
						}
						else
						{
							if ($row->discount)
							{
								$single_fee = $single_fee - ($single_fee * ($row->value / 100));
							}
							else
							{
								$single_fee = $single_fee + ($single_fee * ($row->value / 100));
							}
						}
					}

					// If we have none we fall back to the normal wee
					$total += ($p * $single_fee);
				}
				else
				{
					// We have event specific overrides
					$cdate = new DateTime;

					foreach ($ofee as $o)
					{
						if ($o->id == $t)
						{
							// Check if the fee is still active
							if (($o->published_up < $cdate->format('Y-m-d') || $o->published_up == null)
								&& ($o->published_down > $cdate->format('Y-m-d') || $o->published_down == null))
							{
								// Valid fee
								if ($o->percent == 0)
								{
									if ($o->discount)
									{
										$single_fee -= $o->value;
									}
									else
									{
										$single_fee += $o->value;
									}
								}
								else
								{
									if ($o->discount)
									{
										$single_fee = $single_fee - ($single_fee * ($o->value / 100));
									}
									else
									{
										$single_fee = $single_fee + ($single_fee * ($o->value / 100));
									}
								}
							}
						}
					}

					// If we have none we fall back to the default single fee
					$total += ($p * $single_fee);
				}
			}
		}

		$coupon_code = $input->get("coupon_code", '');

		// Calculate Coupons if any
		if (!empty($coupon_code))
		{
			$cdate = new DateTime;

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('*')->from('#__matukio_booking_coupons')
				->where('code = ' . $db->quote($coupon_code) . ' AND published = 1 AND (published_up < '
					. $db->quote($cdate->format('Y-m-d H:i:s')) . ' OR published_up = ' . $db->quote("0000-00-00 00:00:00") . ') '
					. 'AND (published_down > ' . $db->quote($cdate->format('Y-m-d H:i:s'))
					. ' OR published_down = ' . $db->quote("0000-00-00 00:00:00") . ')'
				);

			$db->setQuery($query);
			$coupon = $db->loadObject();

			if (!empty($coupon))
			{
				if ($coupon->procent == 1)
				{
					// Get a procent value
					$total = round($total * ((100 - $coupon->value) / 100), 2);
				}
				else
				{
					// Get a real value
					$total = $total - $coupon->value;
				}

				// Check how often the coupon is used and if used to often set published to 0 (since 3.0.0)
				$coupon->hits++;

				// Check if coupon has to be disabled now
				if (!empty($coupon->max_hits) && $coupon->hits >= $coupon->max_hits)
				{
					$coupon->published = 0;
				}

				$coupontable = JTable::getInstance('coupons', 'Table');

				if (!$coupontable->bind($coupon))
				{
					throw new Exception(42, $coupontable->getError());
				}

				if (!$coupontable->check())
				{
					throw new Exception(42, $coupontable->stderr());
				}

				if (!$coupontable->store())
				{
					throw new Exception(42, $coupontable->stderr());
				}

				$coupontable->checkin();
			}
			else
			{
				throw new Exception("Coupon code not found!", 500);
			}
		}

		return $total;
	}

	/**
	 * Gets the Edit field for the different prices :)
	 *
	 * @param   int    $num  - The number (current count)
	 * @param   array  $fee  - The fee array.. need to convert to std class
	 *
	 * @return string
	 */
	public static function getDifferentFeeEdit($num, $fee = null)
	{
		if (!$fee)
		{
			$fee = JTable::getInstance('differentfees', 'MatukioTable');
			$fee->discount = 1;
			$fee->percent = 1;
			$fee->id = $num + 1;
		}
		else
		{
			// Convert the array to to std class...
			$f = JTable::getInstance('differentfees', 'MatukioTable');
			$f->title = $fee["title"];
			$f->value = $fee["value"];
			$f->discount = $fee["discount"];
			$f->percent = $fee["percent"];
			$f->id = $fee["id"];

			if (!empty($fee["published_up"]))
			{
				$f->published_up = $fee["published_up"];
			}

			if (!empty($fee["published_down"]))
			{
				$f->published_down = $fee["published_down"];
			}

			$f->published = 1;
			$fee = $f;
		}

		$select_percent = MatukioHelperInput::getRadioButtonBool("different_fees_override[" . $num . "][percent]",
			"different_fees_override_percent" . $num, $fee->percent
		);
		$select_discount = MatukioHelperInput::getRadioButtonBool("different_fees_override[" . $num . "][discount]",
			"different_fees_override_discount" . $num, $fee->discount
		);

		$html = '<div class="different_fee" id="fee_' . $num . '">';
		$html .= '<div class="row show-grid">';

		$html .= '<div class="col-sm-2">';
		$html .= '<input class="form-control" type="text" name="different_fees_override[' . $num
			. '][title]" id="different_fees_override_title' . $num . '" size="50" maxlength="250"
								 value="' . $fee->title . '" placeholder="' . JText::_("COM_MATUKIO_FEE_TITLE") . '" />';
		$html .= '</div>';

		$html .= '<div class="col-sm-1">';
		$html .= '<input class="form-control price" type="text" name="different_fees_override[' . $num
			. '][value]" id="different_fees_override_value' . $num . '" size="10" maxlength="15"
								       value="' . $fee->value . '" placeholder="' . JText::_("COM_MATUKIO_VALUE") . '"/>';
		$html .= '</div>';

		$html .= '<div class="col-sm-2">';
		$html .= JText::_("COM_MATUKIO_PERCENT") . " ";
		$html .= $select_percent;
		$html .= '</div>';

		$html .= '<div class="col-sm-2">';
		$html .= JText::_("COM_MATUKIO_FEE_DISCOUNT") . " ";
		$html .= $select_discount;
		$html .= '</div>';

		$html .= '<div class="col-sm-2">';
		$html .= JHtml::_('calendar', $fee->published_up, 'different_fees_override[' . $num
			. '][published_up]', 'different_fees_override_published_up' . $num,
			'%Y-%m-%d', array(
				'style' => "width: 100px;",
				'class' => "form-control date",
				'placeholder' => JText::_("COM_MATUKIO_PUBLISHED_UP")
			)
		);
		$html .= '</div>';

		$html .= '<div class="col-sm-2">';
		$html .= JHtml::_('calendar', $fee->published_down, 'different_fees_override[' . $num . '][published_down]',
			'different_fees_override_published_down' . $num,
			'%Y-%m-%d', array(
				'style' => "width: 100px;",
				'class' => "form-control date",
				'placeholder' => JText::_("COM_MATUKIO_PUBLISHED_DOWN")
			)
		);
		$html .= '</div>';

		if ($num != 0)
		{
			$html .= '<div class="col-sm-1">';
			$html .= '<button id="rem_fee' . $num . '" class="btn btn-danger" type="button">' . JText::_("COM_MATUKIO_REMOVE") . '</button>';
			$html .= '</div>';
		}
		else
		{
			$html .= '<div class="col-sm-1">';
			$html .= '<button id="add_fee" class="btn btn-success" type="button">' . JText::_("COM_MATUKIO_ADD") . '</button>';
			$html .= '</div>';
		}


		$html .= '<input type="hidden" name="different_fees_override[' . $num
			. '][num]" id="different_fees_override_num' . $num . '" value="' . $num . '" />';

		$html .= '<input type="hidden" name="different_fees_override[' . $num
			. '][id]" id="different_fees_override_id' . $num . '" value="' . ($fee->id) . '" />';

		$html .= '</div>';
		$html .= '</div>';

		// Add JavaSCript to Remove other fees
		if ($num != 0)
		{
			$html .= '
				<script type="text/javascript">
				jQuery("#rem_fee' . $num . '").click(function() {
					jQuery("#fee_' . $num . '").remove();
				});
				</script>
			';
		}

		return $html;
	}

	/**
	 * Gets the override fees (converting to feeobj)
	 *
	 * @param   string  $json  - The json
	 *
	 * @return  array
	 */
	public static function getOverrideFees($json)
	{
		$fee_array = json_decode($json, true);
		$fees = Array();

		foreach ($fee_array as $fee)
		{
			// Convert the array to to std class...
			$f = JTable::getInstance('differentfees', 'MatukioTable');
			$f->title = $fee["title"];
			$f->value = $fee["value"];
			$f->discount = $fee["discount"];
			$f->percent = $fee["percent"];
			$f->num = $fee["num"];
			$f->id = $fee["id"];

			if (!empty($fee["published_up"]))
			{
				$f->published_up = $fee["published_up"];
			}

			if (!empty($fee["published_down"]))
			{
				$f->published_down = $fee["published_down"];
			}

			$fees[] = $f;
		}

		return $fees;
	}

	/**
	 * Returns the fees html code
	 *
	 * @param   string  $different_fees  - The json encoded string
	 * @param   object  $event           - The event
	 * @param   array   $buchopt         - Array of booking informations
	 *
	 * @return string
	 */
	public static function getEditBookingFeesList($different_fees, $event, $buchopt)
	{
		$json = json_decode($different_fees, true);

		$places = $json["places"];
		$types = $json["types"];

		$html = '<div id="fees_table">';
		$html .= '<table class="table">';

		// Fix for empty places / types - we need at least one
		if (empty($places))
		{
			$places = array(1);
			$types = array(0);
		}

		// We also have to look for unpublished / ended fees..
		if (empty($event->different_fees_override))
		{
			$fees_list = self::getFees("all", 0);
		}
		else
		{
			$html .= "<tr><td colspan=\"3\">" . JText::_("COM_MATUKIO_OVERRIDEN_CUSTOM_EVENT_FEES") . "</td></tr>";
			$fees_list = self::getOverrideFees($event->different_fees_override);
		}

		$html .= '<tr><td colspan="3">';

		// Add additional tickets in another category!
		$html .= "<div align=\"right\"><a id=\"addticket\" class=\"mat_addticket\" border=\"0\" href=\"#\">
		<span class=\"mat_add btn btn-success\" type=\"button\">
					<img src=\""	. MatukioHelperUtilsBasic::getComponentImagePath()
			. "1832.png\" border=\"0\" align=\"absmiddle\" style=\"width: 16px; height: 16px;\">&nbsp;"
			. JTEXT::_('COM_MATUKIO_ADD') . "</span></a></div>";

		$html .= '</td></tr>';

		$cnt = 0;

		for ($i = 0; $i < count($places); $i++)
		{
			$p = $places[$i];
			$t = $types[$i];

			$html .= '<tr id="tickets_' . $i . '">';
			$html .= '<td class="key" width="150px">';

			$html .= JText::_("COM_MATUKIO_PLACES") . ": ";
			$html .= MatukioHelperUtilsEvents::getPlaceSelect($buchopt, $event, $i, $p);

			$html .= '</td>';
			$html .= '<td>';

			$html .= JText::_("COM_MATUKIO_TICKET_TYPE_SHORT") . ": ";

			$html .= '<select id="ticket_fees' . $i . '" name="ticket_fees[' . $i . ']" class="sem_inputbox input-medium chzn-single ticket_fees" size="1">';

			if ($t != 0)
			{
				$html .= '<option value="0">- ' . JText::_("COM_MATUKIO_NORMAL") . ' -</option>';
			}
			else
			{
				$html .= '<option value="0" selected="selected">- ' . JText::_("COM_MATUKIO_NORMAL") . ' -</option>';
			}

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

				$selected = "";

				if ($f->id == $t)
				{
					$selected = ' selected="selected"';
				}

				$html .= '<option value="' . $f->id . '"' . $selected . '>' . JText::_($f->title) . ' (' . $disc_text . $fval . ")" . '</option>';
			}

			$html .= '</select>';

			if ($buchopt[4] <= 0) // If booking is on waitlist
			{
				$html .= " * " . JText::_("COM_MATUKIO_BOOKING_ON_WAITLIST");
			}

			$html .= '<td style="text-align: right;">';

			// Delete ticket button
			$html .= " <a id=\"delticket" . $i . "\" border=\"0\" id=\"" . $i
				. "\"><span class=\"mat_remove btn btn-danger\" type=\"button\">"
				. "<img src=\""	. MatukioHelperUtilsBasic::getComponentImagePath()
				. "1532.png\" border=\"0\" align=\"absmiddle\" style=\"width: 16px; height: 16px;\">"
				. JTEXT::_('COM_MATUKIO_REMOVE_SMALL') . "</span></a>";
			$html .= '</td>';
			$html .= '</tr>';

			$html .= '<script type="text/javascript">';
			$html .= 'jQuery( document ).ready(function( $ ) {
				$("#delticket' . $i . '").click(function(){
					$("#tickets_' . $i . '").remove();
				});
			});';
			$html .= '</script>';

			$cnt++;
		}

		echo '<input type="hidden" name="numfees" id="numfees" value="' . $cnt . '" />';

		$html .= '</table>';
		$html .= '</div>';

		// Add the necessary js

		$doc = JFactory::getDocument();

		$doc->addScriptDeclaration('
			jQuery( document ).ready(function( $ ) {
				$("#addticket").click(function(){
					var numfees = $("#numfees").val();

					$.get( "' . JUri::root() . 'index.php?option=com_matukio&format=raw&view=requests&task=getnewfeerow&backend=1&event_id=' . $event->id . '",
					{ num: numfees } )
					.done(function( data ) {
						$("#fees_table").append( data );
						$("input .btn").button();

						numfees++;
						$("#numfees").val(numfees);
					});

					return false;
				});
			});
		');

		return $html;
	}

	/**
	 * Gets the fees inlcuding (if existing) the override
	 *
	 * @param   object  $event  - The event
	 *
	 * @return  array|mixed
	 */
	public static function getFeesIncOverride($event)
	{
		$fees = self::getFees();

		if (!empty($event->different_fees_override))
		{
			$fees = self::getOverrideFees($event->different_fees_override);
		}

		return $fees;
	}

	/**
	 * Gets the different fees output for this event
	 *
	 * @param   object  $event  - The event
	 *
	 * @return  string
	 */
	public static function getFeesShow($event)
	{
		$html = "";
		$fees = self::getFeesIncOverride($event);

		if (empty($fees))
		{
			return $html;
		}

		$html .= "*<br />";

		foreach ($fees as $f)
		{
			$disc_text = ($f->discount) ? '-' : '+';

			if (MatukioHelperSettings::getSettings('different_fees_absolute', 1))
			{
				if (!$f->percent)
				{
					$fval = ($f->discount) ? $event->fees - $f->value : $event->fees + $f->value;
				}
				else
				{
					// Calculate fees
					$fval = ($f->discount) ? $event->fees - ($event->fees * ($f->value / 100)) : $event->fees + ($event->fees * ($f->value / 100));

				}

				$fval = MatukioHelperUtilsEvents::getFormatedCurrency($fval, MatukioHelperSettings::getSettings('currency_symbol', '$'));
				$html .= $fval . " " . JText::_($f->title) . "<br />";
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

				$html .= JText::_($f->title) . ' (' . $disc_text . $fval . ")" . "<br />";
			}
		}

		return $html;
	}

	/**
	 * Generates a new Row of different fees
	 *
	 * @return  void - directly printed
	 */
	public static function printDifferentFeesRow()
	{
		$db = JFactory::getDBO();
		$input = JFactory::getApplication()->input;

		$event_id = $input->getInt("event_id", 0);

		$event = JTable::getInstance('Matukio', 'Table');
		$event->load($event_id);

		$num = $input->getInt("num", 0);

		echo self::getDifferentFeeEdit($num);

		echo '<script type="text/javascript">
			window.addEvent("domready", function() {Calendar.setup({
				// Id of the input field
				inputField: "published_up' . $num . '",
				// Format of the input field
				ifFormat: "%Y-%m-%d",
				// Trigger for the calendar (button ID)
				button: "different_fees_override_published_up' . $num . '_img",
				// Alignment (defaults to "Bl")
				align: "Tl",
				singleClick: true,
				firstDay: 0
				});
			});

			window.addEvent("domready", function() {Calendar.setup({
				// Id of the input field
				inputField: "published_down' . $num . '",
				// Format of the input field
				ifFormat: "%Y-%m-%d",
				// Trigger for the calendar (button ID)
				button: "different_fees_override_published_down' . $num . '_img",
				// Alignment (defaults to "Bl")
				align: "Tl",
				singleClick: true,
				firstDay: 0
				});
			});

		jQuery("#rem_fee' . $num . '").click(function() {
			jQuery("#fee_' . $num . '").remove();
		});
		</script>';
	}
}
