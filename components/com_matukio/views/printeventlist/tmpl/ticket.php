<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       29.01.14
 *
 * @copyright  Copyright (C) 2008 - 2014 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die ('Restricted access');

$database = JFactory::getDBO();

if (!empty($this->uuid))
{
	$database->setQuery("SELECT * FROM #__matukio_bookings WHERE uuid='" . $this->uuid . "'");
}

$booking = $database->loadObject();

$kurs = MatukioHelperUtilsEvents::getEventRecurring($booking->semid);

$tmpl_code = MatukioHelperTemplates::getTemplate("ticket")->value;

// Parse language strings
$tmpl_code = MatukioHelperTemplates::replaceLanguageStrings($tmpl_code);

$replaces = MatukioHelperTemplates::getReplaces($kurs, $booking);

foreach ($replaces as $key => $replace)
{
	$tmpl_code = str_replace($key, $replace, $tmpl_code);
}

$subject = "TICKET";

MatukioHelperPDF::generateTicket($booking, $tmpl_code, $subject);
exit();
