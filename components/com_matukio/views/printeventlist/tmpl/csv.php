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


$backend = true;

if (!isset($this->backend))
{
	$backend = false;
}

$kurs = null;

// Load event only if we are not in the backend / or got an whole event to print
if (!$backend)
{
	$kurs = MatukioHelperUtilsEvents::getEventRecurring($this->cid);
}

if (!isset($this->cid))
{
	$this->cid = 0;
}

if (!isset($this->bookings))
{
	$this->bookings = null;
}

$konvert = MatukioHelperSettings::getSettings('csv_export_charset', 'UTF-8');

header("Content-Encoding: " . $konvert);
header("Content-Type: text/csv; charset=" . $konvert);

if (!empty($kurs))
{
	header("Content-Disposition: attachment; filename=\"" . $kurs->title . ".csv\"");
}
else
{
	$filename = "bookings-" . JHTML::_('date', 'now', MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y')) . ".csv";
	header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
}

header('Pragma: no-cache');

if ($konvert == "UTF-8")
{
	// It does not help, as far as we know
	// $csvdata = chr(239) . chr(187) . chr(191);
}

$csvdata .= MatukioHelperUtilsEvents::generateCSVFile($backend, $this->cid, $this->bookings, $kurs);

$konvert = MatukioHelperSettings::getSettings('csv_export_charset', 'UTF-8');
$csvdata = iconv("UTF-8", $konvert, $csvdata);

echo $csvdata;
exit();
