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

$database = JFactory::getDBO();

$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id = '" . $this->uid . "'");
$booking = $database->loadObject();

$kurs = MatukioHelperUtilsEvents::getEventRecurring($booking->semid);
$tmpl_code = MatukioHelperTemplates::getTemplate("export_certificate")->value;

if (!empty($kurs->certicate_code))
{
	// Custom code for certificates
	$tmpl_code = $kurs->certificate_code;
}

// Parse language strings
$tmpl_code = MatukioHelperTemplates::replaceLanguageStrings($tmpl_code);

echo "\n<body onload=\" parent.sbox-window.focus(); parent.sbox-window.print(); \">";
echo "<div class=\"compojoom-bootstrap\">";

$replaces = MatukioHelperTemplates::getReplaces($kurs, $booking);

foreach ($replaces as $key => $replace)
{
	$tmpl_code = str_replace($key, $replace, $tmpl_code);
}

echo $tmpl_code;

echo "</div>";
echo "</body>";
