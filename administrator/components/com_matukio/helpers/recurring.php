<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       24.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');


/**
 * Class MatukioHelperRecurring
 *
 * @since  3.1.0
 */
class MatukioHelperRecurring
{
	/**
	 * Deletes recurring events of an event incl. notifications
	 *
	 * @param   int  $event_id  - The event id
	 *
	 * @throws  Exception
	 * @return  Null
	 */
	public static function deleteRecurringEvents($event_id)
	{
		// Delete old recurring events
		$db = JFactory::getDbo();

		// Notify users
		if (MatukioHelperSettings::_("notify_participants_delete", 1))
		{
			$events = MatukioHelperUtilsEvents::getEventsRecurringOnEventId($event_id);

			foreach ($events as $e)
			{
				$db->setQuery("SELECT * FROM #__matukio_bookings WHERE semid = " . $e->id . "");
				$rows = $db->loadObjectList();

				if ($db->getErrorNum())
				{
					throw new Exception($db->getErrorMsg(), 42);
				}

				foreach ($rows as $b)
				{
					MatukioHelperUtilsEvents::sendBookingConfirmationMail($e, $b->id, 4);
				}
			}
		}

		$query = $db->getQuery(true);

		$query->delete("#__matukio_recurring")
			->where("event_id = " . $event_id);

		$db->setQuery($query);

		$db->execute();
	}

	/**
	 * Saves the recurring date
	 *
	 * @param   bool  $frontend  - Are we in the frontend?
	 *
	 * @throws  Exception on db error
	 * @return  string    - (id of the recurring date)
	 */
	public static function saveRecurring($frontend = false)
	{
		$database = JFactory::getDBO();
		$input = JFactory::getApplication()->input;

		// Zeit formatieren
		$_begin_date = $input->get('_begin_date', '0000-00-00', 'string');
		$_end_date = $input->get('_end_date', '0000-00-00', 'string');
		$_booked_date = $input->get('_booked_date', '0000-00-00', 'string');
		$id = $input->getInt('id', 0);

		$post = JRequest::get('post');

		$row = JTable::getInstance('Recurring', 'MatukioTable');
		$row->load($id);

		if (!$row->bind($post))
		{
			throw new Exception($row->getError(), 42);
		}

		// Zuweisung der Startzeit
		$row->begin = JFactory::getDate($_begin_date, MatukioHelperUtilsBasic::getTimeZone())->format('Y-m-d H:i:s', false, false);

		// Zuweisung der Endzeit
		$row->end = JFactory::getDate($_end_date, MatukioHelperUtilsBasic::getTimeZone())->format('Y-m-d H:i:s', false, false);

		// Zuweisung der Buchungszeit
		$row->booked = JFactory::getDate($_booked_date, MatukioHelperUtilsBasic::getTimeZone())->format('Y-m-d H:i:s', false, false);

		if (!$row->check())
		{
			throw new Exception($database->stderr(), 42);
		}

		if (!$row->store())
		{
			throw new Exception($database->stderr(), 42);
		}

		$row->checkin();

		// Return id
		return $row->id;
	}

	/**
	 * Saves a new recurring date for the given event
	 *
	 * @param   object  $row  - The event
	 *
	 * @return  mixed
	 *
	 * @throws  Exception
	 */
	public static function saveRecurringDateForEvent($row)
	{
		// Add to dates
		// Insert date into recurring table
		$robj = new stdClass;
		$robj->event_id = $row->id;
		$robj->semnum = $row->semnum;
		$robj->begin = $row->begin;
		$robj->end = $row->end;
		$robj->booked = $row->booked;
		$robj->published = $row->published;

		$rect = JTable::getInstance('Recurring', 'MatukioTable');

		if (!$rect->bind($robj))
		{
			throw new Exception($rect->getError(), 42);
		}

		if (!$rect->check())
		{
			throw new Exception($rect->getError(), 42);
		}

		if (!$rect->store())
		{
			throw new Exception($rect->getError(), 42);
		}

		$row->checkin();

		return $rect->id;
	}

	/**
	 * Prints the generated dates for this event (frontend and backend)
	 *
	 * @return  void - direct echo
	 */
	public static function printGenerateRecurring()
	{
		$input = JFactory::getApplication()->input;

		$begin = $input->get('begin', '0000-00-00 00:00:00', 'String');
		$end = $input->get('end', '0000-00-00 00:00:00', 'String');
		$type = $input->get('repeat_type', 'monthly');
		$month_week = $input->get('recurring_month_week', '', 'String');
		$week_day = $input->get('recurring_week_day', '', 'String');
		$count = $input->getInt('recurring_count', 0);
		$until = $input->get('recurring_until', '0000-00-00');

		if ($begin == '0000-00-00 00:00:00')
		{
			echo JText::_("COM_MATUKIO_RECURRING_PLEASE_INSERT_BEGIN_FIRST");

			return;
		}

		if ($count == 0 && $until == "0000-00-00")
		{
			echo JText::_("COM_MATUKIO_RECURRING_PLEASE_CHOOSE_AN_END");

			return;
		}

		// Fix for empty date
		if (empty($until))
		{
			$until = "0000-00-00";
		}

		if (!empty($count) && $until != "0000-00-00")
		{
			echo JText::_("COM_MATUKIO_RECURRING_PLEASE_CHOOSE_ONE_END");

			return;
		}

		if (($type == 'weekly' || $type == 'monthly') && $week_day == "")
		{
			echo JText::_("COM_MATUKIO_RECURRING_PLEASE_SELECT_A_WEEKDAY");

			return;
		}

		$begin_arry = explode(" ", $begin);
		$begin = $begin_arry[0];

		$week_day = explode(",", $week_day);
		$month_week = explode(",", $month_week);

		$begin_date = new DateTime($begin);
		$until_date = new DateTime($until);

		if ($begin < date("Y-m-d"))
		{
			echo JText::_("COM_MATUKIO_YOUR_BEGIN_MUST_BE_IN_THE_FUTURE");

			return;
		}

		$dates = array();

		// Count
		if ($count != null)
		{
			$pointer = new DateTime($begin);

			for ($i = 0; $i < $count; $i++)
			{
				if ($type == "daily")
				{
					$dates[] = date("Y-m-d", strtotime($begin . "+" . $i . " days"));
				}
				elseif ($type == "weekly")
				{
					foreach ($week_day as $d)
					{
						$dates[] = date("Y-m-d", strtotime($begin . "+" . $i . " " . $d));
					}
				}
				elseif ($type == "monthly")
				{
					$pointer->modify('first day of next month');

					foreach ($month_week as $w)
					{
						$w--;

						$wp = $pointer->modify("+" . $w . " week");

						foreach ($week_day as $d)
						{
							$next = strtotime($wp->format("Y-m-d") . " next " . $d);

							$dates[] = date("Y-m-d", $next);
						}
					}
				}
				elseif ($type == "yearly")
				{
					$dates[] = date("Y-m-d", strtotime($begin . "+" . $i . " year"));
				}
			}
		}
		else
		{
			if ($begin_date < $until)
			{
				echo JText::_("COM_MATUKIO_YOUR_END_DATE_MUST_BE_AFTER_THE_BEGIN");

				return;
			}

			// We have an until
			$curdate = clone $begin_date;

			while (true)
			{
				if ($type == "daily")
				{
					$dates[] = $curdate->format("Y-m-d");
					$curdate = $curdate->modify("next day");
				}
				elseif ($type == "weekly")
				{
					foreach ($week_day as $d)
					{
						$curdate = $curdate->modify("next " . $d);

						if ($curdate <= $until_date)
						{
							$dates[] = $curdate->format("Y-m-d");
						}
					}
				}
				elseif ($type == "monthly")
				{
					$curdate->modify('first day of this month');

					foreach ($month_week as $w)
					{
						$w--;

						$pointer = clone $curdate;

						$pointer->modify("+" . $w . " week");

						foreach ($week_day as $d)
						{
							$pointer->modify("next " . $d);

							if ($pointer >= $begin_date && $pointer <= $until_date)
							{
								$dates[] = $pointer->format("Y-m-d");
							}
						}
					}

					$curdate->modify('next month');
				}
				elseif ($type == "yearly")
				{
					$dates[] = $curdate->format("Y-m-d");
					$curdate = $curdate->modify("next year");
				}

				if ($curdate > $until_date)
				{
					// End the loop we ar done
					break;
				}
			}
		}

		echo '<h3>' . JText::_("COM_MATUKIO_GENERATED_DATES") . '</h3>';
		echo '<table class="table">';
		echo '<tr>';
		echo '<td class="key" style="width: 20%">';
		echo JText::_("COM_MATUKIO_BEGIN");
		echo '</td>';
		echo '<td>';
		echo $begin;
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td class="key" style="width: 20%">';
		echo JText::_("COM_MATUKIO_NUMBER_EVENTS");
		echo '</td>';
		echo '<td>';
		echo count($dates);
		echo '</td>';
		echo '</tr>';

		echo '<tr>';
		echo '<td colspan="2">';
		echo '<input type="hidden" name="recurring_dates" id="recurring_dates" style="width: 100%" value="'
			. implode(',', $dates) . '" tabindex="-1" class="select2-offscreen" />';
		echo '</td>';
		echo '</tr>';
		echo '</table>';

		echo '<script>';
		echo 'jQuery("#recurring_dates").select2({
		tags:[],
		tokenSeparators: [",", " "]
		 });';
		echo '</script>';
	}

	/**
	 * Cancels an recurring event and notfies (if enabled) all participants
	 *
	 * @param   array  $ids        - Array of recurring events
	 * @param   int    $cancelled  - Should it be cancelled or reactivated?
	 *
	 * @return  bool
	 * @throws  Exception on Error
	 */
	public static function cancelRecurringEvents($ids, $cancelled)
	{
		if (count($ids))
		{
			// First update event
			$db = JFactory::getDbo();
			$cids = implode(',', $ids);

			$db->setQuery("UPDATE #__matukio_recurring SET cancelled = '" . $cancelled . "' WHERE id IN (" . $cids . ") ");

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			if (MatukioHelperSettings::_("booking_stornoconfirmation", 1))
			{
				// Set bookings to deleted
				$db->setQuery("UPDATE #__matukio_bookings SET status = " . MatukioHelperUtilsBooking::$DELETED . " WHERE semid IN (" . $cids . ")");
				$db->execute();

				// Notify participants over the change
				$db->setQuery("SELECT * FROM #__matukio_bookings WHERE semid IN (" . $cids . ")");
				$rows = $db->loadObjectList();

				if ($db->getErrorNum())
				{
					throw new Exception($db->getErrorMsg(), 42);
				}

				foreach ($rows AS $row)
				{
					$event = MatukioHelperUtilsEvents::getEventRecurring($row->semid);

					if ($cancelled == 0)
					{
						MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $row->id, 9);
					}
					else
					{
						MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $row->id, 10);
					}
				}
			}
		}

		return true;
	}

	/**
	 * Confirms (notfies organizer + participants) an event
	 *
	 * @param   object  $event  - An recurring event
	 *
	 * @return  bool
	 * @throws  Exception on Error
	 */
	public static function confirmRecurringEvent($event)
	{
		// First update event
		$db = JFactory::getDbo();

		if (MatukioHelperSettings::_("booking_confirmation", 1))
		{
			// Notify participants and organizer over the event is taking place
			$db->setQuery("SELECT * FROM #__matukio_bookings WHERE semid = " . $event->id);
			$rows = $db->loadObjectList();

			if ($db->getErrorNum())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			foreach ($rows AS $row)
			{
				MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $row->id, 12);
			}
		}

		return true;
	}
}
