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
$my = JFactory::getuser();
$neudatum = MatukioHelperUtilsDate::getCurrentDate();
JHTML::_('behavior.modal');
JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
JHTML::_('script', 'media/com_matukio/js/matukio.js');

$params = JComponentHelper::getParams('com_matukio');
$menuitemid = JFactory::getApplication()->input->get('Itemid');

if ($menuitemid)
{
	$site = new JSite;
	$menu = $site->getMenu();

	$menuparams = $menu->getParams($menuitemid);
	$params->merge($menuparams);
}
?>
<!-- Matukio by compojoom.com -->
<div id="matukio_holder">
<?php
// Heading
echo MatukioHelperUtilsBasic::printFormStart(1);
$knopfoben = "";
$knopfunten = MatukioHelperUtilsEvents::getEventlistHeader(($this->art + 1));

if ($this->art == 2)
{
	$newlink = JRoute::_("index.php?option=com_matukio&view=createevent");

	$knopfoben .= JHTML::_('link', $newlink, JHTML::_('image', MatukioHelperUtilsBasic::getComponentImagePath()
		. '1832.png', null, array('border' => '0', 'align' => 'absmiddle')
		), array('title' => JTEXT::_('COM_MATUKIO_NEW_EVENT'))
	);

	$knopfunten .= '<a href="' . $newlink . '">' .
		"<span class=\"mat_button\" style=\"cursor:pointer;\" \">"
		. JHTML::_('image', MatukioHelperUtilsBasic::getComponentImagePath() . '1816.png', null,
			array('border' => '0', 'align' => 'absmiddle')
		) . "&nbsp;" . JTEXT::_('COM_MATUKIO_NEW_EVENT')
		. "</span></a>";
}

if (count($this->rows) > 0)
{
	if ($this->art == 0 AND MatukioHelperSettings::getSettings('rss_feed', 1) == 1)
	{
		$href = JURI::ROOT() . "index.php?tmpl=component&option=" . JFactory::getApplication()->input->get('option') . "&view=rss&format=raw";

		$knopfoben .= "<a href=\"" . $href . "\" target=\"_new\" title=\"" . JTEXT::_('COM_MATUKIO_RSS_FEED') . "\" border=\"0\">" . JHTML::_('image',
				MatukioHelperUtilsBasic::getComponentImagePath() . '3132.png', null, array('border' => '0', 'align' => 'absmiddle')) . "</a>";
		$knopfunten .= " <span class=\"mat_button\" style=\"cursor:pointer;\" type=\"button\" onClick=\"window.open('" . $href . "');\"><img src=\""
			. MatukioHelperUtilsBasic::getComponentImagePath() . "3116.png\" border=\"0\" align=\"absmiddle\">&nbsp;" . JTEXT::_('COM_MATUKIO_RSS_FEED') . "</span>";
	}

	if ($this->art == 0 AND MatukioHelperSettings::getSettings('frontend_usericsdownload', 1) == 1)
	{
		$href = JURI::ROOT() . "index.php?tmpl=component&option=" . JFactory::getApplication()->input->get('option') . "&view=ics&format=raw";

		$knopfoben .= "<a href=\"" . $href . "\" target=\"_new\" title=\"" . JTEXT::_('COM_MATUKIO_DOWNLOAD_CALENDER_FILE') . "\" border=\"0\">" . JHTML::_('image',
				MatukioHelperUtilsBasic::getComponentImagePath() . '3316.png', null, array('border' => '0', 'align' => 'absmiddle')) . "</a>";

		$knopfunten .= " <span class=\"mat_button\" style=\"cursor:pointer;\" type=\"button\" onClick=\"window.open('" . $href . "');\"><img src=\""
			. MatukioHelperUtilsBasic::getComponentImagePath() . "3316.png\" border=\"0\" align=\"absmiddle\">&nbsp;" . JTEXT::_('COM_MATUKIO_DOWNLOAD_CALENDER_FILE')
			. "</span>";
	}

	$knopfoben .= MatukioHelperUtilsEvents::getPrintWindow(($this->art + 2), '', '', '');
	$knopfunten .= "&nbsp;" . MatukioHelperUtilsEvents::getPrintWindow(($this->art + 2), '', '', 'b');
}
if (MatukioHelperSettings::getSettings('event_buttonposition', 2) == 0 OR MatukioHelperSettings::getSettings('event_buttonposition', 2) == 2)
{
	echo $knopfoben;
}
MatukioHelperUtilsEvents::getEventlistHeaderEnd();
$html = "";

// ---------------------
// Anzeige Kategoriekopf
// ---------------------

$navioben1 = array();
if ($this->art == 0)
{
	if ($this->catid == 0)
	{
		$headline = array(JTEXT::_('COM_MATUKIO_ALL_CATS'), JTEXT::_('COM_MATUKIO_DETAILS_PAGE_FOR_EVENTS'));
	}
	else
	{
		$headline = MatukioHelperUtilsEvents::getCategoryDescriptionArray($this->catid);
	}

	$navioben1 = explode(" ", MatukioHelperSettings::getSettings('frontend_topnavshowmodules', 'SEM_NUMBER SEM_SEARCH SEM_CATEGORIES SEM_RESET'));
}
elseif ($this->art == 1)
{
	$headline = array(JTEXT::_('COM_MATUKIO_MY_BOOKINGS'), JTEXT::_('COM_MATUKIO_SEE_ALL_BOOKED_EVENTS'));
	$navioben1 = explode(" ", MatukioHelperSettings::getSettings('frontend_topnavbookingmodules', 'SEM_NUMBER SEM_SEARCH SEM_TYPES SEM_RESET'));
}
elseif ($this->art == 2)
{
	$headline = array(JTEXT::_('COM_MATUKIO_MY_OFFERS'), JTEXT::_('COM_MATUKIO_ALL_OFFERED_EVENTS'));
	$navioben1 = explode(" ", MatukioHelperSettings::getSettings('frontend_topnavoffermodules', 'SEM_NUMBER SEM_SEARCH SEM_TYPES SEM_RESET'));
}

MatukioHelperUtilsEvents::printHeading($headline[0], $headline[1]);

$navioben2 = array('SEM_NUMBER', 'SEM_SEARCH', 'SEM_CATEGORIES', 'SEM_TYPES', 'SEM_RESET');
$navioben3 = array_diff($navioben2, $navioben1);

if (count($navioben1) > 0 OR $navioben1[0] != "NULL")
{
	$html .= MatukioHelperUtilsEvents::getTableHeader(4) . "<tr>";

	foreach ($navioben1 AS $el)
	{
		switch ($el)
		{
			case "SEM_NUMBER":
				$html .= MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_DISPLAY') . "&nbsp;"
				. MatukioHelperUtilsEvents::getLimitboxSiteNav(1, $this->limit), 'd', 'l', '', 'sem_nav');
				break;

			case "SEM_SEARCH":
				// JTEXT::_('COM_MATUKIO_SEARCH')
				$html .= MatukioHelperUtilsEvents::getTableCell("<input class=\"sem_inputbox\" type=\"text\" name=\"search\" id=\"search_field\" height=\"16\" size=\"15\" value=\""
				. $this->search . "\" onChange=\"searchEventlist();\" onkeypress=\"return event.keyCode!=13\" /> <button onclick=\"searchEventlist(); return false;\">"
				. JText::_("COM_MATUKIO_SEARCH") . "</button>", 'd', 'c', '', 'sem_nav'); // onkeydown=\"if (event.keyCode == 13) {searchEventlist();event.returnValue=false;event.canc‌​el=true;}\"
				break;

			case "SEM_CATEGORIES":
				$html .= MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_CATEGORY') . ": "
				. $this->clist, 'd', 'c', '', 'sem_nav');
				break;

			case "SEM_TYPES":
				$html .= MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_KIND') . ": "
				. $this->datelist, 'd', 'c', '', 'sem_nav');
				break;

			case "SEM_RESET":
				$html .= MatukioHelperUtilsEvents::getTableCell("<button class=\"button\" style=\"cursor:pointer;\"
                    type=\"button\" onclick=\"resetEventlist();\">"
				. JTEXT::_('COM_MATUKIO_RESET') . "</button>", 'd', 'r', '', 'sem_nav'); // Should be button and not mat_button too big
				break;
		}
	}
	$html .= "</tr>" . MatukioHelperUtilsEvents::getTableHeader('e');
}

$n = count($this->rows);

if ($n < $this->total)
{
	$html .= $this->pageNav;
}

// ---------------------------
// Anzeige der einzelnen Kurse
// ---------------------------

$html .= MatukioHelperUtilsEvents::getTableHeader(4);

if ($n > 0)
{
	// Schleife beginnen
	for ($i = 0, $n; $i < $n; $i++)
	{
		$row = $this->rows[$i];

		// Pruefung, ob Lehrgang buchbar
		$buchopt = MatukioHelperUtilsEvents::getEventBookableArray($this->art, $row, $my->id);
		$eventid_l = $row->id . ':' . JFilterOutput::stringURLSafe($row->title);
		$catid_l = $row->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($row->catid));

		$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l), false);

		if ($this->art == 1)
		{
			$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l, $this->art), false);
		}

		// Edit own events
		if ($this->art == 2)
		{
			$link = JRoute::_("index.php?option=com_matukio&view=createevent&cid=" . $row->id);
		}

		// Bild ausgeben
		$html .= "<tr>";
		$zusimage = "";
		$zusbild = 0;

		if ($this->art == 0)
		{
			$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2601.png";

			if ($my->id == $row->publisher)
			{
				$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2603.png";
				$zusimage = MatukioHelperUtilsBasic::getComponentImagePath() . "2607.png";
			}

			if ($buchopt[0] == 2)
			{
				$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2602.png";
				$zusimage = MatukioHelperUtilsBasic::getComponentImagePath() . "2606.png";
			}

			$funktion = array(JTEXT::_('COM_MATUKIO_DESCRIPTION'), 3);
		}
		elseif ($this->art == 1)
		{
			$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2701.png";
			$funktion = array(JTEXT::_('COM_MATUKIO_DESCRIPTION'), 4);
			$zusimage = MatukioHelperUtilsBasic::getComponentImagePath() . "2606.png";
		} elseif ($this->art == 2)
		{
			$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2801.png";
			$funktion = array(JTEXT::_('COM_MATUKIO_EDIT_EVENT'), 9);

			if ($row->publisher == $my->id)
			{
				$zusimage = MatukioHelperUtilsBasic::getComponentImagePath() . "2607.png";
			}
		}

		if ($my->id == 0)
		{
			$zusimage = "";
		}

		if ($row->cancelled == 1)
		{
			$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2604.png";
			$zusimage = MatukioHelperUtilsBasic::getComponentImagePath() . "2200.png";
		}

		if ($row->image != "" AND  MatukioHelperSettings::getSettings('event_image', 1) == 1)
		{
			$linksbild = MatukioHelperUtilsBasic::getEventImagePath(1) . $row->image;
			$zusbild = 1;
		}

		$htxt = "<div style=\"position:relative;top:0px;left:0px;\"><a title=\"" . $funktion[0]
			. "\" href=\"" . $link . "\">
            <img src=\"" . $linksbild . "\" border=\"0\">";

		if ($zusbild == 1 AND $zusimage != "" AND MatukioHelperSettings::getSettings('event_image', 1) > 0)
		{
			$htxt .= "<div style=\"position:absolute;top:4px;left:4px;\"><img src=\"" . $zusimage . "\"></div>";
		}

		$htxt .= "</a></div>";
		$html .= MatukioHelperUtilsEvents::getTableCell($htxt, 'd', 'l', '', "sem_row");

		// Gebuehren anzeigen
		$htxt = "";

		if ($row->fees > 0)
		{
			$gebuehr = MatukioHelperUtilsEvents::getFormatedCurrency($row->fees);
			$klasse = "sem_fees";

			if ($this->art == 1 AND $buchopt[0] == 2)
			{
				if (count($buchopt[2]) > 0)
				{
					if ($buchopt[2][0]->paid == 1)
					{
						$klasse = "sem_fees_paid";
					}
					else
					{
						$klasse = "sem_fees_notpaid";
					}

					if ($buchopt[2][0]->nrbooked > 1)
					{
						$gebuehr = MatukioHelperUtilsEvents::getFormatedCurrency($buchopt[2][0]->payment_brutto);
					}
				}
			}

			$htxt .= "<span class=\"" . $klasse . "\">" . MatukioHelperSettings::getSettings('currency_symbol', '$')
				. " " . $gebuehr . "</span>";
		}

		// Beginn anzeigen                                 fse
		if ($row->showbegin > 0)
		{
			if ($row->cancelled == 1)
			{
				$cltimezone = "";

				if (MatukioHelperSettings::getSettings('show_timezone', '1'))
				{
					$cltimezone = " (GMT " . JHTML::_('date', $row->begin, 'P') . ")";
				}

				$htxt .= "\n<span class=\"sem_cancelled\">" . JTEXT::_('COM_MATUKIO_CANCELLED') . "</span><span class=\"sem_date\"> (<del>"
					. JHTML::_('date', $row->begin, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')) . $cltimezone . "</del>)</span><br />";
			}
			else
			{
				$cltimezone = "";

				if (MatukioHelperSettings::getSettings('show_timezone', '1'))
				{
					$cltimezone = " (GMT " . JHTML::_('date', $row->begin, 'P') . ")";
				}

				$htxt .= "\n<span class=\"sem_date\">" . JHTML::_('date', $row->begin, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i'))
					. $cltimezone . "</span><br />";
			}
		}

		// Titel anzeigen
		$htxt .= "\n<a class=\"sem_title\" href=\"" . $link . "\" title=\"" . $funktion[0] . "\">" . JText::_($row->title) . "</a><br />";

		// Kurzbeschreibung anzeigen
		$htxt .= "\n<span class=\"sem_shortdesc\">" . JText::_($row->shortdesc) . "</span>";

		// Anmeldeschluss bzw. Buchungsdatum anzeigen
		if ($row->nrbooked < 1)
		{
			$htxt .= "<br />\n<span class=\"sem_cat\">" . JTEXT::_('COM_MATUKIO_CANNOT_BOOK_ONLINE') . "</span>";
		}
		elseif ($row->showbooked > 0)
		{
			if ($buchopt[0] == 2)
			{
				$cltimezone = "";

				if (MatukioHelperSettings::getSettings('show_timezone', '1'))
				{
					$cltimezone = " (GMT " . JHTML::_('date', $buchopt[2][0]->bookingdate, 'P') . ")";
				}

				$htxt .= "<br />\n<span class=\"sem_cat\">" . JTEXT::_('COM_MATUKIO_DATE_OF_BOOKING') . ": " . JHTML::_('date', $buchopt[2][0]->bookingdate,
						MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')) . $cltimezone . "</span>";
			}
			else
			{
				$cltimezone = "";

				if (MatukioHelperSettings::getSettings('show_timezone', '1'))
				{
					$cltimezone = " (GMT " . JHTML::_('date', $row->booked, 'P') . ")";
				}

				if ($row->cancelled == 1)
				{
					$htxt .= "<br />\n<span class=\"sem_cat\">" . JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ": <del>" . JHTML::_('date', $row->booked,
							MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
						) . $cltimezone . "</del></span>";
				}
				else
				{
					$htxt .= "<br />\n<span class=\"sem_cat\">" . JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ": " . JHTML::_('date', $row->booked,
							MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
						) . $cltimezone . "</span>";
				}
			}
		}

		// Infozeile anzeigen
		$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($row);

		if (MatukioHelperSettings::getSettings('event_showinfoline', 1) == 1)
		{
			$htxt .= "<br />\n<span class=\"sem_cat\">" . JTEXT::_('COM_MATUKIO_CATEGORY') . ": " . $row->category;

			if ($row->nrbooked > 0)
			{
				$htxt .= " - " . JTEXT::_('COM_MATUKIO_BOOKED_PLACES') . ": " . $gebucht->booked . " - " . JTEXT::_('COM_MATUKIO_BOOKABLE')
					. ": " . $buchopt[4] . " - " . JTEXT::_('COM_MATUKIO_HITS') . ": " . $row->hits;
				$htxt .= "</span>";
			}
		}

		$html .= MatukioHelperUtilsEvents::getTableCell($htxt, 'd', '', '98%', "sem_row");

		// Zertifikatdruck erlauben
		if (MatukioHelperSettings::getSettings('frontend_certificatesystem', 0) > 0 AND $this->art == 1)
		{
			if ($buchopt[2][0]->certificated == 1 AND $row->nrbooked > 0)
			{
				if (JFactory::getUser()->id > 0)
				{
					$htxt = MatukioHelperUtilsEvents::getPrintWindow(1, $row->sid, $buchopt[2][0]->id, '');
					$htbr = 30;
				}
			}
			else
			{
				$htxt = "&nbsp;";
				$htbr = "";
			}

			$html .= MatukioHelperUtilsEvents::getTableCell($htxt, 'h', '', $htbr, "sem_row");
		}

		// Anzeige der Teilnehmer erlauben          -- todo fix acl
		if ((MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 2 AND $my->id > 0 // Falls registrierte sehen dürfen und user registriert ist und art 0 ist
				AND $this->art == 0) OR (MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 1 //    ODER Jeder (auch unregistrierte die Teilnehmer sehen dürfen und art 0 ist
				AND $this->art == 0)
			OR (MatukioHelperSettings::getSettings('frontend_teilnehmerviewteilnehmer', 0) > 0 AND $my->id > 0 // Wenn  Teilnehmer Teilnehmer sehen dürfen (wtf ist der check ob er teilnehmer ist?? nur mit art = 1??)
				AND $this->art == 1)
			OR (MatukioHelperSettings::getSettings('frontend_ownereditevent', 1) > 0 AND $this->art == 2)) //Falls Frontendedit event 1 ist und art = 2
		{
			$htxt = "&nbsp";

			if ($row->nrbooked > 0)
			{
				$viewteilnehmerlink = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $row->id . "&art=" . $this->art);

				$htxt = "<a href=\"" . $viewteilnehmerlink . "\"><span class=\"mat_button\" style=\"cursor:pointer;\"
                title=\"" . JTEXT::_('COM_MATUKIO_BOOKINGS') . "\">" . $gebucht->booked . "</span></a>";
			}

			$html .= MatukioHelperUtilsEvents::getTableCell($htxt, 'h', '', '30', "sem_row");
		}

		// Bewertung erlauben
		if (MatukioHelperSettings::getSettings('frontend_ratingsystem', 0) > 0 AND $this->art > 0)
		{
			$htxt = "&nbsp";

			if ($neudatum > $row->end AND $row->nrbooked > 0)
			{
				if ($this->art == 1)
				{
					$htxt = MatukioHelperUtilsEvents::getRatingPopup(MatukioHelperUtilsBasic::getComponentImagePath(),
						$row->id, $buchopt[2][0]->grade);
				}
				elseif ($this->art == 2)
				{
					$htxt = "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "240"
						. $row->grade . ".png\" alt=\"" . JTEXT::_('COM_MATUKIO_RATING') . "\">";
				}

				$htbr = 30;
			}
			else
			{
				$htxt = "&nbsp;";
				$htbr = "";
			}

			$html .= MatukioHelperUtilsEvents::getTableCell($htxt, 'h', '', $htbr, "sem_row");
		}

		// Ausgabe der Statusgrafik
		if (MatukioHelperSettings::getSettings('event_statusgraphic', 2) > 0)
		{
			$htxt = "&nbsp;";

			// Ampel
			if (MatukioHelperSettings::getSettings('event_statusgraphic', 2) == 1 AND $row->nrbooked > 0)
			{
				$htxt = "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "230" . $buchopt[3]
					. ".png\" alt=\"" . $buchopt[1] . "\">";

			}
			elseif (MatukioHelperSettings::getSettings('event_statusgraphic', 2) == 2 AND $row->nrbooked > 0)
			{
				// Säule
				if ((MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 2 AND JFactory::getUser()->id // Falls registrierte sehen dürfen und user registriert ist und art 0 ist
						AND $this->art == 0) OR (MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 1 //    ODER Jeder (auch unregistrierte die Teilnehmer sehen dürfen und art 0 ist
						AND $this->art == 0)
					OR (MatukioHelperSettings::getSettings('frontend_teilnehmerviewteilnehmer', 0) > 0 AND JFactory::getUser()->id // Wenn  Teilnehmer Teilnehmer sehen dürfen (wtf ist der check ob er teilnehmer ist?? nur mit art = 1??)
						AND $this->art == 1)
					OR (MatukioHelperSettings::getSettings('frontend_ownereditevent', 1) > 0 AND $this->art == 2) //Falls Frontendedit event 1 ist und art = 2
				)
				{
					$htxt = MatukioHelperUtilsEvents::getProcentBar($row->maxpupil, $buchopt[4], $buchopt[3]);
				}
			}

			$html .= MatukioHelperUtilsEvents::getTableCell($htxt, 'd', 'c', '24', "sem_row");
		}

		$html .= "</tr>";
	}
}
else
{
	$html .= "<tr>";
	$html .= MatukioHelperUtilsEvents::getTableCell(JTEXT::_('COM_MATUKIO_NO_EVENT_FOUND'), 'h', '', '100%', 'sem_row');
	$html .= "</tr>";
}

$html .= "</table>";
$html .= "</table>";

// ---------------------------------------
// Ausgabe der Seitennavigation
// ---------------------------------------

if (count($this->rows) < $this->total)
{
	$html .= $this->pageNav;
}

// ---------------------------------------
// Ausgabe der unsichtbaren Formularfelder
// ---------------------------------------

if ($this->art == 0)
{
	$dots = array(JTEXT::_('COM_MATUKIO_NOT_EXCEEDED'), JTEXT::_('COM_MATUKIO_BOOKING_ON_WAITLIST'), JTEXT::_('COM_MATUKIO_UNBOOKABLE'));
}
elseif ($this->art == 1)
{
	$dots = array(JTEXT::_('COM_MATUKIO_PARTICIPANT_ASSURED'),
		JTEXT::_('COM_MATUKIO_WAITLIST'), JTEXT::_('COM_MATUKIO_NO_SPACE_AVAILABLE'));
}
elseif ($this->art == 2)
{
	$dots = array(JTEXT::_('COM_MATUKIO_EVENT_HAS_NOT_STARTED_YET'), JTEXT::_('COM_MATUKIO_EVENT_IS_RUNNING'), JTEXT::_('COM_MATUKIO_EVENT_HAS_ENDED'));
}

// ---------------------------------------
// Farbbeschreibungen anzeigen
// ---------------------------------------

if (count($this->rows) > 0 AND MatukioHelperSettings::getSettings('sem_hide_ampel', '') == 0
	AND MatukioHelperSettings::getSettings('event_statusgraphic', 2) > 0)
{
	$html .= MatukioHelperUtilsEvents::getColorDescriptions($dots[0], $dots[1], $dots[2], $this->art);
}

// ---------------------------------
// Anzeige Funktionsknoepfe unten
// ---------------------------------

if (MatukioHelperSettings::getSettings('event_buttonposition', 2) > 0)
{
	$html .= MatukioHelperUtilsEvents::getTableHeader(4) . "<tr>"
		. MatukioHelperUtilsEvents::getTableCell($knopfunten, 'd', 'c', '100%', 'sem_nav_d')
		. "</tr>" . MatukioHelperUtilsEvents::getTableHeader('e');
}

// ---------------------------------------
// Ausgabe der unsichtbaren Formularfelder
// ---------------------------------------

foreach ($navioben1 AS $el)
{
	switch ($el)
	{
		case "SEM_NUMBER":
			$html .= "<input type=\"hidden\" name=\"limit\" value=\"" . $this->limit . "\">";
			break;
		case "SEM_SEARCH":
			$html .= "<input type=\"hidden\" name=\"search\" value=\"" . $this->search . "\">";
			break;
		case "SEM_CATEGORIES":
			$html .= "<input type=\"hidden\" name=\"catid\" value=\"" . $this->catid . "\">";
			break;
		case "SEM_TYPES":
			$html .= "<input type=\"hidden\" name=\"dateid\" value=\"" . $this->dateid . "\">";
			break;
	}
}

if ($this->art == 0)
{
	$html .= "<input type=\"hidden\" name=\"dateid\" id=\"dateid\" value=\"" . $this->dateid . "\">";
}
elseif ($this->art == 1)
{
	$html .= "<input type=\"hidden\" name=\"catid\" id=\"catid\" value=\"" . $this->catid . "\">";
}
elseif ($this->art == 2)
{
	$html .= "<input type=\"hidden\" name=\"catid\" id=\"catid\" value=\"" . $this->catid . "\">";
}

$html .= MatukioHelperUtilsEvents::getHiddenFormElements($this->art, "", "", "", $this->limitstart, 0, "", -1);
$html .= " <input type=\"hidden\" name=\"art\" id=\"hidden_art\" value=\"" . $this->art . "\">";
echo $html;

echo MatukioHelperUtilsBasic::getCopyright();
?>
</form>
</div>
<!-- End Matukio by compojoom.com -->
