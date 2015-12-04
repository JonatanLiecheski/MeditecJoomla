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

CompojoomHtmlBehavior::bootstrap31(false, false, false, false);

$database = JFactory::getDBO();

if (!empty($this->uid))
{
	$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id='" . $this->uid . "'");
}
elseif (!empty($this->uuid))
{
	$database->setQuery("SELECT * FROM #__matukio_bookings WHERE uuid='" . $this->uuid . "'");
}

$booking = $database->loadObject();

$kurs = MatukioHelperUtilsEvents::getEventRecurring($booking->semid);

$tmpl_code = MatukioHelperTemplates::getTemplate("invoice")->value;

// Parse language strings
$tmpl_code = MatukioHelperTemplates::replaceLanguageStrings($tmpl_code);

$replaces = MatukioHelperTemplates::getReplaces($kurs, $booking);

foreach ($replaces as $key => $replace)
{
	$tmpl_code = str_replace($key, $replace, $tmpl_code);
}

$subject = "INVOICE";

MatukioHelperPDF::generateInvoice($booking, $tmpl_code, $subject);
exit();
