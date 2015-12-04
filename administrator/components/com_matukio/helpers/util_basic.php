<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die();

/**
 * Class MatukioHelperUtilsBasic
 *
 * @since  2.0.0
 */
class MatukioHelperUtilsBasic
{
	private static $instance;

	/**
	 * Gets a formated Joomla version
	 *
	 * @return  string
	 */
	public static function getJoomlaVersion()
	{
		$version = new JVersion;
		$joomla = $version->getShortVersion();

		return (substr($joomla, 0, 3));
	}

	/**
	 * include the bootstrap css and js if necessary
	 *
	 * @param   boolean $frontend - Are we in the frontend
	 *
	 * @deprecated  Use compojoom library instead
	 * @return  void
	 */
	public static function bootstrap($frontend = false)
	{
		if (JVERSION < 3.0)
		{
			// Load our backport of bootstrap and jQuery
			JHTML::_('stylesheet', 'media/com_matukio/css/bootstrap.css');
			JHTML::_('stylesheet', 'media/com_matukio/css/bootstrap25.css');

			// Load jQuery first
			JHTML::_('script', 'media/com_matukio/js/jquery-1.11.0.min.js');

			// Load jQuery compat
			JHTML::_('script', 'media/com_matukio/js/jquery.noconflict.js');

			JHTML::_('script', 'media/com_matukio/js/radiobtns.js');

			JHTML::_('script', 'media/com_matukio/js/bootstrap.min.js');
			JHTML::_('script', 'media/com_matukio/js/bootstrap25.js');
		}
		else
		{
			// Be sure to load bootstrap in Joomla 3.x
			if ($frontend)
			{
				JHtml::_('bootstrap.framework');
			}
		}

		// Always load the strapper css
		JHTML::_('stylesheet', 'media/com_matukio/css/strapper.css');
	}

	/**
	 * Loads the jQuery validation
	 *
	 * @return  void
	 */
	public static function loadValidation()
	{
		// Load validation language file
		$lang = JFactory::getLanguage();

		// For example en-GB
		$langcode = explode("-", $lang->getTag());
		$langcode = $langcode[0];

		$file = JPATH_BASE . "/media/com_matukio/js/languages/jquery.validationEngine-" . $langcode . ".js";

		if (JFile::exists($file))
		{
			JHTML::_('script', 'media/com_matukio/js/languages/jquery.validationEngine-' . $langcode . '.js');
		}
		else
		{
			// Fallback to en
			JHTML::_('script', 'media/com_matukio/js/languages/jquery.validationEngine-en.js');
		}

		// Load script file
		JHTML::_('script', 'media/com_matukio/js/jquery.validationEngine.js');

		JHTML::_('stylesheet', 'media/com_matukio/css/validationEngine.jquery.css');
	}

	/**
	 * Gets the status image (used in backend and frontend)
	 *
	 * @param   object $event - The event
	 *
	 * @return  string
	 */
	public static function getStatusImage($event)
	{
		$curdate = MatukioHelperUtilsDate::getCurrentDate();

		$img = "2502.png";
		$imgalt = JTEXT::_('COM_MATUKIO_EVENT_HAS_NOT_STARTED_YET');


		if ($curdate > $event->end)
		{
			$img = "2500.png";
			$imgalt = JTEXT::_('COM_MATUKIO_EVENT_HAS_ENDED');
		}
		elseif ($curdate > $event->begin)
		{
			$img = "2501.png";
			$imgalt = JTEXT::_('COM_MATUKIO_EVENT_IS_RUNNING');
		}

		return "<img src=\"" . self::getComponentImagePath() . $img . "\" border=\"0\" alt=\"" . $imgalt . "\" title=\"" . $imgalt . "\" />";
	}


	/**
	 * Gets the available image
	 *
	 * @param   object $event  - The event
	 * @param   int    $booked - The Number of bookings
	 *
	 * @return  string
	 */
	public static function getAvailableImage($event, $booked)
	{
		$abild = "2502.png";
		$altabild = JTEXT::_('COM_MATUKIO_BOOKABLE');

		if ($event->maxpupil - $booked < 1 && $event->stopbooking == 1)
		{
			$abild = "2500.png";
			$altabild = JTEXT::_('COM_MATUKIO_FULLY_BOOKED');
		}
		elseif ($event->maxpupil - $booked < 1 && $event->stopbooking == 0)
		{
			$abild = "2501.png";
			$altabild = JTEXT::_('COM_MATUKIO_WAITLIST');
		}

		return "<img src=\"" . self::getComponentImagePath() . $abild . "\" border=\"0\" alt=\"" . $altabild . "\" title=\"" . $altabild . "\" />";
	}


	/**
	 * Gets the bookable image (used in backend and frontend)
	 *
	 * @param   object $event - The event
	 *
	 * @return  string
	 */
	public static function getBookableImage($event)
	{
		$curdate = MatukioHelperUtilsDate::getCurrentDate();

		$bbild = "2502.png";
		$altbbild = JTEXT::_('COM_MATUKIO_NOT_EXCEEDED');

		if ($curdate > $event->booked)
		{
			$bbild = "2500.png";
			$altbbild = JTEXT::_('COM_MATUKIO_EXCEEDED');
		}

		return "<img src=\"" . self::getComponentImagePath() . $bbild . "\" border=\"0\" alt=\"" . $altbbild . "\" title=\"" . $altbbild . "\" />";
	}

	/**
	 * Gets the rating image
	 *
	 * @param   object $event - The event
	 *
	 * @return string
	 */
	public static function getRatingImage($event)
	{
		return "<img src=\"" . self::getComponentImagePath() . "240" . $event->grade . ".png\" border=\"0\" alt=\""
		. JTEXT::_('COM_MATUKIO_RATING') . "\">";
	}

	/**
	 * Gets the publish / unpublish image
	 *
	 * @param   object $event - The event
	 * @param   int    $i     - The current pos
	 *
	 * @return string
	 */

	public static function getPublishedImage($event, $i)
	{
		$task = $event->published ? "unpublish" : "publish";
		$link = JRoute::_("index.php?option=com_matukio&controller=eventlist&task=" . $task . "&cid=" . $event->id);

		$img = $event->published ? "2201.png" : "2200.png";

		return "<a href=\"" . $link . "\" ><img src=\""
		. self::getComponentImagePath() . $img . "\" border=\"0\" alt=\"" . $task . "\" /></a>";
	}

	/**
	 * Gets the publish / unpublish image
	 *
	 * @param   object $rec - The recurring event
	 * @param   int    $i   - The current pos
	 *
	 * @return string
	 */

	public static function getPublishedImageRecurring($rec, $i)
	{
		$task = $rec->published ? "unpublish" : "publish";
		$link = JRoute::_("index.php?option=com_matukio&controller=recurring&task=" . $task . "&cid=" . $rec->id);

		$img = $rec->published ? "2201.png" : "2200.png";

		return "<a href=\"" . $link . "\" ><img src=\""
		. self::getComponentImagePath() . $img . "\" border=\"0\" alt=\"" . $task . "\" /></a>";
	}

	/**
	 * Gets the cancel / uncancel image
	 *
	 * @param   object $event - The event
	 * @param   int    $i     - The current pos
	 *
	 * @return string
	 */
	public static function getCancelImage($event, $i)
	{
		$task = $event->cancelled ? "uncancelEvent" : "cancelEvent";

		// $task = $event->cancelled ? "25" : "24";
		$img = $event->cancelled ? "2201.png" : "2200.png";

		$link = JRoute::_("index.php?option=com_matukio&controller=eventlist&task=" . $task . "&cid=" . $event->id);

		return "<a href=\"" . $link . "\"><img src=\""
		. self::getComponentImagePath() . $img . "\" border=\"0\" alt=\"" . $task . "\" /></a>";
	}

	/**
	 * Gets the cancel / uncancel image
	 *
	 * @param   object $rec - The recurring event
	 * @param   int    $i   - The current pos
	 *
	 * @return string
	 */
	public static function getCancelImageRecurring($rec, $i)
	{
		$task = $rec->cancelled ? "uncancelRecurring" : "cancelRecurring";

		// $task = $event->cancelled ? "25" : "24";
		$img = $rec->cancelled ? "2201.png" : "2200.png";

		$link = JRoute::_("index.php?option=com_matukio&controller=recurring&task=" . $task . "&cid=" . $rec->id);

		return "<a href=\"" . $link . "\"><img src=\""
		. self::getComponentImagePath() . $img . "\" border=\"0\" alt=\"" . $task . "\" /></a>";
	}


	/**
	 * Gets the status img for the booking
	 *
	 * @param   object $booking - The booking
	 *
	 * @return  string
	 */
	public static function getBStatusImage($booking)
	{
		if ($booking->status == MatukioHelperUtilsBooking::$PENDING)
		{
			$bild = "pending.png";
			$altbild = JTEXT::_('COM_MATUKIO_PENDING');
		}
		elseif ($booking->status == MatukioHelperUtilsBooking::$ACTIVE)
		{
			$bild = "2502.png";
			$altbild = JTEXT::_('COM_MATUKIO_PARTICIPANT_ASSURED');
		}
		elseif ($booking->status == MatukioHelperUtilsBooking::$WAITLIST)
		{
			$bild = "2501.png";
			$altbild = JTEXT::_('COM_MATUKIO_WAITLIST');
		}
		else
		{
			$bild = "2500.png";
			$altbild = JTEXT::_('COM_MATUKIO_NO_SPACE_AVAILABLE');
		}

		return "<img src=\"" . self::getComponentImagePath() . $bild . "\" border=\"0\" title=\"" . $altbild . "\" />";
	}

	/**
	 * Gets the paid object
	 *
	 * @param   object $booking - The booking
	 * @param   object $event   - The event
	 *
	 * @return  string
	 */
	public static function getPaidStatus($booking, $event = null)
	{
		if ($event != null && $event->fees <= 0)
		{
			return "";
		}

		if ($event == null && $booking->eventfees == 0)
		{
			return "";
		}

		$task = $booking->paid ? "unpaid" : "paid";

		$paidbild = "2200.png";
		$paidtitel = JTEXT::_('COM_MATUKIO_MARK_AS_PAID');

		if ($booking->paid == 1)
		{
			$paidbild = "2201.png";
			$paidtitel = JTEXT::_('COM_MATUKIO_MARK_AS_NOT_PAID');
		}

		return "<a title=\"" . $paidtitel . "\" href=\"index.php?option=com_matukio&controller=bookings&task="
		. $task . "&booking_id=" . $booking->sid . "&event_id=" . $booking->semid
		. "\"><img src=\"" . self::getComponentImagePath() . $paidbild . "\" border=\"0\" alt=\""
		. JTEXT::_('COM_MATUKIO_PAID') . "\" /></a>";
	}

	/**
	 * Gets the certificate image
	 *
	 * @param   object $booking - The booking
	 *
	 * @return  string
	 */

	public static function getCertificateImage($booking)
	{
		$task = $booking->certificated ? "uncertificate" : "certificate";

		$certbild = "2200.png";
		$certtemp = "";
		$certtitel = JTEXT::_('COM_MATUKIO_CERTIFICATE');

		if ($booking->certificated == 1)
		{
			$certbild = "2201.png";
			$certtemp = " " . MatukioHelperUtilsAdmin::getBackendPrintWindow(3, $booking->semid, $booking->sid);
			$certtitel = JTEXT::_('COM_MATUKIO_WITHDREW_CERTIFICATE');
		}

		return "<a title=\"" . $certtitel . "\" href=\"index.php?option=com_matukio&controller=bookings&task="
		. $task . "&cid=" . $booking->sid . "&event_id=" . $booking->semid
		. "\"><img src=\"" . self::getComponentImagePath() . $certbild . "\" border=\"0\" alt=\""
		. JTEXT::_('COM_MATUKIO_CERTIFICATES') . "\" /></a>" . $certtemp;
	}

	/**
	 * Gets the rating image
	 *
	 * @param   object $booking - The booking
	 *
	 * @return  string
	 */
	public static function getBRatingImage($booking)
	{
		return "<img src=\"" . self::getComponentImagePath() . "240" . $booking->grade . ".png\" border=\"0\" alt=\""
		. JTEXT::_('COM_MATUKIO_RATING') . "\" />";
	}

	/**
	 * Gets the invoice image
	 *
	 * @param   object  $booking - The booking
	 * @param   object  $event   - The event (opt)
	 *
	 * @return  string
	 */

	public static function getInvoiceImage($booking, $event = null)
	{
		if ($event != null && $event->fees <= 0)
		{
			return "";
		}

		if ($event == null && $booking->eventfees == 0)
		{
			return "";
		}

		$invoice_link = MatukioHelperInvoice::getInvoiceNumber($booking->sid, JHTML::_('date', $booking->bookingdate, 'Y')) . " " . MatukioHelperUtilsAdmin::getBackendPrintWindow(6, $booking->semid, $booking->sid);

		return $invoice_link;
	}

	// TODO: REMOVE.
	public static function getUserType($user)
	{
		$userid = $user->get('id');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('g.title AS group_name')
			->from('#__usergroups AS g')
			->leftJoin('#__user_usergroup_map AS map ON map.group_id = g.id')
			->where('map.user_id = ' . (int) $userid);
		$db->setQuery($query);
		$ugp = $db->loadObject();

		return $ugp->group_name;
	}

	// TODO: REMOVE.
	public static function getUserTypeID($user)
	{
		if ($user->get('id') == '')
			return -1;

		$userid = $user->get('id');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('g.id AS id')
			->from('#__usergroups AS g')
			->leftJoin('#__user_usergroup_map AS map ON map.group_id = g.id')
			->where('map.user_id = ' . (int) $userid);
		$db->setQuery($query);
		$ugp = $db->loadObject();
		return $ugp->id;
	}

	/**
	 * Returns the userTime zone if the user has set one, or the global config one
	 * @return mixed
	 */
	public static function getTimeZone()
	{
		$userTz = JFactory::getUser()->getParam('timezone');
		$timeZone = JFactory::getConfig()->get('offset');
		if ($userTz)
		{
			$timeZone = $userTz;
		}
		return new DateTimeZone($timeZone);
	}

	public static function getExtensionVersion()
	{
		return MATUKIO_VERSION;
	}

	/**
	 * sem_f004()
	 * @return mixed
	 */

	public static function getSitePath()
	{
		return JURI::ROOT();
	}

	// ++++++++++++++++++++++++++++++++++++++
	// +++ Komponentenverzeichnis ausgeben ++  sem_f005()
	// ++++++++++++++++++++++++++++++++++++++

	public static function getComponentPath()
	{
		return self::getSitePath() . "components/" . JFactory::getApplication()->input->get('option') . "/";
	}


	// ++++++++++++++++++++++++++++++++++++++
	// +++ Bildverzeichnis 1 ausgeben     +++        sem_f006
	// ++++++++++++++++++++++++++++++++++++++

	public static function getComponentImagePath()
	{
		return self::getSitePath() . "media/com_matukio/images/";
	}

	// ++++++++++++++++++++++++++++++++++++++
	// +++ Bildverzeichnis 2 ausgeben     +++        sem_f007
	// ++++++++++++++++++++++++++++++++++++++

	public static function getEventImagePath($art)
	{
		$htxt = "";
		if (MatukioHelperSettings::getSettings('image_path', "") != "" AND $art > 0)
		{
			$htxt = trim(MatukioHelperSettings::getSettings('image_path', ""), "/") . "/";
		}
		return self::getSitePath() . "images/" . $htxt;
	}

	// ++++++++++++++++++++++++++++++++++++++
	// +++ Benutzerlevel festlegen        +++
	// ++++++++++++++++++++++++++++++++++++++

	/**
	 * TODO Fix this
	 * @deprecated  Moved to ACL
	 * @return int|string
	 */
	public static function getUserLevel()
	{
		// Public
		$reglevel = 0;
		$my = JFactory::getuser();

		// Zugriffslevel festlegen

		if (JVERSION >= 3)
		{
			$utype = 0;
		}
		else
		{
			$utype = strtolower($my->usertype);
		}

		// > Joomla 1.5
		$utype = self::getUserTypeID($my);

		$reglevel = $utype;

		if ($utype == -1)
		{
			$reglevel = 0;

			if (MatukioHelperSettings::getSettings('booking_unregistered', 1) == 1)
			{
				$reglevel = 1;
			}
		}

		return $reglevel;
	}


	// ++++++++++++++++++++++++++++++++++++++
	// +++ Auf Benutzerlevel testen       +++ sem_f043($temp)
	// ++++++++++++++++++++++++++++++++++++++

	/**
	 * Checks the user level !! 1.5 / SEMINAR - DEPRECATED
	 *
	 * @param   int $temp - The level
	 *
	 * @deprecated  should be removed!!
	 *
	 * @return  void
	 */
	public static function checkUserLevel($temp)
	{
		$reglevel = self::getUserLevel();

		if ($reglevel < $temp)
		{
			JError::raiseError(403, JText::_("ALERTNOTAUTH"));
			exit;
		}

		if ($temp == 0)
		{
			JError::raiseError(403, JText::_("ALERTNOTAUTH"));
			exit;
		}
	}


	// ++++++++++++++++++++++++++++++++++
	// +++ Pathway erweitern                sem_f019
	// ++++++++++++++++++++++++++++++++++

	public static function expandPathway($text, $link)
	{
		$mainframe = JFactory::getApplication();
		$pathway = $mainframe->getPathWay();
		$pathway->addItem($text, $link);
	}

	// ++++++++++++++++++++++++++++++++++++++
	// +++ Formularstart ausgeben          sem_f026
	// ++++++++++++++++++++++++++++++++++++++

	public static function printFormstart($art)
	{
		$htxt = "FrontForm";
		if ($art == 2 OR $art == 4)
		{
			$htxt = "adminForm";
		}
		$type = "";
		if ($art > 2)
		{
			$type = " enctype=\"multipart/form-data\"";
		}
		echo "<form action=\"" . JRoute::_("index.php?option=com_matukio") . "\" method=\"post\" name=\"" . $htxt . "\" id=\"" . $htxt . "\"" . $type . ">";
	}

	/**
	 * sem_f036 Zufälliges Zeichen
	 *
	 * @static
	 * @return string
	 */

	public static function getRandomChar()
	{
		$zufall = "";
		for ($i = 0; $i <= 200; $i++)
		{
			$gkl = rand(1, 3);
			if ($gkl == 1)
			{
				$zufall .= chr(rand(97, 121));
			}
			else if ($gkl == 0)
			{
				$zufall .= chr(rand(65, 90));
			}
			else
			{
				$zufall .= rand(0, 9);
			}
		}
		return $zufall;
	}

	public static function loginUser()
	{
		$mainframe = JFactory::getApplication();
		$username = JFactory::getApplication()->input->get('semusername', JTEXT::_('USERNAME'), 'string');
		$password = JFactory::getApplication()->input->get('sempassword', JTEXT::_('PASSWORD'), 'string');
		if ($username != JTEXT::_('USERNAME'))
		{
			$data['username'] = $username;
			$data['password'] = $password;
			$option['remember'] = true;
			$option['silent'] = true;
			$mainframe->login($data, $option);
		}
	}

	/**
	 * Get booked user list (sem_f011)
	 *
	 * @param   object $row - The event
	 *
	 * @return  bool|mixed|string
	 */
	public static function getBookedUserList($row)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT userid AS id FROM #__matukio_bookings WHERE semid = '$row->id'");
		$users = $database->loadObjectList();

		if ($database->getErrorNum())
		{
			echo $database->stderr();

			return false;
		}

		if ((count($users) >= $row->maxpupil) AND ($row->stopbooking > 0))
		{
			$blist = "";
		}
		else
		{
			$userout = array();

			if (MatukioHelperSettings::getSettings('booking_ownevents', 1) == 0)
			{
				$userout[] = $row->publisher;
			}

			foreach ($users as $user)
			{
				$userout[] = $user->id;
			}

			$where = "";

			if (count($userout) > 0)
			{
				$userout = implode(',', $userout);
				$where = "\nWHERE id NOT IN ($userout)";
			}

			$database->setQuery("SELECT id AS value, name AS text FROM #__users"
				. $where
				. "\nORDER BY name"
			);

			$benutzer = $database->loadObjectList();

			if (count($benutzer))
			{
				$benutzer = array_merge($benutzer);
				$blist = JHTML::_('select.genericlist', $benutzer, 'uid', 'class="sem_inputbox" size="1"', 'value', 'text', '');
			}
			else
			{
				$blist = "";
			}
		}

		return $blist;
	}

	/**
	 * Generate a toltip (sem_f055)
	 *
	 * @param   string $text - The tooltip text can be separated into title / text with |
	 *
	 * @return  string
	 */
	public static function createToolTip($text)
	{
		$html = "";

		if ($text != "")
		{
			$text = explode("|", $text);

			if (count($text) > 1)
			{
				$hinttext = $text[0] . "::" . $text[1];
			}
			else
			{
				$hinttext = JTEXT::_('COM_MATUKIO_FIELD_TIP') . "::" . $text[0];
			}

			$html = " <span class=\"editlinktip hasTip\" title=\"" . $hinttext . "\" style=\"text-decoration: none;cursor: help;\"><img src=\""
				. self::getComponentImagePath() . "0012.png\" border=\"0\" style=\"vertical-align: absmiddle;\"/></span>";
		}

		return $html;
	}


	// ++++++++++++++++++++++++++++++++++++++
// +++ Ausgabe parsen                 +++      sem_f065
// ++++++++++++++++++++++++++++++++++++++

	public static function parseOutput($text, $status)
	{
		preg_match_all("`\[" . $status . "\](.*)\[/" . $status . "\]`U", $text, $ausgabe);

		for ($i = 0; $i < count($ausgabe[0]); $i++)
		{
			$text = str_replace($ausgabe[0][$i], $ausgabe[1][$i], $text);
		}

		preg_match_all("`\[sem_[^\]]+\](.*)\[/sem_[^\]]+\]`U", $text, $ausgabe);
		for ($i = 0; $i < count($ausgabe[0]); $i++)
		{
			$text = str_replace($ausgabe[0][$i], "", $text);
		}

		return $text;
	}


	// ++++++++++++++++++++++++++++++++++++++
// +++ Fensterstatus loeschen                    sem_f025
// ++++++++++++++++++++++++++++++++++++++

	public static function getMouseOverWindowStatus($status)
	{
		return "onmouseover=\"window.status='" . $status . "';return true;\" onmouseout=\"window.status='';return true;\"";
	}

	/**
	 * Removes HTML from a string... Use JInput Filter! (sem_f018)
	 *
	 * @param   string $text - The text
	 *
	 * @deprecated  WTF for that we have Joomla filters..
	 *
	 * @return mixed
	 */
	public static function cleanHTMLfromText($text)
	{
		$text = preg_replace("'<script[^>]*>.*?</script>'si", '', $text);
		$text = preg_replace('/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2 (\1)', $text);
		$text = preg_replace('/<!--.+?-->/', '', $text);
		$text = preg_replace('/{.+?}/', '', $text);
		$text = preg_replace('/&nbsp;/', ' ', $text);
		$text = preg_replace('/&amp;/', ' ', $text);
		$text = str_replace("'", "'", $text);
		$text = str_replace('\"', '"', $text);
		$text = strip_tags($text);

		return $text;
	}


	/**
	 * HTML Heaer        sem_f031
	 * @return string
	 */
	public static function getHTMLHeader()
	{
		$lang = JFactory::getLanguage();
		$html = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">";
		$html .= "\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"" . $lang->getName() . "\" lang=\"" . $lang->getName() . "\" >";
		$html .= "\n<head>";
		$html .= "\n<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />";
		$html .= "\n</head>";

		return $html;
	}

	/**
	 * Gets the powered by
	 *
	 * @param   bool $frontend - Are we in the frontend?
	 *
	 * @return  string
	 */
	public static function getCopyright($frontend = true)
	{
		$html = "";

		// Always show in the backend
		if (MatukioHelperSettings::getSettings('frontend_showfooter', 1) == 1 || $frontend == false)
		{
			$html = "<div id=\"copyright_box\" align=\"center\" style=\"margin-top: 20px;\">
           <a href=\"https://compojoom.com/joomla-extensions/matukio-events-management-made-easy\" target=\"_new\">Matukio</a> - Events for <a href=\"http://joomla.org\">Joomla!™</a>
           by <a href=\"https://compojoom.com\" target=\"_new\">compojoom.com</a>
           </div>";
		}

		return $html;
	}

	/**
	 * Generates the menu
	 *
	 * @return  array
	 */
	public static function getMenu()
	{
		$menu = array(
/*			'dashboard' => array(
				'link' => '', 'title' => 'COM_MATUKIO_DASHBOARD', 'icon' => 'fa-dashboard', 'anchor' => '', 'children' => array(), 'label' => '',
				'keywords' => 'dashboard home overview'
			),*/

			'events' => array(
				'link' => '#', 'title' => 'COM_MATUKIO_MENU_EVENTS', 'icon' => 'fa-briefcase', 'anchor' => '',
				'children' => array(
					'eventlist' => array(
						'link' => '', 'title' => 'COM_MATUKIO_EVENTS', 'icon' => 'fa-briefcase', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'events eventlist seminar workshops'
					),

					'recurring' => array(
						'link' => '', 'title' => 'COM_MATUKIO_RECURRING_DATES', 'icon' => 'fa-calendar', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'dates'
					),

					'bookings' => array(
						'link' => '', 'title' => 'COM_MATUKIO_BOOKINGS', 'icon' => 'fa-ticket', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'bookings user participants'
					),

					'categories' => array(
						'link' => 'index.php?option=com_categories&extension=com_matukio', 'title' => 'COM_MATUKIO_CATEGORIES',
						'icon' => 'fa-tags', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'category categories'
					),

					'locations' => array(
						'link' => '', 'title' => 'COM_MATUKIO_LOCATIONS', 'icon' => 'fa-location-arrow', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'locations places'
					),

					'organizers' => array(
						'link' => '', 'title' => 'COM_MATUKIO_ORGANIZERS', 'icon' => 'fa-users', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'organizers manager tutor'
					),
				),
				'label' => '', 'keywords' => 'events eventlist seminar workshops'
			),

			'payment' => array(
				'link' => '#', 'title' => 'COM_MATUKIO_MENU_PAYMENT', 'icon' => 'fa-money', 'anchor' => '',
				'children' => array(
					'coupons' => array(
						'link' => '', 'title' => 'COM_MATUKIO_COUPONS', 'icon' => 'fa-gift', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'coupons gift'
					),
					'differentfees' => array(
						'link' => '', 'title' => 'COM_MATUKIO_DIFFERENTFEES', 'icon' => 'fa-money', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'different fees price'
					),
					'taxes' => array(
						'link' => '', 'title' => 'COM_MATUKIO_TAXRATES', 'icon' => 'fa-gavel', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'taxes'
					),
				),
				'label' => '', 'keywords' => 'differentfees price payment'
			),

			'import' => array(
				'link' => '', 'title' => 'COM_MATUKIO_IMPORT', 'icon' => 'fa-download', 'anchor' => '', 'children' => array(), 'label' => '',
				'keywords' => 'import seminar ics'
			),

			'statistics' => array(
				'link' => '', 'title' => 'COM_MATUKIO_STATISTICS', 'icon' => ' fa-bar-chart-o', 'anchor' => '', 'children' => array(), 'label' => '',
				'keywords' => 'statistics'
			),

			'configuration' => array(
				'link' => '#', 'title' => 'COM_MATUKIO_MENU_CONFIG', 'icon' => 'fa-wrench', 'anchor' => '',
				'children' => array(
					'settings' => array(
						'link' => '', 'title' => 'COM_MATUKIO_CONFIGURATION', 'icon' => 'fa-flask', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'settings configuration parameters'
					),
					'bookingfields' => array(
						'link' => '', 'title' => 'COM_MATUKIO_BOOKINGFIELDS', 'icon' => 'fa-list-ol', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'bookingfields bookingform'
					),
					'templates' => array(
						'link' => '', 'title' => 'COM_MATUKIO_TEMPLATES', 'icon' => 'fa-code', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'taxes'
					),

					// index.php?option=com_plugins&view=plugins&filter_folder=payment

					'paymentplugins' => array(
						'link' => 'index.php?option=com_plugins&view=plugins&filter_folder=payment', 'title' => 'COM_MATUKIO_PAYMENT_PLUGINS',
						'icon' => 'fa-money', 'anchor' => '', 'children' => array(), 'label' => '',
						'keywords' => 'payment plugins'
					),
				),
				'label' => '', 'keywords' => 'settings configuration parameters'
			),

			'liveupdate' => array(
				'link' => '', 'title' => 'COM_MATUKIO_LIVEUPDATE', 'icon' => 'fa-cloud-download', 'anchor' => '', 'children' => array(), 'label' => '',
				'keywords' => 'liveupdate'
			),

			'information' => array(
				'link' => '', 'title' => 'COM_MATUKIO_INFORMATIONS', 'icon' => ' fa-info-circle', 'anchor' => '', 'children' => array(), 'label' => '',
				'keywords' => 'informations version'
			),
		);

		return $menu;
	}
}
