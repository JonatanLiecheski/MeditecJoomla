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

/**
 * Class MatukioViewStatistic
 *
 * @since  3.0.0
 */
class MatukioViewStatistics extends JViewLegacy
{
	/**
	 * Displays the templates view
	 *
	 * @param   string  $tpl  - Differen template
	 *
	 * @return  mixed|void
	 */

	public function display($tpl = null)
	{
		$database = JFactory::getDBO();

		$startjahr = 2007;
		$stats = array();
		$mstats = array();
		$temp = array();
		$Monate = array(JTEXT::_('JANUARY'), JTEXT::_('FEBRUARY'), JTEXT::_('MARCH'), JTEXT::_('APRIL'),
			JTEXT::_('MAY'), JTEXT::_('JUNE'), JTEXT::_('JULY'), JTEXT::_('AUGUST'), JTEXT::_('SEPTEMBER'),
			JTEXT::_('OCTOBER'), JTEXT::_('NOVEMBER'), JTEXT::_('DECEMBER'));

		$stats[0] = new stdClass;
		$stats[0]->courses = 0;
		$stats[0]->bookings = 0;
		$stats[0]->certificated = 0;
		$stats[0]->hits = 0;
		$stats[0]->maxpupil = 0;
		$stats[0]->year = JTEXT::_('COM_MATUKIO_COMMON_PERIOD');

		for ($i = 0, $n = 12; $i < $n; $i++)
		{
			$month = $i + 1;
			$database->setQuery(
				"SELECT a.*, r.* FROM #__matukio_recurring AS r
				LEFT JOIN #__matukio AS a ON r.event_id = a.id
				WHERE MONTH(r.begin)='$month' AND pattern = ''"
			);
			$rows = $database->loadObjectList();
			$bookings = 0;
			$certificated = 0;
			$hits = 0;
			$maxpupil = 0;

			foreach ($rows AS $row)
			{
				$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($row);
				$bookings = $bookings + $gebucht->booked;
				$certificated = $certificated + $gebucht->certificated;
				$hits = $hits + $row->hits;
				$maxpupil = $maxpupil + $row->maxpupil;
			}

			$temp[$i] = new stdClass;
			$temp[$i]->courses = count($rows);
			$stats[0]->courses += $temp[$i]->courses;
			$temp[$i]->bookings = $bookings;
			$stats[0]->bookings += $temp[$i]->bookings;
			$temp[$i]->certificated = $certificated;
			$stats[0]->certificated += $temp[$i]->certificated;
			$temp[$i]->hits = $hits;
			$stats[0]->hits += $temp[$i]->hits;
			$temp[$i]->maxpupil = $maxpupil;
			$stats[0]->maxpupil += $temp[$i]->maxpupil;
			$temp[$i]->year = $Monate[$i];
		}

		$mstats[0] = $temp;

		$zaehler = 0;

		for ($i = 0, $n = 25; $i < $n; $i++)
		{
			$aktjahr = $startjahr + $i;
			$database->setQuery(
				"SELECT COUNT(*) AS courses FROM #__matukio_recurring AS r
				LEFT JOIN #__matukio AS a ON r.event_id = a.id
				WHERE YEAR(r.begin)='$aktjahr' AND a.pattern = ''"
			);
			$rows = $database->loadObjectList();

			if ($rows[0]->courses == 0)
			{
				continue;
			}

			$temp = array();
			$zaehler++;
			$stats[$zaehler] = new stdClass;
			$stats[$zaehler]->courses = 0;
			$stats[$zaehler]->bookings = 0;
			$stats[$zaehler]->certificated = 0;
			$stats[$zaehler]->hits = 0;
			$stats[$zaehler]->maxpupil = 0;
			$stats[$zaehler]->year = $aktjahr;

			for ($l = 0, $m = 12; $l < $m; $l++)
			{
				$month = $l + 1;
				$database->setQuery(
					"SELECT a.*, r.* FROM #__matukio_recurring AS r
					LEFT JOIN #__matukio AS a ON r.event_id = a.id
					WHERE MONTH(r.begin)='$month' AND YEAR(r.begin)='$aktjahr' AND pattern = ''"
				);
				$rows = $database->loadObjectList();
				$bookings = 0;
				$certificated = 0;
				$hits = 0;
				$maxpupil = 0;

				foreach ($rows AS $row)
				{
					$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($row);
					$bookings = $bookings + $gebucht->booked;
					$certificated = $certificated + $gebucht->certificated;
					$hits = $hits + $row->hits;
					$maxpupil = $maxpupil + $row->maxpupil;
				}

				$temp[$l] = new stdClass;
				$temp[$l]->courses = count($rows);
				$stats[$zaehler]->courses += $temp[$l]->courses;
				$temp[$l]->bookings = $bookings;
				$stats[$zaehler]->bookings += $temp[$l]->bookings;
				$temp[$l]->certificated = $certificated;
				$stats[$zaehler]->certificated += $temp[$l]->certificated;
				$temp[$l]->hits = $hits;
				$stats[$zaehler]->hits += $temp[$l]->hits;
				$temp[$l]->maxpupil = $maxpupil;
				$stats[$zaehler]->maxpupil += $temp[$l]->maxpupil;
				$temp[$l]->year = $Monate[$l];
			}

			$mstats[$zaehler] = $temp;
		}

		$this->stats = $stats;
		$this->mstats = $mstats;

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Adds the toolbar buttons
	 *
	 * @return  void
	 */
	public function addToolbar()
	{
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_MATUKIO_STATS'), 'sem_statistic');
		JToolBarHelper::help('screen.matukio', true);
	}
}
