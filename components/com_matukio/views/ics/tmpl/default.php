<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

$mainconfig = JFactory::getConfig();
$filename = $this->events[0]->title;

//var_dump($this->events);

if (count($this->events) > 1)
{
	$filename = $mainconfig->get('sitename') . " - Events";
}

$icsdata = "BEGIN:VCALENDAR\n";
$icsdata .= "VERSION:2.0\n";
$icsdata .= "PRODID:" . MatukioHelperUtilsBasic::getSitePath() . "\n";
$icsdata .= "METHOD:PUBLISH\n";

foreach ($this->events as $event)
{
	$user = JFactory::getuser($event->publisher);
	$icsdata .= "BEGIN:VEVENT\n";
	$icsdata .= "UID:" . MatukioHelperUtilsBooking::getBookingId($event->id) . "\n";
	$icsdata .= "ORGANIZER;CN=\"" . $user->name . "\":MAILTO:" . $user->email . "\n";
	$icsdata .= "SUMMARY:" . JText::_($event->title) . "\n";

	if ($event->webinar == 1)
	{
		$location = JText::_("COM_MATUKIO_WEBINAR");
	}
	elseif ($event->place_id != 0)
	{
		$locobj = MatukioHelperUtilsEvents::getLocation($event->place_id);

		$location = $locobj->location;
	}
	else
	{
		$location = $event->place;
	}

	$icsdata .= "LOCATION:" . str_replace("(\r\n|\n|\r)", ", ", $location) . "\n";

	$icsdata .= "DESCRIPTION:" . str_replace("(\r\n|\n|\r)", " ", $event->shortdesc) . "\n";
	$icsdata .= "CLASS:PUBLIC\n";
	$icsdata .= "DTSTART:" . strftime("%Y%m%dT%H%M%S", JFactory::getDate($event->begin)->toUnix()) . "\n";
	$icsdata .= "DTEND:" . strftime("%Y%m%dT%H%M%S", JFactory::getDate($event->end)->toUnix()) . "\n";
	$icsdata .= "DTSTAMP:" . strftime("%Y%m%dT%H%M%S", JFactory::getDate(MatukioHelperUtilsDate::getCurrentDate())->toUnix()) . "\n";
	$icsdata .= "BEGIN:VALARM\n";
	$icsdata .= "TRIGGER:-PT1440M\n";
	$icsdata .= "ACTION:DISPLAY\n";
	$icsdata .= "END:VALARM\n";
	$icsdata .= "END:VEVENT\n";
}

$icsdata .= "END:VCALENDAR";

header("Content-Type: text/calendar; charset=utf-8");
header("Content-Length: " . strlen($icsdata));
header("Content-Disposition: attachment; filename=\"" . $filename . ".ics\"");
header('Pragma: no-cache');

echo $icsdata;
