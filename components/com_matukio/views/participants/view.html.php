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

jimport('joomla.application.component.view');

/**
 * Class MatukioViewParticipants
 *
 * @since  1.0.0
 */
class MatukioViewParticipants extends JViewLegacy
{
	/**
	 * Shows the form
	 *
	 * @param   string  $tpl  - The tmpl
	 *
	 * @return  bool|mixed|object
	 */
	public function display($tpl = null)
	{
		$art = JFactory::getApplication()->input->getInt('art', 0);
		$cid = JFactory::getApplication()->input->getInt('cid', 0);

		if (empty($cid))
		{
			JError::raiseError('404', "COM_MATUKIO_NO_ID");
		}

		$database = JFactory::getDBO();

		$dateid = JFactory::getApplication()->input->getInt('dateid', 1);
		$catid = JFactory::getApplication()->input->getInt('catid', 0);
		$search = JFactory::getApplication()->input->get('search', '');
		$limit = JFactory::getApplication()->input->getInt('limit', 5);
		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);

		$user = JFactory::getUser();

		// Load event (use model function)
		$emodel = JModelLegacy::getInstance('Event', 'MatukioModel');
		$kurs = $emodel->getItem($cid);

		if ($art == 0)
		{
			$anztyp = array(JTEXT::_('COM_MATUKIO_EVENTS'), 0);
		}
		elseif ($art == 1)
		{
			$anztyp = array(JTEXT::_('COM_MATUKIO_MY_BOOKINGS'), 1);
		}
		elseif ($art == 2)
		{
			$anztyp = array(JTEXT::_('COM_MATUKIO_MY_OFFERS'), 2);
		}
		elseif ($art == 3)
		{
			$anztyp = array(JTEXT::_('COM_MATUKIO_MY_OFFERS'), 3);
		}

		if ($art == 0)
		{
			if (!((MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 2 AND $user->id > 0)
				OR (MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 1)))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}

		if ($art == 1)
		{
			if (!((MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 2 AND $user->id > 0)
				OR (MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 1)))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}

		if ($art == 2)
		{
			if ($kurs->publisher == JFactory::getUser()->id)
			{
				if (!JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
				{
					return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
				}
			}
			else
			{
				if (!JFactory::getUser()->authorise('core.edit', 'com_matukio'))
				{
					return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
				}
			}
		}

		$fstatus = JFactory::getApplication()->input->get("filter_status", 'activeandpending');
		$status = "AND (a.status = '1' OR a.status = '0')";

		if (!empty($fstatus))
		{
			switch ($fstatus)
			{
				case "pending":
					// 0 is active in Seminar :(
					$status = "AND a.status = '0'";
					break;

				case "active":
					$status = "AND a.status = '1'";
					break;

				case 'activeandpending':
					$status = "AND (a.status = '1' OR a.status = '0')";
					break;

				case "waitlist":
					$status = "AND a.status = '2'";
					break;

				case "archived":
					$status = "AND a.status = '3'";
					break;

				case "deleted":
					$status = "AND a.status = '4'";
					break;

				case "paid":
					$status = "AND a.paid = '1'";
					break;

				case "unpaid":
					$status = "AND a.paid = '0'";
					break;

				case "all":
					$status = "";
					break;
			}
		}

		$this->filterStatus = $fstatus;

		$database->setQuery("SELECT a.*, cc.*, a.id AS sid, a.name AS aname, a.email AS aemail FROM #__matukio_bookings
                AS a LEFT JOIN #__users AS cc ON cc.id = a.userid WHERE a.semid = '" . $kurs->id . " '
                " . $status . " ORDER BY a.id");

		$rows = $database->loadObjectList();

		if ($database->getErrorNum())
		{
			echo $database->stderr();

			return false;
		}

		MatukioHelperUtilsBasic::expandPathway($anztyp[0], JRoute::_("index.php?option=com_matukio&art=" . $art));
		MatukioHelperUtilsBasic::expandPathway($kurs->title, "");

		$this->rows = $rows;
		$this->art = $art;
		$this->search = $search;
		$this->limit = $limit;
		$this->limitstart = $limitstart;
		$this->kurs = $kurs;
		$this->catid = $catid;
		$this->dateid = $dateid;

		parent::display($tpl);
	}
}
