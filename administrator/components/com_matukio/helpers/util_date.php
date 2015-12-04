<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die ('Restricted access');

/**
 * Class MatukioHelperUtilsDate
 *
 * @since  1.0.0
 */
class MatukioHelperUtilsDate
{
	/**
	 * Gets the current date (toSQL)
	 *
	 * @return  string
	 */
	public static function getCurrentDate()
	{
		return JFactory::getDate()->toSql();
	}

	/**
	 * Gets the timezone
	 *
	 * @param   Date  $date  The date Obj
	 *
	 * @return  string
	 */
	public static function getTimezone($date)
	{
		$cltimezone = "";

		if (MatukioHelperSettings::getSettings('show_timezone', '1'))
		{
			$cltimezone = " (GMT " . JHTML::_('date', $date, 'P') . ")";
		}

		return $cltimezone;
	}

	/**
	 * Calculate count of days
	 *
	 * @param   DateTime  $begin       - The begin
	 * @param   DateTime  $until       - Until when
	 * @param   string    $type        - The type
	 * @param   array     $week_day    - The days array
	 * @param   array     $month_week  - The weeks array
	 *
	 * @return float|int
	 */
	public static function calculateCount($begin, $until, $type, $week_day, $month_week)
	{
		echo $begin->format("Y-m-d") . " | " . $until->format("Y-m-d") . " type: " . $type . " week_day: " . count($week_day) . " month: " . count($month_week);

		$count = 0;

		if ($type == "daily")
		{
			$interval = date_diff($begin, $until);
			return $interval->days;
		}
		elseif ($type == "weekly")
		{
			$interval = self::datediffInWeeks($begin, $until);
			return $interval * count($week_day);
		}
		elseif ($type == "monthly")
		{
			$interval = date_diff($begin, $until);

			$count = ($interval->y * 12) + $interval->m;

			$multi = count($month_week) * count($week_day);

			return $count * $multi;
		}
		elseif ($type == "yearly")
		{
			$interval = date_diff($begin, $until);

			return $interval->y;
		}
	}

	/**
	 * The difference in weeks
	 *
	 * @param   DateTime  $first   - The first date
	 * @param   DateTime  $second  - The second date
	 *
	 * @return float
	 */
	public static function datediffInWeeks($first, $second)
	{
		if($first > $second) return datediffInWeeks($second, $first);
		return floor($first->diff($second)->days/7);
	}
}
