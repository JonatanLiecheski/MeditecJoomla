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

/**
 * Class MatukioHelperInvoice
 *
 * @since  3.1.0
 */
class MatukioHelperInvoice
{
	private static $instance;

	/**
	 * Get an booking number
	 *
	 * @param   int  $booking_id  - The booking id
	 * @param   int  $year        - The year
	 *
	 * @return  string
	 *
	 * @throws  Exception
	 */
	public static function getInvoiceNumber($booking_id, $year)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("*")
			->from("#__matukio_invoice_number")
			->where("booking_id = " . $db->quote($booking_id));

		$db->setQuery($query);

		$temp = $db->loadObject();

		if (!empty($temp))
		{
			return $temp->number . "/" . $temp->year;
		}

		// Generate new one
		$query = $db->getQuery(true);

		$query->select("*")
			->from("#__matukio_invoice_number")
			->where("year = " . $db->quote($year))
			->order('number DESC');

		$db->setQuery($query);

		$res = $db->loadObject();

		if (empty($res))
		{
			$neu = JTable::getInstance("InvoiceNumber", "Table");

			$invoice = new StdClass;
			$invoice->year = $year;
			$invoice->booking_id = $booking_id;
			$invoice->number = "1";

			if (!$neu->bind($invoice))
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			if (!$neu->store())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			$neu->checkin();
		}
		else
		{
			$last = $res->number;
			$last++;

			$neu = JTable::getInstance("InvoiceNumber", "Table");

			$invoice = new StdClass;
			$invoice->year = $year;
			$invoice->booking_id = $booking_id;
			$invoice->number = $last;

			if (!$neu->bind($invoice))
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			if (!$neu->store())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			$neu->checkin();
		}

		return $neu->number . "/" . $year;
	}

	/**
	 * Generates the html for the invoice button in the frontend
	 *
	 * @return  string  - The html markup
	 */
	public static function getInvoiceButton()
	{

	}
}
