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
define ('K_PATH_CACHE', JPATH_CACHE . "/");


// Load FPDF
require_once JPATH_COMPONENT_ADMINISTRATOR . "/include/tcpdf/tcpdf.php";

/**
 * Class MatukioHelperPDF
 *
 * @since  3.1.0
 */
class MatukioHelperPDF
{
	private static $instance;

	/**
	 * Generates the invoice
	 *
	 * @param   string  $booking      - The booking
	 * @param   string  $text         - The text
	 * @param   string  $subject      - The subject
	 * @param   string  $destination  - Where should the generated file been send
	 *
	 * @return  string
	 */
	public static function generateInvoice($booking, $text, $subject, $destination = "D")
	{
		$fn = "invoice-" . MatukioHelperUtilsBooking::getBookingId($booking->id) . ".pdf";

		// Check if PDF was already created
		if (JFile::exists($fn))
		{
			return $fn;
		}

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Matukio by compojoom.com');
		$pdf->SetTitle($subject);
		$pdf->SetSubject($subject);
		$pdf->SetKeywords('Invoice, Matukio, compojoom');

		// remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// add a page
		$pdf->AddPage();
		$pdf->writeHTML($text, true, false, true, false, '');

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		return $pdf->Output($fn, $destination);
	}

	/**
	 * Generates the
	 *
	 * @param   string  $booking      - The booking
	 * @param   string  $text         - The text
	 * @param   string  $subject      - The subject
	 * @param   string  $destination  - Where should the generated file been send
	 *
	 * @return  string
	 */
	public static function generateTicket($booking, $text, $subject, $destination = "D")
	{
		$fn = "ticket-" . MatukioHelperUtilsBooking::getBookingId($booking->id) . ".pdf";

		// Check if PDF was already created
		if (JFile::exists($fn))
		{
			return $fn;
		}

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('Matukio by compojoom.com');
		$pdf->SetTitle($subject);
		$pdf->SetSubject($subject);
		$pdf->SetKeywords('Ticket, Matukio, compojoom');

		// remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// add a page
		$pdf->AddPage();
		$pdf->writeHTML($text, true, false, true, false, '');

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		return $pdf->Output($fn, $destination);
	}

	/**
	 * Gets the invoice
	 *
	 * @param   string  $booking      - The booking
	 * @param   string  $kurs         - The event
	 * @param   string  $subject      - The subject
	 * @param   string  $destination  - The destination
	 *
	 * @return string
	 */
	public static function getInvoice($booking, $kurs, $subject = "INVOICE", $destination = "D")
	{
		$tmpl_code = MatukioHelperTemplates::getTemplate("invoice")->value;

		// Parse language strings
		$tmpl_code = MatukioHelperTemplates::replaceLanguageStrings($tmpl_code);

		$replaces = MatukioHelperTemplates::getReplaces($kurs, $booking);

		foreach ($replaces as $key => $replace)
		{
			$tmpl_code = str_replace($key, $replace, $tmpl_code);
		}

		return MatukioHelperPDF::generateInvoice($booking, $tmpl_code, $subject, $destination);
	}


	/**
	 * Gets the invoice
	 *
	 * @param   string  $booking      - The booking
	 * @param   string  $kurs         - The event
	 * @param   string  $subject      - The subject
	 * @param   string  $destination  - The destination
	 *
	 * @return string
	 */
	public static function getTicket($booking, $kurs, $subject = "Ticket", $destination = "D")
	{
		$tmpl_code = MatukioHelperTemplates::getTemplate("ticket")->value;

		// Parse language strings
		$tmpl_code = MatukioHelperTemplates::replaceLanguageStrings($tmpl_code);

		$replaces = MatukioHelperTemplates::getReplaces($kurs, $booking);

		foreach ($replaces as $key => $replace)
		{
			$tmpl_code = str_replace($key, $replace, $tmpl_code);
		}

		return MatukioHelperPDF::generateTicket($booking, $tmpl_code, $subject, $destination);
	}
}
