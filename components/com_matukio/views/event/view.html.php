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
 * Class MatukioViewEvent
 *
 * @since  2.0.0
 */
class MatukioViewEvent extends JViewLegacy
{
	/**
	 * Displays the event form
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @throws  Exception
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;
		$model = $this->getModel();

		$art = $input->getInt('art', 0);

		$database = JFactory::getDBO();
		$dateid = $input->getInt('dateid', 1);

		// Event id
		$cid = $input->getInt('id', 0);

		// Booking id!!
		$uid = $input->getInt('uid', 0);
		$uuid = $input->get('uuid', '');

		$dispatcher = JDispatcher::getInstance();

		$booking = "";

		$user = JFactory::getUser();

		if ($art == 1)
		{
			// Updated 2.2.4 to show the cancel booking button if logged in!
			$booking = MatukioHelperUtilsBooking::getBooking($uid, $cid);
		}

		// Fix if uid / booking not found or booking as not registered user
		if (empty($booking))
		{
			$art = 0;
		}

		// Category id
		$catid = $input->getInt('catid', 0);
		$search = $input->get('search', '', 'string');
		$limit = $input->getInt('limit', 5);

		// TODO Pagination should be updated to Joomla Framework
		$limitstart = $input->getInt('limitstart', 0);

		$params = JComponentHelper::getParams('com_matukio');
		$menuitemid = $input->get('Itemid');

		if ($menuitemid)
		{
			$site = new JSite;
			$menu = $site->getMenu();
			$menuparams = $menu->getParams($menuitemid);
			$params->merge($menuparams);
		}

		$menu_cid = $params->get('eventId', 0);

		if (empty($cid))
		{
			if (empty($menu_cid))
			{
				JError::raiseError('404', JTEXT::_("COM_MATUKIO_NO_ID"));
			}
			else
			{
				$cid = $menu_cid;
			}
		}

		$row = $model->getItem($cid, true);

		if ($art == 3)
		{
			if ($uid > 0)
			{
				$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id='" . $uid . "'");
				$temp = $database->loadObjectList();
				$userid = $temp[0]->userid;

				if ($userid == 0)
				{
					$uid = $uid * -1;
				}
				else
				{
					$uid = $userid;
				}
			}
		}
		else
		{
			if ($uid > 0)
			{
				$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id='$uid'");
				$temp = $database->loadObjectList();

				if ($temp[0]->userid != 0 || $art != 1)
				{
					$uid = $temp[0]->userid;
				}
				else
				{
					$uid = $uid * -1;
				}
			}
		}

		if ($art == 0)
		{
			// Hits erhoehen
			$database->setQuery("UPDATE #__matukio_recurring SET hits=hits+1 WHERE id='$cid'");

			if (!$database->execute())
			{
				throw new Exception("COM_MATUKIO_ERROR_ADDING_HIT" . ":" . $row->getError());
			}

			// Ausgabe des Kurses
			// MatukioHelperUtilsBasic::expandPathway(JTEXT::_('COM_MATUKIO_EVENTS'), JRoute::_("index.php?option=com_matukio"));
		}
		elseif ($art == 1 OR $art == 2)
		{
			if ($user->id > 0)
			{
				MatukioHelperUtilsBasic::expandPathway(JTEXT::_('COM_MATUKIO_MY_BOOKINGS'), JRoute::_("index.php?option=com_matukio&view=eventlist&art=1"));
			}
		}
		else
		{
			MatukioHelperUtilsBasic::expandPathway(JTEXT::_('COM_MATUKIO_MY_OFFERS'), JRoute::_("index.php?option=com_matukio&view=eventlist&art=2"));
		}

		// Add category link to breadcrumb
		MatukioHelperUtilsBasic::expandPathway(
			$row->category, JRoute::_("index.php?option=com_matukio&view=eventlist&art=" . $art
				. "&catid=" . $row->catid . ":" . JFilterOutput::stringURLSafe($row->category)
			)
		);

		// Add event to breadcrumb
		MatukioHelperUtilsBasic::expandPathway($row->title, "");

		$ueberschrift = array(JTEXT::_('COM_MATUKIO_DESCRIPTION'), $row->shortdesc);

		if (empty($row))
		{
			JError::raiseError('404', JTEXT::_("COM_MATUKIO_NO_ID"));

			return;
		}

		$locobj = null;

		if ($row->place_id > 0)
		{
			$locobj = MatukioHelperUtilsEvents::getLocation($row->place_id);
		}

		$title = JFactory::getDocument()->getTitle();
		JFactory::getDocument()->setTitle($title . " - " . JText::_($row->title));

		JPluginHelper::importPlugin('content');
		$this->jevent = new stdClass;
		$results = $dispatcher->trigger('onContentAfterDisplay', array('com_matukio.event', &$row, &$params, 0));
		$this->jevent->afterDisplayContent = trim(implode("\n", $results));

		$this->id = $cid;
		$this->art = $art;
		$this->event = $row;
		$this->uid = $uid;
		$this->uuid = $uuid;
		$this->search = $search;
		$this->catid = $catid;
		$this->limit = $limit;
		$this->limitstart = $limitstart;
		$this->dateid = $dateid;
		$this->ueberschrift = $ueberschrift;
		$this->booking = $booking;
		$this->user = $user;
		$this->location = $locobj;

		parent::display($tpl);
	}
}
