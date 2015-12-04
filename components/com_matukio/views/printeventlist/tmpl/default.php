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

$document = JFactory::getDocument();
$database = JFactory::getDBO();

$neudatum = MatukioHelperUtilsDate::getCurrentDate();

JHTML::_('behavior.modal');
JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
CompojoomHtmlBehavior::bootstrap31(false, false, false, false);

$html = MatukioHelperUtilsBasic::getHTMLHeader();

$neudatum = MatukioHelperUtilsDate::getCurrentDate();

$html .= "\n<body onload=\" parent.sbox-window.focus(); parent.sbox-window.print(); \">";
$html .= "<div class=\"compojoom-bootstrap\">";
$html .= "\n<br /><center><span class=\"sem_list_title\">" . $this->headertext . "</span><br /><span class=\"sem_list_date\">"
	. JHTML::_('date', $neudatum, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')) . "</span></center><br />";
$k = 0;

for ($i = 0, $n = count($this->rows); $i < $n; $i++)
{
	$row = $this->rows[$i];
	$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($row);
	$gebucht = $gebucht->booked;
	$freieplaetze = $row->maxpupil - $gebucht;

	if ($freieplaetze < 0)
	{
		$freieplaetze = 0;
	}

	$html .= MatukioHelperUtilsEvents::getTableHeader(4, "sem_list");
	$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell($row->title, 'd', 'c', '100%', 'sem_list_head', 2) . "</tr>";
	$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell($row->shortdesc, 'd', 'l', '100%', 'sem_list_row', 2) . "</tr>";

	if ($row->semnum != "")
	{
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_NUMBER') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell($row->semnum, 'd', 'l', '90%', 'sem_list_row') . "</tr>";
	}

	$htxt = $this->status[$i];

	if ($row->nrbooked < 1)
	{
		$htxt = JTEXT::_('COM_MATUKIO_CANNOT_BOOK_ONLINE');
	}

	$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_STATUS') . ":", 'd', 'l', '', 'sem_list_row')
		. MatukioHelperUtilsEvents::getTableCell($htxt, 'd', 'l', '', 'sem_list_row') . "</tr>";

	if ($row->codepic != "")
	{
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_BOOKING_ID') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell(MatukioHelperUtilsBooking::getBookingId($row->codepic), 'd', 'l', '', 'sem_list_row') . "</tr>";
	}

	if ($row->showbegin > 0)
	{
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_BEGIN') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell(JHTML::_('date', $row->begin, MatukioHelperSettings::getSettings('date_format_small',
				'd-m-Y, H:i')), 'd', 'l', '', 'sem_list_row') . "</tr>";
	}

	if ($row->showend > 0)
	{
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_END') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell(JHTML::_('date', $row->end), 'd', 'l', '', 'sem_list_row') . "</tr>";
	}

	if ($row->showbooked > 0)
	{
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell(JHTML::_('date', $row->booked), 'd', 'l', '', 'sem_list_row') . "</tr>";
	}

	if ($row->teacher != "")
	{
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_TUTOR') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell($row->teacher, 'd', 'l', '', 'sem_list_row') . "</tr>";
	}

	if ($row->target != "")
	{
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_TARGET_GROUP') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell($row->target, 'd', 'l', '', 'sem_list_row') . "</tr>";
	}

	$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_CITY') . ":", 'd', 'l', '', 'sem_list_row')
		. MatukioHelperUtilsEvents::getTableCell($row->place, 'd', 'l', '', 'sem_list_row') . "</tr>";

	if ($row->nrbooked > 0)
	{
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_MAX_PARTICIPANT') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell($row->maxpupil, 'd', 'l', '', 'sem_list_row') . "</tr>";
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_BOOKINGS') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell($gebucht, 'd', 'l', '', 'sem_list_row') . "</tr>";
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_BOOKABLE') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell($freieplaetze, 'd', 'l', '', 'sem_list_row') . "</tr>";
	}

	if ($row->fees > 0)
	{
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_FEES') . ":", 'd', 'l', '', 'sem_list_row')
			. MatukioHelperUtilsEvents::getTableCell(MatukioHelperSettings::getSettings('currency_symbol', '$')
			. " " . $row->fees, 'd', 'l', '', 'sem_list_row') . "</tr>";
	}

	if ($row->description != "")
	{
		$row->description = str_replace("images/", "../images/", $row->description);
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(MatukioHelperUtilsEvents::getCleanedMailText($row->description),
				'd', 'l', '100%', 'sem_list_row', 2) . "</tr>";
	}

	if ($row->codepic != "")
	{
		$html .= "<tr>" . MatukioHelperUtilsEvents::getTableCell(MatukioHelperUtilsBooking::getBookingIdCodePicture($row->codepic),
				'd', 'c', '100%', 'sem_list_row', 2) . "</tr>";
	}

	$html .= "\n</table><br />";
}

$html .= MatukioHelperUtilsBasic::getCopyright();
$html .= "</div></body></html>";

echo $html;
