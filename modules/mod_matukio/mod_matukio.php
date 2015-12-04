<?php/** * Matukio - Module * Based on Seminar for Joomla! * by Dirk Vollmar * * @package     Matukio * @author      Yves Hoppe <yves@compojoom.com> * @copyright   (C) 2012 - 2013 Yves Hoppe - compojoom.com * @license     GPL v2 or later * @since       1.0.0 * @deprecated  Use the new modules instead **/defined('_JEXEC') or die('Restricted access');jimport('joomla.database.table');JLoader::register('MatukioHelperSettings', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/settings.php');JLoader::register('MatukioHelperUtilsBasic', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_basic.php');JLoader::register('MatukioHelperUtilsBooking', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_booking.php');JLoader::register('MatukioHelperRoute', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_route.php');JLoader::register('MatukioHelperCategories', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_categories.php');$app = JFactory::getApplication();$database = JFactory::getDBO();$my = JFactory::getuser();$offset = $app->getCfg('offset');if (MatukioHelperSettings::getSettings('date_format_summertime', 1) > 0){	$jahr = date("Y");	$sombeginn = mktime(2, 0, 0, 3, 31 - date('w', mktime(2, 0, 0, 3, 31, $jahr)), $jahr);	$somende = mktime(2, 0, 0, 10, 31 - date('w', mktime(2, 0, 0, 10, 31, $jahr)), $jahr);	$aktuell = time();	if ($aktuell > $sombeginn AND $aktuell < $somende)	{		$offset++;	}}$neudatum = JFactory::getDate();$html = "";$anzahl = $params->get('sem_m066', 5);if (!function_exists('sem_m003')){	function sem_m003($row, $params)	{		$html = "";		if ($params->get('sem_m067', 0) > 0 AND $row->showbegin > 0)		{			$html .= "<i>" . JText::_('MOD_MATUKIO_BEGIN') . "</i>: ";			$htxt = JHTML::_('date', $row->begin, $params->get('sem_m043', 'd.m.Y, H:i'));			if ($row->cancelled > 0)			{				$htxt = "<del>" . $htxt . "</del>";			}			$html .= $htxt . "<br />";		}		if ($params->get('sem_m068', 0) > 0 AND $row->showend > 0)		{			$html .= "<i>" . JText::_('MOD_MATUKIO_END') . "</i>: ";			$htxt = JHTML::_('date', $row->end, $params->get('sem_m043', 'd.m.Y, H:i'));			if ($row->cancelled > 0)			{				$htxt = "<del>" . $htxt . "</del>";			}			$html .= $htxt . "<br />";		}		if ($params->get('sem_m069', 0) > 0 AND $row->showbooked > 0)		{			$html .= "<i>" . JText::_('MOD_MATUKIO_CLOSING_DATE') . "</i>: ";			$htxt = JHTML::_('date', $row->booked, $params->get('sem_m043', 'd.m.Y, H:i'));			if ($row->cancelled > 0)			{				$htxt = "<del>" . $htxt . "</del>";			}			$html .= $htxt . "<br />";		}		if ($params->get('sem_m070', 0) > 0)		{			$html .= "<i>" . JText::_('MOD_MATUKIO_PUBLISHING_DATE') . "</i>: " . JHTML::_('date', $row->publishdate, $params->get('sem_m043', 'd.m.Y, H:i')) . "<br />";		}		if ($params->get('sem_m079', 0) > 0)		{			if (!empty($row->place))			{				$htxt = str_replace(array("\r\n", "\n", "\r"), ', ', trim($row->place));				$html .= "<i>" . JText::_('MOD_MATUKIO_LOCATION') . "</i>: " . htmlspecialchars($htxt) . "<br />";			}		}		if ($params->get('sem_m071', 0) > 0)		{			$html .= "<i>" . htmlspecialchars($row->shortdesc) . "</i><br />";		}		return $html;	}}if (!function_exists('sem_m001')){	function sem_m001($database, $my, $art, $params, $neudatum)	{		$where = array();		// Check category ACL rights		$groups = implode(',', JFactory::getUser()->getAuthorisedViewLevels());		$query = $database->getQuery(true);		$query->select("id, access")->from("#__categories")->where(array("extension = " . $database->quote("com_matukio"),			"published = 1", "access in (" . $groups . ")"));		$database->setQuery($query);		$cats = $database->loadObjectList();		$allowedcat = array();		foreach ((array) $cats AS $cat)		{			$allowedcat[] = $cat->id;		}		if (count($allowedcat) > 0)		{			$allowedcat = implode(',', $allowedcat);			$where[] = "a.catid IN ($allowedcat)";		}		$where[] = "r.published = '1'";		$where[] = "a.pattern = ''";		if ($params->get('sem_m050', '') != "")		{			$temp = explode(" ", $params->get('sem_m050', ''));			$temq = array();			foreach ($temp AS $el)			{				$temq[] .= "a.catid='" . $el . "'";			}			$where[] = implode(" OR ", $temq);		}		if ($params->get('sem_m082', '') != "")		{			$temp = explode(" ", $params->get('sem_m082', ''));			$temq = array();			foreach ($temp AS $el)			{				$temq[] .= "a.publisher='" . $el . "'";			}			$where[] = implode(" OR ", $temq);		}		switch ($art)		{			case "0":				$showend = "r.begin";				break;			case "1":				$showend = "r.booked";				break;			default:				$showend = "r.end";				break;		}		switch ($params->get('sem_m046', 0))		{			case 0:				$where[] = "$showend > '$neudatum'";				break;			case 1:				$where[] = "$showend <= '$neudatum'";				break;		}		return $where;	}}if (!function_exists('sem_m002')){	function sem_m002($id)	{		$database = JFactory::getDBO();		$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid='" . $id . "'");		$temps = $database->loadObjectList();		$gebucht = 0;		$zertifiziert = 0;		$bezahlt = 0;		$zurueck = new JObject;		foreach ($temps as $el)		{			$gebucht = $gebucht + $el->nrbooked;			$zertifiziert = $zertifiziert + $el->certificated;			$bezahlt = $bezahlt + $el->paid;		}		$zurueck->booked = $gebucht;		$zurueck->certificated = $zertifiziert;		$zurueck->paid = $bezahlt;		$zurueck->number = count($temps);		return $zurueck;	}}$html = "";$index = "";if ($params->get('sem_m074', 0) > 0){	$html .= "<marquee height=\"" . $params->get('sem_m023', 150) . "px\" align=\"left\" behavior=\"" . $params->get('sem_m017', 'scroll')		. "\" direction=\"" . $params->get('sem_m021', 'up') . "\" scrollamount=\"" . $params->get('sem_m015', 1)		. "\" scrolldelay=\"" . $params->get('sem_m019', 50) . "\" truespeed onmouseover=\"this.stop();\" onmouseout=\"this.start();\">";}switch ($params->get('sem_m039', 0)){	case "0";		$werte = array();		if ($params->get('sem_m035', 0) > 0)		{			$database->setQuery("SELECT a.*, r.* FROM #__matukio_recurring AS r				LEFT JOIN #__matukio AS a ON r.event_id = a.id				WHERE r.published = '1' AND a.pattern = ''");			$rows = $database->loadObjectList();			$rows[0]->header = JTEXT::_('MOD_MATUKIO_ALL_EVENTS');			$werte[] = $rows;		}		if ($params->get('sem_m037', 0) > 0)		{			$database->setQuery(				"SELECT a.*, r.* FROM #__matukio_recurring AS r				LEFT JOIN #__matukio AS a ON r.event_id = a.id				WHERE r.published = '1' AND a.pattern = '' AND r.end > '$neudatum'"			);			$rows = $database->loadObjectList();			$rows[0]->header = JTEXT::_('MOD_MATUKIO_RECENT_EVENTS');			$werte[] = $rows;		}		if ($params->get('sem_m036', 0) > 0)		{			$database->setQuery(				"SELECT a.*, r.* FROM #__matukio_recurring AS r				LEFT JOIN #__matukio AS a ON r.event_id = a.id				WHERE r.published = '1' AND a.pattern = '' AND r.end <= '$neudatum'"			);			$rows = $database->loadObjectList();			$rows[0]->header = JTEXT::_('MOD_MATUKIO_OLD_EVENTS');			$werte[] = $rows;		}		foreach ($werte AS $wert)		{			$hits = 0;			$bookings = 0;			$certificated = 0;			$courses = 0;			$paid = 0;			$number = 0;			$free = 0;			$html .= "<b>" . $wert[0]->header . "</b><br />";			foreach ($wert as $row)			{				$gebucht = sem_m002($row->id);				$hits += isset($row->hits) ? $row->hits : 0;				$bookings += $gebucht->booked;				$freetemp = $row->maxpupil - $gebucht->booked;				if ($freetemp > 0)				{					$free += $freetemp;				}				$certificated += $gebucht->certificated;				$paid += $gebucht->paid;				$number += $gebucht->number;				$courses++;			}			$html .= JTEXT::_('MOD_MATUKIO_EVENTS') . ": " . $courses . "<br />";			if ($params->get('sem_m034', 0) > 0)			{				$html .= JTEXT::_('MOD_MATUKIO_HITS') . ": " . $hits . "<br />";			}			if ($params->get('sem_m028', 0) > 0)			{				$html .= JTEXT::_('MOD_MATUKIO_BOOKED_PLACES') . ": " . $bookings . "<br />";			}			if ($params->get('sem_m033', 0) > 0)			{				$html .= JText::_('MOD_MATUKIO_FREE_SPACES') . ": " . $free . "<br />";			}			if ($params->get('sem_m029', 0) > 0)			{				$html .= JTEXT::_('MOD_MATUKIO_BOOKINGS') . ": " . $number . "<br />";			}			if ($params->get('sem_m030', 0) > 0)			{				$html .= JTEXT::_('MOD_MATUKIO_PAID_BOOKINGS') . ": " . $paid . "<br />";			}			if ($params->get('sem_m031', 0) > 0)			{				$html .= JTEXT::_('MOD_MATUKIO_CERTIFICATES') . ": " . $certificated . "<br />";			}			$html .= "<br />";		}		break;	case "1":		$where = sem_m001($database, $my, MatukioHelperSettings::getSettings('event_stopshowing', 2), $params, $neudatum);		$limit = "";		if ($params->get('sem_m081', 0) < 1)		{			$limit = "\nLIMIT " . $params->get('sem_m066', 5);		}		$sortierung = array("r.begin", "r.end", "r.booked", "a.publishdate", "r.hits", "a.bookedpupil", "a.certificated", "r.grade");		$richtung = array(" ASC", " DESC");		$database->setQuery(			"SELECT a.*, r.* FROM #__matukio_recurring AS r			LEFT JOIN #__matukio AS a ON r.event_id = a.id"			. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")			. "\nORDER BY " . $sortierung[$params->get('sem_m052', 0)] . $richtung[$params->get('sem_m063', 1)]			. $limit		);		$rows = $database->loadObjectList();		if ($params->get('sem_m081', 0) == 1)		{			$neu = array();			$rox = array();			$max = $params->get('sem_m066', 5);			if (count($rows) < $max)			{				$max = count($rows);			}			while (count($neu) < $max)			{				srand(microtime() * 1000000);				$zufall = rand(0, count($rows) - 1);				if (!in_array($zufall, $neu))				{					$neu[] = $zufall;				}			}			sort($neu);			foreach ($neu AS $el)			{				$rox[] = $rows[$el];			}			$rows = $rox;		}		foreach ($rows AS $row)		{			$buchdate = "";			if ($my->id > 0)			{				$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid='$row->id' AND userid='$my->id'");				$temp = $database->loadObjectList();				if (count($temp) > 0)				{					$buchdate = $temp[0]->bookingdate;				}			}			$gebucht = sem_m002($row->id);			if ($params->get('sem_m078', 0) > 0)			{				// Link				$eventid_l = $row->id . ':' . JFilterOutput::stringURLSafe($row->title);				$catid_l = $row->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($row->catid));				$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l), false);				$html .= "<a href='" . $link . "'>";			}			$html .= "<strong>" . $row->title;			if ($row->cancelled > 0)			{				$html .= " - " . JText::_('MOD_MATUKIO_CANCELLED');			}			elseif ($buchdate != "")			{				$html .= " - " . JText::_('MOD_MATUKIO_BOOKED');			}			$html .= "</strong>";			if ($params->get('sem_m078', 0) > 0)			{				$html .= "</a>";			}			$html .= "<br />";			$html .= sem_m003($row, $params);			if ($params->get('sem_m034', 0) > 0)			{				$html .= "<i>" . JText::_('MOD_MATUKIO_HITS') . "</i>: " . $row->hits . "<br />";			}			if ($params->get('sem_m028', 0) > 0)			{				$html .= "<i>" . JText::_('MOD_MATUKIO_BOOKED_PLACES') . "</i>: " . $gebucht->booked . "<br />";			}			if ($params->get('sem_m033', 0) > 0)			{				$free = $row->maxpupil - $gebucht->booked;				if ($free < 0)				{					$free = 0;				}				$html .= "<i>" . JText::_('MOD_MATUKIO_FREE_SPACES') . "</i>: " . $free . "<br />";			}			if ($params->get('sem_m029', 0) > 0)			{				$html .= "<i>" . JText::_('MOD_MATUKIO_BOOKINGS') . "</i>: " . $gebucht->number . "<br />";			}			if ($params->get('sem_m030', 0) > 0)			{				$html .= "<i>" . JText::_('MOD_MATUKIO_PAID_BOOKINGS') . "</i>: " . $gebucht->paid . "<br />";			}			if ($params->get('sem_m031', 0) > 0)			{				$html .= "<i>" . JText::_('MOD_MATUKIO_CERTIFICATES') . "</i>: " . $gebucht->certificated . "<br />";			}			$html .= "<br />";		}		break;	case "2":		// Calendar view		JHTML::_('behavior.tooltip');		$monatidx = JFactory::getApplication()->input->getInt('sem_midx', 0);		$jahridx = JFactory::getApplication()->input->getInt('sem_jidx', 0);		$startdatum = mktime(0, 0, 0, date('m') + $monatidx, 1, date('Y') + $jahridx);		$monatkal = date('m', $startdatum);		$jahrkal = date('Y', $startdatum);		$monatstage = $monatkal == 2 ? ($jahrkal % 4 ? 28 : ($jahrkal % 100 ? 29 : ($jahrkal % 400 ? 28 : 29))) : (($monatkal - 1) % 7 % 2 ? 30 : 31);		$where = sem_m001($database, $my, MatukioHelperSettings::getSettings('event_stopshowing', 2), $params, $neudatum);		$where[] = "r.begin >= '" . $jahrkal . "-" . $monatkal . "-01 00:00:00'";		$where[] = "r.begin <= '" . $jahrkal . "-" . $monatkal . "-" . $monatstage . " 23:59:59'";		$database->setQuery(			"SELECT a.*, r.*, cat.title AS category FROM #__matukio_recurring AS r			LEFT JOIN #__matukio AS a ON r.event_id = a.id			LEFT JOIN #__categories AS cat ON a.catid = cat.id"			. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")			. "\nORDER BY r.begin"		);		$rows = $database->loadObjectList();		$database->setQuery("SELECT semid AS id FROM #__matukio_bookings WHERE (userid='$my->id' AND userid>0)");		$gebucht = $database->loadObjectList();		$html = "<center><table style=\"" . $params->get('sem_m084', '') . "\">";		$url_query = array_diff_key(JRequest::get('get'), array('sem_midx' => 0, 'sem_jidx' => 0));		$url_query = http_build_query($url_query, "", "&amp;");		if ($url_query != "")		{			$url_query .= "&amp;";		}		$vmonat = "sem_midx=" . ($monatidx - 1) . "&amp;sem_jidx=" . $jahridx;		$nmonat = "sem_midx=" . ($monatidx + 1) . "&amp;sem_jidx=" . $jahridx;		$vjahr = "sem_midx=" . $monatidx . "&amp;sem_jidx=" . ($jahridx - 1);		$njahr = "sem_midx=" . $monatidx . "&amp;sem_jidx=" . ($jahridx + 1);		$monat = mktime(0, 0, 0, $monatkal, 1, $jahrkal);		$monatsname = date('F', $monat);		$html .= "<tr><td colspan=\"7\" style=\"" . $params->get('sem_m089', '') . "\"><a href=\"" . JROUTE::_('index.php?'			. $url_query . $vjahr, 1, 0) . "\" style=\"" . $params->get('sem_m091', '') . "\">&laquo;</a>&nbsp;<a href=\""			. JROUTE::_('index.php?' . $url_query . $vmonat, 1, 0) . "\" style=\"" . $params->get('sem_m091', '')			. "\">&lsaquo;</a>&nbsp;&nbsp;" . JTEXT::_($monatsname) . " " . $jahrkal . "&nbsp;&nbsp;<a href=\""			. JROUTE::_('index.php?' . $url_query . $nmonat, 1, 0) . "\" style=\"" . $params->get('sem_m091', '')			. "\">&rsaquo;</a>&nbsp;<a href=\"" . JROUTE::_('index.php?' . $url_query . $njahr, 1, 0) . "\" style=\""			. $params->get('sem_m091', '') . "\">&raquo;</a></td></tr>";		$html .= "<tr>";		for ($i = 0; $i < 7; $i++)		{			$tag = ($i + $params->get('sem_m088', 1) + 3) * 86400;			$html .= "<td style=\"" . $params->get('sem_m090', '') . "\">" . substr(gmstrftime('%A', $tag), 0, 2) . "</td>";		}		$html .= "</tr>";		$html .= "<tr>";		$tag = strftime("%w", $monat);		$leertage = ($tag + 7 - $params->get('sem_m088', 1)) % 7;		if ($leertage > 0)		{			for ($i = 1; $i <= $leertage; $i++)			{				$html .= "<td style=\"" . $params->get('sem_m085', '') . "\">&nbsp;</td>";			}		}		$spalten = $leertage;		$index = "&sem_midx=" . $monatidx . "&sem_jidx=" . $jahridx;		for ($i = 1; $i <= $monatstage; $i++)		{			if ($spalten == 7)			{				$html .= "</tr><tr>";				$spalten = 0;			}			$temp = $where;			$events = "";			$heutestil = "";			if ($jahrkal == date('Y') AND $monatkal == date('m') AND $i == date('d'))			{				$heutestil = $params->get('sem_m087', '');			}			$tag = $i;			if ($tag < 10)			{				$tag = "0" . $tag;			}			$beginn = $jahrkal . "-" . $monatkal . "-" . $tag . " 00:00:00";			$ende = $jahrkal . "-" . $monatkal . "-" . $tag . " 23:59:59";			$stil = "style=\"" . $params->get('sem_m085', '') . $heutestil . "\"";			foreach ($rows AS $row)			{				if ($row->begin >= $beginn AND $row->begin <= $ende)				{					$grafik = "2608";					foreach ($gebucht AS $gebid)					{						if ($gebid->id == $row->id)						{							$grafik = "2609";							break;						}					}					// Link					$eventid_l = $row->id . ':' . JFilterOutput::stringURLSafe($row->title);					$catid_l = $row->catid . ':' . JFilterOutput::stringURLSafe($row->category);					$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l), false);					$events .= "<a class=\"editlinktip hasTip\" title=\""						. htmlspecialchars($row->title) . "::" . sem_m003($row, $params) . "\" style=\"text-decoration: none;\" href='"						. $link . "'><img src=\"modules/mod_matukio/images/" . $grafik . ".png\" border=\"0\"></a>";				}			}			if ($events != "")			{				$stil = "style=\"" . $params->get('sem_m086', '') . $heutestil . "\"";			}			$html .= "<td " . $stil . ">" . $i . "<br />" . $events . "</td>";			$spalten++;		}		if ($spalten < 7)		{			$leertage = (7 - $spalten);			for ($i = 1; $i <= $leertage; $i++)			{				$html .= "<td style=\"" . $params->get('sem_m085', '') . "\">&nbsp;</td>";			}		}		$html .= "</tr></table></center>";		break;}if ($params->get('sem_m074', 0) > 0){	$html .= "</marquee>";}if ($params->get('sem_m075', 0) > 0 AND $params->get('sem_m039', 0) > 0){	$needles = array(		'event' => (int) 1,		'category' => (int) 0,	);	$item = MatukioHelperRoute::_findItem($needles);	$html .= "<hr style='height:1px;color:#808080;background-color:#808080;border:0px;'><a href='"		. JROUTE::_('index.php?option=com_matukio&view=eventlist' . $index . '&Itemid=' . $item->id, 1, 0) . "'><b>"		. JText::_('MOD_MATUKIO_MORE') . "</b></a>";}echo $html;