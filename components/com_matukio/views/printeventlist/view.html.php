<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');

/**
 * Class MatukioViewPrintEventlist
 *
 * @since  1.0.0
 */
class MatukioViewPrintEventlist extends JViewLegacy
{
	/**
	 * Shows the form
	 *
	 * @param   string  $tpl  - The tmpl
	 *
	 * @return  bool|mixed|object
	 */
	public function display($tpl = NULL)
	{
		$database = JFactory::getDBO();
		$my = JFactory::getuser();
		$dateid = JFactory::getApplication()->input->getInt('dateid', 1);
		$catid = JFactory::getApplication()->input->getInt('catid', 0);
		$search = JFactory::getApplication()->input->get('search', '', 'string');
		$limit = JFactory::getApplication()->input->getInt('limit', 5);
		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
		$cid = JFactory::getApplication()->input->getInt('cid', 0);
		$uid = JFactory::getApplication()->input->getInt('uid', 0);

		$todo = JFactory::getApplication()->input->get('todo', 'print_eventlist'); // print_eventlist, print_booking, print_myevents, print
		$rows = null;
		$status = null;
		$headertext = null;


		$neudatum = MatukioHelperUtilsDate::getCurrentDate();

		if ($limitstart < 0)
		{
			$limitstart = 0;
		}

		$ttlimit = "";

		if ($limit > 0)
		{
			$ttlimit = "\nLIMIT $limitstart, $limit";
		}

		/**
		 * 65O9805443904 =    public ?!
		 * 653O875032490 =    Meine Angebote
		 * 6530387504345 =  Meine Buchungen / Buchungsbestätigung ?!
		 *
		 * 3728763872762 =
		 * csv
		 */

		$where = array();
		$where[] = "a.pattern = ''";
		$where[] = "a.published = '1'";

		switch ($todo)
		{
			case "print_eventlist":
				$navioben = explode(" ", MatukioHelperSettings::getSettings('frontend_topnavshowmodules', 'SEM_NUMBER SEM_SEARCH SEM_CATEGORIES SEM_RESET'));
				break;
			case "print_booking":
				$navioben = explode(" ", MatukioHelperSettings::getSettings('frontend_topnavbookingmodules', 'SEM_NUMBER SEM_SEARCH SEM_CATEGORIES SEM_RESET'));
				break;
			case "print_myevents":
				$navioben = explode(" ", MatukioHelperSettings::getSettings('frontend_topnavoffermodules', 'SEM_NUMBER SEM_SEARCH SEM_CATEGORIES SEM_RESET'));
				break;
			case "print_teilnehmerliste":
				$navioben = "";
				break;

		}

		if ($todo != "print_teilnehmerliste" && $todo != "csvlist" && $todo != "certificate" && $todo != "invoice")
		{
			if (in_array('SEM_TYPES', $navioben))
			{
				switch ($dateid)
				{
					case "1":
						$where[] = "a.end > '$neudatum'";
						break;
					case "2":
						$where[] = "a.end <= '$neudatum'";
						break;
				}
			}
		}


		switch ($todo)
		{
			default:
			case "print_eventlist":
				if (!in_array('SEM_TYPES', $navioben))
				{
					$where[] = "r.end > '$neudatum'";
				}

				if ((isset($_GET["catid"]) OR in_array('SEM_CATEGORIES', $navioben)) AND $catid > 0)
				{
					$where[] = "a.catid ='$catid'";
				}

				$headertext = JTEXT::_('COM_MATUKIO_EVENTS');

				if ($cid)
				{
					$where[] = "r.id= '$cid'";
					$headertext = JTEXT::_('COM_MATUKIO_EVENT');
				}

				$database->setQuery("SELECT a.*, r.*, cc.title AS category FROM #__matukio_recurring AS r"
					. "\nLEFT JOIN #__matukio AS a ON r.event_id = a.id"
					. "\nLEFT JOIN #__categories AS cc ON cc.id = a.catid"
					. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
					. "\nAND (r.semnum LIKE'%$search%' OR a.teacher LIKE '%$search%' OR a.title LIKE '%$search%'"
					. " OR a.shortdesc LIKE '%$search%' OR a.description LIKE '%$search%')"
				);

				$rows = $database->loadObjectList();

				// Abzug der Kurse, die wegen Ausbuchung nicht angezeigt werden sollen
				if (!$cid)
				{
					$abid = array();

					foreach ($rows as $row)
					{
						if ($row->stopbooking == 2)
						{
							$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($row);

							if ($row->maxpupil - $gebucht->booked < 1)
							{
								$abid[] = $row->id;
							};
						}
					}

					if (count($abid) > 0)
					{
						$abid = implode(',', $abid);
						$where[] = "r.id NOT IN ($abid)";
					}
				}

				$database->setQuery("SELECT a.*, r.*, cc.title AS category FROM #__matukio_recurring AS r"
					. "\nLEFT JOIN #__matukio AS a ON r.event_id = a.id"
					. "\nLEFT JOIN #__categories AS cc"
					. "\nON cc.id = a.catid"
					. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
					. "\nAND (a.semnum LIKE'%$search%' OR a.teacher LIKE '%$search%' OR a.title LIKE '%$search%' OR a.shortdesc LIKE '%$search%' OR a.description LIKE '%$search%')"
					. "\nORDER BY r.begin"
					. $ttlimit
				);

				$rows = $database->loadObjectList();
				$status = array();
				$paid = array();
				$abid = array();

				for ($i = 0, $n = count($rows); $i < $n; $i++)
				{
					$row = & $rows[$i];
					$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($row);
					$gebucht = $gebucht->booked;

					if (MatukioHelperUtilsDate::getCurrentDate() > $row->booked
						OR ($row->maxpupil - $gebucht < 1 AND $row->stopbooking == 1)
						OR ($my->id == $row->publisher AND MatukioHelperSettings::getSettings('booking_ownevents', 1) == 0))
					{
						$status[$i] = JTEXT::_('COM_MATUKIO_UNBOOKABLE');
					}
					elseif ($row->maxpupil - $gebucht < 1 && $row->stopbooking == 0)
					{
						$status[$i] = JTEXT::_('COM_MATUKIO_BOOKING_ON_WAITLIST');
					}
					elseif ($row->maxpupil - $gebucht < 1 && $row->stopbooking == 2)
					{
						$abid[] = $row->id;
					}
					else
					{
						$status[$i] = JTEXT::_('COM_MATUKIO_NOT_EXCEEDED');
					}

					$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid='$row->id' AND userid='$my->id'");
					$temp = $database->loadObjectList();

					if (count($temp) > 0)
					{
						$status[$i] = JTEXT::_('COM_MATUKIO_ALREADY_BOOKED');

						if ($temp[0]->paid == 1)
						{
							$rows[$i]->fees = $rows[$i]->fees . " - " . JTEXT::_('COM_MATUKIO_PAID');
						}
					}

					$rows[$i]->codepic = "";
				}
				break;

			// My bookings ?!
			case "print_booking":
				$headertext = JTEXT::_('COM_MATUKIO_MY_BOOKINGS') . " - " . $my->name;

				if (in_array('SEM_CATEGORIES', $navioben) AND $catid > 0)
				{
					$where[] = "a.catid ='$catid'";
				}

				$where[] = "cc.userid = '" . $my->id . "'";

				if ($cid)
				{
					$where[] = "cc.semid = '" . $cid . "'";
					$headertext = JTEXT::_('COM_MATUKIO_BOOKING_CONFIRMATION') . " - " . $my->name;
				}

				$database->setQuery("SELECT a.*, r.*, cat.title AS category, cc.bookingdate AS bookingdate, cc.id AS bookid, cc.status AS bookingstatus
					FROM #__matukio_recurring AS r
					LEFT JOIN #__matukio AS a ON r.event_id = a.id
					LEFT JOIN #__matukio_bookings AS cc ON cc.semid = r.id
					LEFT JOIN #__categories AS cat ON cat.id = a.catid"
					. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
					. "\nAND (r.semnum LIKE'%$search%' OR a.teacher LIKE '%$search%' OR a.title LIKE '%$search%' OR a.shortdesc LIKE '%$search%'
                        OR a.description LIKE '%$search%')"
					. "\nORDER BY r.begin"
					. $ttlimit
				);
				$rows = $database->loadObjectList();
				$status = array();

				for ($i = 0, $n = count($rows); $i < $n; $i++)
				{
					$row = & $rows[$i];
					$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid = '$row->id' ORDER BY id");
					$temps = $database->loadObjectList();
					$status[$i] = MatukioHelperUtilsBooking::getBookingStatusName($row->bookingstatus);

					$rows[$i]->codepic = $row->bookid;

					if ($temps[0]->paid == 1)
					{
						$rows[$i]->fees = $rows[$i]->fees . " - " . JTEXT::_('COM_MATUKIO_PAID');
					}
				}
				break;

			// My events ?!
			case "print_myevents":
				if (in_array('SEM_CATEGORIES', $navioben) AND $catid > 0)
				{
					$where[] = "a.catid ='$catid'";
				}

				$where[] = "a.publisher = '" . $my->id . "'";

				$database->setQuery("SELECT a.*, r.* cat.title AS category FROM #__matukio_recurring AS r
			 	    LEFT JOIN #__matukio AS a ON r.eventid = a.id
					LEFT JOIN #__categories AS cat ON cat.id = a.catid"
					. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
					. "\nAND (r.semnum LIKE'%$search%' OR a.teacher LIKE '%$search%' OR a.title LIKE '%$search%' OR a.shortdesc LIKE '%$search%' OR a.description LIKE '%$search%')"
					. "\nORDER BY r.begin"
					. $ttlimit
				);

				$rows = $database->loadObjectList();
				$status = array();
				$headertext = JTEXT::_('COM_MATUKIO_MY_OFFERS') . " - " . $my->name;

				for ($i = 0, $n = count($rows); $i < $n; $i++)
				{
					$row = & $rows[$i];
					$status[$i] = MatukioHelperUtilsBooking::getBookingStatusName($row->bookingstatus);
					$rows[$i]->codepic = "";
				}
				break;

			case "print_teilnehmerliste":
				// TODO implement userchecking

				$art = JFactory::getApplication()->input->getInt('art', 0);
				$this->art = $art;

				if ($art == 1)
				{
					$this->setLayout("signaturelist");
				}
				else
				{
					$this->setLayout("participants");
				}
				break;


			case "csvlist":
				// TODO implement userchecking
				$art = JFactory::getApplication()->input->getInt('art', 0);
				$this->art = $art;
				$this->cid = $cid;

				$this->setLayout("csv");
				break;

			case "certificate":
				// TODO implement userchecking
				$art = JFactory::getApplication()->input->getInt('art', 0);
				$uid = JFactory::getApplication()->input->getInt('uid', 0);

				$this->art = $art;
				$this->uid = $uid;

				$this->setLayout("certificate");
				break;

			case "invoice":
				// TODO implement userchecking
				$art = JFactory::getApplication()->input->getInt('art', 0);
				$uid = JFactory::getApplication()->input->getInt('uid', 0);

				$this->art = $art;
				$this->uid = $uid;

				$this->setLayout("invoice");
				break;
		}

		$this->rows = $rows;
		$this->status = $status;
		$this->headertext = $headertext;

		parent::display($tpl);
	}
}
