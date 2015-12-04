<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       21.04.2012
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die ('Restricted access');

/**
 * Class MatukioHelperUtilsEvents
 *
 * @todo cleanup this mess.. (i am so sorry)
 *
 * @since  1.0.0
 */
class MatukioHelperUtilsEvents
{
	/**
	 * This is a static helper class (should not be initialised)
	 *
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Creates a new event number depending on the given year sem_f064
	 *
	 * @param   int  $newyear  - Year e.g. 2013
	 *
	 * @throws  exception  - if the database queries fail
	 * @return  string
	 */

	public static function createNewEventNumber($newyear)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("*")->from("#__matukio_number")->where("year = " . $newyear);

		$db->setQuery($query);

		$temp = $db->loadObjectList();

		if (count($temp) == 0)
		{
			$neu = JTable::getInstance("Number", "Table");

			if (!$neu->bind(JRequest::get('post')))
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			$neu->year = $newyear;
			$neu->number = "1";

			if (!$neu->store())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			$neu->checkin();
		}
		else
		{
			$db->setQuery("UPDATE #__matukio_number SET number = number+1 WHERE year = '$newyear'");

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}
		}

		$query = $db->getQuery(true);
		$query->select("*")->from("#__matukio_number")->where("year = " . $newyear);

		$db->setQuery($query);
		$zaehlers = $db->loadObjectList();
		$zaehler = $zaehlers[0];

		return $zaehler->number . "/" . substr($newyear, 2);
	}


	/**
	 * Formats the given money value for the regional output
	 * sem_f044
	 *
	 * @param   double  $value     - The value to format
	 * @param   string  $currency  - The opt currency.. if included it will be added to the value
	 *
	 * @return  string
	 */
	public static function getFormatedCurrency($value, $currency = "")
	{
		if (empty($currency))
		{
			return number_format(
				$value, MatukioHelperSettings::getSettings('dezimal_stellen', 2), MatukioHelperSettings::getSettings('dezimal_trennzeichen', '.'), ' '
			);
		}
		else
		{
			$val = number_format(
				$value, MatukioHelperSettings::getSettings('dezimal_stellen', 2), MatukioHelperSettings::getSettings('dezimal_trennzeichen', '.'), ' '
			);

			if ($currency == "$" || $currency == "R")
			{
				return $currency . " " . $val;
			}
			else
			{
				return $val . " " . $currency;
			}
		}
	}

	/**
	 * Cleans the site navigation.. sem_f039
	 *
	 * @param   int  $total       - The total items
	 * @param   int  $limit       - The limit
	 * @param   int  $limitstart  - The limit start
	 *
	 * @deprecated 2.2 should be replaced in code
	 *
	 * @return  string
	 */
	public static function cleanSiteNavigation($total, $limit, $limitstart)
	{
		$pagenav = array();
		$navi = "";
		$pageone = 1;
		$seiten = 1;
		$kurse = "";

		if ($limit > 0)
		{
			$pageone = $limitstart / $limit + 1;
			$seiten = ceil($total / $limit);

			if ($pageone > 1)
			{
				$navi .= "<a class=\"sem_tab\" href=\"javascript:document.FrontForm.limitstart.value='0';document.FrontForm.submit();\">"
					. JTEXT::_('COM_MATUKIO_START') . "</a>";
				$navi .= " - <a class=\"sem_tab\" href=\"javascript:document.FrontForm.limitstart.value='"
					. ($limitstart - $limit) . "';document.FrontForm.submit();\">" . JTEXT::_('COM_MATUKIO_PREV') . "</a>";
			}
			else
			{
				$navi .= JTEXT::_('COM_MATUKIO_START');
				$navi .= " - " . JTEXT::_('COM_MATUKIO_PREV');
			}

			$start = 0;
			$ende = $seiten;
			$navi .= " -";

			if ($seiten > 5)
			{
				if ($pageone > 3)
				{
					$navi .= " ...";

					if ($seiten - 2 >= $pageone)
					{
						$start = $pageone - 3;
						$ende = $pageone + 2;
					}
					else
					{
						$start = $seiten - 5;
						$ende = $seiten;
					}
				}
				else
				{
					$ende = 5;
				}
			}

			for ($i = $start; $i < $ende; $i++)
			{
				if ($i * $limit != $limitstart)
				{
					$navi .= " <a class=\"sem_tab\" href=\"javascript:document.FrontForm.limitstart.value='"
						. ($i * $limit) . "';document.FrontForm.submit();\">" . ($i + 1) . "</a>";
				}
				else
				{
					$navi .= " " . ($i + 1);
					$kurs1 = (($i * $limit) + 1);
					$kurs2 = (($i + 1) * $limit);

					if ($kurs2 > $total)
					{
						$kurs2 = $total;
					}

					if ($kurs1 == $kurs2)
					{
						$kurse = $kurs2 . "/" . $total;
					}
					else
					{
						$kurse = $kurs1 . "-" . $kurs2 . "/" . $total;
					}
				}
			}

			if ($seiten > 5)
			{
				if ($pageone + 2 < $seiten)
				{
					$navi .= " ...";
				}
			}

			$navi .= " -";

			if ($pageone < $seiten)
			{
				$navi .= " <a class=\"sem_tab\" href=\"javascript:document.FrontForm.limitstart.value='"
					. ($limitstart + $limit) . "';document.FrontForm.submit();\">" . JTEXT::_('COM_MATUKIO_NEXT') . "</a>";
				$navi .= " - <a class=\"sem_tab\" href=\"javascript:document.FrontForm.limitstart.value='"
					. ($seiten * $limit) . "';document.FrontForm.submit();\">" . JTEXT::_('COM_MATUKIO_END') . "</a>";
			}
			else
			{
				$navi .= " " . JTEXT::_('COM_MATUKIO_NEXT');
				$navi .= " - " . JTEXT::_('COM_MATUKIO_END');
			}
		}

		$seite = JTEXT::_('COM_MATUKIO_PAGE') . "&nbsp;" . $pageone . "/" . ($seiten);

		return "\n" . self::getTableHeader(4) . "<tr>" . self::getTableCell($seite, 'd', 'l', '', 'sem_nav')
		. self::getTableCell($navi, 'd', 'c', '', 'sem_nav')
		. self::getTableCell($kurse, 'd', 'r', '', 'sem_nav') . "</tr>" . self::getTableHeader('e');
	}

	/**
	 * Returns a table heading
	 * sem_f023
	 *
	 * @deprecated 2.2 should be written in code
	 *
	 * @return string
	 */
	public static function getTableHeader()
	{
		$args = func_get_args();

		if (is_numeric($args[0]))
		{
			$html = "\n<table cellpadding=\"" . $args[0] . "\" cellspacing=\"0\" border=\"0\"";

			if (count($args) == 2)
			{
				$html .= " class=\" table " . $args[1] . "\"";
			}

			$html .= " width=\"100%\">";
		}
		else
		{
			$html = "\n</table>";
		}

		return $html;
	}


	/**
	 * Returns a a table cell
	 * sem_f022(text,art,align,width,class,colspan)
	 *
	 * @deprecated 2.2 should be written in code
	 *
	 * @return string
	 */

	public static function getTableCell()
	{
		$args = func_get_args();
		$html = "\n<t" . $args[1];

		if (count($args) > 4)
		{
			if ($args[4] != "")
			{
				$html .= " class=\"" . $args[4] . "\"";
			}
		}

		if (count($args) > 2)
		{
			if ($args[2] != "")
			{
				$html .= " style=\"text-align:";

				switch ($args[2])
				{
					case "l":
						$html .= "left";
						break;
					case "r":
						$html .= "right";
						break;
					case "c":
						$html .= "center";
						break;
				}

				$html .= ";\"";
			}
		}

		if (count($args) > 3)
		{
			if ($args[3] != "")
			{
				$html .= " width=\"" . $args[3] . "\"";
			}
		}

		if (count($args) > 5)
		{
			if ($args[5])
			{
				$html .= " colspan=\"" . $args[5] . "\"";
			}
		}

		$html .= ">" . $args[0] . "</t" . $args[1] . ">";

		return $html;
	}

	/**
	 * Returns the heading of a tab..
	 * sem_f032
	 *
	 * @param   object  $tab  - The tab
	 *
	 * @deprecated 2.2
	 *
	 * @return string
	 */
	public static function getEventlistHeader($tab)
	{
		$confusers = JComponentHelper::getParams('com_users');

		switch ($tab)
		{
			case "2":
				$tabnum = array(0, 1, 0);
				break;

			case "3":
				$tabnum = array(0, 0, 1);
				break;

			default:
				$tabnum = array(1, 0, 0);
				break;
		}

		$html = "<table cellpadding=\"5\" cellspacing=\"0\" border=\"0\" width=\"100%\"><tr>";

		if (JFactory::getUser()->id > 0)
		{
			// Default View
			$defaultlink = JRoute::_("index.php?option=com_matukio");
			$html .= "\n<td class=\"sem_tab" . $tabnum[0] . "\">";
			$html .= "\n<a class=\"sem_tab\" href=\"" . $defaultlink . "\" title=\""
				. JTEXT::_('COM_MATUKIO_EVENTS') . "\" " . MatukioHelperUtilsBasic::getMouseOverWindowStatus(JTEXT::_('COM_MATUKIO_EVENTS')) . ">
                <img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "2600.png\" border=\"0\" align=\"absmiddle\"> "
				. JTEXT::_('COM_MATUKIO_EVENTS') . "</a>";
			$html .= "</td>";
			$html .= "\n<td class=\"sem_tab" . $tabnum[1] . "\">";

			// Own Booking
			$linkownbook = JRoute::_("index.php?option=com_matukio&art=1");

			$html .= "\n<a class=\"sem_tab\" title=\"" . JTEXT::_('COM_MATUKIO_MY_BOOKINGS') . "\" href=\"" . $linkownbook . "\" "
				. MatukioHelperUtilsBasic::getMouseOverWindowStatus(JTEXT::_('COM_MATUKIO_MY_BOOKINGS')) . "><img src=\""
				. MatukioHelperUtilsBasic::getComponentImagePath()
				. "2700.png\" border=\"0\" align=\"absmiddle\"> " . JTEXT::_('COM_MATUKIO_MY_BOOKINGS') . "</a>";
			$html .= "\n</td>";

			$linkownevents = JRoute::_("index.php?option=com_matukio&art=2");

			if (JFactory::getUser()->authorise('core.edit.own', 'com_matukio') && MatukioHelperSettings::getSettings('frontend_ownereditevent', 1))
			{
				$html .= "\n<td class=\"sem_tab" . $tabnum[2] . "\">";
				$html .= "\n<a class=\"sem_tab\" title=\"" . JTEXT::_('COM_MATUKIO_MY_OFFERS') . "\" href=\"" . $linkownevents . "\" "
					. MatukioHelperUtilsBasic::getMouseOverWindowStatus(JTEXT::_('COM_MATUKIO_MY_OFFERS')) . "><img src=\""
					. MatukioHelperUtilsBasic::getComponentImagePath()
					. "2800.png\" border=\"0\" align=\"absmiddle\"> " . JTEXT::_('COM_MATUKIO_MY_OFFERS') . "</a>";
				$html .= "\n</td>";
			}
		}
		elseif (MatukioHelperSettings::getSettings('frontend_unregisteredshowlogin', 1) > 0)
		{
			// Joomla > 1.6 com_users !
			$baseuserurl = "index.php?option=com_user";

			if (MatukioHelperUtilsBasic::getJoomlaVersion() != '1.5')
			{
				$baseuserurl = "index.php?option=com_users";
			}

			$registrationurl = "&amp;view=register";

			if (MatukioHelperUtilsBasic::getJoomlaVersion() != '1.5')
			{
				$registrationurl = "&amp;view=registration";
			}

			$html .= "<td class=\"sem_notableft\">";
			$html .= "<input type=\"text\" name=\"semusername\" value=\"" . JTEXT::_('USERNAME') . "\" class=\"sem_inputbox\" style=\"background-image:url("
				. MatukioHelperUtilsBasic::getComponentImagePath() . "0004.png);background-repeat:no-repeat;background-position:2px;"
				. "padding-left:18px;width:100px;vertical-align:middle;\" onFocus=\"if(this.value=='"
				. JTEXT::_('USERNAME') . "') this.value='';\" onBlur=\"if(this.value=='') {this.value='"
				. JTEXT::_('USERNAME') . "';form.semlogin.disabled=true;}\" onKeyup=\"if(this.value!='') form.semlogin.disabled=false;\"> ";
			$html .= "<input type=\"password\" name=\"sempassword\" value=\"" . JTEXT::_('PASSWORD')
				. "\" class=\"sem_inputbox\" style=\"background-image:url("
				. MatukioHelperUtilsBasic::getComponentImagePath() . "0005.png);background-repeat:no-repeat;background-position:2px;"
			. "padding-left:18px;width:100px;vertical-align:middle;\" onFocus=\"if(this.value=='"
				. JTEXT::_('PASSWORD') . "') this.value='';\" onBlur=\"if(this.value=='') this.value='" . JTEXT::_('PASSWORD') . "';\"> ";

			$html .= "<button class=\"button\" type=\"submit\" style=\"cursor:pointer;vertical-align:middle;padding-left:0pt;"
				."padding-right:0pt;padding-top:0pt;padding-bottom:0pt;\" title=\""
				. JTEXT::_('LOGIN') . "\" id=\"semlogin\" disabled><img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
				. "0007.png\" style=\"vertical-align:middle;\"></button>";

			$html .= "&nbsp;&nbsp;&nbsp;";
			$html .= " <button class=\"button\" type=\"button\" style=\"cursor:pointer;vertical-align:middle;padding-left:0pt;"
				. "padding-right:0pt;padding-top:0pt;padding-bottom:0pt;\" title=\""
				. JTEXT::_('COM_MATUKIO_FORGOTTEN_USERNAME') . "\" onClick=\"location.href='"
				. MatukioHelperUtilsBasic::getSitePath() . $baseuserurl . "&amp;view=remind'\"><img src=\""
				. MatukioHelperUtilsBasic::getComponentImagePath() . "0008.png\" style=\"vertical-align:middle;\"></button>";

			$html .= " <button class=\"button\" type=\"button\" style=\"cursor:pointer;vertical-align:middle;padding-left:0pt;"
				. "padding-right:0pt;padding-top:0pt;padding-bottom:0pt;\" title=\""
				. JTEXT::_('COM_MATUKIO_CHANGE_PASSWORD') . "\" onClick=\"location.href='"
				. MatukioHelperUtilsBasic::getSitePath() . $baseuserurl . "&amp;view=reset'\"><img src=\""
				. MatukioHelperUtilsBasic::getComponentImagePath() . "0009.png\" style=\"vertical-align:middle;\"></button>";

			if ($confusers->get('allowUserRegistration', 0) > 0)
			{
				$html .= " <button class=\"button\" type=\"button\" style=\"cursor:pointer;vertical-align:middle;padding-left:0pt;"
					. "padding-right:0pt;padding-top:0pt;padding-bottom:0pt;\" title=\""
					. JTEXT::_('COM_MATUKIO_REGISTER') . "\" onClick=\"location.href='" . MatukioHelperUtilsBasic::getSitePath()
					. $baseuserurl . $registrationurl . "'\"><img src=\""
					. MatukioHelperUtilsBasic::getComponentImagePath() . "0006.png\" style=\"vertical-align:middle;\"></button>";
			}

			$html .= "</td>";
		}

		$html .= "<td class=\"sem_notab\">&nbsp;";
		$knopfunten = "";

		if (JFactory::getUser()->id > 0 and MatukioHelperSettings::getSettings('frontend_unregisteredshowlogin', 1) > 0)
		{
			$logoutlink = JRoute::_("index.php?option=com_matukio&view=matukio&task=logoutUser");

			$html .= JHTML::_('
				link', $logoutlink, JHTML::_('image', MatukioHelperUtilsBasic::getComponentImagePath() . '3232.png', null,
					array('border' => '0', 'align' => 'absmiddle')
					), array('title' => JTEXT::_('COM_MATUKIO_LOGOUT'))
				)
				. "&nbsp;&nbsp;";

			$knopfunten .= "<a href=\"" . $logoutlink . "\"><span class=\"mat_button\" style=\"cursor:pointer;\">" . JHTML::_('image',
					MatukioHelperUtilsBasic::getComponentImagePath() . '3216.png', null, array('border' => '0',
						'align' => 'absmiddle')
				) . "&nbsp;" . JTEXT::_('COM_MATUKIO_LOGOUT') . "</span></a>";
		}

		echo $html;

		return $knopfunten;
	}

	/**
	 * Gets the print window link
	 * sem_f037
	 *
	 * @param   int     $art    - The int expression of what should be printed (e.g. 1 = certificicate, 2 = course overview)
	 * @param   int     $cid    - The event id (?)
	 * @param   int     $uid    - The user id (?)
	 * @param   string  $knopf  - Something about the image
	 * @param   string  $class  - Modern images?
	 *
	 * @todo rewrite into a failsafe, readable form
	 * @return  string
	 */
	public static function getPrintWindow($art, $cid, $uid, $knopf, $class = "default")
	{
		$dateid = trim(JFactory::getApplication()->input->get('dateid', 1));
		$catid = trim(JFactory::getApplication()->input->getInt('catid', 0));
		$search = trim(strtolower(JFactory::getApplication()->input->get('search', '', 'string')));
		$limit = trim(JFactory::getApplication()->input->getInt('limit', MatukioHelperSettings::getSettings('event_showanzahl', 10)));
		$limitstart = trim(JFactory::getApplication()->input->getInt('limitstart', 0));

		if ($knopf == "")
		{
			$image = "1932";
		}
		else
		{
			$image = "1916";
		}

		$titel = JTEXT::_('COM_MATUKIO_PRINT');
		$href = JURI::ROOT() . "index.php?tmpl=component&s=" . MatukioHelperUtilsBasic::getRandomChar()
			. "&option=" . JFactory::getApplication()->input->get('option')
			. "&view=printeventlist&dateid=" . $dateid . "&catid=" . $catid . "&search=" . $search . "&amp;limit=" . $limit . "&limitstart="
			. $limitstart . "&cid=" . $cid . "&uid=" . $uid . "&todo=";

		$x = 800;
		$y = 600;

		switch ($art)
		{
			case 1:
				// Zertifikat
				$image = "2900";
				$titel = JTEXT::_('COM_MATUKIO_PRINT_CERTIFICATE');
				$href .= "certificate";
				break;

			case 2:
				// Kursuebersicht
				$href .= "print_eventlist";
				break;

			case 3:
				// Gebuchte Kurse
				$href .= "print_booking";
				break;

			case 4:
				// Kursangebot
				$href .= "print_myevents";
				break;

			case 5:
				// Lie this is not the participants in the frontend..
				// Art = 1 = Signature list
				$href .= "print_teilnehmerliste&art=1";
				$titel = JText::_("COM_MATUKIO_PRINT_SIGNATURELIST");

				if ($knopf == "")
				{
					$image = "2032";
				}
				else
				{
					$image = "2016";
				}
				break;

			case 6:
				// Buchungsbestaetigung
				$href .= "1495735268456&amp;task=printbook";
				break;

			case 7:
				// Teilnehmerliste2
				$titel = JText::_("COM_MATUKIO_PRINT_PARTICIPANTSLIST");
				$href .= "print_teilnehmerliste";
				break;
		}

		$btnclass = "mat_button";

		if ($class == "bootstrap")
		{
			$btnclass = "btn";
		}

		if (($art > 1 && MatukioHelperSettings::getSettings('frontend_userprintlists', 1) > 0
			OR ($art == 1 && MatukioHelperSettings::getSettings('frontend_userprintcertificate', 0) > 0
			&& MatukioHelperSettings::getSettings('frontend_certificatesystem', 0) > 0)))
		{
			if ($knopf == "")
			{
				return "<a title=\"" . $titel . "\" class=\"modal cjmodal\" href=\"" . $href
				. "\" rel=\"{handler: 'iframe', size: {x: " . $x . ", y: " . $y . "}}\"><img src=\""
				. MatukioHelperUtilsBasic::getComponentImagePath() . $image . ".png\" border=\"0\" align=\"absmiddle\"></a>";
			}
			else
			{
				$img = "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . $image . ".png\" border=\"0\" align=\"absmiddle\">&nbsp;";

				if ($class == "bootstrap")
				{
					$img = "";
				}

				return "<a class=\"modal cjmodal\" href=\"" . $href . "\" rel=\"{handler: 'iframe', size: {x: "
				. $x . ", y: " . $y . "}}\"><span class=\"" . $btnclass . "\" style=\"cursor:pointer;\" type=\"button\">"
				. $img . $titel . "</span></a>";
			}
		}
		elseif ($art == 1 AND MatukioHelperSettings::getSettings('frontend_certificatesystem', 0) > 0)
		{
			if ($knopf == "")
			{
				return "\n<a title=\"" . $titel . "\" class=\"modal cjmodal\" href=\"" . $href
				. "\" rel=\"{handler: 'iframe', size: {x: " . $x . ", y: " . $y . "}}\"><img src=\""
				. MatukioHelperUtilsBasic::getComponentImagePath() . "2900.png\" border=\"0\" align=\"absmiddle\"></a>";
			}
			else
			{
				return "\n<a title=\"" . $titel . "\" class=\"modal cjmodal btn\" href=\"" . $href
				. "\" rel=\"{handler: 'iframe', size: {x: " . $x . ", y: " . $y . "}}\">"
				. JText::_("COM_MATUKIO_CERTIFICATE") . "</a>";
			}
		}
	}

	/**
	 * Returns the rating image (including link)
	 * sem_f035
	 *
	 * @param   string  $dir    - The image directory (We could get this with getImagePath too)
	 * @param   int     $cid    - The Event id
	 * @param   int     $imgid  - The images ending number (added to 240X.png)
	 *
	 * @todo update and rewrite
	 * @return  string
	 */

	public static function getRatingPopup($dir, $cid, $imgid)
	{
		if (JFactory::getUser()->id > 0)
		{
			$image = "240" . $imgid;
			$titel = JTEXT::_('COM_MATUKIO_YOUR_RATING');
			$href = JURI::ROOT() . "index.php?tmpl=component&s=" . MatukioHelperUtilsBasic::getRandomChar() . "&option="
				. JFactory::getApplication()->input->get('option') . "&cid=" . $cid . "&view=rateevent";
			$x = 500;
			$y = 280;

			return "<a title=\"" . $titel . "\" class=\"modal cjmodal\" href=\"" . $href . "\" rel=\"{handler: 'iframe', size: {x: "
			. $x . ", y: " . $y . "}}\"><img id=\"graduate" . $cid . "\" src=\"" . $dir . $image
			. ".png\" border=\"0\" align=\"absmiddle\" /></a>";
		}
	}

	/**
	 * Prints the eventlist header end
	 *
	 * @deprecated 2.0 - should be replaced through normal html
	 * @return  void
	 */
	public static function getEventlistHeaderEnd()
	{
		echo "</td></tr>" . self::getTableHeader('e') . self::getTableHeader(4)
			. "<tr><td class=\"sem_anzeige\">";
	}

	/**
	 * Prints the heading / caption (in a own table
	 * sem_f041
	 *
	 * @param   string  $temp1  - The first title
	 * @param   string  $temp2  - The second title (optional, checked if "")
	 *
	 * @deprecated 2.0 - should be replaced through normal html
	 * @return  void
	 */
	public static function printHeading($temp1, $temp2)
	{
		$html = "<table cellpadding=\"2\" cellspacing=\"0\" border=\"0\" width=\"100%\">";
		$html .= "\n<tr><td class=\"sem_cat_title\">" . $temp1 . "</td></tr>";

		if ($temp2 != "")
		{
			$html .= "\n<tr><td class=\"sem_cat_desc\">" . $temp2 . "</td></tr>";
		}

		$html .= "\n</table>";
		echo $html;
	}

	/**
	 * Gets the limitbox for the sitenavigation
	 * sem_f040
	 *
	 * @param   int     $art    - The art (0 = overview, 1 = my bookings, 2 = my offers)
	 * @param   int     $limit  - The limit (nr of events)
	 * @param   string  $where  - The where (default eventlist)
	 * @param   string  $tmpl   - The template (default old)
	 *
	 * @deprecated 2.0 - should be replaced through normal html
	 * @return  mixed
	 */

	public static function getLimitboxSiteNav($art, $limit, $where = "eventlist", $tmpl = "old")
	{
		$limits = array();

		$limits[] = JHTML::_('select.option', '3');

		for ($i = 5; $i <= 30; $i += 5)
		{
			$limits[] = JHTML::_('select.option', "$i");
		}

		$limits[] = JHTML::_('select.option', '50');
		$limits[] = JHTML::_('select.option', '100');
		$limits[] = JHTML::_('select.option', '0', JText::_('all'));

		$class = "sem_inputbox";

		if ($tmpl == "modern")
		{
			$class = "mat_inputbox chzn-single";
		}

		return JHTML::_('select.genericlist', $limits, 'limit', 'class="' . $class
			. '" size="1" onchange="changeLimitEventlist()" style="width: 70px;"', 'value', 'text', $limit
		);
	}

	/**
	 * Get a array of informations over the events booking status (still bookable etc.)
	 * [0] = $buchbar, is a event still bookable
	 * [1] = $altbild, the description / reason for 0 (text?!)
	 * [2] = $temp, The booking of the user (if he is logged in or a user id is given)
	 * [3] = $buchgraf, Waitlist? No idea..
	 * [4] = $freieplaetze Number of free places!
	 * sem_f012
	 *
	 * @param   int     $art     - The art  (0 = overview, 1 = my bookings, 2 = my offers)
	 * @param   object  $row     - The event
	 * @param   int     $usrid   - The userid
	 * @param   string  $uuid    - The uuid
	 * @param   int     $status  - Which status has a booking
	 *
	 * @todo Rewrite, cleanup and optimize
	 * @return array
	 */

	public static function getEventBookableArray($art, $row, $usrid, $uuid = null, $status = 1, $booking = null)
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid= " . $database->quote($row->id) . " AND status = "
			. $database->quote($status) . " ORDER BY id");

		$temps = $database->loadObjectList();

		$gebucht = 0;

		foreach ($temps as $el)
		{
			$gebucht = $gebucht + $el->nrbooked;
		}

		if ($usrid < 0)
		{
			$sid = $usrid * -1;
			$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id='$sid'");
			$userid = 0;
		}
		else
		{
			if ($usrid == 0)
			{
				$usrid = -1;
			}

			$query = "SELECT * FROM #__matukio_bookings WHERE semid='" . $row->id . "' AND userid = '" . $usrid . "'";
			$database->setQuery($query);
		}

		$temp = $database->loadObjectList();

		// Saves one query.. hack for backward compat
		if (!empty($booking))
		{
			$temp = array($booking);
		}

		if (empty($temp) && !empty($uuid))
		{
			$query = "SELECT * FROM #__matukio_bookings WHERE uuid = " . $database->quote($uuid);
			$database->setQuery($query);
			$temp = $database->loadObjectList();
		}

		$freieplaetze = $row->maxpupil - $gebucht;

		if ($freieplaetze < 0)
		{
			$freieplaetze = 0;
		}

		$buchbar = 3;
		$buchgraf = 2;
		$altbild = JTEXT::_('COM_MATUKIO_NOT_EXCEEDED');
		$reglevel = MatukioHelperUtilsBasic::getUserLevel();
		$neudatum = MatukioHelperUtilsDate::getCurrentDate();

		if ($neudatum > $row->booked)
		{
			$buchbar = 1;
			$buchgraf = 0;
			$altbild = JTEXT::_('COM_MATUKIO_REGISTRATION_END');
		}
		elseif ($row->cancelled == 1 OR ($freieplaetze < 1
				AND $row->stopbooking == 1) OR ($usrid == $row->publisher
				AND MatukioHelperSettings::getSettings('booking_ownevents', 1) == 0))
		{
			$buchbar = 1;
			$buchgraf = 0;
			$altbild = JTEXT::_('COM_MATUKIO_UNBOOKABLE');
		}
		elseif ($freieplaetze < 1 AND ($row->stopbooking == 0 OR $row->stopbooking == 2))
		{
			$buchgraf = 1;
			$altbild = JTEXT::_('COM_MATUKIO_BOOKING_ON_WAITLIST');
		}

		if (count($temp) > 0)
		{
			if (MatukioHelperSettings::getSettings('frontend_usermehrereplaetze', 1) == 0)
			{
				$buchbar = 2;
				$buchgraf = 0;
				$altbild = JTEXT::_('COM_MATUKIO_ALREADY_BOOKED');
			}
		}

		if ($reglevel < 1)
		{
			$buchbar = 0;
		}

		if ($art == 1)
		{
			if ($temp[0]->status == MatukioHelperUtilsBooking::$WAITLIST)
			{
				$buchgraf = 1;
				$altbild = JTEXT::_('COM_MATUKIO_WAITLIST');
			}
			elseif ($temp[0]->status == MatukioHelperUtilsBooking::$ACTIVE)
			{
				$buchgraf = 2;
				$altbild = JTEXT::_('COM_MATUKIO_PARTICIPANT_ASSURED');
			}
			elseif ($temp[0]->status == MatukioHelperUtilsBooking::$ARCHIVED || $temp[0]->status == MatukioHelperUtilsBooking::$DELETED )
			{
				$buchgraf = 0;
				$altbild = JTEXT::_('COM_MATUKIO_DELETED_ARCHIVED');
			}
			elseif ($temp[0]->status == MatukioHelperUtilsBooking::$PENDING )
			{
				$buchgraf = 3;
				$altbild = JTEXT::_('COM_MATUKIO_PENDING');
			}

			if ($row->cancelled == 1)
			{
				$buchgraf = 0;
				$altbild = JTEXT::_('COM_MATUKIO_UNBOOKABLE');
			}
		}

		if ($art == 2)
		{
			$buchgraf = 2;
			$altbild = JTEXT::_('COM_MATUKIO_EVENT_HAS_NOT_STARTED_YET');

			if ($neudatum > $row->end)
			{
				$buchgraf = 0;
				$altbild = JTEXT::_('COM_MATUKIO_EVENT_HAS_ENDED');
			}
			elseif ($neudatum > $row->begin)
			{
				$buchgraf = 1;
				$altbild = JTEXT::_('COM_MATUKIO_EVENT_IS_RUNNING');
			}
		}

		return array($buchbar, $altbild, $temp, $buchgraf, $freieplaetze);
	}

	/**
	 * Calculates how many places (booked, certificated etc.) an event has and more!
	 * (->booked, ->certificated, ->paid, ->total)
	 * sem_f020
	 *
	 * @param   object  $row     - The event
	 * @param   int     $status  - The status (opt)
	 *
	 * @return  stdClass
	 */
	public static function calculateBookedPlaces($row, $status = 1)
	{
		$zurueck = new stdClass;
		$database = JFactory::getDBO();
		$database->setQuery("SELECT nrbooked, certificated, paid FROM #__matukio_bookings
			WHERE semid='" . $row->id . "' AND status = " . $database->quote($status)
		);

		$temps = $database->loadObjectList();

		$gebucht = 0;
		$zertifiziert = 0;
		$bezahlt = 0;

		foreach ($temps as $el)
		{
			$gebucht = $gebucht + $el->nrbooked;
			$zertifiziert = $zertifiziert + $el->certificated;
			$bezahlt = $bezahlt + $el->paid;
		}

		$zurueck->booked = $gebucht;
		$zurueck->certificated = $zertifiziert;
		$zurueck->paid = $bezahlt;
		$zurueck->number = count($temps);

		return $zurueck;
	}

	/**
	 * Calculates how many places (booked, certificated etc.) an event has and more!
	 * (->booked, ->certificated, ->paid, ->total)
	 * sem_f020
	 *
	 * @param   object  $rec  - The event
	 *
	 * @return  stdClass
	 */
	public static function calculateBookedPlacesRecurring($rec)
	{
		$zurueck = new stdClass;
		$database = JFactory::getDBO();

		// SEMID
		$database->setQuery(
			"SELECT * FROM #__matukio_bookings WHERE semid = '"
			. $rec->id . "' AND status = '" . MatukioHelperUtilsBooking::$ACTIVE . "'"
		);

		$temps = $database->loadObjectList();

		$gebucht = 0;
		$zertifiziert = 0;
		$bezahlt = 0;

		foreach ($temps as $el)
		{
			$gebucht = $gebucht + $el->nrbooked;
			$zertifiziert = $zertifiziert + $el->certificated;
			$bezahlt = $bezahlt + $el->paid;
		}

		$zurueck->booked = $gebucht;
		$zurueck->certificated = $zertifiziert;
		$zurueck->paid = $bezahlt;
		$zurueck->number = count($temps);

		return $zurueck;
	}

	/**
	 * Show the color description
	 * sem_f029
	 *
	 * @param   string  $green   -
	 * @param   string  $yellow  -
	 * @param   string  $red     -
	 *
	 * @todo rewrite pls..
	 * @return string
	 */
	public static function getColorDescriptions($green, $yellow, $red, $art = 0)
	{
		$html = '<table cellpadding="4" class="mat_table" border="0" width="100%" style="margin: 15px 0 8px 0;">';
		$html .= "<tr>";

		if ($art == 1)
		{
			$html .= self::getTableCell("<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
				. "pending.png\" border=\"0\" align=\"absmiddle\"> " . JText::_("COM_MATUKIO_PENDING"), 'd', 'c', '', 'sem_nav');
		}

		if ($green != "")
		{
			$html .= self::getTableCell("<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
			. "2502.png\" border=\"0\" align=\"absmiddle\"> " . $green, 'd', 'c', '', 'sem_nav');
		}

		if ($yellow != "")
		{
			$html .= self::getTableCell("<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
			. "2501.png\" border=\"0\" align=\"absmiddle\"> " . $yellow, 'd', 'c', '', 'sem_nav');
		}

		if ($red != "")
		{
			$html .= self::getTableCell("<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
			. "2500.png\" border=\"0\" align=\"absmiddle\"> " . $red, 'd', 'c', '', 'sem_nav');
		}

		$html .= "</tr>";
		$html .= "</table>";

		return $html;
	}

	/**
	 * Returns the hidden values (frontend)
	 * sem_f014
	 *
	 * @param   string  $task        -
	 * @param   int     $catid       -
	 * @param   string  $search      -
	 * @param   int     $limit       -
	 * @param   int     $limitstart  -
	 * @param   int     $cid         -
	 * @param   int     $dateid      -
	 * @param   int     $uid         -
	 *
	 * @return string
	 */

	public static function getHiddenFormElements($task, $catid, $search, $limit, $limitstart, $cid, $dateid, $uid)
	{
		$html = "<input type=\"hidden\" name=\"option\" value=\"com_matukio\" />";
		$html = "<input type=\"hidden\" name=\"task\" value=\"" . $task . "\" />";
		$html .= "<input type=\"hidden\" name=\"limitstart\" value=\"" . $limitstart . "\" />";
		$html .= "<input type=\"hidden\" name=\"cid\" value=\"" . $cid . "\" />";

		if ($catid != "")
		{
			$html .= "<input type=\"hidden\" name=\"catid\" value=\"" . $catid . "\" />";
		}

		if ($search != "")
		{
			$html .= "<input type=\"hidden\" name=\"search\" value=\"" . $search . "\" />";
		}

		if ($limit != "")
		{
			$html .= "<input type=\"hidden\" name=\"limit\" value=\"" . $limit . "\" />";
		}

		if ($uid != "")
		{
			if ($uid == -1)
			{
				$uid = "";
			}

			$html .= "<input type=\"hidden\" name=\"uid\" value=\"" . $uid . "\" />";
		}

		if ($dateid != "")
		{
			$html .= "<input type=\"hidden\" name=\"dateid\" value=\"" . $dateid . "\" />";
		}

		return $html;
	}

	/**
	 * Gets an array of additional fields..
	 * sem_f017
	 *
	 * @param   object  $row      - The event
	 * @param   object  $booking  - The opt booking..
	 *
	 * @deprecated 2.0 - Why an array? Why?
	 * @return  array
	 */

	public static function getAdditionalFieldsFrontend($row, $booking = null)
	{
		$zusfeld = array();

		$zusfeld[] = array($row->zusatz1, $row->zusatz2, $row->zusatz3, $row->zusatz4, $row->zusatz5, $row->zusatz6, $row->zusatz7, $row->zusatz8,
			$row->zusatz9, $row->zusatz10, $row->zusatz11, $row->zusatz12, $row->zusatz13, $row->zusatz14, $row->zusatz15, $row->zusatz16,
			$row->zusatz17, $row->zusatz18, $row->zusatz19, $row->zusatz20);

		if (isset($row->zusatz1hint))
		{
			$zusfeld[] = array($row->zusatz1hint, $row->zusatz2hint, $row->zusatz3hint, $row->zusatz4hint, $row->zusatz5hint,
				$row->zusatz6hint, $row->zusatz7hint, $row->zusatz8hint, $row->zusatz9hint, $row->zusatz10hint, $row->zusatz11hint,
				$row->zusatz12hint, $row->zusatz13hint, $row->zusatz14hint, $row->zusatz15hint, $row->zusatz16hint,
				$row->zusatz17hint, $row->zusatz18hint, $row->zusatz19hint, $row->zusatz20hint);

			$zusfeld[] = array($row->zusatz1show, $row->zusatz2show, $row->zusatz3show, $row->zusatz4show, $row->zusatz5show,
				$row->zusatz6show, $row->zusatz7show, $row->zusatz8show, $row->zusatz9show, $row->zusatz10show, $row->zusatz11show,
				$row->zusatz12show, $row->zusatz13show, $row->zusatz14show, $row->zusatz15show, $row->zusatz16show, $row->zusatz17show,
				$row->zusatz18show, $row->zusatz19show, $row->zusatz20show);
		}

		return $zusfeld;
	}

	/**
	 * Gets the link (modal) to the E-Mail window
	 * sem_f034
	 *
	 * @param   string  $dir    - The image directory
	 * @param   int     $cid    - The event id
	 * @param   int     $art    - The art (0, 1, 2 see other)
	 * @param   string  $class  - The class
	 *
	 * @todo rewrite and move to code
	 * @return  string
	 */
	public static function getEmailWindow($dir, $cid, $art = 0, $class = "default")
	{
		$html = "";
		$href = MatukioHelperUtilsBasic::getSitePath() . "index.php?tmpl=component&s=" . MatukioHelperUtilsBasic::getRandomChar()
			. "&option=" . JFactory::getApplication()->input->get('option') . "&view=contactorganizer&cid=" . $cid . "&art=" . $art . "&task=";
		$x = 600;
		$y = 550;
		$htxt = "<a class=\"modal cjmodal\" rel=\"{handler: 'iframe', size: {x: " . $x . ", y: " . $y . "}}\" href=\"" . $href;

		$btnclass = "mat_button";

		if ($class == "bootstrap")
		{
			$btnclass = "btn";
		}

		if ($art == 1 AND MatukioHelperSettings::getSettings('contact_organizer', 1) > 0)
		{
			$html = $htxt . "19\" title=\"" . JTEXT::_('COM_MATUKIO_CONTACT') . "\"><span class=\"" . $btnclass . "\" type=\"button\">";

			if ($class != "bootstrap")
			{
				$html .= "<img src=\"" . $dir . "1716.png\" border=\"0\" align=\"absmiddle\">";
			}

			$html .= "&nbsp;" . JTEXT::_('COM_MATUKIO_CONTACT') . "</span></a>";
		}
		elseif ($art == 2 && JFactory::getUser()->authorise('core.edit.own', 'com_matukio')
			&& MatukioHelperSettings::getSettings('sendmail_contact', 1) > 0)
		{
			$html = $htxt . "19\"><span class=\"" . $btnclass . "\" type=\"button\"><img src=\"" . $dir
				. "1716.png\" border=\"0\" align=\"absmiddle\">&nbsp;" . JTEXT::_('COM_MATUKIO_CONTACT') . "</span></a>";
		}
		elseif ($art == 3 AND JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
		{
			$html = $htxt . "30\" title=\"" . JTEXT::_('COM_MATUKIO_CONTACT') . "\"><img src=\"" . $dir . "1732.png\" border=\"0\" align=\"absmiddle\"></a>";
		}
		elseif ($art == 4 AND JFactory::getUser()->authorise('core.edit.own', 'com_matukio'))
		{
			$html = $htxt . "30\"><span class=\"" . $btnclass . "\" type=\"button\"><img src=\""
				. $dir . "1716.png\" border=\"0\" align=\"absmiddle\">&nbsp;" . JTEXT::_('COM_MATUKIO_CONTACT') . "</span></a>";
		}
		elseif ($art == 2)
		{
			$html = $htxt . "19\"><span class=\"" . $btnclass . "\" type=\"button\"><img src=\"" . $dir
				. "1716.png\" border=\"0\" align=\"absmiddle\">&nbsp;" . JTEXT::_('COM_MATUKIO_CONTACT') . "</span></a>";
		}
		elseif ($art == "organizer")
		{
			$html = $htxt . "19\"><span class=\"" . $btnclass . "\" type=\"button\">" . JTEXT::_('COM_MATUKIO_CONTACT')
				. "</span></a>";
		}

		return $html;
	}

	// ++++++++++++++++++++++++++++++++++
	// +++ Aray mit Dateien erzeugen       sem_f060
	// ++++++++++++++++++++++++++++++++++

	public static function getEventFileArray($row)
	{
		$zusfeld = array();
		$zusfeld[] = array($row->file1, $row->file2, $row->file3, $row->file4, $row->file5);
		$zusfeld[] = array($row->file1desc, $row->file2desc, $row->file3desc, $row->file4desc, $row->file5desc);
		$zusfeld[] = array($row->file1down, $row->file2down, $row->file3down, $row->file4down, $row->file5down);

		return $zusfeld;
	}

	// ++++++++++++++++++++++++++++++++++++++
	// +++ Bestaetigungs-Emails versenden +++         sem_f050
	// ++++++++++++++++++++++++++++++++++++++

	/**
	 * Sends E-Mails to participants, organizers etc.
	 *
	 * $art:
	 * 1: booking confirmation
	 * 2: cancel confirmation (user)
	 * 3: cancel confirmation (admin)
	 * 4: deleted
	 * 5: published
	 * 6: certificated
	 * 7: certificate revoked
	 * 8: max number reached
	 * 9: republished event
	 * 10: canceld
	 * 11: updated
	 * 12: confirmation
	 * 13: confirmation to organizer with CSV file
	 * 14: info to organizer
	 * 15: invoice email
	 *
	 * @param   object  $event         - The event object
	 * @param   int     $uid           - The user id?
	 * @param   int     $art           - The Task
	 * @param   bool    $cancel        - Should we cancel
	 * @param   object  $booking       - The booking
	 * @param   bool    $send_invoice  - Should the invoice send?
	 *
	 * @todo update, rewrite and optimize
	 * @throws  Exception
	 * @return  void
	 */
	public static function sendBookingConfirmationMail($event, $uid, $art, $cancel = false, $booking = null, $send_invoice = true)
	{
		jimport('joomla.mail.helper');
		jimport('joomla.mail.mail');
		$mainframe = JFactory::getApplication();

		if (MatukioHelperSettings::getSettings('sendmail_teilnehmer', 1) > 0
			OR MatukioHelperSettings::getSettings('sendmail_owner', 1) > 0)
		{
			$database = JFactory::getDbo();

			// Load event (use events helper function)
			if ($booking == null)
			{
				if (!$cancel)
				{
					$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id = " . $uid);
				}
				else
				{
					$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid = " . $event->id . " AND userid = " . $uid);
				}

				$booking = $database->loadObject();
			}

			if ($booking->userid == 0)
			{
				$user = JFactory::getUser(0);
				$user->name = $booking->name;
				$user->email = $booking->email;
			}
			else
			{
				$user = JFactory::getuser($booking->userid);
			}

			$publisher = JFactory::getuser($event->publisher);

			$body1 = "<p><span style=\"font-size:10pt;\">" . JTEXT::_('COM_MATUKIO_PLEASE_DONT_ANSWER_THIS_EMAIL') . "</span><p>";
			$body2 = $body1;
			$gebucht = self::calculateBookedPlaces($event);
			$gebucht = $gebucht->booked;

			// We just add a first line and then switch to default booking confirmation
			if ($art == 11)
			{
				$body1 = JTEXT::_('COM_MATUKIO_ORGANISER_UPDATED_YOUR_BOOKING');
				$art = 1;
			}

			switch ($art)
			{
				// Booking confirmation
				case 1:
				case 2:
				case 3:
					break;

				case 4:
					$body1 .= JTEXT::_('COM_MATUKIO_ADMIN_DELETED_THE_FOLLOWING_EVENT');
					$body2 .= JTEXT::_('COM_MATUKIO_ADMIN_DELETED_EVENT');
					break;
				case 5:
					$body1 .= JTEXT::_('COM_MATUKIO_ADMIN_PUBLISHED_EVENT_YOUR_BOOKING_IS_VALID');
					$body2 .= JTEXT::_('COM_MATUKIO_ADMIN_PUBLISHED_EVENT_THE_BOOKING_OF_PARTICIPANTS_IS_VALID');
					break;
				case 6:
					$body1 .= JTEXT::_('COM_MATUKIO_THE_ADMIN_CERTIFIED_YOU');
					$body2 .= JTEXT::_('COM_MATUKIO_ADMIN_HAS_CERTIFICATED_FOLLOWING_PARTICIPANT');

					if (MatukioHelperSettings::getSettings('frontend_userprintcertificate', 0) > 0)
					{
						$body1 .= " " . JTEXT::_('COM_MATUKIO_YOU_CAN_PRINT_YOUR_CERTIFICATE');
					}
					break;
				case 7:
					$body1 .= JTEXT::_('COM_MATUKIO_CERTIFICAT_REVOKED');
					$body2 .= JTEXT::_('COM_MATUKIO_ADMIN_HAS_WITHDRAWN_CERTIFICATE_FOR_FOLLOWNG_PARITICIPANTS');
					break;
				case 8:
					if ($gebucht > $event->maxpupil)
					{
						if ($event->stopbooking = 0)
						{
							$body1 .= JTEXT::_('COM_MATUKIO_MAX_PARTICIPANT_NUMBER_REACHED');
						}
						else
						{
							$body1 .= JTEXT::_('COM_MATUKIO_ORGANISER_REGISTERED_YOU') . " " . JTEXT::_('COM_MATUKIO_YOU_ARE_BOOKED_ON_THE_WAITING_LIST');
						}
					}
					else
					{
						$body1 .= JTEXT::_('COM_MATUKIO_ORGANISER_REGISTERED_YOU');
					}

					$body2 .= JTEXT::_('COM_MATUKIO_YOU_HAVE_REGISTRED_PARTICIPANT_FOR');
					break;
				case 9:
					$body1 .= JTEXT::_('COM_MATUKIO_ORGANISER_HAS_REPUBLISHED_EVENT');
					$body2 .= JTEXT::_('COM_MATUKIO_THE_BOOKING_OF_THE_PARTICIPANT_IS_VALID_AGAIN');
					break;
				case 10:
					$body1 .= JTEXT::_('COM_MATUKIO_ORGANISER_CANCELLED');
					$body2 .= JTEXT::_('COM_MATUKIO_BOOKING_NO_LONGER_VALID');
					break;
				case 11:
					$body1 .= JTEXT::_('COM_MATUKIO_ORGANISER_UPDATED_YOUR_BOOKING');
					$body2 .= JTEXT::_('');
					break;
				case 12:
					$body1 .= JTEXT::_('COM_MATUKIO_EVENT_IS_TAKING_PLACE');
					break;
				case 13:
					$body1 .= JTEXT::_('COM_MATUKIO_ORGANIZER_EVENT_IS_TAKING_PLACE');
					break;
				case 14:
					$body1 .= JTEXT::_('COM_MATUKIO_ORGANIZER_EVENT_HAS_TAKEN_PLACE');
					break;
				case 15:
					// Invoice
					break;
			}

			$abody = "\n<head>\n<style type=\"text/css\">\n<!--\nbody {\nfont-family: Verdana, Tahoma, Arial;\nfont-size:12pt;\n}\n-->\n</style></head><body>";
			$sender = $mainframe->getCfg('fromname');
			$from = $mainframe->getCfg('mailfrom');
			$htxt = "";

			if ($event->semnum != "")
			{
				$htxt = " " . $event->semnum;
			}

			$subject = JTEXT::_('COM_MATUKIO_EVENT') . $htxt . ": " . $event->title;
			$subject = JMailHelper::cleanSubject($subject);

			$replyname = $publisher->name;
			$replyto = $publisher->email;
			$email = $user->email;

			if ($art == 1 || $art == 2 || $art == 3 || $art == 15)
			{
				// New booking templates @since  2.2.0
				if (($art == 2 || $art == 3) && MatukioHelperSettings::_("booking_stornoconfirmation") == 0)
				{
					// The user should get no storno confirmationen email
					return;
				}

				$start = $body1;

				if (!empty($body1))
				{
					$start .= " \n";
				}

				if ($booking->status == MatukioHelperUtilsBooking::$WAITLIST && $art == 1)
				{
					$start .= JTEXT::_('COM_MATUKIO_YOU_ARE_BOOKED_ON_THE_WAITING_LIST');
				}
				elseif ($booking->status == MatukioHelperUtilsBooking::$ACTIVE && $art == 1)
				{
					$start .= JTEXT::_('COM_MATUKIO_YOUR_BOOKING_IS_ACTIVE_AND_RESERVED');
				}
				elseif ($booking->status == MatukioHelperUtilsBooking::$PENDING && $art == 1)
				{
					$start .= JTEXT::_('COM_MATUKIO_YOUR_BOOKING_IS_PENDING_AND_NOT_RESERVED');
				}

				$tmpl_name = MatukioHelperTemplates::getEmailTemplateName($art);
				$tmpl = MatukioHelperTemplates::getEmailBody($tmpl_name, $event, $booking);

				// Use HTML or text E-Mail
				if (MatukioHelperSettings::getSettings('email_html', 1))
				{
					// Start html output
					$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
					$body .= '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
					$body .= "<head>\n";
					$body .= "</head>\n";
					$body .= "<body>\n";
					$body .= $start . "<br />" . $tmpl->value;
					$body .= "</body>\n</html>";
				}
				else
				{
					$body = $start . "\n" . $tmpl->value_text;
				}

				$subject = $tmpl->subject;
				$mailer = JFactory::getMailer();

				if ($art == 1 && MatukioHelperSettings::getSettings('sendmail_ticket', 1)
					&& $booking->status == MatukioHelperUtilsBooking::$ACTIVE)
				{
					$t_sub = JText::_("COM_MATUKIO_TICKET");
					$ticket_pdf = MatukioHelperPDF::getTicket($booking, $event, $t_sub, "S");

					$fn = "ticket-" . MatukioHelperUtilsBooking::getBookingId($booking->id) . ".pdf";
					$mailer->AddStringAttachment($ticket_pdf, $fn, 'base64', 'application/pdf');
				}

				// Check if we really want to send this E-Mail
				if (MatukioHelperSettings::getSettings('sendmail_teilnehmer', 1) > 0 AND $art < 11)
				{
					$success = $mailer->sendMail(
						$from, $sender, $email, $subject, $body, MatukioHelperSettings::getSettings('email_html', 1),
						null, null, null, $replyto, $replyname
					);
				}

				if (MatukioHelperSettings::getSettings('sendmail_owner', 1) > 0 AND $art < 11)
				{
					$mailer->ClearAllRecipients();

					$success = $mailer->sendMail(
						$from, $sender, $publisher->email, $subject, $body, MatukioHelperSettings::getSettings('email_html', 1),
						null, null, null, $replyto, $replyname
					);
				}

				// E-Mail to Admin / Operator etc.
				if (MatukioHelperSettings::getSettings('sendmail_operator', '') != "" AND $art < 11)
				{
					$mailer->ClearAllRecipients();

					$success = $mailer->sendMail(
						$from, $sender, explode(",", MatukioHelperSettings::getSettings('sendmail_operator', '')), $subject,
						$body, MatukioHelperSettings::getSettings('email_html', 1),
						null, null, null, $replyto, $replyname
					);
				}

				// We need to clear attachements here (Ticket etc.)
				$mailer->ClearAttachments();
				$mailer->ClearAllRecipients();

				if (($art == 1 || $art == 15)
					&& MatukioHelperSettings::getSettings('sendmail_invoice', 1)
					&& $booking->status == MatukioHelperUtilsBooking::$ACTIVE
					&& $event->fees > 0
					&& $send_invoice)
				{
					$invoice = MatukioHelperTemplates::getEmailBody("invoice_email", $event, $booking);
					$inv_body = $invoice->value;

					$inv_subject = $invoice->subject;
					$inv_pdf = MatukioHelperPDF::getInvoice($booking, $event, $inv_subject, "S");

					$fn = "invoice-" . MatukioHelperUtilsBooking::getBookingId($booking->id) . ".pdf";
					$mailer->AddStringAttachment($inv_pdf, $fn, 'base64', 'application/pdf');

					if (MatukioHelperSettings::getSettings('sendmail_teilnehmer', 1) > 0)
					{
						$success = $mailer->sendMail(
							$from, $sender, $email, $inv_subject, $inv_body, 1,
							null, null, null, $replyto, $replyname
						);

						$mailer->ClearAllRecipients();
					}

					// E-Mail Organizer
					if (MatukioHelperSettings::getSettings('sendmail_owner', 1) > 0)
					{
						$success = $mailer->sendMail(
							$from, $sender, $publisher->email, $inv_subject,
							$inv_body, 1,
							null, null, null, $replyto, $replyname
						);

						$mailer->ClearAllRecipients();
					}

					// E-Mail to Admin / Operator etc.
					if (MatukioHelperSettings::getSettings('sendmail_operator', '') != '')
					{
						$success = $mailer->sendMail(
							$from, $sender, explode(",", MatukioHelperSettings::getSettings('sendmail_operator', '')), $inv_subject,
							$inv_body, 1,
							null, null, null, $replyto, $replyname
						);

						$mailer->ClearAllRecipients();
					}
				}
			}
			elseif ($art == 13 || $art == 14)
			{
				// E-Mails only to organizers (not to bookings!)
				$body = $abody . $body1 . self::getEmailBody($event, $booking, $user);
				$mailer = JFactory::getMailer();

				if ($art == 13)
				{
					// ADD CSV list
					$fn = "bookings-" . $event->title . ".csv";

					$csvcontent = MatukioHelperUtilsEvents::generateCSVFile(false, $event->id, null, $event);

					$mailer->AddStringAttachment($csvcontent, $fn, 'base64', 'application/octet-stream');
				}

				if (MatukioHelperSettings::getSettings('sendmail_owner', 1) > 0)
				{
					$success = $mailer->sendMail(
						$from, $sender, $publisher->email, $subject, $body, 1,
						null, null, null, $replyto, $replyname
					);

					$mailer->ClearAllRecipients();
				}

				// E-Mail to Admin / Operator etc.
				if (MatukioHelperSettings::getSettings('sendmail_operator', '') != "")
				{
					$success = $mailer->sendMail(
						$from, $sender, explode(",", MatukioHelperSettings::getSettings('sendmail_operator', '')), $subject,
						$body, MatukioHelperSettings::getSettings('email_html', 1),
						null, null, null, $replyto, $replyname
					);

					$mailer->ClearAllRecipients();
				}
			}
			else
			{
				// Old ones
				$body = $abody . $body1 . self::getEmailBody($event, $booking, $user);
				$mailer = JFactory::getMailer();

				if (MatukioHelperSettings::getSettings('sendmail_teilnehmer', 1) > 0)
				{
					$success = $mailer->sendMail(
						$from, $sender, $email, $subject, $body, 1,
						null, null, null, $replyto, $replyname
					);

					$mailer->ClearAllRecipients();
				}

				if (MatukioHelperSettings::getSettings('sendmail_owner', 1) > 0)
				{
					$success = $mailer->sendMail(
						$from, $sender, $publisher->email, $subject, $body, 1,
						null, null, null, $replyto, $replyname
					);

					$mailer->ClearAllRecipients();
				}

				// E-Mail to Admin / Operator etc.
				if (MatukioHelperSettings::getSettings('sendmail_operator', '') != "")
				{
					$success = $mailer->sendMail(
						$from, $sender, explode(",", MatukioHelperSettings::getSettings('sendmail_operator', '')), $subject,
						$body, MatukioHelperSettings::getSettings('email_html', 1),
						null, null, null, $replyto, $replyname
					);

					$mailer->ClearAllRecipients();
				}
			}
		}
	}

	// ++++++++++++++++++++++++++++++++++++++
	// +++ Email-Koerper ausgeben         +++        sem_f049
	// ++++++++++++++++++++++++++++++++++++++

	public static function getEmailBody($row, $buchung, $user)
	{
		$gebucht = self::calculateBookedPlaces($row);
		$gebucht = $gebucht->booked;
		$freieplaetze = $row->maxpupil - $gebucht;

		if ($freieplaetze < 0)
		{
			$freieplaetze = 0;
		}

		$body = "<p>\n<table cellpadding=\"2\" border=\"0\" width=\"100%\">";

		if (count($buchung) > 0)
		{
			$body .= "\n<tr><td><b>" . JTEXT::_('COM_MATUKIO_NAME') . "</b>: </td><td>" . $buchung->name . " (" . $user->name . ")" . "</td></tr>";
			$body .= "\n<tr><td><b>" . JTEXT::_('COM_MATUKIO_EMAIL') . "</b>: </td><td>" . $user->email . "</td></tr>";
		}

		if (count($buchung) > 0)
		{
			$body .= "\n<tr><td><b>" . JTEXT::_('COM_MATUKIO_BOOKING_ID') . "</b>: </td><td>" . MatukioHelperUtilsBooking::getBookingId($buchung->id) . "</td></tr>";
			$body .= "\n<tr><td colspan=\"2\"><hr></td></tr>";
			$body .= "\n<tr><td colspan=\"2\"><b>" . JTEXT::_('COM_MATUKIO_ADDITIONAL_INFO') . "</b></td></tr>";
			$zusfeld = self::getAdditionalFieldsFrontend($row);
			$zusbuch = self::getAdditionalFieldsFrontend($buchung);

			for ($i = 0; $i < count($zusfeld[0]); $i++)
			{
				if ($zusfeld[0][$i] != "")
				{
					$zusart = explode("|", $zusfeld[0][$i]);
					$body .= "\n<tr><td>" . $zusart[0] . ": </td><td>" . $zusbuch[0][$i] . "</td></tr>";
				}
			}

			if ($row->nrbooked > 1)
			{
				$body .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_BOOKED_PLACES') . ": </td><td>" . $buchung->nrbooked . "</td></tr>";
			}
		}
		$body .= "\n<tr><td colspan=\"2\"><hr></td></tr>";
		$body .= "\n<tr><td colspan=\"2\"><b>" . $row->title . "</b></td></tr>";
		$body .= "\n<tr><td colspan=\"2\">" . $row->shortdesc . "</td></tr>";

		if ($row->semnum != "")
		{
			$body .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_NUMBER') . ": </td><td>" . $row->semnum . "</td></tr>";
		}

		if ($row->showbegin > 0)
		{
			$body .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_BEGIN') . ": </td><td>" . JHTML::_('date', $row->begin,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')) . "</td></tr>";
		}

		if ($row->showend > 0)
		{
			$body .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_END') . ": </td><td>" . JHTML::_('date', $row->end,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')) . "</td></tr>";
		}

		if ($row->showbooked > 0)
		{
			$body .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ": </td><td>" . JHTML::_('date', $row->booked,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')) . "</td></tr>";
		}

		if ($row->teacher != "")
		{
			$body .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_TUTOR') . ": </td><td>" . $row->teacher . "</td></tr>";
		}


		if ($row->target != "")
		{
			$body .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_TARGET_GROUP') . ": </td><td>" . $row->target . "</td></tr>";
		}

		$body .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_CITY') . ": </td><td>" . $row->place . "</td></tr>";

		if (MatukioHelperSettings::getSettings('event_showinfoline', 1) > 0)
		{
			$body .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_MAX_PARTICIPANT') . ": </td><td>" . $row->maxpupil . "</td></tr>";
		}

		if ($row->fees > 0)
		{
			$body .= "\n<tr><td>" . JTEXT::_('COM_MATUKIO_FEES') . ": </td><td>" . MatukioHelperSettings::getSettings('currency_symbol', '$') . " " . $buchung->payment_brutto;

			if (MatukioHelperSettings::getSettings('frontend_usermehrereplaetze', 1) > 0)
			{
				// $body .= " " . JTEXT::_('COM_MATUKIO_PRO_PERSON');
			}

			$body .= "</td></tr>";
		}

		if ($row->description != "")
		{
			$body .= "\n<tr><td colspan=\"2\">" . self::getCleanedMailText($row->description) . "</td></tr>";
		}

		$body .= "</table><p>";
		$htxt = str_replace('SEM_HOMEPAGE', "<a href=\"" . JURI::root() . "\">" . JURI::root() . "</a>", JTEXT::_('COM_MATUKIO_FOR_MORE_INFO_VISIT'));
		$body .= $htxt . "</body>";

		return $body;
	}

	// ++++++++++++++++++++++++++++++++++++++
// +++ Ausgabe saeubern                +++       sem_f066
// ++++++++++++++++++++++++++++++++++++++

	public static function getCleanedMailText($text)
	{
		preg_match_all("`\[sem_[^\]]+\](.*)\[/sem_[^\]]+\]`U", $text, $ausgabe);

		for ($i = 0; $i < count($ausgabe[0]); $i++)
		{
			$text = str_replace($ausgabe[0][$i], "", $text);
		}

		preg_match_all("`\{[^\}]+\}`U", $text, $ausgabe);

		for ($i = 0; $i < count($ausgabe[0]); $i++)
		{
			$text = str_replace($ausgabe[0][$i], "", $text);
		}

		return $text;
	}


	// ++++++++++++++++++++++++++++++++++++++
// +++ Kategorienliste ausgeben     +++        sem_f010
// ++++++++++++++++++++++++++++++++++++++

	/**
	 * Gets a category list array based on the allowed user groups
	 *
	 * @param   int  $catid  - The catid
	 *
	 * @deprecated not acl conform
	 *
	 * @throws  Exception  - if no category exists
	 * @return  array
	 */
	public static function getCategoryListArray($catid)
	{
		jimport('joomla.database.table');
		$database = JFactory::getDBO();

		$categories[] = JHTML::_('select.option', '0', JTEXT::_('COM_MATUKIO_CHOOSE_CATEGORY'));
		$database->setQuery("Select id AS value, title AS text FROM #__categories WHERE extension = 'com_matukio' ORDER BY lft");
		$dats = $database->loadObjectList();

		if (!count($dats))
		{
			throw new Exception("Please create a category first!", 500);
		}

		$categories = array_merge($categories, (array) $dats);
		$clist = JHTML::_(
			'select.genericlist', $categories, 'caid', 'class="form-control validate[required]"',
			'value', 'text', intval($catid)
		);
		$ilist = array();

		foreach ((array) $dats as $el)
		{
			$el->image = "";
			$bild = "";

			if ($el->image != "")
			{
				$bild->id = $el->value;
				$bild->image = $el->image;
				$ilist[] = $bild;
			}
		}

		return array($clist, $ilist);
	}


// +++++++++++++++++++++++++++++++++++++++++++++++
// +++ Templateliste erstellen                 +++        sem_f057
// +++++++++++++++++++++++++++++++++++++++++++++++

	public static function getTemplateListSelect($vorlage, $art)
	{
		$html = "";
		$database = JFactory::getDBO();

		$my = JFactory::getuser();
		$where = array();

		// Nur veroeffentlichte Kurse anzeigen
		$where[] = "published = '1'";
		$where[] = "pattern != ''";
		$where[] = "publisher = '" . $my->id . "'";

		// Nur Kurse anzeigen, deren Kategorie fuer den Benutzer erlaubt ist
		$groups = implode(',', $my->getAuthorisedViewLevels());
		$query = $database->getQuery(true);
		$query->select("id, access")->from("#__categories")->where(
			array("extension = " . $database->quote("com_matukio"),
			"published = 1", "access in (" . $groups . ")")
		);

		$database->setQuery($query);
		$cats = $database->loadObjectList();
		$allowedcat = array();

		foreach ((array) $cats AS $cat)
		{
			$allowedcat[] = $cat->id;
		}

		if (count($allowedcat) > 0)
		{
			$allowedcat = implode(',', $allowedcat);
			$where[] = "catid IN ($allowedcat)";
		}

		$database->setQuery("SELECT * FROM #__matukio"
			. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
			. "\nORDER BY pattern"
		);
		$rows = $database->loadObjectList();
		$patterns = array();
		$patterns[] = JHTML::_('select.option', '', JTEXT::_('COM_MATUKIO_CHOOSE_TEMPLATE'));

		foreach ($rows AS $row)
		{
			$patterns[] = JHTML::_('select.option', $row->id, $row->pattern);
		}

		$htxt = JTEXT::_('COM_MATUKIO_TEMPLATE') . ": ";
		$disabled = "";

		if ($vorlage == 0)
		{
			$disabled = " disabled";
		}

		if ($art == 1)
		{
			if (count($patterns) > 1)
			{
				$htxt .= JHTML::_('select.genericlist', $patterns, 'vorlage', 'class="sem_inputbox" size="1"
                    onChange="form.cid.value=form.vorlage.value;form.task.value=9;form.submit();"', 'value', 'text', $vorlage
				);
				$htxt .= " <button class=\"button\" id=\"tmpldel\" style=\"cursor:pointer;\" type=\"button\"
                    onclick=\"form.cid.value=form.vorlage.value;form.task.value=11;form.submit();\"" . $disabled . ">
                    <img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "1516.png\" border=\"0\"
                    align=\"absmiddle\">&nbsp;" . JTEXT::_('COM_MATUKIO_DELETE') . "</button>";
			}
			else
			{
				$htxt .= "<input type=\"hidden\" name=\"vorlage\" value=\"0\">";
			}

			$htxt .= " <input type=\"text\" name=\"pattern\" id=\"pattern\" class=\"sem_inputbox\" value=\"\"
            onKeyup=\"if(this.value=='') {form.tmplsave.disabled=true;} else {form.tmplsave.disabled=false;}\" />";
			$htxt .= " <button class=\"button\" id=\"tmplsave\" style=\"cursor:pointer;\" type=\"button\"
                onclick=\"form.task.value=10;form.submit();\" disabled><img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
				. "1416.png\" border=\"0\" align=\"absmiddle\">&nbsp;" . JTEXT::_('COM_MATUKIO_SAVE') . "</button>";
			$html = "<tr>" . self::getTableCell($htxt, 'd', 'c', '80%', 'sem_nav', 2) . "</tr>";
		}
		elseif ($art == 2)
		{
			if (count($patterns) > 1)
			{
				$htxt .= JHTML::_('select.genericlist', $patterns, 'vorlage', 'class="sem_inputbox" size="1" '
					. 'onChange="form.id.value=form.vorlage.value;form.task.value=\'12\';form.submit();"', 'value', 'text', $vorlage
				);

				$html = "<tr>" . self::getTableCell($htxt, 'd', 'c', '80%', 'sem_nav', 2) . "</tr>";
			}
		}

		return $html;
	}

	// ++++++++++++++++++++++++++++++++++++++
// +++ Veranstalterliste ausgeben     +++     sem_f009
// ++++++++++++++++++++++++++++++++++++++

	public static function getOranizerList($pub)
	{
		// TODO update !!!
		return JHTML::_('list.users', "publisher", $pub, false, ' class="form-control"', "name", 0);
	}

	/**
	 * Returns the html event edit fields
	 * sem_f008
	 *
	 * @param   object  $row       - The event
	 * @param   int     $art       - The art (no idea)
	 * @param   bool    $frontend  - Is this form shown in the frontend or backend?!
	 *
	 * @return string
	 */
	public static function getEventEdit($row, $art, $frontend = false)
	{
		jimport('joomla.database.table');

		// We need Bootstrap since 3.0
		if ($frontend)
		{
			CompojoomHtmlBehavior::bootstrap31(true, false, true, false);
			JHTML::_('script', 'media/lib_compojoom/js/jquery.radiobtns.js');
		}

		MatukioHelperUtilsBasic::loadValidation();

		JHTML::_('script', 'media/com_matukio/js/select2.min.js');
		JHTML::_('script', 'media/com_matukio/js/recurring.jquery.js');
		JHTML::_('stylesheet', 'media/com_matukio/css/select2.css');
		JHTML::_('stylesheet', 'media/com_matukio/css/select2-bootstrap.css');

		$doc = JFactory::getDocument();

		// Small css fixes
		$doc->addStyleDeclaration('
			.table td { vertical-align: middle !important; }
		');

		// Add JS for different fees
		$doc->addScriptDeclaration('
			(function ($) {
				$( document ).ready(function( $ ) {
					$("#adminForm").validationEngine();

					var numfees = $("#numfees").val();

					$("#add_fee").click(function() {
						$.get( "index.php?option=com_matukio&format=raw&view=requests&task=get_override_fee_edit_row",
						{ num: numfees } )
						.done(function( data ) {
							$( "#feecont" ).append( data );
							$("input .btn").button();

							numfees++;
							$("#numfees").val(numfees);

	                        // Turn radios into btn-group
							$.getScript( "' . JUri::root() . 'media/com_matukio/js/radiobtns.js" );
						});
					});

					$(".compojoom-bootstrap").mat_recurring({
					});

					// Turn checkboxes into btn-group
				$(\'.checkbox.btn-group label\').addClass(\'btn\');

				// Isis template and others may already have done this so remove these!
				$(".checkbox.btn-group label").unbind(\'click\');
				$(".checkbox.btn-group label input[type=\'checkbox\']").unbind(\'click\');

				$(".checkbox.btn-group label").click(function(event) {
					event || (event = window.event);

					// stop the event being triggered twice is click on input AND label outside it!
					if (event.target.tagName.toUpperCase()=="INPUT"){
						//event.preventDefault();
						return;
					}

					var label = $(this);
					var input = $(\'#\' + label.attr(\'for\'));

					if (input.prop(\'disabled\')) {
						label.removeClass(\'active btn-success btn-danger btn-primary\');
						input.prop(\'checked\', false);
						event.stopImmediatePropagation();
						return;
					}

					if (!input.prop(\'checked\')) {
						label.addClass(\'active btn-success\');
					} else {
						label.removeClass(\'active btn-success btn-danger btn-primary\');
					}

					// bootstrap takes care of the checkboxes themselves!
				});

				$(".btn-group input[type=checkbox]").each(function() {
					var input = $(this);
					input.css(\'display\',\'none\');
				});

				$("#place_id").change(function(){
					var pval = $(this).val();

					if (pval == "0") {
						$("#custom_place").show();
						$("#gmaps").show();
					} else {
						$("#custom_place").hide();
						$("#gmaps").hide();
					}
				});

				})
			})(jQuery);
		');

		$editor = JFactory::getEditor();
		$catlist = self::getCategoryListArray($row->catid);

		$reqfield = " <span class=\"sem_reqfield\">*</span>";

		// Vorlage
		$html = "";

		if ($art == 1 OR $art == 2)
		{
			$html = "<input type=\"hidden\" name=\"pattern\" value=\"\" />";
			$html .= "<input type=\"hidden\" name=\"vorlage\" value=\"0\" />";
		}

		if ($row->id == 0 AND ($art == 1 OR $art == 2))
		{
			// $html = self::getTemplateListSelect($row->vorlage, $art);
		}

		// Surrounding div
		// $html .= '<div class="compojoom-bootstrap">';
		$html .= '<div id="mat_event_edit">';

		$html .= '<!-- List of tabs -->
			<ul class="nav nav-tabs nav-justified">
				<li class="active">
					<a href="#basic" data-toggle="tab">' . JText::_('COM_MATUKIO_BASIC_SETTINGS') . '</a>
				</li>
				<li>
					<a href="#advanced" data-toggle="tab">' . JText::_('COM_MATUKIO_ADDITIONAL_SETTINGS') . '</a>
				</li>
				<li>
					<a href="#eventfields" data-toggle="tab">' . JText::_('COM_MATUKIO_GENERAL_INPUT_FIELDS') . '</a>
				</li>
				<li>
					<a href="#files" data-toggle="tab">' . JText::_('COM_MATUKIO_FILES') . '</a>
				</li>
				<li>
					<a href="#overrides" data-toggle="tab">' . JText::_('COM_MATUKIO_OVERRIDES') . '</a>
				</li>
			</ul>';

		// Basics
		$html .= '<div class="tab-content">';
		$html .= '<div id="basic" class="tab-pane active">';

		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-offset-2 col-sm-10">';
		$html .= JTEXT::_('COM_MATUKIO_SETTINGS_NEEDED');
		$html .= '</div>';
		$html .= '</div>';

		// Vorlagenname und Besitzer
		if ($art == 3)
		{
			$html .= "<tr>" . self::getTableCell(JTEXT::_('COM_MATUKIO_TEMPLATE') . ':', 'd', 'r', '20%', 'sem_edit')
				. self::getTableCell(
					"<input class=\"sem_inputbox\" type=\"text\" name=\"pattern\" size=\"50\" maxlength=\"100\"
			value=\"" . $row->pattern . "\" />" . $reqfield, 'd', 'l', '80%', 'sem_edit') . "</tr>";
			$html .= "<tr>" . self::getTableCell(JTEXT::_('COM_MATUKIO_OWNER') . ':', 'd', 'r', '20%', 'sem_edit')
				. self::getTableCell(self::getOranizerList($row->publisher) . $reqfield, 'd', 'l', '80%', 'sem_edit') . "</tr>";
			$reqfield = "";
		}

		// ID der Veranstaltung
		if ($row->id < 1)
		{
			$htxt = JTEXT::_('COM_MATUKIO_ID_NOT_CREATED');
			$htx2 = JTEXT::_('COM_MATUKIO_SHOULD_REGISTERED_USERS_RECEIVE_MAIL');
			$htx3 = JTEXT::_('COM_MATUKIO_NEW_EVENT_PUBLISHED_INTERESTED_SEE_HOMEPAGE');
			$htx4 = "";
			$htx5 = " checked=\"checked\"";
		}
		else
		{
			$htxt = $row->id;
			$htx2 = JTEXT::_('COM_MATUKIO_INFORM_PER_EMAIL');
			$htx3 = JTEXT::_('COM_MATUKIO_EVENTS_DATAS_CHANGED');

			if ($row->cancelled == 0)
			{
				$htx4 = "";
				$htx5 = " checked=\"checked\"";

				if ($art != 3)
				{
					$htx4 = " onClick=\"infotext.value='" . JTEXT::_('COM_MATUKIO_ORGANISER_CANCELLED') . "'\"";
					$htx5 = " onClick=\"infotext.value='" . JTEXT::_('COM_MATUKIO_EVENTS_DATAS_CHANGED') . "'\"" . $htx5;
				}
			}
			else
			{
				$htx4 = " checked=\"checked\"";
				$htx5 = "";

				if ($art != 3)
				{
					$htx4 = " onClick=\"infotext.value='" . JTEXT::_('COM_MATUKIO_EVENTS_DATAS_CHANGED') . "'\"" . $htx4;
					$htx5 = " onClick=\"infotext.value='" . JTEXT::_('COM_MATUKIO_ORGANISER_HAS_REPUBLISHED_EVENT') . "'\"";
				}
			}
		}

		if ($row->id > 0 && false)
		{
			$html .= "<tr>" . self::getTableCell(
					JTEXT::_('COM_MATUKIO_ID')
				. MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_AUTO_ID')), 'd', 'l', '20%', 'sem_edit'
				);
			$html .= self::getTableCell($htxt, 'd', 'l', '80%', 'sem_edit') . "</tr>";
		}

		// Titel
		$html .= '<div class="form-group">';
		$html .= '<label for="title" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_TITLE') . $reqfield . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= "<input class=\"form-control validate[required]\" type=\"text\" name=\"title\" id=\"title\"
            maxlength=\"250\" value=\"" . $row->title . "\" />";
		$html .= '</div>';
		$html .= '</div>';

		// Category
		$htxt = $catlist[0];

		if (MatukioHelperSettings::getSettings('event_image', 1) == 1)
		{
			foreach ($catlist[1] as $el)
			{
				$htxt .= "<input type=\"hidden\" id=\"im" . $el->id . "\" value=\"" . $el->image . "\" />";
			}
		}

		$html .= '<div class="form-group">';
		$html .= '<label for="category" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_CATEGORY')
			. MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_EVENT_ASSIGNED_CATEGORY')) . $reqfield . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= $htxt;
		$html .= '</div>';
		$html .= '</div>';

		// Event number
		$html .= '<div class="form-group">';
		$html .= '<label for="semnum" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_NUMBER')
			. MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_UNIQUE_NUMBER')) . $reqfield . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= "<input class=\"form-control validate[required]\"
				type=\"text\" id=\"semnum\" name=\"semnum\" maxlength=\"100\" value=\""
				. $row->semnum . "\" />";
		$html .= '</div>';
		$html .= '</div>';

		$radios = array();
		$radios[] = JHTML::_('select.option', 1, JTEXT::_('COM_MATUKIO_YES'));
		$radios[] = JHTML::_('select.option', 0, JTEXT::_('COM_MATUKIO_NO'));

		if ($row->showbegin == "")
		{
			$row->showbegin = 1;
		}

		if ($row->showend == "")
		{
			$row->showend = 1;
		}

		if ($row->showbooked == "")
		{
			$row->showbooked = 1;
		}

		// Event begin
		$htxt = "<div class=\"col-sm-4\">" . JHTML::_('calendar', JHtml::_('date', $row->begin, 'Y-m-d H:i:s'), '_begin_date', '_begin_date',
			'%Y-%m-%d %H:%M:%S', array('class' => 'form-control validate[required]')
		);

		$radio_showbegin = MatukioHelperInput::getRadioButtonBool("showbegin", "showbegin", $row->showbegin);

		$htxt .= "</div> <div class=\"col-sm-4\">" . JTEXT::_('COM_MATUKIO_DISPLAY') . " " . $radio_showbegin . "</div>";

		$html .= '<div class="form-group">';
		$html .= '<label for="_begin_date" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_BEGIN')
			. MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_DATE_TIME_FORMAT')) . $reqfield . '</label>';
		$html .= $htxt;
		$html .= '</div>';

		// Event end
		$htxt = "<div class=\"col-sm-4\">" . JHTML::_('calendar', JHtml::_('date', $row->end, 'Y-m-d H:i:s'), '_end_date', '_end_date',
				'%Y-%m-%d %H:%M:%S', array('class' => 'form-control validate[required]')
			);

		$radio_showend = MatukioHelperInput::getRadioButtonBool("showend", "showend", $row->showend);

		$htxt .= "</div> <div class=\"col-sm-4\">" . JTEXT::_('COM_MATUKIO_DISPLAY') . " " . $radio_showend . "</div>";

		$html .= '<div class="form-group">';
		$html .= '<label for="_end_date" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_END')
			. MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_DATE_TIME_FORMAT')) . $reqfield . '</label>';
		$html .= $htxt;
		$html .= '</div>';

		// Anmeldeschluss
		// Closing end
		$htxt = "<div class=\"col-sm-4\">" . JHTML::_('calendar', JHtml::_('date', $row->booked, 'Y-m-d H:i:s'), '_booked_date', '_booked_date',
				'%Y-%m-%d %H:%M:%S', array('class' => 'form-control validate[required]')
			);

		$radio_showbooked = MatukioHelperInput::getRadioButtonBool("showbooked", "showbooked", $row->showbooked);

		$htxt .= "</div> <div class=\"col-sm-4\">" . JTEXT::_('COM_MATUKIO_DISPLAY') . " " . $radio_showbooked . "</div>";

		$html .= '<div class="form-group">';
		$html .= '<label for="_booked_date" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_CLOSING_DATE')
			. MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_DATE_TIME_FORMAT')) . $reqfield . '</label>';
		$html .= $htxt;
		$html .= '</div>';

		// RECURRING
		$html .= '<div class="form-group">';
		$html .= '<label for="recurring" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_IS_RECURRING')
			. $reqfield . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= MatukioHelperInput::getRadioButtonBool("recurring", "recurring", $row->recurring);
		$html .= '</div>';
		$html .= '</div>';

		// Recurring events
		$rstyle = "";

		if (!$row->recurring)
		{
			$rstyle = ' style="display: none;"';
		}

		$html .= '<div id="reccuring-gen"' . $rstyle . '>';


		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-offset-2 col-sm-10">';
		$html .= JTEXT::_('COM_MATUKIOR_RECURRING_INTRO');
		$html .= '</div>';
		$html .= '</div>';

		// Check for old events
		if (empty($row->recurring_type))
		{
			$row->recurring_type = "daily";
		}

		$repeat_type = array();
		$repeat_type["daily"] = JText::_("COM_MATUKIO_REPEAT_DAILY");
		$repeat_type["weekly"] = JText::_("COM_MATUKIO_REPEAT_WEEKLY");
		$repeat_type["monthly"] = JText::_("COM_MATUKIO_REPEAT_MONTHLY");
		$repeat_type["yearly"] = JText::_("COM_MATUKIO_REPEAT_YEARLY");

		$recurring = MatukioHelperInput::getRadioButton("recurring_type", "recurring_type", $repeat_type, $row->recurring_type);

		$html .= '<div class="form-group">';
		$html .= '<label for="recurring_type" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_REPEAT_TYPE') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= $recurring;
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-offset-2 col-sm-10">';

		$html .= '<div id="recurring_daily">';
		$html .= '</div>';

		$html .= '<div id="recurring_monthly">';

		$repeat_week = array();
		$repeat_week[1] = JText::_("COM_MATUKIO_RECURRING_WEEK1");
		$repeat_week[2] = JText::_("COM_MATUKIO_RECURRING_WEEK2");
		$repeat_week[3] = JText::_("COM_MATUKIO_RECURRING_WEEK3");
		$repeat_week[4] = JText::_("COM_MATUKIO_RECURRING_WEEK4");
		$repeat_week[5] = JText::_("COM_MATUKIO_RECURRING_WEEK5");

		$recurring_month_week = MatukioHelperInput::getCheckboxButton("recurring_month_week", "recurring_month_week",
			$repeat_week, $row->recurring_month_week
		);

		$html .= $recurring_month_week . "<br />";
		$html .= "<br />";
		$html .= '</div>';

		$html .= '<div id="recurring_weekly">';

		$repeat_week_day = array();
		$repeat_week_day["Monday"] = JText::_("COM_MATUKIO_MONDAY");
		$repeat_week_day["Tuesday"] = JText::_("COM_MATUKIO_TUESDAY");
		$repeat_week_day["Wednesday"] = JText::_("COM_MATUKIO_WEDNESDAY");
		$repeat_week_day["Thursday"] = JText::_("COM_MATUKIO_THURSDAY");
		$repeat_week_day["Friday"] = JText::_("COM_MATUKIO_FRIDAY");
		$repeat_week_day["Saturday"] = JText::_("COM_MATUKIO_SATURDAY");
		$repeat_week_day["Sunday"] = JText::_("COM_MATUKIO_SUNDAY");

		$recurring_week_day = MatukioHelperInput::getCheckboxButton("recurring_week_day", "recurring_week_day", $repeat_week_day, $row->recurring_week_day);

		$html .= $recurring_week_day;
		$html .= "<br />";
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="form-group">';
		$html .= '<label for="recurring_count" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_RECURRING_REPEAT_COUNT') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= '<input type="text" name="recurring_count" id="recurring_count" value="' . $row->recurring_count . '" class="form-control" />';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="form-group">';
		$html .= '<label for="recurring_count" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_RECURRING_REPEAT_UNTIL') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= '<input type="text" name="recurring_until" id="recurring_until" value="' . $row->recurring_until . '" class="form-control" />';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-offset-2 col-sm-10">';
		$html .= '<button id="generateRecurring" class="btn btn-success">' . JText::_("COM_MATUKIO_RECURRING_BUTTON_GENERATE") . '</button>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-offset-2 col-sm-10">';
		$html .= '<div id="generated_events">' . '</div>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<input type="hidden" name="recurring_edited" id="recurring_edited" value="0" />';
		$html .= '</div>';

		// Short description
		$html .= '<div class="form-group">';
		$html .= '<label for="shortdesc" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_BRIEF_DESCRIPTION')
			. MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_BRIEF_DESCRIPTION_DESC')) . $reqfield . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= "<textarea class=\"form-control validate[required]\" rows=\"3\" name=\"shortdesc\" placeholder=\""
			. JText::_("COM_MATUKIO_BRIEF_DESCRIPTION_DESC") . "\">" . $row->shortdesc . "</textarea>";
		$html .= '</div>';
		$html .= '</div>';

		// Locations
		$locations = array(
			JHTML::_('select.option', '0', JText::_('COM_MATUKIO_CUSTOM_LOCATION'))
		);

		$dblocs = self::getLocations();

		if ($dblocs)
		{
			foreach ($dblocs as $l)
			{
				$locations[] = JHTML::_('select.option', $l->id, JText::_($l->title));
			}
		}

		$select_locations = JHTML::_('select.genericlist', $locations, 'place_id', 'class="form-control chzn-single"', 'value', 'text', $row->place_id);

		$html .= '<div class="form-group">';
		$html .= '<label for="place_id" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_CITY')
			. $reqfield . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= $select_locations;
		$html .= '</div>';
		$html .= '</div>';

		$pstyle = "";

		if ($row->place_id > 0)
		{
			$pstyle = ' style="display:none"';
		}

		$html .= '<div id="custom_place" class="form-group"' . $pstyle . '>';
		$html .= '<label for="place" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_CUSTOM_LOCATION')
			. '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= "<textarea class=\"form-control\" rows=\"3\" name=\"place\">" . $row->place . "</textarea>";
		$html .= '</div>';
		$html .= '</div>';

		// Organiser
		if ($art != 3 && (!$frontend || MatukioHelperSettings::_("frontend_organizer_allevent", 1)))
		{
			$html .= '<div class="form-group">';
			$html .= '<label for="publisher" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_ORGANISER')
				. MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_ORGANISER_MANAGE_FRONTEND')) . $reqfield . '</label>';
			$html .= '<div class="col-sm-10">';
			$html .= self::getOranizerList($row->publisher);
			$html .= '</div>';
			$html .= '</div>';
		}
		else
		{
			$html .= '<input type="hidden" name="publisher" value="' . JFactory::getUser()->id . '" />';
		}

		$webinar = MatukioHelperInput::getRadioButtonBool("webinar", "webinar", $row->webinar);

		$html .= '<div class="form-group">';
		$html .= '<label for="webinar" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_WEBINAR')
			. $reqfield . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= $webinar;
		$html .= '</div>';
		$html .= '</div>';

		// Available seats
		$htxt = "<div class=\"col-sm-6\"><input class=\"form-control validate[required, custom[integer]]\" type=\"text\" name=\"maxpupil\" value=\""
			. $row->maxpupil . "\" /></div><div class=\"col-sm-4\"> - " . JTEXT::_('COM_MATUKIO_IF_FULLY_BOOKED') . ": ";
		$radios = array();
		$radios[] = JHTML::_('select.option', 0, JTEXT::_('COM_MATUKIO_WAITLIST'));
		$radios[] = JHTML::_('select.option', 1, JTEXT::_('COM_MATUKIO_END_BOOKING'));
		$radios[] = JHTML::_('select.option', 2, JTEXT::_('COM_MATUKIO_HIDE_EVENT'));
		$htxt .= JHTML::_('select.genericlist', $radios, 'stopbooking', 'class="inputbox"', 'value', 'text', $row->stopbooking) . "</div>";

		$html .= '<div class="form-group">';
		$html .= '<label for="webinar" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_MAX_PARTICIPANT')
			. $reqfield . '</label>';
		$html .= $htxt;
		$html .= '</div>';

		// Min. participants (since 4.3.0 for cronjob)
		$html .= '<div class="form-group">';
		$html .= '<label for="hot_event" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_MIN_PARTICIPANTS') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= "<input class=\"form-control\" type=\"text\" name=\"minpupil\" id=\"minpupil\" value=\"" . $row->minpupil . "\" />";
		$html .= '</div>';
		$html .= '</div>';

		// Max. bookable places
		$bookableplaces = $row->nrbooked;

		if ($bookableplaces == "")
		{
			$bookableplaces = 1;
		}

		if (MatukioHelperSettings::getSettings('frontend_usermehrereplaetze', 2) > 0)
		{
			$htxt = "<input class=\"inputbox form-control validate[required, custom[integer]]\" type=\"text\" name=\"nrbooked\" value=\""
				. $bookableplaces . "\" />";
		}
		else
		{
			$radios = array();
			$radios[] = JHTML::_('select.option', 0, "0");
			$radios[] = JHTML::_('select.option', 1, "1");
			$htxt = JHTML::_(
				'select.genericlist', $radios, 'nrbooked', 'class="form-control validate[required, custom[integer]]" ',
				'value', 'text', $row->nrbooked
			);
		}

		$html .= '<div class="form-group">';
		$html .= '<label for="webinar" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_MAX_BOOKABLE_PLACES')
			. MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_CANNOT_BOOK_ONLINE')) . $reqfield . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= $htxt;
		$html .= '</div>';
		$html .= '</div>';

		$html .= "</div>";

		// ### Panel 2 ###
		$html .= '<div class="tab-pane" id="advanced">';

		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-12">' . JTEXT::_('COM_MATUKIO_ADDITIONAL_SETTINGS_DESC') . '</div>';
		$html .= '</div>';

		// Description
		$htxt = $editor->display("description", $row->description, "600", "300", "50", "5");

		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-12">';
		$html .= '<label for="description" class="control-label">' . JTEXT::_('COM_MATUKIO_DESCRIPTION') . '</label><br /><br />';
		$html .= JTEXT::_('COM_MATUKIO_USE_FOLLOWING_TAGS') . $htxt;
		$html .= '</div>';
		$html .= '</div>';

		// Veranstaltungsbild
		if (MatukioHelperSettings::getSettings('event_image', 1) == 1)
		{
			jimport('joomla.filesystem.folder');
			$htxt = "";

			if (MatukioHelperSettings::getSettings('image_path', '') != "")
			{
				$htxt = trim(MatukioHelperSettings::getSettings('image_path', ''), "/") . "/";
			}

			$htxt = JPATH_SITE . "/images/" . $htxt;

			if (!is_dir($htxt))
			{
				mkdir($htxt, 0755);
			}

			$imageFiles = JFolder::files($htxt);
			$images = array(JHTML::_('select.option', '', '- ' . JText::_('COM_MATUKIO_STANDARD_IMAGE') . ' -'));

			foreach ($imageFiles as $file)
			{
				if (preg_match("/jpg|png|gif/i", $file))
				{
					$images[] = JHTML::_('select.option', $file);
				}
			}

			$imagelist = JHTML::_('select.genericlist', $images, 'image', 'class="sem_inputbox form-control" size="1" ', 'value', 'text', $row->image);
			$htxt = "<span style=\"position:absolute;display:none;border:3px solid #FF9900;background-color:#FFFFFF;\" id=\"1\"><img id=\"toolbild\"
        src=\"images/stories/" . $row->image
				. "\" /></span><span style=\"position:absolute;display:none;border:3px solid #FF9900;background-color:#FFFFFF;\"
        id=\"2\"><img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "2601.png\" /></span>";
			$htxt .= $imagelist . "&nbsp;";

			$html .= '<div class="form-group">';
			$html .= '<label for="image" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_IMAGE_FOR_OVERVIEW') . '</label>';
			$html .= '<div class="col-sm-10">';
			$html .= $htxt;
			$html .= '</div>';
			$html .= '</div>';
		}

		// Google-Maps
		$htxt = "<input class=\"sem_inputbox form-control\" type=\"text\" name=\"gmaploc\" id=\"gmaploc\" value=\"" . $row->gmaploc . "\" /> ";

		$html .= '<div id="gmaps" class="form-group">';
		$html .= '<label for="gmaploc" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_GMAPS_LOCATION') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= $htxt;
		$html .= '</div>';
		$html .= '</div>';

		// Tutor
		$html .= '<div class="form-group">';
		$html .= '<label for="teacher" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_TUTOR') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= "<input class=\"form-control\" type=\"text\" name=\"teacher\" id=\"teacher\" value=\"" . $row->teacher . "\" />";
		$html .= '</div>';
		$html .= '</div>';

		// Target
		$html .= '<div class="form-group">';
		$html .= '<label for="target" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_TARGET_GROUP') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= "<input class=\"form-control\" type=\"text\" name=\"target\" id=\"target\" value=\"" . $row->target . "\" />";
		$html .= '</div>';
		$html .= '</div>';

		// Fees
		// Yeah i know this also is true if fees is 0, but we need to check if it is "" or null
		if (empty($row->fees))
		{
			$row->fees = 0;
		}

		// Gebuehr
		$html .= '<div class="form-group">';
		$html .= '<label for="fees" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_FEES')
			. " (" . MatukioHelperSettings::getSettings('currency_symbol', '$') . ')' . " " . JTEXT::_('COM_MATUKIO_PRO_PERSON')
			. " " . JText::_("COM_MATUKIO_BRUTTO")
			. '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= "<input class=\"form-control validate[required]\" type=\"text\" name=\"fees\" id=\"fees\" value=\"" . $row->fees . "\" />";
		$html .= '</div>';
		$html .= '</div>';

		// Taxes
		$taxes = array(JHTML::_('select.option', '0', '- ' . JText::_('COM_MATUKIO_NO_TAX') . ' -'));

		$taxes_list = MatukioHelperTaxes::getTaxes();

		foreach ($taxes_list as $t)
		{
			$taxes[] = JHTML::_('select.option', $t->id, '- ' . JText::_($t->title) . ' -');
		}

		$taxlist = JHTML::_('select.genericlist', $taxes, 'tax_id', 'class="form-control chzn-single"', 'value', 'text', $row->tax_id);

		$html .= '<div class="form-group">';
		$html .= '<label for="tax_id" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_EVENT_TAX') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= $taxlist;
		$html .= '</div>';
		$html .= '</div>';

		// Different fees
		$different_fees = MatukioHelperInput::getRadioButtonBool("different_fees", "different_fees", $row->different_fees);

		$html .= '<div class="form-group">';
		$html .= '<label for="different_fees" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_DIFFERENT_FEES') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= $different_fees;
		$html .= '</div>';
		$html .= '</div>';

		// Top event
		$top_event = MatukioHelperInput::getRadioButtonBool("top_event", "top_event", $row->top_event);

		$html .= '<div class="form-group">';
		$html .= '<label for="top_event" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_TOP_EVENT') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= $top_event;
		$html .= '</div>';
		$html .= '</div>';

		// Hot event
		$hot_event = MatukioHelperInput::getRadioButtonBool("hot_event", "hot_event", $row->hot_event);

		$html .= '<div class="form-group">';
		$html .= '<label for="hot_event" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_HOT_EVENT') . '</label>';
		$html .= '<div class="col-sm-10">';
		$html .= $hot_event;
		$html .= '</div>';
		$html .= '</div>';

		// Abgesagt
/*		$htxt = MatukioHelperInput::getRadioButtonBool(
			"cancel", "cancel", $row->cancelled, "inputbox required", "required=\"required\" aria-required=\"true\""
		);

		$html .= '<div class="form-group">';
		$html .= '<label for="cancel" class="col-sm-2 control-label">' . JTEXT::_('COM_MATUKIO_CANCELLED')
			. MatukioHelperUtilsBasic::createToolTip(JTEXT::_('COM_MATUKIO_CANCELLED_EVENT_NO_BOOKINGS'))
			. '</label>' ;
		$html .= '<div class="col-sm-10">';
		$html .= $htxt . "<input type=\"hidden\" name=\"cancelled\" value=\"" . $row->cancelled . "\">";
		$html .= '</div>';
		$html .= '</div>';
*/

		$html .= "<input type=\"hidden\" name=\"cancel\" value=\"" . $row->cancelled . "\">";
		$html .= "<input type=\"hidden\" name=\"cancelled\" value=\"" . $row->cancelled . "\">";
		$html .= "</div>";

		// ### Panel 3 ###
		$html .= '<div class="tab-pane" id="eventfields">';
		$html .= "<table class=\"table\">";
		$html .= "<tr>" . self::getTableCell(
				JTEXT::_('COM_MATUKIO_FILLED_IN_ONCE') . "<br />&nbsp;<br />"
			. JTEXT::_('COM_MATUKIO_FIELD_INPUT_SPECIFIED') . "<br />&nbsp;<br />" . JTEXT::_('COM_MATUKIO_FIELD_TIPS_SPECIFIED')
			. "<br />&nbsp;<br />", 'd', 'l', '100%', 'sem_edit', 2
			) . "</tr>";

		// Zusatzfelder
		$zusfeld = self::getAdditionalFieldsFrontend($row);

		if (!empty($zusfeld))
		{
			for ($i = 0; $i < count($zusfeld[0]); $i++)
			{
				$html .= "<tr>" . self::getTableCell(
						JTEXT::_('COM_MATUKIO_INPUT') . " " . ($i + 1)
					. ":", 'd', 'l', '20%', 'sem_edit');
				$htxt = "<input class=\"input sem_inputbox\" type=\"text\" name=\"zusatz" . ($i + 1) . "\" size=\"50\" value=\""
					. $zusfeld[0][$i] . "\" />";

				$html .= self::getTableCell($htxt, 'd', 'l', '80%', 'sem_edit') . "</tr>";

				if (empty($zusfeld[1][$i]))
				{
					$zusfeld[1][$i] = "";
				}

				$html .= "<tr>" . self::getTableCell(JTEXT::_('COM_MATUKIO_FIELD_TIP') . ":", 'd', 'l', '20%', 'sem_edit');

				$htxt = "<input class=\"input sem_inputbox\" type=\"text\" name=\"zusatz"
					. ($i + 1) . "hint\" size=\"50\" maxlength=\"250\" value=\"" . $zusfeld[1][$i] . "\" />";
				$html .= self::getTableCell($htxt, 'd', 'l', '80%', 'sem_edit') . "</tr>";

				$radios = array();
				$radios[] = JHTML::_('select.option', 1, JTEXT::_('COM_MATUKIO_YES'));
				$radios[] = JHTML::_('select.option', 0, JTEXT::_('COM_MATUKIO_NO'));
				$htxt = "";

				if (empty($zusfeld[1][$i]))
				{
					// Set radio button to 0
					$zusfeld[2][$i] = 0;
				}

				$html .= "<tr>" . self::getTableCell(
						str_replace("SEM_FNUM", $i + 1, JTEXT::_('COM_MATUKIO_DISPLAY_SEM_FNUM')) . ":", 'd', 'l', '20%', 'sem_edit'
					);

				$showoverview = MatukioHelperInput::getRadioButtonBool('zusatz' . ($i + 1) . 'show', 'zusatz' . ($i + 1) . 'show', $zusfeld[2][$i]);
				$html .= self::getTableCell($htxt . " " . $showoverview, 'd', 'l', '80%', 'sem_edit') . "</tr>";
			}
		}

		$html .= "</table>";
		$html .= "</div>";

		// ### Panel 4 ###
		if (MatukioHelperSettings::_('file_maxsize', 500) > 0)
		{
			$html .= '<div class="tab-pane" id="files">';

			$htxt = str_replace("SEM_FILESIZE", MatukioHelperSettings::getSettings('file_maxsize', 500), JTEXT::_('COM_MATUKIO_FILE_SIZE_UP_TO'));
			$htxt = str_replace("SEM_FILETYPES", strtoupper(MatukioHelperSettings::getSettings('file_endings', 'txt pdf zip jpg')), $htxt);
			$html .= "<table class=\"table\">";
			$html .= "<tr>" . self::getTableCell($htxt, 'd', 'l', '100%', 'sem_edit', 2) . "</tr>";
			$datfeld = self::getEventFileArray($row);
			$select = array();
			$select[] = JHTML::_('select.option', 0, JTEXT::_('COM_MATUKIO_EVERYONE'));
			$select[] = JHTML::_('select.option', 1, JTEXT::_('COM_MATUKIO_REGISTERED_USERS'));
			$select[] = JHTML::_('select.option', 2, JTEXT::_('COM_MATUKIO_USERS_BOOKED_EVENT'));
			$select[] = JHTML::_('select.option', 3, JTEXT::_('COM_MATUKIO_USERS_PAID_FOR_EVENT'));

			for ($i = 0; $i < count($datfeld[0]); $i++)
			{
				$html .= "<tr>" . self::getTableCell(JTEXT::_('COM_MATUKIO_FILE') . " " . ($i + 1) . ":", 'd', 'l', '20%', 'sem_edit');

				if ($datfeld[0][$i] != "")
				{
					$htxt = "<b>" . $datfeld[0][$i] . "</b> - <input class=\"sem_inputbox\" type=\"checkbox\" name=\"deldatei" . ($i + 1) . "\"
                        value=\"1\" onClick=\"if(this.checked==true) {datei" . ($i + 1) . ".disabled=true;} else {datei"
						. ($i + 1) . ".disabled=false;}\"> "
						. JTEXT::_('COM_MATUKIO_DELETE_FILE');
					$html .= self::getTableCell($htxt, 'd', 'l', '80%', 'sem_edit') . "</tr>";
					$html .= "<tr>" . self::getTableCell("&nbsp;", 'd', 'l', '20%', 'sem_edit');
				}

				$htxt = "<input class=\"sem_inputbox btn-success\" name=\"datei" . ($i + 1) . "\" type=\"file\">";
				$html .= self::getTableCell($htxt, 'd', 'l', '80%', 'sem_edit') . "</tr>";
				$html .= "<tr>" . self::getTableCell("&nbsp;", 'd', 'l', '20%', 'sem_edit');
				$htxt = JTEXT::_('COM_MATUKIO_DESCRIPTION') . ": <input class=\"sem_inputbox form-control\" type=\"text\" name=\"file" . ($i + 1)
					. "desc\" size=\"50\" maxlength=\"255\" value=\"" . $datfeld[1][$i] . "\" />";
				$html .= self::getTableCell($htxt, 'd', 'l', '80%', 'sem_edit') . "</tr>";
				$html .= "<tr>" . self::getTableCell("&nbsp;", 'd', 'l', '20%', 'sem_edit');
				$htxt = JHTML::_('select.genericlist', $select, 'file' . ($i + 1) . 'down', 'class="sem_inputbox" ', 'value', 'text', $datfeld[2][$i]);
				$html .= self::getTableCell(JTEXT::_('COM_MATUKIO_WHO_MAY_DOWNLOAD') . " " . $htxt, 'd', 'l', '80%', 'sem_edit') . "</tr>";
			}

			$html .= "</table>";
			$html .= "</div>";
		}

		// ### Panel 5 ###
		$html .= '<div class="tab-pane" id="overrides">';

		$editor = JFactory::getEditor();

		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-12">';
		$html .= JText::_("COM_MATUKIO_OVERRIDES_INTRO");
		$html .= '</div>';
		$html .= '</div>';


		// Custom different fees  @since 3.0
		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-12">';
		$html .=  '<label>' . JText::_("COM_MATUKIO_DIFFERENT_FEES_OVERRIDE") . '</label>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-12">';
		$html .= JText::_("COM_MATUKIO_DIFFERENT_FEES_OVERRIDE_TEXT");
		$html .= '</div>';
		$html .= '</div>';


		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-12">';
		$html .= '<div id="feecont">';

		if (empty($row->different_fees_override))
		{
			$html .= MatukioHelperFees::getDifferentFeeEdit(0);
			$count = 1;
		}
		else
		{
			$json = str_replace('&quot;', '"', $row->different_fees_override);
			$fees = json_decode($json, true);

			$count = 1;

			foreach ($fees as $f)
			{
				$html .= MatukioHelperFees::getDifferentFeeEdit($f["num"], $f);
				$count++;
			}
		}
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<input type="hidden" name="numfees" id="numfees" value="' . $count . '" />';

		$html .= "</div>";


		$html .= '<br /><br /><div class="form-group">';
		$html .= '<div class="col-sm-12">';
		$html .= '<label for="booking_mail">' . JText::_("COM_MATUKIO_CUSTOM_BOOKING_MAIL_TEXT") . '</label>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-12">';
		$html .= $editor->display("booking_mail", $row->booking_mail, 600, 300, 40, 20, 1);
		$html .= '</div>';
		$html .= '</div>';


		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-12">';
		$html .= '<label for="certificate_code">' . JText::_("COM_MATUKIO_CUSTOM_CERTIFICATE_CODE") . '</label>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<div class="form-group">';
		$html .= '<div class="col-sm-12">';
		$html .= $editor->display("certificate_code", $row->certificate_code, 600, 300, 40, 20, 1);
		$html .= '</div>';
		$html .= '</div>';


		$html .= "</div>";

		// End intern row fluid div

		$html .= "</div>";
		$html .= '<div class="clr clear"></div>';
		return $html;
	}


	// ++++++++++++++++++++++++++++++++++++++
// +++ Eingabe prüfen                 +++     sem_f067
// ++++++++++++++++++++++++++++++++++++++

	public static function checkRequiredFieldValues($text, $art = 'leer')
	{
		$htxt = false;

		switch ($art)
		{
// Texteingabe prüfen - alle eingaben auf leere eingaben prüfen
			case 'leer':
				$text = trim($text);

				if ($text != '')
				{
					$htxt = true;
				}
				break;
// Auf nur zahlen prüfen
			case 'nummer':
				if (preg_match("#^[0-9]+$#", $text))
				{
					$htxt = true;
				}
				break;
// Auf telefonnummer prüfen mit min. 6 zahlen
			case 'telefon':
				if (preg_match("#^[ 0-9\/-+]{6,}+$#", $text))
				{
					$htxt = true;
				}
				break;
// Auf nur buchstaben prüfen
			case 'buchstabe':
				if (preg_match("/^[ a-za-zäöüß]+$/i", $text))
				{
					$htxt = true;
				}
				break;
// auf nur ein wort prüfen
			case 'wort':
				if (preg_match("/^[a-za-zäöüß]+$/i", $text))
				{
					$htxt = true;
				}
				break;
// Url prüfen
			case 'url':
				$text = trim($text);
				if (preg_match("#^(http|https)+(://www.)+([a-z0-9-_.]{2,}\.[a-z]{2,4})$#i", $text))
				{
					$htxt = true;
				}
				break;
// Email-adresse prüfen
			case 'email':
				$text = trim($text);

				if ($text != '')
				{
					$_pat = "^[_a-za-z0-9-]+(.[_a-za-z0-9-]+)*@([a-z0-9-]{3,})+.([a-za-z]{2,4})$";

					if (!preg_match("|$_pat|i", $text))
					{
						$htxt = false;
					}
				}
				else
				{
					$htxt = false;
				}
				break;
// Zahl der Laenge art pruefen
			default:
				if (preg_match("/^[0-9]{$art}$/", $text))
				{
					$htxt = true;
				}
				break;
		}

		return $htxt;
	}

	// ++++++++++++++++++++++++++++++++++++++++
// +++ Konstanten in Text austauschen   +++      sem_f054
// ++++++++++++++++++++++++++++++++++++++++

	public static function replaceSEMConstants($html, $row, $user)
	{
		$neudatum = MatukioHelperUtilsDate::getCurrentDate();

		$html = str_replace('SEM_IMAGEDIR', MatukioHelperUtilsBasic::getComponentImagePath(), $html);

		$html = str_replace('SEM_BEGIN_EXPR', JTEXT::_('COM_MATUKIO_BEGIN'), $html);
		$html = str_replace('SEM_END_EXPR', JTEXT::_('COM_MATUKIO_END'), $html);
		$html = str_replace('SEM_LOCATION_EXPR', JTEXT::_('COM_MATUKIO_CITY'), $html);
		$html = str_replace('SEM_TUTOR_EXPR', JTEXT::_('COM_MATUKIO_TUTOR'), $html);
		$html = str_replace('SEM_DATE_EXPR', JTEXT::_('COM_MATUKIO_DATE'), $html);
		$html = str_replace('SEM_TIME_EXPR', JTEXT::_('COM_MATUKIO_TIME'), $html);

		$html = str_replace('SEM_COURSE', $row->title, $html);
		$html = str_replace('SEM_TITLE', $row->title, $html);
		$html = str_replace('SEM_COURSENUMBER', $row->semnum, $html);
		$html = str_replace('SEM_NUMBER', $row->semnum, $html);
		$html = str_replace('SEM_ID', $row->id, $html);
		$html = str_replace('SEM_LOCATION', $row->place, $html);
		$html = str_replace('SEM_TEACHER', $row->teacher, $html);
		$html = str_replace('SEM_TUTOR', $row->teacher, $html);

		$html = str_replace('SEM_BEGIN', JHTML::_('date', $row->begin, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_BEGIN_OVERVIEW', JHTML::_('date', $row->begin, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_BEGIN_DETAIL', JHTML::_('date', $row->begin, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_BEGIN_LIST', JHTML::_('date', $row->begin, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_BEGIN_DATE', JHTML::_(
			'date', $row->begin, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y')
		), $html);
		$html = str_replace('SEM_BEGIN_TIME', JHTML::_('date', $row->begin, MatukioHelperSettings::getSettings('time_format', 'H:i')), $html);
		$html = str_replace('SEM_END', JHTML::_('date', $row->end, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_END_OVERVIEW', JHTML::_('date', $row->end, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_END_DETAIL', JHTML::_('date', $row->end, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_END_LIST', JHTML::_('date', $row->end, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_END_DATE', JHTML::_('date', $row->end, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y')), $html);
		$html = str_replace('SEM_END_TIME', JHTML::_('date', $row->end, MatukioHelperSettings::getSettings('time_format', 'H:i')), $html);
		$html = str_replace('SEM_TODAY', JHTML::_('date', $neudatum, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y')), $html);
		$html = str_replace('SEM_NOW', JHTML::_('date', $neudatum, MatukioHelperSettings::getSettings('time_format', 'H:i')), $html);
		$html = str_replace('SEM_NOW_OVERVIEW', JHTML::_('date', $neudatum, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_NOW_DETAIL', JHTML::_('date', $neudatum, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_NOW_LIST', JHTML::_('date', $neudatum, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')), $html);
		$html = str_replace('SEM_NOW_DATE', JHTML::_('date', $neudatum, MatukioHelperSettings::getSettings('date_format_without_time', 'd-m-Y')), $html);
		$html = str_replace('SEM_NOW_TIME', JHTML::_('date', $neudatum, MatukioHelperSettings::getSettings('time_format', 'H:i')), $html);

		$html = str_replace('SEM_NAME', $user->name, $html);
		$html = str_replace('SEM_EMAIL', $user->email, $html);

		return $html;
	}

	// ++++++++++++++++++++++++++++++++++++++++++++++++
// +++ Name und Beschreibung der Kategorie ausgeben      sem_f012
// ++++++++++++++++++++++++++++++++++++++++++++++++

	public static function getCategoryDescriptionArray($catid)
	{
		$database = JFactory::getDBO();
		$database->setQuery("Select * FROM #__categories WHERE extension='com_matukio' AND id = '$catid'");
		$rows = $database->loadObjectList();

		return array($rows[0]->title, $rows[0]->description);
	}


	public static function getAdditionalFieldValue($field, $bookingid)
	{
		$database = JFactory::getDBO();
		$database->setQuery("Select id, " . $field . " FROM #__matukio_bookings WHERE  id = '" . $bookingid . "'");
		$row = $database->loadObject();

		return $row;
	}


// +++++++++++++++++++++++++++++++++++++++
// +++ Ausgabe des Prozentbalkens             sem_f013
// +++++++++++++++++++++++++++++++++++++++

	public static function getProcentBar($max, $frei, $art)
	{
		if ($max == 0)
		{
			$max = 1;
		}

		$hoehe = 30;
		$hoehefrei = round($frei * $hoehe / $max);
		$hoehebelegt = $hoehe - $hoehefrei;
		$html = "<span class=\"sem_bar\">" . $max . "</span><br />";
		$html .= "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "2100.png\" width=\"18\" height=\"1\"><br />";

		if ($hoehefrei > 0)
		{
			$html .= "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "212"
				. $art . ".png\" width=\"18\" height=\"" . $hoehefrei . "\"><br />";
		}

		if ($hoehebelegt > 0)
		{
			$html .= "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "211"
				. $art . ".png\" width=\"18\" height=\"" . $hoehebelegt . "\"><br />";
		}

		$html .= "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "2100.png\" width=\"18\" height=\"1\"><br />";
		$html .= "<span class=\"sem_bar\">0</span>";

		return $html;
	}


	/**
	 * Gets a routed link
	 *
	 * @param   string  $link  - The link which should be routed
	 *
	 * @return  string
	 */
	public static function getRoutedLink($link)
	{
		$db = JFactory::getDBO();
		$uri = 'index.php?option=com_matukio&view=eventlist';

		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE ' . $db->Quote($uri . '%') . ' AND published = 1 LIMIT 1');

		$itemId = ($db->getErrorNum()) ? 0 : intval($db->loadResult());

		$link = $link . "&Itemid=" . $itemId;

		// Routing of a link
		$link = JRoute::_($link);

		return $link;
	}

	/**
	 * Gets the place selector
	 *
	 * @param   object  $buchopt   - The bookable array
	 * @param   object  $event     - The event
	 * @param   int     $nr        - The count
	 * @param   int     $selected  - The selected element
	 *
	 * @return mixed
	 */
	public static function getPlaceSelect($buchopt, $event, $nr, $selected = 0)
	{
		$limits = array();

		if ($buchopt[4] <= 0) // If booking is on waitlist
		{
			for ($i = 1; $i <= $event->nrbooked; $i++)
			{
				// Check how many places are left (to prevent booking more places then allowed)
				$limits[] = JHTML::_('select.option', $i);
			}
		}
		else
		{
			for ($i = 1; $i <= $event->nrbooked; $i++)
			{
				// Check how many places are left (to prevent booking more places then allowed)
				if ($i <= $buchopt[4])
				{
					$limits[] = JHTML::_('select.option', $i);
				}
			}
		}

		$psel = JHTML::_('select.genericlist', $limits, 'places[' . $nr . ']',
			'class="sem_inputbox chzn-single ticket_places" size="1" style="width: 50px;"',
			'value', 'text', $selected
		);

		return $psel;
	}

	/**
	 * Gets the locations out of the database
	 *
	 * @param   int     $published  - Published events?
	 * @param   string  $order_by   - Order by - default title ASC
	 *
	 * @return mixed
	 */
	public static function getLocations($published = 1, $order_by = "title ASC")
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("*")->from("#__matukio_locations");

		if ($published == 1)
		{
			$query->where("published = 1");
		}
		elseif ($published === 0)
		{
			$query->where("published = 0");
		}

		$query->order($order_by);

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Gets the location Object
	 *
	 * @param   int  $id         - The id
	 * @param   int  $published  - only published locations
	 *
	 * @return mixed
	 */
	public static function getLocation($id, $published = 1)
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true);

		$query->select("*")->from("#__matukio_locations");

		if ($published == 1)
		{
			$query->where("published = 1");
		}
		elseif ($published === 0)
		{
			$query->where("published = 0");
		}

		$query->where("id = " . $db->quote($id));

		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Gets the events with the given options
	 *
	 * @TODO optimize, minimize and clean up
	 *
	 * @param        $art
	 * @param        $search
	 * @param        $dateid
	 * @param        $catid
	 * @param        $order_by
	 * @param        $user
	 * @param        $navioben
	 * @param        $limitstart
	 * @param        $limit
	 * @param string $layout
	 *
	 * @return mixed|null
	 */
	public static function getEventList($art, $search, $dateid, $catid, $order_by, $user, $navioben, $limitstart, $limit, $layout = "modern")
	{
		$where = array();
		$cd = MatukioHelperUtilsDate::getCurrentDate();

		// Only published ones
		$where[] = "r.published = '1'";
		$where[] = "a.pattern = ''";

		// Get the allowed categories
		$allowedcat = self::getUserACLCategories($user);

		$where[] = "a.catid IN (" . implode(',', $allowedcat) . ")";

		// When should an event not be shown anymore?
		switch (MatukioHelperSettings::getSettings('event_stopshowing', 2))
		{
			case "0":
				$showend = "r.begin";
				break;

			case "1":
				$showend = "r.end";
				break;

			// Never stop showing
			case "3":
				$showend = "";
				$dateid = 0;
				break;

			case "2":
			default:
				$showend = "r.booked";
				break;
		}

		if ((in_array('SEM_TYPES', $navioben) && MatukioHelperSettings::getSettings('event_stopshowing', 2) != 3)
			|| ($layout == "modern" && MatukioHelperSettings::getSettings('event_stopshowing', 2) != 3
			&& MatukioHelperSettings::getSettings('navi_eventlist_types', 1) == 1 && $user->id > 0))
		{
			switch ($dateid)
			{
				case "1":
					if (empty($showend))
					{
						// In this case we have to set a begin
						$showend = "r.begin";
					}

					$where[] = "$showend > '$cd'";
					break;

				case "2":
					if (empty($showend))
					{
						// In this case we have to set a begin
						$showend = "r.begin";
					}

					$where[] = "$showend <= '$cd'";
					break;
			}
		}
		else
		{
			// TODO test if not bringing new problems

			if (empty($showend))
			{
				// In this case we have to set a begin
				$showend = "r.begin";
			}
			$where[] = "$showend > '$cd'";
		}

		$events = null;

		switch ($art)
		{
			case 0:
				$events = self::getAllEventsNormal($navioben, $showend, $cd, $catid, $search, $order_by, $limitstart, $limit, $where);
				break;

			case 1:
				$events = self::getAllEventsBooked($navioben, $showend, $cd, $catid, $search, $order_by, $limitstart, $limit, $user, $where);
				break;

			case 2:
				$events = self::getAllEventsOffered($navioben, $showend, $cd, $catid, $search, $order_by, $limitstart, $limit, $user, $where);
				break;
		}

		return $events;
	}

	/**
	 * Gets the users allowed event categories
	 *
	 * @param   JUser  $user  - The user
	 *
	 * @return  array  - List of categories
	 */
	public static function getUserACLCategories($user)
	{
		$db = JFactory::getDbo();

		// Check category ACL rights
		$groups = implode(',', $user->getAuthorisedViewLevels());

		$query = $db->getQuery(true);
		$query->select("id, access")->from("#__categories")
			->where(array("extension = " . $db->quote("com_matukio"), "published = 1", "access in (" . $groups . ")"));

		$db->setQuery($query);
		$cats = $db->loadObjectList();

		$allowedcat = array();

		foreach ((array) $cats AS $cat)
		{
			$allowedcat[] = $cat->id;
		}

		return $allowedcat;
	}

	/**
	 * Gets all normal events - art 0
	 *
	 * @param $navioben
	 * @param $showend
	 * @param $cd
	 * @param $catid
	 * @param $search
	 * @param $order_by
	 * @param $limitstart
	 * @param $limit
	 * @param $where
	 *
	 * @return mixed
	 */
	public static function getAllEventsNormal($navioben, $showend, $cd, $catid, $search, $order_by, $limitstart, $limit, $where)
	{
		$db = JFactory::getDbo();

		if (in_array('SEM_CATEGORIES', $navioben) AND $catid > 0)
		{
			$where[] = "a.catid = '$catid'";
		}

		$leftjoin = "";
		$bookdate = "";

		if (!empty($search))
		{
			$suche = "\nAND (r.semnum LIKE '%" . $search . "%' OR a.gmaploc LIKE '%" . $search . "%' OR a.target LIKE '%"
				. $search . "%' OR a.place LIKE '%" . $search . "%' OR a.teacher LIKE '%" . $search . "%' OR a.title LIKE '%"
				. $search . "%' OR a.shortdesc LIKE '%" . $search . "%' OR a.description LIKE '%" . $search . "%')";
		}
		else
		{
			$suche = "";
		}

		// Get all events.. need to limit that.. mess
		$db->setQuery("SELECT a.*, r.*, a.id as eventid FROM #__matukio_recurring AS r
			LEFT JOIN #__matukio AS a ON r.event_id = a.id"
			. $leftjoin
			. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
			. $suche
		);

		$rows = $db->loadObjectList();

		$total = count($rows);

		$events = self::filterEvents($rows, 0, $limitstart, $limit, $leftjoin, $suche, $order_by, $where, "");

		return array($events, $total);
	}

	/**
	 * Gets all booked events
	 *
	 * @param $navioben
	 * @param $showend
	 * @param $cd
	 * @param $catid
	 * @param $search
	 * @param $order_by
	 * @param $limitstart
	 * @param $limit
	 * @param $user
	 *
	 * @return mixed
	 */
	public static function getAllEventsBooked($navioben, $showend, $cd, $catid, $search, $order_by, $limitstart, $limit, $user, $where)
	{
		$db = JFactory::getDbo();

		if ((isset($catid) OR in_array('SEM_CATEGORIES', $navioben)) AND $catid > 0)
		{
			$where[] = "a.catid = '$catid'";
		}

		$where[] = "cc.userid = '" . $user->id . "'";
		$leftjoin = "\n LEFT JOIN #__matukio_bookings AS cc ON cc.semid = r.id";

		$bookdate = ", cc.bookingdate AS bookingdate, cc.id AS sid";

		if (!empty($search))
		{
			$suche = "\nAND (r.semnum LIKE '%" . $search . "%' OR a.gmaploc LIKE '%" . $search . "%' OR a.target LIKE '%"
				. $search . "%' OR a.place LIKE '%" . $search . "%' OR a.teacher LIKE '%" . $search . "%' OR a.title LIKE '%"
				. $search . "%' OR a.shortdesc LIKE '%" . $search . "%' OR a.description LIKE '%" . $search . "%')";
		}
		else
		{
			$suche = "";
		}

		// Add filter for status
		$where[] = "(cc.status = 0 OR cc.status = 1)";

		// Get all events.. need to limit that.. mess
		$db->setQuery("SELECT a.*, r.*, cc.status AS bstatus " . $bookdate . " FROM #__matukio_recurring AS r
			LEFT JOIN #__matukio AS a ON r.event_id = a.id"
			. $leftjoin
			. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
			. $suche
		);

		$rows = $db->loadObjectList();

		$total = count($rows);

		$events = self::filterEvents($rows, 1, $limitstart, $limit, $leftjoin, $suche, $order_by, $where, $bookdate);

		return array($events, $total);
	}

	/**
	 * Gets all offered events
	 *
	 * @param $navioben
	 * @param $showend
	 * @param $cd
	 * @param $catid
	 * @param $search
	 * @param $order_by
	 * @param $limitstart
	 * @param $limit
	 * @param $user
	 *
	 * @return mixed
	 */
	public static function getAllEventsOffered($navioben, $showend, $cd, $catid, $search, $order_by, $limitstart, $limit, $user, $where)
	{
		$db = JFactory::getDbo();

		if ((isset($catid) OR in_array('SEM_CATEGORIES', $navioben)) AND $catid > 0)
		{
			$where[] = "a.catid = '$catid'";
		}

		// Check if he is allowed to see all events if not set his publisher id
		if (!MatukioHelperSettings::_("frontend_organizer_allevent", 0))
		{
			$where[] = "a.publisher = '" . $user->id . "'";
		}

		$leftjoin = "";

		if (!empty($search))
		{
			$suche = "\nAND (r.semnum LIKE '%" . $search . "%' OR a.gmaploc LIKE '%" . $search . "%' OR a.target LIKE '%"
				. $search . "%' OR a.place LIKE '%" . $search . "%' OR a.teacher LIKE '%" . $search . "%' OR a.title LIKE '%"
				. $search . "%' OR a.shortdesc LIKE '%" . $search . "%' OR a.description LIKE '%" . $search . "%')";
		}
		else
		{
			$suche = "";
		}

		$db->setQuery("SELECT a.*, r.* FROM #__matukio_recurring AS r
			LEFT JOIN #__matukio AS a ON r.event_id = a.id "
			. $leftjoin
			. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
			. $suche
		);

		$rows = $db->loadObjectList();

		$total = count($rows);

		$events = self::filterEvents($rows, 2, $limitstart, $limit, $leftjoin, $suche, $order_by, $where, "");

		return array($events, $total);
	}

	/**
	 * Filter the events after getting all (overbooked etc. - should all be moved sometime)
	 *
	 * @param   array   $rows        - The events
	 * @param   int     $art         - The art
	 * @param   int     $limitstart  - The limit start
	 * @param   int     $limit       - The cur limit
	 * @param   string  $leftjoin    - The leftjoin
	 * @param   string  $search      - The search
	 * @param   string  $order_by    - The orderby
	 * @oaram   array   $where       - Orderby array
	 * @param   string  $bookdate    - The bookdate
	 *
	 * @return mixed
	 */
	public static function filterEvents($rows, $art, $limitstart, $limit, $leftjoin, $search, $order_by, $where, $bookdate)
	{
		$db = JFactory::getDbo();

		// Abzug der Kurse, die wegen Ausbuchung nicht angezeigt werden sollen
		$abzug = 0;
		$abid = array();

		if ($art == 0)
		{
			foreach ((array) $rows as $row)
			{
				if ($row->stopbooking == 2)
				{
					$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($row);

					if ($row->maxpupil - $gebucht->booked < 1)
					{
						$abzug++;
						$abid[] = $row->id;
					}
				}
			}
		}

		if (count($abid) > 0)
		{
			$abid = implode(',', $abid);
			$where[] = "r.id NOT IN ($abid)";
		}

		$total = count($rows) - $abzug;

		if (!is_numeric($limitstart))
		{
			$limitstart = explode("=", $limitstart);
			$limitstart = end($limitstart);

			if (!is_numeric($limitstart))
			{
				$limitstart = 0;
			}
		}

		if ($total <= $limitstart)
		{
			$limitstart = $limitstart - $limit;
		}

		if ($limitstart < 0)
		{
			$limitstart = 0;
		}

		$ttlimit = "";

		if ($limit > 0)
		{
			$ttlimit = "\nLIMIT $limitstart, $limit";
		}

		$db->setQuery("SELECT a.*, r.*, a.id as eventid, cat.title as category, cat.params as catparams"
			. $bookdate . " FROM #__matukio_recurring AS r
			LEFT JOIN #__matukio AS a ON r.event_id = a.id
		    LEFT JOIN #__categories AS cat ON cat.id = a.catid"
			. $leftjoin
			. (count($where) ? "\nWHERE " . implode(' AND ', $where) : "")
			. $search
			. "\nORDER BY " . $order_by
			. $ttlimit
		);

		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * Loads an event with recurring out of the database..
	 *
	 * @param   int  $id  - The event id
	 *
	 * @return mixed
	 */
	public static function getEventRecurring($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("e.*, r.*, e.id as eventid, cat.title AS category")->from("#__matukio_recurring AS r")
			->leftJoin("#__matukio AS e ON e.id = r.event_id")
			->leftJoin("#__categories AS cat ON e.catid = cat.id")
			->where("r.id = " . $db->quote($id));

		$db->setQuery($query, 0, 1);

		return $db->loadObject();
	}

	/**
	 * Loads the events for the given real event id
	 *
	 * @param   int  $event_id  - The event id
	 *
	 * @return  mixed
	 */
	public static function getEventsRecurringOnEventId($event_id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("e.*, r.*, e.id as eventid")->from("#__matukio_recurring AS r")
			->leftJoin("#__matukio AS e ON e.id = r.event_id")
			->where("r.event_id = " . $db->quote($event_id));

		$db->setQuery($query, 0, 1);

		return $db->loadObjectList();
	}

	/**
	 * Generates an event template with all the "normal" default values
	 *
	 * @param   int  $event_id  - The event id (optional - if 0 we create a new one)
	 *
	 * @return  object - The event
	 */
	public static function getEventEditTemplate($event_id = 0)
	{
		$event = JTable::getInstance('Matukio', 'Table');
		$event->load($event_id);

		// Set default values
		if (empty($event_id))
		{
			$def_begin = MatukioHelperSettings::_("event_default_begin", "14:00:00");
			$def_end = MatukioHelperSettings::_("event_default_end", "17:00:00");
			$def_booked = MatukioHelperSettings::_("event_default_booked", "12:00:00");

			$event->begin = date("Y-m-d") . " " . $def_begin;
			$event->end = date("Y-m-d") . " " . $def_end;
			$event->booked = date("Y-m-d") . " " . $def_booked;

			// Current user
			$event->publisher = JFactory::getUser()->id;
			$event->semnum = MatukioHelperUtilsEvents::createNewEventNumber(date('Y'));

			$event->published = 1;

			// Default event settings @since 3.0
			$event->title = MatukioHelperSettings::_("event_default_title", "");

			$event->catid = MatukioHelperSettings::_("event_default_category", "");

			$event->shortdesc = MatukioHelperSettings::_("event_default_short_description", "");

			$event->place = MatukioHelperSettings::_("event_default_place", "");

			// $event->publisher = MatukioHelperSettings::_("event_default_publisher", "");

			$event->webinar = MatukioHelperSettings::_("event_default_webinar", 0);

			$event->maxpupil = MatukioHelperSettings::_("event_default_maxpupil", 12);

			$event->minpupil = MatukioHelperSettings::_("event_default_minpupil", 0);

			$event->nrbooked = MatukioHelperSettings::_("event_default_nrbooked", 1);

			$event->description = MatukioHelperSettings::_("event_default_description", "");

			$event->gmaploc = MatukioHelperSettings::_("event_default_map_location", "");

			$event->teacher = MatukioHelperSettings::_("event_default_teacher", "");

			$event->target = MatukioHelperSettings::_("event_default_target", "");

			$event->fees = MatukioHelperSettings::_("event_default_fees", 0);

			$event->different_fees = MatukioHelperSettings::_("event_default_different_fees", 0);

			$event->recurring = MatukioHelperSettings::_("event_default_recurring", 0);

			$event->recurring_type = MatukioHelperSettings::_("event_default_recurring_type", "daily");

			$event->recurring_count = MatukioHelperSettings::_("event_default_recurring_count", 0);

			$event->recurring_week_day = MatukioHelperSettings::_("event_default_recurring_week_day", 1);

			$event->recurring_until = MatukioHelperSettings::_("event_default_recurring_until", "0000-00-00");
		}

		$zeit = explode(" ", $event->begin);
		$event->begin_date = $zeit[0];
		$zeit = explode(":", $zeit[1]);
		$event->begin_hour = $zeit[0];
		$event->begin_minute = $zeit[1];
		$zeit = explode(" ", $event->end);
		$event->end_date = $zeit[0];
		$zeit = explode(":", $zeit[1]);
		$event->end_hour = $zeit[0];
		$event->end_minute = $zeit[1];
		$zeit = explode(" ", $event->booked);
		$event->booked_date = $zeit[0];
		$zeit = explode(":", $zeit[1]);
		$event->booked_hour = $zeit[0];
		$event->booked_minute = $zeit[1];

		return $event;
	}

	public static function saveEvent($frontend = false)
	{
		$database = JFactory::getDBO();
		$input = JFactory::getApplication()->input;

		$caid = $input->getInt('caid', 0);
		$cancel = $input->getInt('cancel', 0);
		$deldatei1 = $input->get('deldatei1', 0);
		$deldatei2 = $input->get('deldatei2', 0);
		$deldatei3 = $input->get('deldatei3', 0);
		$deldatei4 = $input->get('deldatei4', 0);
		$deldatei5 = $input->get('deldatei5', 0);
		$vorlage = $input->getInt('vorlage', 0, 'string');
		$id = $input->getInt('id', 0);
		$art = $input->getInt('art', 2);
		$neudatum = MatukioHelperUtilsDate::getCurrentDate();
		$recurring = $input->getInt("recurring", 0);
		$isNew = true;

		JPluginHelper::importPlugin('content');
		$dispatcher = JDispatcher::getInstance();

		// Zeit formatieren
		$_begin_date = $input->get('_begin_date', '0000-00-00', 'string');
		$_end_date = $input->get('_end_date', '0000-00-00', 'string');
		$_booked_date = $input->get('_booked_date', '0000-00-00', 'string');

		if ($id > 0)
		{
			$kurs = JTable::getInstance('Matukio', 'Table');
			$kurs->load($id);
			$isNew = false;
		}

		if ($vorlage > 0)
		{
			$kurs = JTable::getInstance('Matukio', 'Table');
			$kurs->load($vorlage);
		}

		$post = JRequest::get('post');

		// Allow HTML for certain fields
		$post['description'] = JRequest::getVar('description', '', 'post', 'html', JREQUEST_ALLOWHTML);
		$post['booking_mail'] = JRequest::getVar('booking_mail', '', 'post', 'html', JREQUEST_ALLOWHTML);
		$post['certificate_code'] = JRequest::getVar('certificate_code', '', 'post', 'html', JREQUEST_ALLOWHTML);
		$post['shortdesc'] = JRequest::getVar('shortdesc', '', 'post', 'html', JREQUEST_ALLOWHTML);
		$post['place'] = JRequest::getVar('place', '', 'post', 'html', JREQUEST_ALLOWHTML);

		$row = JTable::getInstance('Matukio', 'Table');
		$row->load($id);

		if (!$row->bind($post))
		{
			throw new Exception($row->getError(), 42);
		}

		// Zuweisung der aktuellen Zeit
		if ($id == 0)
		{
			$row->publishdate = $neudatum;
		}

		$row->updated = $neudatum;

		if ($cancel != $row->cancelled && MatukioHelperSettings::_("notify_participants_cancel", 1))
		{
			$tempmail = 9 + $cancel;

			$events = MatukioHelperUtilsEvents::getEventsRecurringOnEventId($row->id);

			foreach ($events as $e)
			{
				$database->setQuery("SELECT * FROM #__matukio_bookings WHERE semid='$e->id'");
				$rows = $database->loadObjectList();

				for ($i = 0, $n = count($rows); $i < $n; $i++)
				{
					MatukioHelperUtilsEvents::sendBookingConfirmationMail($e, $rows[$i]->id, $tempmail);
				}
			}
		}

		$row->cancelled = $cancel;
		$row->catid = $caid;

		// Zuweisung der Startzeit
		$row->begin = JFactory::getDate($_begin_date, MatukioHelperUtilsBasic::getTimeZone())->format('Y-m-d H:i:s', false, false);

		// Zuweisung der Endzeit
		$row->end = JFactory::getDate($_end_date, MatukioHelperUtilsBasic::getTimeZone())->format('Y-m-d H:i:s', false, false);

		// Zuweisung der Buchungszeit
		$row->booked = JFactory::getDate($_booked_date, MatukioHelperUtilsBasic::getTimeZone())->format('Y-m-d H:i:s', false, false);

		// Neue Daten eintragen
		$row->description = str_replace('<br>', '<br />', $row->description);
		$row->description = str_replace('\"', '"', $row->description);
		$row->description = str_replace("'", "'", $row->description);

		$row->fees = str_replace(",", ".", $row->fees);

		$row->different_fees_override = "";

		$different_fees_override = $input->get("different_fees_override", array(), 'Array');

		if (count($different_fees_override))
		{
			// Check if element 0 is not empty
			if (!empty($different_fees_override[0]["title"]))
			{
				$row->different_fees_override = json_encode($different_fees_override);
			}
		}

		if ($row->id > 0 OR $vorlage > 0)
		{
			if ($deldatei1 != 1)
			{
				$row->file1 = $kurs->file1;
				$row->file1code = $kurs->file1code;
			}

			if ($deldatei2 != 1)
			{
				$row->file2 = $kurs->file2;
				$row->file2code = $kurs->file2code;
			}

			if ($deldatei3 != 1)
			{
				$row->file3 = $kurs->file3;
				$row->file3code = $kurs->file3code;
			}

			if ($deldatei4 != 1)
			{
				$row->file4 = $kurs->file4;
				$row->file4code = $kurs->file4code;
			}

			if ($deldatei5 != 1)
			{
				$row->file5 = $kurs->file5;
				$row->file5code = $kurs->file5code;
			}
		}

		if ($row->id > 0)
		{
			$row->hits = $kurs->hits;
		}

		$fileext = explode(' ', strtolower(MatukioHelperSettings::getSettings('file_endings', 'txt zip pdf')));
		$filesize = MatukioHelperSettings::getSettings('file_maxsize', 500) * 1024;
		$fehler = array('', '', '', '', '', '', '', '', '', '');

		if (!empty($_FILES['datei1']))
		{
			if (is_file($_FILES['datei1']['tmp_name']) AND $_FILES['datei1']['size'] > 0)
			{
				if ($_FILES['datei1']['size'] > $filesize)
				{
					$fehler[0] = str_replace("SEM_FILE", $_FILES['datei1']['name'], JTEXT::_('COM_MATUKIO_UPLOAD_FAILED_MAX_SIZE'));
				}

				$datei1ext = array_pop(explode(".", strtolower($_FILES['datei1']['name'])));

				if (!in_array($datei1ext, $fileext))
				{
					$fehler[1] = str_replace("SEM_FILE", $_FILES['datei1']['name'], JTEXT::_('COM_MATUKIO_UPLOAD_FAILED_FILE_TYPE'));
				}

				if ($fehler[0] == "" AND $fehler[1] == "")
				{
					if ($deldatei1 != 1)
					{
						$row->file1 = $_FILES['datei1']['name'];
						$row->file1code = base64_encode(file_get_contents($_FILES['datei1']['tmp_name']));
					}
					else
					{
						$row->file1 = "";
						$row->file1code = "";
					}
				}
			}
		}
		else
		{
			// Delete file
			if ($deldatei1 == 1)
			{
				$row->file1 = "";
				$row->file1code = "";
			}
		}

		if (!empty($_FILES['datei2']))
		{
			if (is_file($_FILES['datei2']['tmp_name']) AND $_FILES['datei2']['size'] > 0)
			{
				if ($_FILES['datei2']['size'] > $filesize)
				{
					$fehler[2] = str_replace("SEM_FILE", $_FILES['datei2']['name'], JTEXT::_('COM_MATUKIO_UPLOAD_FAILED_MAX_SIZE'));
				}

				$datei2ext = array_pop(explode(".", strtolower($_FILES['datei2']['name'])));

				if (!in_array($datei2ext, $fileext))
				{
					$fehler[3] = str_replace("SEM_FILE", $_FILES['datei2']['name'], JTEXT::_('COM_MATUKIO_UPLOAD_FAILED_FILE_TYPE'));
				}

				if ($fehler[2] == "" AND $fehler[3] == "")
				{
					$row->file2 = $_FILES['datei2']['name'];
					$row->file2code = base64_encode(file_get_contents($_FILES['datei2']['tmp_name']));
				}
			}
		}
		else
		{
			// Delete file
			if ($deldatei2 == 1)
			{
				$row->file2 = "";
				$row->file2code = "";
			}
		}

		if (!empty($_FILES['datei3']))
		{
			if (is_file($_FILES['datei3']['tmp_name']) AND $_FILES['datei3']['size'] > 0)
			{
				if ($_FILES['datei3']['size'] > $filesize)
				{
					$fehler[4] = str_replace("SEM_FILE", $_FILES['datei3']['name'], JTEXT::_('COM_MATUKIO_UPLOAD_FAILED_MAX_SIZE'));
				}

				$datei3ext = array_pop(explode(".", strtolower($_FILES['datei3']['name'])));

				if (!in_array($datei3ext, $fileext))
				{
					$fehler[5] = str_replace("SEM_FILE", $_FILES['datei3']['name'], JTEXT::_('COM_MATUKIO_UPLOAD_FAILED_FILE_TYPE'));
				}

				if ($fehler[4] == "" AND $fehler[5] == "")
				{
					$row->file3 = $_FILES['datei3']['name'];
					$row->file3code = base64_encode(file_get_contents($_FILES['datei3']['tmp_name']));
				}
			}
		}
		else
		{
			// Delete file
			if ($deldatei3 == 1)
			{
				$row->file3 = "";
				$row->file3code = "";
			}
		}

		if (!empty($_FILES['datei4']))
		{
			if (is_file($_FILES['datei4']['tmp_name']) AND $_FILES['datei4']['size'] > 0)
			{
				if ($_FILES['datei4']['size'] > $filesize)
				{
					$fehler[6] = str_replace("SEM_FILE", $_FILES['datei4']['name'], JTEXT::_('COM_MATUKIO_UPLOAD_FAILED_MAX_SIZE'));
				}

				$datei4ext = array_pop(explode(".", strtolower($_FILES['datei4']['name'])));

				if (!in_array($datei4ext, $fileext))
				{
					$fehler[7] = str_replace("SEM_FILE", $_FILES['datei4']['name'], JTEXT::_('COM_MATUKIO_UPLOAD_FAILED_FILE_TYPE'));
				}

				if ($fehler[6] == "" AND $fehler[7] == "")
				{
					$row->file4 = $_FILES['datei4']['name'];
					$row->file4code = base64_encode(file_get_contents($_FILES['datei4']['tmp_name']));
				}
			}
		}
		else
		{
			// Delete file
			if ($deldatei4 == 1)
			{
				$row->file4 = "";
				$row->file4code = "";
			}
		}


		if (!empty($_FILES['datei5']))
		{
			if (is_file($_FILES['datei5']['tmp_name']) AND $_FILES['datei5']['size'] > 0)
			{
				if ($_FILES['datei5']['size'] > $filesize)
				{
					$fehler[8] = str_replace("SEM_FILE", $_FILES['datei5']['name'], JTEXT::_('COM_MATUKIO_UPLOAD_FAILED_MAX_SIZE'));
				}

				$datei5ext = array_pop(explode(".", strtolower($_FILES['datei5']['name'])));

				if (!in_array($datei5ext, $fileext))
				{
					$fehler[9] = str_replace("SEM_FILE", $_FILES['datei5']['name'], JTEXT::_('COM_MATUKIO_UPLOAD_FAILED_FILE_TYPE'));
				}

				if ($fehler[8] == "" AND $fehler[9] == "")
				{
					$row->file5 = $_FILES['datei5']['name'];
					$row->file5code = base64_encode(file_get_contents($_FILES['datei5']['tmp_name']));
				}
			}
		}
		else
		{
			// Delete file
			if ($deldatei5 == 1)
			{
				$row->file5 = "";
				$row->file5code = "";
			}
		}

		// Eingaben ueberpruefen
		$speichern = true;

		// Template?? Deprecated
		if ($art == 3)
		{
			if (!MatukioHelperUtilsEvents::checkRequiredFieldValues($row->pattern, 'leer'))
			{
				$speichern = false;
				$fehler[] = JTEXT::_('COM_MATUKIO_YOU_HAVENT_FILLED_OUT_ALL_REQUIRED_FIELDS');
			}
		}
		else
		{
			if (!MatukioHelperUtilsEvents::checkRequiredFieldValues($row->semnum, 'leer')
				OR !MatukioHelperUtilsEvents::checkRequiredFieldValues($row->title, 'leer')
				OR $row->catid == 0
				OR !MatukioHelperUtilsEvents::checkRequiredFieldValues($row->shortdesc, 'leer'))
			{
				$speichern = false;
				$fehler[] = JTEXT::_('COM_MATUKIO_YOU_HAVENT_FILLED_OUT_ALL_REQUIRED_FIELDS');
			}
			elseif (!MatukioHelperUtilsEvents::checkRequiredFieldValues($row->maxpupil, 'nummer')
				OR !MatukioHelperUtilsEvents::checkRequiredFieldValues($row->nrbooked, 'nummer'))
			{
				$speichern = false;
				$fehler[] = JTEXT::_('COM_MATUKIO_YOU_HAVENT_TYPED_A_NUMBER');
			}
			else
			{
				$database->setQuery("SELECT id FROM #__matukio WHERE semnum='$row->semnum' AND id!='$row->id'");
				$rows = $database->loadObjectList();

				if (count($rows) > 0)
				{
					$speichern = false;
					$htxt = JTEXT::_('COM_MATUKIO_NOT_UNIQUE_NUMBERS');

					if ($id < 1)
					{
						$htxt .= " " . JTEXT::_('COM_MATUKIO_EVENT_NOT_STORED');
					}

					$fehler[] = $htxt;
				}
			}
		}

		// Kurs speichern
		if ($speichern == true)
		{
			// Trigger plugin event
			$results = $dispatcher->trigger('onBeforeSaveEvent', $row);

			// Check if we already created recurring events
			if ($recurring == 1)
			{
				$edited = $input->getInt("recurring_edited", 0);

				if ($row->recurring_created && $edited && $row->id > 0)
				{
					// Delete old recurring events
					$db = JFactory::getDbo();
					$query = $db->getQuery(true);

					$query->delete("#__matukio_recurring")
						->where("event_id = " . $row->id);

					$db->setQuery($query);
					$db->execute();

					// Maybe set booking status to deleted too?
				}
				else
				{
					// Set it to 1
					$row->recurring_created = 1;
				}
			}

			if (!$row->check())
			{
				throw new Exception($database->stderr(), 42);
			}

			if (!$row->store())
			{
				throw new Exception($database->stderr(), 42);
			}

			$row->checkin();

			// Trigger plugin event
			$results = $dispatcher->trigger('onAfterSaveEvent', array('com_matukio.event', &$row, $isNew));

			// Create recurring events
			if ($recurring == 1)
			{
				$dates_string = $input->get("recurring_dates", '', 'string');

				if (!empty($dates_string))
				{
					$bdate = explode(" ", $row->begin);
					$bdate = $bdate[0];

					// Add begin date (if not already in there)
					if (strpos($dates_string, $bdate) === false)
					{
						$dates_string = $bdate . "," . "$dates_string";
					}

					$dates = explode(",", $dates_string);

					$begin_date = new DateTime($row->begin);
					$end_date = new DateTime($row->end);
					$closing_date = new DateTime($row->booked);

					$diff = $begin_date->diff($end_date);
					$diff2 = $begin_date->diff($closing_date);

					$start_time = $begin_date->format("H:i:s");

					$year = date('Y');

					foreach ($dates as $d)
					{
						$rec_start = new DateTime($d . " " . $start_time);
						$rec_end = clone $rec_start;
						$rec_end->add($diff);
						$rec_close = clone $rec_start;
						$rec_close->add($diff2);

						$robj = new stdClass;
						$robj->event_id = $row->id;
						$robj->semnum = MatukioHelperUtilsEvents::createNewEventNumber($year);
						$robj->begin = $rec_start->format("Y-m-d H:i:s");
						$robj->end = $rec_end->format("Y-m-d H:i:s");
						$robj->booked = $rec_close->format("Y-m-d H:i:s");
						$robj->published = 1;

						$rect = JTable::getInstance('Recurring', 'MatukioTable');

						if (!$rect->bind($robj))
						{
							throw new Exception($rect->getError(), 42);
						}

						if (!$rect->check())
						{
							throw new Exception($rect->getError(), 42);
						}

						if (!$rect->store())
						{
							throw new Exception($rect->getError(), 42);
						}
					}
				}
			}
			else
			{
				// Delete the current date from recurring table and insert the new one
				// Delete old recurring events
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);

				$query->select("*")
					->from("#__matukio_recurring")
					->where("event_id = " . $row->id);

				$db->setQuery($query);

				$recd = $db->loadObjectList();

				// Switch from recurring events to none recurring
				if (count($recd) > 1)
				{
					$query = $db->getQuery(true);

					$query->delete("#__matukio_recurring")
						->where("event_id = " . $row->id);

					$db->setQuery($query);
					$db->execute();

					// Insert it ones
					MatukioHelperRecurring::saveRecurringDateForEvent($row);
				}
				elseif (count($recd) == 1)
				{
					// Ugly hack
					$recd = $recd[0];

					$rect = JTable::getInstance('Recurring', 'MatukioTable');

					$recd->semnum = $row->semnum;
					$recd->begin = $row->begin;
					$recd->end = $row->end;
					$recd->booked = $row->booked;
					$recd->published = $row->published;

					// We just update the date
					if (!$rect->bind($recd))
					{
						throw new Exception($rect->getError(), 42);
					}

					if (!$rect->check())
					{
						throw new Exception($rect->getError(), 42);
					}

					if (!$rect->store())
					{
						throw new Exception($rect->getError(), 42);
					}
				}
				else
				{
					// Insert date into recurring table
					// Add recurring date
					MatukioHelperRecurring::saveRecurringDateForEvent($row);
				}
			}

			// Trigger plugin event
			$results = $dispatcher->trigger('onAfterSaveRecurring', $row);
		}

		// Ausgabe der Kurse
		$fehlerzahl = array_unique($fehler);

		if (count($fehlerzahl) > 1)
		{
			$fehler = array_unique($fehler);

			if ($fehler[0] == "")
			{
				$fehler = array_slice($fehler, 1);
			}

			$fehler = implode("<br />", $fehler);

			JFactory::getApplication()->enqueueMessage($fehler, 'Warning');
		}

		// Notify Admin BCC of event creation
		if (MatukioHelperSettings::getSettings('sendmail_operator', '') != '' && $isNew && $speichern)
		{
			$mailer = JFactory::getMailer();
			$mainframe = JFactory::getApplication();

			$sender = $mainframe->getCfg('fromname');
			$from = $mainframe->getCfg('mailfrom');

			$user = JFactory::getUser($row->publisher);
			$replyname = $user->name;
			$replyto = $user->email;

			$subject = JText::_("COM_MATUKIO_NEW_EVENT_CREATED");

			$body = JText::_("COM_MATUKIO_NEW_EVENT_CREATED") . "\n\n";
			$body .= JText::_("COM_MATUKIO_EVENT_DETAILS") . ":\n\n";
			$body .= JText::_("COM_MATUKIO_TITLE") . ":\t\t" . $row->title. "\n";
			$body .= JText::_("COM_MATUKIO_RECURRING_SEMNUM") . ":\t\t" . $row->semnum. "\n";
			$body .= JText::_("COM_MATUKIO_BEGIN") . ":\t\t" . $row->begin. "\n";
			$body .= JText::_("COM_MATUKIO_END") . ":\t\t" . $row->end. "\n";
			$body .= JText::_("COM_MATUKIO_EVENT_DEFAULT_PLACE") . ":\t\t" . $row->place. "\n";
			$body .= JText::_("COM_MATUKIO_EVENT_DEFAULT_SHORT_DESCRIPTION") . ":\t\t" . $row->shortdesc. "\n";
			$body .= JText::_("COM_MATUKIO_PUBLISHER") . ":\t\t" . $user->name. "\n";

			$success = $mailer->sendMail(
				$from, $sender, explode(",", MatukioHelperSettings::getSettings('sendmail_operator', '')), $subject,
				$body, MatukioHelperSettings::getSettings('email_html', 1),
				null, null, null, $replyto, $replyname
			);
		}

		// Send an notification email to all users with new event details @since 4.3.0
		if (MatukioHelperSettings::getSettings('sendmail_newevent', 1) && $isNew && $speichern)
		{
			// We send an notification of the new event to all users / user group
			if (MatukioHelperSettings::_("sendmail_newevent_group", 0))
			{
				// Filter users to the given group if not 0 (all) given
				jimport( 'joomla.access.access' );
				$ids = JAccess::getUsersByGroup(MatukioHelperSettings::_("sendmail_newevent_group", 0));

				$query = "SELECT * FROM #__users WHERE block = 0 AND id IN (" . implode(",", $ids) . ")";
				$db->setQuery($query);
				$users = $db->loadObjectList();
			}
			else
			{
				// Get all users
				$query = "SELECT * FROM #__users WHERE block = 0" ;
				$db->setQuery($query);
				$users = $db->loadObjectList();
			}

			$mailer = JFactory::getMailer();

			// Set an empty category here - TODO query it from #__category table
			$row->category = "";

			$tmpl = MatukioHelperTemplates::getEmailBody("mail_newevent", $row, null);

			// Use HTML or text E-Mail
			if (MatukioHelperSettings::getSettings('email_html', 1))
			{
				// Start html output
				$body = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
				$body .= '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
				$body .= "<head>\n";
				$body .= "</head>\n";
				$body .= "<body>\n";
				$body .= $tmpl->value;
				$body .= "</body>\n</html>";
			}
			else
			{
				$body = $tmpl->value_text;
			}

			$subject = $tmpl->subject;

			$mainframe = JFactory::getApplication();
			$sender = $mainframe->getCfg('fromname');
			$from = $mainframe->getCfg('mailfrom');

			// Loop and sent mail
			foreach($users as $u)
			{
				$success = $mailer->sendMail(
					$from, $sender, $u->email, $subject, $body, MatukioHelperSettings::getSettings('email_html', 1),
					null, null, null
				);

				$mailer->ClearAllRecipients();
			}

			// E-Mail to Admin / Operator etc.
			if (MatukioHelperSettings::getSettings('sendmail_operator', '') != "")
			{
				$success = $mailer->sendMail(
					$from, $sender, explode(",", MatukioHelperSettings::getSettings('sendmail_operator', '')), $subject,
					$body, MatukioHelperSettings::getSettings('email_html', 1),
					null, null, null
				);

				$mailer->ClearAllRecipients();
			}
		}

		$obj = new StdClass;
		$obj->id = $row->id;
		$obj->error =$row->fehler;
		$obj->error_count = count($fehlerzahl);
		$obj->saved = $speichern;
		$obj->event = $row;

		return $obj;
	}

	/**
	 * Gets the calendar button
	 *
	 * @param   object  $event     - THe event
	 * @param   string  $template  - The template
	 *
	 * @return  string
	 */
	public static function getCalendarButton($event, $template = "modern")
	{
		$config = JFactory::getConfig();
		$_suffix = $config->get('sef_suffix');

		if ($_suffix == 0)
		{
			// No .html suffix
			$icslink = JRoute::_("index.php?option=com_matukio&tmpl=component&view=ics&format=raw&cid=" . $event->id);
		}
		else
		{
			$icslink = JURI::ROOT() . "index.php?tmpl=component&option=com_matukio&view=ics&format=raw&cid=" . $event->id;
		}

		$btn_class = "mat_button";

		if ($template == "bootstrap")
		{
			$btn_class = "btn";
		}

		$img = "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "3316.png\" border=\"0\" align=\"absmiddle\">&nbsp;";

		if ($template == "bootstrap")
		{
			$img = "";
		}

		return " <a title=\"" . JTEXT::_('COM_MATUKIO_DOWNLOAD_CALENDER_FILE') . "\" href=\"" . $icslink . "\" target=\"_BLANK\">"
			. "<span class=\"" . $btn_class . "\">"
			. $img
			. JTEXT::_('COM_MATUKIO_DOWNLOAD_CALENDER_FILE') . "</span></a> ";
	}

	public static function generateCSVFile($backend, $cid = 0, $bookings = null, $kurs = null)
	{
		$db = JFactory::getDbo();

		// Load event only if we are not in the backend / or got an whole event to print
		if (!$backend)
		{
			$kurs = MatukioHelperUtilsEvents::getEventRecurring($cid);
		}

		$tmpl = MatukioHelperTemplates::getTemplate("export_csv");

		if (!empty($kurs))
		{
			$db->setQuery("SELECT a.*, cc.*, a.id AS sid, a.name AS aname, a.email AS aemail FROM #__matukio_bookings AS a " .
				"LEFT JOIN #__users AS cc ON cc.id = a.userid WHERE a.semid = '" . $kurs->id . "' AND (a.status = 0 OR a.status = 1) ORDER BY a.id");
		}
		elseif (count($bookings))
		{
			$db->setQuery("SELECT a.*, cc.*, a.id AS sid, a.name AS aname, a.email AS aemail FROM #__matukio_bookings AS a " .
				"LEFT JOIN #__users AS cc ON cc.id = a.userid WHERE a.id IN (" . implode(",", $bookings) . ") ORDER BY a.id");
		}
		else
		{
			throw new Exception("No data supplied (bookings / event)");
		}

		$bookings = $db->loadObjectList();

		if ($db->getErrorNum())
		{
			throw new Exception($db->stderr());
		}

		$csvdata = MatukioHelperTemplates::getCSVHeader($tmpl, $kurs);
		$csvdata .= MatukioHelperTemplates::getCSVData($tmpl, $bookings, $kurs);

		return $csvdata;
	}
}
