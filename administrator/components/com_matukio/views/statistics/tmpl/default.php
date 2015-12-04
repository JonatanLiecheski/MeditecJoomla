<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       05.10.13
 *
 * @copyright  Copyright (C) 2008 - {YEAR} Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

$stats = $this->stats;
$mstats = $this->mstats;

$html = '<div class="box-info full">';
$html .= MatukioHelperUtilsBasic::printFormstart(2) . "\n<script type=\"text/javascript\">";
$html .= "function semauf(semy, semm) {";
$html .= "document.adminForm.year.value = semy;";
$html .= "document.adminForm.month.value = semm;";
$html .= "document.adminForm.submit();}";
$html .= "</script>";

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

// Load bootstrap in Joomla 2.5
echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'statistics', 'COM_MATUKIO_STATS', 'COM_MATUKIO_SLOGAN_STATS');

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

// Header
$n = count($stats);

if ($n == 2)
{
	$o = 1;
}
else
{
	$o = 0;
}

for ($i = $o, $n; $i < $n; $i++)
{
	$daten = $mstats[$i];
	$m = count($daten);

	if ($n > ($o + 1))
	{
		$html .= "\n<div style=\"border: 1px solid #C0C0F0;width: 100%;border-style: ridge;\">";
	}

	$html .= "\n<br /><a style=\"font-size:18px; font-weight: bold;\" href=\"#\" onclick=\"semauf('"
		. $stats[$i]->year . "','');\">" . $stats[$i]->year . "</a>";
	$html0 = "";
	$html1 = "\n<div class=\"table-responsive\"><table class=\"table\">";

	// --------------------------------------------------------
	// Anlegen Tabellenkopfes
	// --------------------------------------------------------

	$html1 .= "\n<thead>";
	$temp = array(JTEXT::_('COM_MATUKIO_MONTH'), JTEXT::_('COM_MATUKIO_EVENTS'), JTEXT::_('COM_MATUKIO_HITS'), JTEXT::_('COM_MATUKIO_BOOKINGS'),
		JTEXT::_('COM_MATUKIO_CERTIFICATES'), JTEXT::_('COM_MATUKIO_MAX_PARTICIPANT'), JTEXT::_('COM_MATUKIO_AVERAGE_UTILISATION'),
		JTEXT::_('COM_MATUKIO_HITS') . " / " . JTEXT::_('COM_MATUKIO_EVENT'), JTEXT::_('COM_MATUKIO_BOOKINGS') . " / " . JTEXT::_('COM_MATUKIO_EVENT'),
		JTEXT::_('COM_MATUKIO_MAX_PARTICIPANT') . " / " . JTEXT::_('COM_MATUKIO_EVENT'));

	$tempa = array("nw", "nw", "nw", "nw", "nw", "nw", "c2", "", "", "");
	$html1 .= "\n" . MatukioHelperUtilsAdmin::getTableLine("th", $tempa, "", $temp, "");
	$html1 .= "\n</thead>";

	// --------------------------------------------------------
	// Anlegen des Tabellenkoerpers
	// --------------------------------------------------------

	$html1 .= "<tbody>";

	if ($m > 0)
	{
		$image = "http://chart.apis.google.com/chart?cht=lc";
		$image .= "&amp;chs=400x200";
		$image .= "&amp;chco=ffa844,44cc44,4444ff,ff4444";
		$image .= "&amp;chm=b,ff8800,0,4,0|b,00cc00,1,4,0|b,0000ff,2,4,0|b,ff0000,3,4,0";
		$image .= "&amp;chg=0,50";
		$image .= "&amp;chdl=" . JTEXT::_('COM_MATUKIO_HITS') . "|" . JTEXT::_('COM_MATUKIO_BOOKINGS') . "|"
			. JTEXT::_('COM_MATUKIO_CERTIFICATES') . "|" . JTEXT::_('COM_MATUKIO_EVENTS');

		$image .= "&amp;chxt=x,y";

		$chl = array(JTEXT::_('JANUARY_SHORT'), JTEXT::_('FEBRUARY_SHORT'), JTEXT::_('MARCH_SHORT'),
			JTEXT::_('APRIL_SHORT'), JTEXT::_('MAY_SHORT'), JTEXT::_('JUNE_SHORT'), JTEXT::_('JULY_SHORT'),
			JTEXT::_('AUGUST_SHORT'), JTEXT::_('SEPTEMBER_SHORT'), JTEXT::_('OCTOBER_SHORT'),
			JTEXT::_('NOVEMBER_SHORT'), JTEXT::_('DECEMBER_SHORT'));

		$imagea = "http://chart.apis.google.com/chart?cht=p3&amp;chs=230x100&amp;&amp;chco=";
		$imagehi = $imagea . "ff8800&amp;chd=t:";
		$imagebo = $imagea . "00cc00&amp;chd=t:";
		$imagece = $imagea . "0000ff&amp;chd=t:";
		$imageco = $imagea . "ff0000&amp;chd=t:";
		$highest = array();

		for ($l = 0, $m; $l < $m; $l++)
		{
			$highest[] = $daten[$l]->hits;
		}

		$maximum = max($highest);

		if ($maximum < 1)
		{
			$maximum = 1;
		}

		$image .= "&amp;chxl=0:|" . implode('|', $chl) . "|1:|0|" . (round($maximum * 0.25))
			. "|" . (round($maximum * 0.5)) . "|" . (round($maximum * 0.75)) . "|" . $maximum;

		$image .= "&amp;chd=t:";
		$ihits = array();
		$ibookings = array();
		$icertificated = array();
		$icourses = array();
		$phits = array();
		$pbookings = array();
		$pcertificated = array();
		$pcourses = array();
		$plhits = array();
		$plbookings = array();
		$plcertificated = array();
		$plcourses = array();

		$k = 0;

		for ($l = 0, $m; $l < $m; $l++)
		{
			if ($daten[$l]->maxpupil == "" OR $daten[$l]->maxpupil == 0)
			{
				$temp0 = 0;
			}
			else
			{
				$temp0 = round($daten[$l]->bookings * 100 / $daten[$l]->maxpupil, 0);
			}

			$temp1 = MatukioHelperChart::getProcentBarchart($temp0);
			$temp11 = $temp0 . "%";

			if ($daten[$l]->hits == "" OR $daten[$l]->hits == 0)
			{
				$temp2 = 0;
				$teiler = 1;
			}
			else
			{
				$temp2 = $daten[$l]->hits;
				$phits[] = $stats[$i]->hits != 0 ? round(($temp2 * 100) / $stats[$i]->hits) : 100;
				$plhits[] = $chl[$l];
			}

			$ihits[] = round(($temp2 * 100) / $maximum);

			if ($daten[$l]->bookings == "" OR $daten[$l]->bookings == 0)
			{
				$temp3 = 0;
			}
			else
			{
				$temp3 = $daten[$l]->bookings;
				$pbookings[] = $stats[$i]->bookings != 0 ? round(($temp3 * 100) / $stats[$i]->bookings) : 100;
				$plbookings[] = $chl[$l];
			}

			$ibookings[] = round(($temp3 * 100) / $maximum);

			if ($daten[$l]->certificated == "" OR $daten[$l]->certificated == 0)
			{
				$temp9 = 0;
			}
			else
			{
				$temp9 = $daten[$l]->certificated;
				$pcertificated[] = $stats[$i]->certificated != 0 ? round(($temp9 * 100) / $stats[$i]->certificated) : 100;
				$plcertificated[] = $chl[$l];
			}

			$icertificated[] = round(($temp9 * 100) / $maximum);

			if ($daten[$l]->maxpupil == "" OR $daten[$l]->maxpupil == 0)
			{
				$temp4 = 0;
			}
			else
			{
				$temp4 = $daten[$l]->maxpupil;
			}

			if ($daten[$l]->courses == "" OR $daten[$l]->courses == 0)
			{
				$temp5 = 0;
				$temp6 = 0;
				$temp7 = 0;
			}
			else
			{
				$temp5 = $daten[$l]->courses != 0 ? round($daten[$l]->hits / $daten[$l]->courses) : $daten[$l]->hits;
				$temp6 = $daten[$l]->courses != 0 ? round($daten[$l]->bookings / $daten[$l]->courses) : $daten[$l]->bookings;
				$temp7 = $daten[$l]->courses != 0 ? round($daten[$l]->maxpupil / $daten[$l]->courses) : $daten[$l]->maxpupil;
				$pcourses[] = $stats[$i]->courses != 0 ? round((($daten[$l]->courses) * 100) / $stats[$i]->courses) : 100;
				$plcourses[] = $chl[$l];
			}

			$icourses[] = round((($daten[$l]->courses) * 100) / $maximum);
			$temp8 = "<a href=\"#\" onclick=\"semauf('" . $stats[$i]->year . "','" . ($l + 1) . "')\">" . $daten[$l]->year . "</a>";
			$temp = array(($temp8), ($daten[$l]->courses), ($temp2), ($temp3), ($temp9), ($temp4), ($temp1), ($temp11), ($temp5), ($temp6), ($temp7));
			$tempa = array("l", "c", "c", "c", "c", "c", "c", "c", "c", "c", "c");
			$tempb = array("", "", "", "", "", "", "nw", "", "", "", "");
			$html1 .= "\n" . MatukioHelperUtilsAdmin::getTableLine("td", $tempa, "", $temp, "row" . $k);
			$k = 1 - $k;
		}

		$image .= implode(',', $ihits) . "|" . implode(',', $ibookings) . "|" . implode(',', $icertificated) . "|" . implode(',', $icourses) . "|0,0";

		$imagehi .= implode(',', $phits) . "&amp;chl=" . implode('|', $plhits);
		$imagebo .= implode(',', $pbookings) . "&amp;chl=" . implode('|', $plbookings);
		$imagece .= implode(',', $pcertificated) . "&amp;chl=" . implode('|', $plcertificated);
		$imageco .= implode(',', $pcourses) . "&amp;chl=" . implode('|', $plcourses);

		$html0 .= "<br /><div class=\"table-responsive\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"adminlist table\">";
		$html0 .= "<tr><th colspan=\"2\" rowspan=\"2\"><img src=\"" . $image . "\" border=\"0\"></th>";
		$html0 .= "<th><img src=\"" . $imagehi . "\" border=\"0\"></th>";
		$html0 .= "<th><img src=\"" . $imagebo . "\" border=\"0\"></th></tr>";
		$html0 .= "<tr><th><img src=\"" . $imagece . "\" border=\"0\"></th>";
		$html0 .= "<th><img src=\"" . $imageco . "\" border=\"0\"></th></tr>";
		$html0 .= "</table></div><br />";
	}
	else
	{
		$html1 .= "<tr class=\"row0\"><td colspan=\"9\">" . JTEXT::_('COM_MATUKIO_NO_STATS') . "</td>";
	}

	$html .= $html0 . $html1 . "</tbody>";

	// --------------------------------------------------------
	// Anlegen des Tabellenfusses
	// --------------------------------------------------------

	if ($m > 0)
	{
		$html .= "<tfoot>";

		if ($stats[$i]->hits == "")
		{
			$temp1 = 0;
		}
		else
		{
			$temp1 = $stats[$i]->hits;
		}

		if ($stats[$i]->bookings == "")
		{
			$temp2 = 0;
		}
		else
		{
			$temp2 = $stats[$i]->bookings;
		}

		if ($stats[$i]->certificated == "")
		{
			$temp9 = 0;
		}
		else
		{
			$temp9 = $stats[$i]->certificated;
		}

		if ($stats[$i]->maxpupil == "")
		{
			$temp3 = 0;
		}
		else
		{
			$temp3 = $stats[$i]->maxpupil;
		}

		if ($stats[$i]->maxpupil == 0)
		{
			$temp4 = "0%";
		}
		else
		{
			$temp4 = round($stats[$i]->bookings * 100 / $stats[$i]->maxpupil, 0) . "%";
		}

		if ($stats[$i]->courses == 0)
		{
			$temp5 = 0;
			$temp6 = 0;
			$temp7 = 0;
		}
		else
		{
			$temp5 = round($stats[$i]->hits / $stats[$i]->courses);
			$temp6 = round($stats[$i]->bookings / $stats[$i]->courses);
			$temp7 = round($stats[$i]->maxpupil / $stats[$i]->courses);
		}

		$temp = array(JTEXT::_('COM_MATUKIO_SUMMARY'), ($stats[$i]->courses), ($temp1), ($temp2), ($temp9),
			($temp3), ($temp4), ($temp5), ($temp6), ($temp7));

		$tempa = array("l", "r", "r", "r", "r", "r", "r", "r", "r", "r");
		$tempb = array("", "", "", "", "", "", "c2", "", "", "");

		$html .= "\n" . MatukioHelperUtilsAdmin::getTableLine("th", $tempa, $tempb, $temp, "");
		$html .= "\n</tfoot>";
	}

	// --------------------------------------------------------
	// Anlegen des Seitenendes und Ausgabe
	// --------------------------------------------------------

	$html .= "</table></div>";

	if ($n > ($o + 1))
	{
		$html .= "</div>";
	}

	$html .= "<br />";
}
if ($n > 0)
{
	$html .= JTEXT::_('COM_MATUKIO_INFO_RELATED_TO_EVENTS') . "<br />";
}

$html .= "\n<input type=\"hidden\" name=\"option\" value=\"" . JFactory::getApplication()->input->get('option') . "\" />";
$html .= "<input type=\"hidden\" name=\"task\" value=\"\" />";
$html .= "<input type=\"hidden\" name=\"year\" value=\"\" />";
$html .= "<input type=\"hidden\" name=\"month\" value=\"\" />";

$html .= "<input type=\"hidden\" name=\"view\" value=\"statistics\" />";

$html .= "\n</form></div>";

echo $html;

echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
