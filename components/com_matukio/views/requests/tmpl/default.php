<?php
/**
 * Matukio
 * @package  Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All      rights reserved
 * @Joomla   ! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version  $Revision: 2.0.0 Stable $
 **/
defined('_JEXEC') or die('Restricted access');
$input = JFactory::getApplication()->input;
$task = $input->get('task', null);

// Checking if task is set
if (!$task)
{
	echo "No task specified";

	return;
}

// Validate Couponcode
if ($task == 'validate_coupon')
{
	$coupon = $input->get('code', '', 'string');

	if (empty($coupon))
	{
		echo "false";

		return;
	}

	$cdate = new DateTime;

	$db = JFactory::getDBO();
	$query = $db->getQuery(true);
	$query->select('*')->from('#__matukio_booking_coupons')
		->where('code = ' . $db->quote($coupon) . ' AND published = 1 AND (published_up < '
			. $db->quote($cdate->format('Y-m-d H:i:s')) . ' OR published_up = ' . $db->quote("0000-00-00 00:00:00") . ') '
			. 'AND (published_down > ' . $db->quote($cdate->format('Y-m-d H:i:s'))
			. ' OR published_down = ' . $db->quote("0000-00-00 00:00:00") . ')'
		);

	$db->setQuery($query);
	$coupon = $db->loadObject();

	if (empty($coupon))
	{
		echo "false";

		return;
	}

	echo "true";
}
elseif ($task == 'get_total')
{
	// Get the total amount
	$total = 0.00;

	$nrbooked = $input->getInt('nrbooked', 1);
	$single_fee = $input->get('fee', 0);
	$coupon_code = $input->get('code', '', 'string');

	$total = $nrbooked * $single_fee;

	if (!empty($coupon_code))
	{
		// Get coupon value
		$cdate = new DateTime;

		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__matukio_booking_coupons')
			->where('code = ' . $db->quote($coupon_code) . ' AND published = 1 AND published_up < '
				. $db->quote($cdate->format('Y-m-d H:i:s')) . " AND published_down > "
				. $db->quote($cdate->format('Y-m-d H:i:s'))
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
				$total = $total - $coupon->value;
			}
		}
	}

	echo MatukioHelperUtilsEvents::getFormatedCurrency($total, MatukioHelperSettings::getSettings('currency_symbol', '$'));
}
// Route a link
elseif ($task == 'route_link')
{
	$link = $input->get('link', '', 'string');

	if (empty($link))
	{
		return;
	}

	$db = JFactory::getDBO();

	$uri = 'index.php?option=com_matukio&view=eventlist';

	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE ' . $db->Quote($uri . '%') . ' AND published = 1 LIMIT 1');

	$itemId = ($db->getErrorNum()) ? 0 : intval($db->loadResult());

	$link = $link . "&Itemid=" . $itemId;

	// Routing of a link
	$link = JRoute::_($link);

	// Get the document object.
	$document = JFactory::getDocument();

	// Set the MIME type for JSON output.
	$document->setMimeEncoding('application/json');

	// Change the suggested filename.
	JResponse::setHeader('Content-Disposition', 'attachment;filename=element.json"');
	$url = array("link" => $link);

	// Output the JSON data.
	echo json_encode($url);
}
// Get the calendar
elseif ($task == 'getcalendar')
{
	$start = $input->get('startDate', '');
	$end = $input->get('endDate', '');

	$db = JFactory::getDBO();

	$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());

	$query = 'SELECT a.*, r.*, cat.title AS category, cat.alias as catalias FROM #__matukio_recurring AS r
			LEFT JOIN #__matukio AS a ON r.event_id = a.id
			LEFT JOIN #__categories AS cat ON cat.id = a.catid
			WHERE r.begin > ' . $db->Quote($start) . ' AND r.begin < '	. $db->Quote($end)
			. ' AND r.published = 1 AND cat.access in (' . $groups . ') ORDER BY r.begin ASC';

	$db->setQuery($query);

	$rows = $db->loadObjectList();

	$events = array();

	foreach ($rows as $row)
	{
		$begin = JHTML::_('date', $row->begin, 'Y-m-d\TH:i:s') . "-00:00";
		$end = JHTML::_('date', $row->end, 'Y-m-d\TH:i:s') . "-00:00";

		// Link
		$eventid_l = $row->id . ':' . JFilterOutput::stringURLSafe($row->title);
		$catid_l = $row->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($row->catid));

		$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l), false);

		$title = '<a href="' . $link . '">' . $row->title . '</a>';
		$events[] = array('title' => $title, 'start' => $begin, 'end' => $end, 'location' => $row->place);
	}

	JResponse::setHeader('Content-Disposition', 'attachment;filename=element.json"');
	echo json_encode($events);
}
// Get the calendar
elseif ($task == 'getcalendarjquery')
{
	$start = JFactory::getDate($input->get('start', ''));
	$end = JFactory::getDate($input->get('end', ''));

	$db = JFactory::getDBO();

	$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());

	$query = 'SELECT a.*, r.*, cat.title AS category, cat.alias as catalias FROM #__matukio_recurring AS r
			LEFT JOIN #__matukio AS a ON r.event_id = a.id
			LEFT JOIN #__categories AS cat ON cat.id = a.catid
			WHERE r.begin > ' . $db->Quote($start) . ' AND r.begin < '	. $db->Quote($end)
		. ' AND r.published = 1 AND cat.access in (' . $groups . ') ORDER BY r.begin ASC';

	$db->setQuery($query);

	$rows = $db->loadObjectList();

	$events = array();

	foreach ($rows as $row)
	{
		$begin = JHTML::_('date', $row->begin, 'Y-m-d\TH:i:s') . "Z";
		$end = JHTML::_('date', $row->end, 'Y-m-d\TH:i:s') . "Z";

		// Link
		$eventid_l = $row->id . ':' . JFilterOutput::stringURLSafe($row->title);
		$catid_l = $row->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($row->catid));

		$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l), false);

		// $title = '<a href="' . $link . '">' . $row->title . '</a>';
		$allday = $row->showend == 1 ? false : true;

		$events[] = array(
			'id' => $row->id, 'title' => $row->title, 'start' => $begin, 'end' => $end, 'location' => $row->place, 'url' => $link, 'allDay' => $allday
		);
	}

	JResponse::setHeader('Content-Disposition', 'attachment;filename=element.json"');
	echo json_encode($events);
}
// Get a new line for a fee
elseif ($task == 'getnewfeerow')
{
	$db = JFactory::getDBO();

	$event_id = $input->getInt("event_id", 0);
	$backend = $input->getInt("backend", 0);

	// Load event (use model function)
	$emodel = JModelLegacy::getInstance('Event', 'MatukioModel');
	$event = $emodel->getItem($event_id);

	$num = $input->getInt("num", 0);

	if (empty($num))
	{
		return;
	}

	if (!empty($event->different_fees_override))
	{
		// We have an override for this event
		$fees_list = MatukioHelperFees::getOverrideFees($event->different_fees_override);
	}
	else
	{
		$fees_list = MatukioHelperFees::getFees();
	}

	echo "<table id=\"tickets_" . $num . "\" class=\"mat_table table\">\n";
	echo '<tr>';
	echo '<td class="key" width="150px">';

	echo JText::_("COM_MATUKIO_PLACES_TO_BOOK") . ": ";
	echo MatukioHelperUtilsEvents::getPlaceSelect(null, $event, $num);

	echo '</td>';
	echo '<td>';

	echo JText::_("COM_MATUKIO_TICKET_TYPE") . ": ";

	echo '<select id="ticket_fees' . $num . '" name="ticket_fees[' . $num . ']" class="sem_inputbox chzn-single ticket_fees" size="1">';
	echo '<option value="0" selected="selected" discvalue="0" discount="1" percent="1">- ' . JText::_("COM_MATUKIO_NORMAL") . ' -</option>';

	foreach ($fees_list as $f)
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
				$fval = ($f->discount) ? $event->fees - ($event->fees * ($f->value / 100)) : $event->fees +($event->fees * ($f->value / 100));
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

		echo '<option value="' . $f->id . '" discvalue="' . $f->value . '" percent="' . $f->percent . '" discount="' . $f->discount . '">'
			. JText::_($f->title) . ' (' . $disc_text . $fval . ")" . '</option>';
	}

	echo '</select>';

	echo '</td>';
	echo '<td style="text-align: right;">';

	// Add additional tickets in another category!
	echo " <a id=\"delticket" . $num . "\" border=\"0\" num=\"" . $num . "\" class=\"btn btn-danger\"><span type=\"button\">"
		. "<img src=\""	. MatukioHelperUtilsBasic::getComponentImagePath()
		. "1532.png\" border=\"0\" align=\"absmiddle\" style=\"width: 16px; height: 16px; margin-right: 8px;\" />"
		. JTEXT::_('COM_MATUKIO_REMOVE') . "</span></a>";
	echo '</td>';
	echo '</tr>';
	echo "</table>";

	if ($backend)
	{
		echo '<script type="text/javascript">';
		echo 'jQuery( document ).ready(function( $ ) {
				$("#delticket' . $num . '").click(function(){
					$("#tickets_' . $num . '").remove();
				});
			});';
		echo '</script>';
	}
}
// Total fees with different fees
elseif ($task == 'get_total_different')
{
	$total = 0.00;

	$nrbooked = $input->getInt('nrbooked', 1);

	$event_id = $input->getInt("event_id", 0);
	$event_fee = $input->get('fee', 0);
	$coupon_code = $input->get('code', '', 'string');

	$places = explode(',', $input->getString("places"));
	$types = explode(',', $input->getString("types"));

	$discount = explode(',', $input->getString("discount"));
	$percent = explode(',', $input->getString("percent"));
	$value = explode(',', $input->getString("disc_value"));

	// Calculate amount
	for ($i = 0; $i < count($places); $i++)
	{
		$single_fee = $event_fee;

		if ($value > 0)
		{
			if ($percent[$i] == 0)
			{
				// Do we have a discount or is it getting more expensive?
				if ($discount[$i])
				{
					$single_fee -= $value[$i];
				}
				else
				{
					$single_fee += $value[$i];
				}
			}
			else
			{
				// Do we have a discount or is it getting more expensive?
				if ($discount[$i])
				{
					$single_fee = $single_fee - ($single_fee * ($value[$i] / 100));
				}
				else
				{
					$single_fee = $single_fee + ($single_fee * ($value[$i] / 100));
				}
			}
		}

		$total += ($places[$i] * $single_fee);
	}

	// Substract coupon code (if any)
	if (!empty($coupon_code))
	{
		// Get coupon value
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
				$total = $total - $coupon->value;
			}
		}
	}

	echo MatukioHelperUtilsEvents::getFormatedCurrency($total, MatukioHelperSettings::getSettings('currency_symbol', '$'));
}
elseif ($task == 'generate_recurring')
{
	MatukioHelperRecurring::printGenerateRecurring();
}
elseif ($task == 'get_override_fee_edit_row')
{
	MatukioHelperFees::printDifferentFeesRow();
}

jexit();
