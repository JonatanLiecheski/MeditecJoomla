<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       24.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * Class MatukioViewEventlist
 *
 * @since  1.0.0
 */
class MatukioViewEventlist extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  - The tmpl
	 *
	 * @return  mixed|object
	 */
	public function display($tpl = null)
	{
		$params = JComponentHelper::getParams('com_matukio');
		$menuitemid = JFactory::getApplication()->input->get('Itemid');

		if ($menuitemid)
		{
			$site = new JSite;
			$menu = $site->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			$params->merge($menuparams);
		}

		// Hardcode in Dirk's matukio-mvc.php task
		$art = JFactory::getApplication()->input->getInt('art', 0);

		$order_by = $params->get("orderby", "a.begin");

		$database = JFactory::getDBO();
		$dateid = JFactory::getApplication()->input->getInt('dateid', 1);
		$catid = JFactory::getApplication()->input->getInt('catid', 0);
		$uuid = JFactory::getApplication()->input->get('uuid', '', 'string');

		if (empty($catid))
		{
			$catid = $params->get('startcat', 0);
		}

		$search = JFactory::getApplication()->input->get('search', '', 'string');
		$search = str_replace("'", "", $search);
		$search = str_replace("\"", "", $search);

		$limit = JFactory::getApplication()->input->getInt('limit', MatukioHelperSettings::getSettings('event_showanzahl', 10));
		$limitstart = JFactory::getApplication()->input->getInt('limitstart', 0);
		$my = JFactory::getuser();
		$groups = implode(',', $my->getAuthorisedViewLevels());


		if ($art == 1)
		{
			if ($my->id == 0 && empty($uuid))
			{
				JError::raiseError("403", JTEXT::_('COM_MATUKIO_NOT_LOGGED_IN'));
			}
		}

		// Check if user is logged in and allowed to edit his OWN events
		if ($art == 2)
		{
			if (!JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
			{
				return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
			}
		}

		switch ($art)
		{
			default:
			case "0":
				$navioben = explode(" ", MatukioHelperSettings::getSettings('frontend_topnavshowmodules', 'SEM_NUMBER SEM_SEARCH SEM_CATEGORIES SEM_RESET'));
				break;
			case "1":
				$navioben = explode(" ", MatukioHelperSettings::getSettings('frontend_topnavbookingmodules', 'SEM_NUMBER SEM_SEARCH SEM_TYPES SEM_RESET'));
				break;
			case "2":
				$navioben = explode(" ", MatukioHelperSettings::getSettings('frontend_topnavoffermodules', 'SEM_NUMBER SEM_SEARCH SEM_TYPES SEM_RESET'));
				break;
		}

		// Old event form
		if ($this->getLayout() != "modern" || $my->id == 0)
		{
			$ret = MatukioHelperUtilsEvents::getEventList($art, $search, $dateid, $catid, $order_by, $my, $navioben, $limitstart, $limit, "old");

			$events = $ret[0];
			$total = $ret[1];

			switch ($art)
			{
				case "0":
					$anztyp = array(JTEXT::_('COM_MATUKIO_EVENTS'), 0);
					break;

				case "1":
					// Show booked events
					$anztyp = array(JTEXT::_('COM_MATUKIO_MY_BOOKINGS'), 1);
					break;

				case "2":
					// Show offered events
					$anztyp = array(JTEXT::_('COM_MATUKIO_MY_OFFERS'), 2);
					break;
			}

			$pageNav = MatukioHelperUtilsEvents::cleanSiteNavigation($total, $limit, $limitstart);

			$this->rows = $events;
			$this->pageNav = $pageNav;
		}

		// Modern Layout - merge that someday :/
		if ($this->getLayout() == "modern")
		{
			// Tabs
			if ($my->id > 0)
			{
				// Just set it to the default
				$anztyp = array(JTEXT::_('COM_MATUKIO_EVENTS'), 0);

				// Normal events view
				$ret = MatukioHelperUtilsEvents::getEventList(0, $search, $dateid, $catid, $order_by, $my, $navioben, $limitstart, $limit, "modern");

				$allEvents = $ret[0];
				$total = $ret[1];

				$this->pageNavAllEvents = MatukioHelperUtilsEvents::cleanSiteNavigation($total, $limit, $limitstart);
				$this->allEvents = $allEvents;

				// My Bookings
				$bookedEvents = MatukioHelperUtilsEvents::getEventList(1, $search, $dateid, $catid, $order_by, $my, $navioben, 0, 1000, "modern");
				$this->mybookedEvents = $bookedEvents[0];

				// My offers
				if (JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
				{
					$editEvents = MatukioHelperUtilsEvents::getEventList(2, $search, $dateid, $catid, $order_by, $my, $navioben, 0, 1000, "modern");
					$this->myofferEvents = $editEvents[0];
				}
			}
			else
			{
				// Not logged in user - we can take rows from above
				$this->allEvents = $events;
				$this->pageNavAllEvents = $pageNav;
			}
		}

		// Kursauswahl erstellen
		$allekurse = array();
		$allekurse[] = JHTML::_('select.option', '0', JTEXT::_('COM_MATUKIO_ALL_EVENTS'));
		$allekurse[] = JHTML::_('select.option', '1', JTEXT::_('COM_MATUKIO_CURRENT_EVENTS'));
		$allekurse[] = JHTML::_('select.option', '2', JTEXT::_('COM_MATUKIO_OLD_EVENTS'));

		$selectclass = ($this->getLayout() == "modern") ? "mat_inputbox" : "sem_inputbox22";

		$datelist = JHTML::_('select.genericlist', $allekurse, "dateid", "class=\"" . $selectclass . " chzn-single\" size=\"1\"
                onchange=\"changeStatus();\"", "value", "text", $dateid
		);

		$categories[] = JHTML::_('select.option', '0', JTEXT::_('COM_MATUKIO_ALL_CATS'));

		$database->setQuery("SELECT id AS value, title AS text FROM #__categories WHERE extension='"
			. JFactory::getApplication()->input->get('option') . "' AND access in (" . $groups . ") AND published = 1 ORDER BY lft");

		$categs = array_merge($categories, (array) $database->loadObjectList());

		$clist = JHTML::_('select.genericlist', $categs, "catid", "class=\"" . $selectclass . " chzn-single\" size=\"1\"
                onchange=\"changeCategoryEventlist();\" style=\"width: 180px;\"", "value", "text", $catid
		);

		$listen = array($datelist, $dateid, $clist, $catid);

		// Navigationspfad erweitern
		MatukioHelperUtilsBasic::expandPathway($anztyp[0], JRoute::_("index.php?option=com_matukio&view=eventlist"));

		$ue_title = $params->get('title', 'COM_MATUKIO_EVENTS_OVERVIEW');

		$this->art = $art;

		$this->search = $search;
		$this->limit = $limit;
		$this->limitstart = $limitstart;
		$this->total = $total;
		$this->datelist = $datelist;
		$this->dateid = $dateid;
		$this->clist = $clist;
		$this->catid = $catid;
		$this->title = $ue_title;
		$this->order_by = $order_by;

		parent::display($tpl);
	}
}
