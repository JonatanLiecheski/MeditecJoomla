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

CompojoomHtmlBehavior::bootstrap31(false, false, false, false);

JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');

$database = JFactory::getDBO();
$neudatum = MatukioHelperUtilsDate::getCurrentDate();

$cid = 0;
$kurs = null;

// Load event only if we are not in the backend / or got an whole event to print
if (!isset($this->backend))
{
	$cid = JFactory::getApplication()->input->getInt('cid', 0);
	$kurs = MatukioHelperUtilsEvents::getEventRecurring($cid);
}

if (!empty($kurs))
{
	$database->setQuery("SELECT a.*, cc.*, a.id AS sid, a.name AS aname, a.email AS aemail FROM #__matukio_bookings AS a " .
		"LEFT JOIN #__users AS cc ON cc.id = a.userid WHERE a.semid = '" . $kurs->id . "' AND (a.status = 0 OR a.status = 1) ORDER BY a.id");
}
elseif (isset($this->bookings))
{
	if (count($this->bookings))
	{
		$database->setQuery("SELECT a.*, cc.*, a.id AS sid, a.name AS aname, a.email AS aemail FROM #__matukio_bookings AS a " .
			"LEFT JOIN #__users AS cc ON cc.id = a.userid WHERE a.id IN (" . implode(",", $this->bookings) . ") ORDER BY a.id");
	}
}
else
{
	throw new Exception("No data supplied (bookings / event)");
}

$bookings = $database->loadObjectList();

if ($this->art > 2)
{
	echo MatukioHelperUtilsBasic::getHTMLHeader();
	$this->art -= 2;
}

$tmpl = MatukioHelperTemplates::getTemplate("export_participantslist");
$tmpl = MatukioHelperTemplates::getParsedExportTemplateHeadding($tmpl, $kurs);

echo "\n<body onload=\" parent.sbox-window.focus(); parent.sbox-window.print(); \">";
echo "<div class=\"compojoom-bootstrap\">";

if (!empty($tmpl->subject))
{
	echo "\n<br /><center><span class=\"sem_list_title\">" . JTEXT::_($tmpl->subject) . "</span></center><br />";
}

/* Header before out of value_text */
if (!empty($kurs))
{
	echo $tmpl->value_text;
}

/* Participants */

// Move to function
$i = 1;

$ptable = "";

foreach ($bookings as $b)
{
	// We are in the backend with bookings instead of an event!
	if (empty($cid))
	{
		$kurs = MatukioHelperUtilsEvents::getEventRecurring($b->semid);
	}

	$replaces = MatukioHelperTemplates::getReplaces($kurs, $b, $i);

	$participant_line = $tmpl->value;

	foreach ($replaces as $key => $replace)
	{
		$participant_line = str_replace($key, $replace, $participant_line);
	}

	$ptable .= $participant_line;

	$i++;
}

echo $ptable;

echo "<br />" . MatukioHelperUtilsBasic::getCopyright();
echo "</div>";
echo "</body>";
