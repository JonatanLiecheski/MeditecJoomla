<?php

/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       06.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.plugin.plugin');

jimport('joomla.database.table');

JLoader::register('MatukioHelperSettings', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/settings.php');
JLoader::register('MatukioHelperUtilsBasic', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/util_basic.php');

$app = JFactory::getApplication('site');

/**
 * Class PlgSearchMatukio
 *
 * @since  1.0
 */
class PlgSearchMatukio extends JPlugin
{
	/**
	 * Adds Matukio to the search area
	 *
	 * @return mixed
	 */
	public function onContentSearchAreas()
	{
		static $areas = array('matukio' => 'PLG_SEARCH_MATUKIO_EVENTS');

		return $areas;
	}

	/**
	 * Integrates into the Joomla search
	 *
	 * @param   string  $text      - The search text
	 * @param   string  $phrase    - The search phrase
	 * @param   string  $ordering  - The ordering of the results
	 * @param   object  $areas     - The Areas
	 *
	 * @return array
	 */
	public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
	{
		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return array();
			}
		}

		// No search text
		$text = trim($text);

		if ($text == "")
		{
			return array();
		}

		// Vorbereitungen
		$database = JFactory::getDBO();
		$my = JFactory::getuser();
		$app = JFactory::getApplication();
		$offset = $app->getCfg('offset');

		if (MatukioHelperSettings::getSettings('date_format_summertime', 1) > 0)
		{
			$jahr = date("Y");
			$sombeginn = mktime(2, 0, 0, 3, 31 - date('w', mktime(2, 0, 0, 3, 31, $jahr)), $jahr);
			$somende = mktime(2, 0, 0, 10, 31 - date('w', mktime(2, 0, 0, 10, 31, $jahr)), $jahr);
			$aktuell = time();

			if ($aktuell > $sombeginn AND $aktuell < $somende)
			{
				$offset++;
			}
		}

		$date = JFactory::getDate();

		// Offset? $date->setOffset($offset);
		$neudatum = $date->toSql();

		// Find corresponding Matukio joomla entry
		$database->setQuery("SELECT id FROM #__menu WHERE link='index.php?option=com_matukio&view=eventlist'");
		$tempitemid = $database->loadResult();

		$slimit = $this->params->get('search_limit', 50);
		$sname = $this->params->get('search_name', 'Matukio');

		// Check category ACL rights
		$groups = implode(',', $my->getAuthorisedViewLevels());

		// TODO Cleanup ..
		$query = $database->getQuery(true);
		$query->select("id, access")->from("#__categories")->where(
			array("extension = " . $database->quote("com_matukio"),
				"published = 1", "access in (" . $groups . ")")
		);

		$database->setQuery($query);
		$cats = $database->loadObjectList();

		// Filter search for allowed categories
		$allowedcat = array();

		foreach ((array) $cats AS $cat)
		{
			$allowedcat[] = $cat->id;
		}

		if (!empty($allowedcat))
		{
			$where[] = "a.catid IN (" . implode(',', $allowedcat) . ")";
		}

		$where[] = "r.published = '1'";
		$where[] = "a.pattern = ''";

		switch (MatukioHelperSettings::getSettings('event_stopshowing', 2))
		{
			case "0":
				$showend = "r.begin";
				break;
			case "1":
				$showend = "r.booked";
				break;
			case "3":
				$showend = "";
				break;

			default:
				$showend = "r.end";
				break;
		}

		$where[] = "$showend > '$neudatum'";

// Sortierung festlegen
		$order = '';

		switch ($ordering)
		{
			case 'newest':
				$order = ' ORDER BY r.id DESC';
				break;
			case 'oldest':
				$order = ' ORDER BY r.id';
				break;
			case 'popular':
				$order = ' ORDER BY r.hits';
				break;
			case 'alpha':
				$order = ' ORDER BY title';
				break;
			case 'category':
				$order = ' ORDER BY category';
				break;
		}

		switch ($phrase)
		{
			case 'exact':
				$text = preg_replace('/\s/', ' ', trim($text));
				$suche = "\nAND (r.semnum LIKE '%" . $text . "%' OR a.gmaploc LIKE '%" . $text . "%' OR a.target LIKE '%" . $text
					. "%' OR a.place LIKE '%" . $text . "%' OR a.teacher LIKE '%" . $text . "%' OR a.title LIKE '%" . $text
					. "%' OR a.shortdesc LIKE '%" . $text . "%' OR a.description LIKE '%" . $text . "%')";
				break;
			case 'all':
			case 'any':
			default:
				$text = preg_replace('/\s\s+/', ' ', trim($text));
				$words = explode(' ', $text);
				$suche = array();

				foreach ($words as $word)
				{
					$word = $database->Quote('%' . $database->getEscaped($word, true) . '%', false);
					$suche2 = array();
					$suche2[] = "a.semnum LIKE $word";
					$suche2[] = "a.gmaploc LIKE $word";
					$suche2[] = "a.target LIKE $word";
					$suche2[] = "a.place LIKE $word";
					$suche2[] = "a.teacher LIKE $word";
					$suche2[] = "a.title LIKE $word";
					$suche2[] = "a.shortdesc LIKE $word";
					$suche2[] = "a.description LIKE $word";
					$suche3[] = implode(' OR ', $suche2);
				}

				$suche = "\nAND (" . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $suche3) . ")";
				break;
		}

		// Rueckgabe des Suchergebnisses
		$database->setQuery("SELECT a.*, r.*,"
			. " a.title AS title,"
			. " r.begin AS begin,"
			. " a.publishdate AS created,"
			. " a.shortdesc AS text,"
			. " CONCAT('index.php?option=com_matukio&Itemid=" . $tempitemid . "&view=event&id=', r.id) AS href,"
			. " '2' AS browsernav,"
			. " '" . $sname . "' AS section,"
			. " cat.title AS category"
			. " FROM #__matukio_recurring AS r"
			. " LEFT JOIN #__matukio AS a ON r.event_id = a.id"
			. " LEFT JOIN #__categories AS cat ON a.catid = cat.id"
			. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
			. $suche
			. $order
			. " LIMIT 0, " . $slimit
		);

		$rows = $database->loadObjectList();

		for ($i = 0; $i < count($rows); $i++)
		{
			$date = JFactory::getDate($rows[$i]->begin);
			$rows[$i]->section = $rows[$i]->section . " - " . JHTML::_('date', $date, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i'));
			$rows[$i]->Itemid = $tempitemid;
		}

		return $rows;
	}
}
