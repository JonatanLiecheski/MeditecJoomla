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

JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
CompojoomHtmlBehavior::bootstrap31(false, false, false, false);

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
	// TODO add option for status filtering
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

$tmpl = MatukioHelperTemplates::getTemplate("export_signaturelist");
$tmpl = MatukioHelperTemplates::getParsedExportTemplateHeadding($tmpl, $kurs);

echo "\n<body onload=\" parent.sbox-window.focus(); parent.sbox-window.print(); \">";
echo "<div class=\"compojoom-bootstrap\">";

if (!empty($tmpl->subject))
{
	echo "\n<br /><center><span class=\"mat_title\">" . JTEXT::_($tmpl->subject) . "</span></center><br />";
}

/* Header before out of value_text */

if (!empty($kurs))
{
	echo $tmpl->value_text;
}

/* Single lines from value */

$signature_line = str_replace("</p>", "", str_replace("<p>", "", $tmpl->value));

$signatures = "<table class=\"mat_table table\" style=\"width: 100%;\">";

// Header table
if (!empty($kurs))
{
	$signatures .= "<tr>" . MatukioHelperTemplates::getExportSignatureHeader($signature_line, $kurs) . "</tr>";
}

// Move to function
$i = 1;

foreach ($bookings as $b)
{
	// We are in the backend with bookings instead of an event!
	if (empty($cid))
	{
		$kurs = MatukioHelperUtilsEvents::getEventRecurring($b->semid);
	}

	$replaces = MatukioHelperTemplates::getReplaces($kurs, $b, $i);
	$line = $signature_line;
	$signatures .= "<tr>\n";

	foreach ($replaces as $key => $replace)
	{
		$line = str_replace($key, "<td>" . $replace . "</td>\n", $line);
	}

	$signatures .= $line;
	$signatures .= "</tr>\n";
	$i++;
}

echo $signatures;

echo "</table>";
echo "<br />" . MatukioHelperUtilsBasic::getCopyright();
echo "</div>";
echo "</body>";
