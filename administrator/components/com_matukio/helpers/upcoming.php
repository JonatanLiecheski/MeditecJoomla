<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       04.03.14
 *
 * @copyright  Copyright (C) 2008 - 2014 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die ('Restricted access');

/**
 * Class MatukioHelperUpcoming
 *
 * @since  3.1.0
 */
class MatukioHelperUpcoming
{
	private static $instance;

	/**
	 * Gets the upcoming events
	 *
	 * @param   object  $events  - The events
	 * @param   object  $user    - The user
	 *
	 * @return  string - The html code
	 */
	public static function getUpcomingEventsHTML($events, $user)
	{
		JHTML::_('stylesheet', 'media/com_matukio/css/upcoming.css');

		$html = "";

		if (count($events))
		{
			$dispatcher = JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$params = JComponentHelper::getParams('com_matukio');

			foreach ($events as $event)
			{
				$buchopt = MatukioHelperUtilsEvents::getEventBookableArray(0, $event, $user->id);

				// Link
				$eventid_l = $event->id . ':' . JFilterOutput::stringURLSafe($event->title);
				$catid_l = $event->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($event->catid));

				$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l), false);

				// Event image   -- TODO Add / Check for category image
				$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2601.png";

				if ($event->image != "" AND  MatukioHelperSettings::getSettings('event_image', 1) == 1)
				{
					$linksbild = MatukioHelperUtilsBasic::getEventImagePath(1) . $event->image;
				}

				$hot = $event->hot_event ? " " : "";
				$top = $event->top_event ? " mat_top_event" : "";

				// Starting Row
				$html .= '<div class="mat_single_event' . $hot . $top . '">';
				$html .= '	<div class="mat_event_header">';
				$html .= '		<div class="mat_event_header_inner">';
				$html .= '			<div class="mat_event_header_line">';
				$html .= '				<div class="mat_event_image">';
				$html .= '					<img src="' . $linksbild . '" alt="' . $event->title . '" align="absmiddle" />';
				$html .= '				</div>';
				$html .= '				<div class="mat_event_title">';
				$html .= '					<h2><a href="' . $link . '" title="' . $event->title . '">' . $event->title . '</a></h2>';
				$html .= '				</div>';
				$html .= '			</div>';
				$html .= '			<div class="mat_event_location">';

				$begin = JHTML::_('date', $event->begin, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i'));

				$location = $event->place;
				$locobj = null;

				if ($event->place_id > 0)
				{
					$locobj = MatukioHelperUtilsEvents::getLocation($event->place_id);
					$placelink = JRoute::_("index.php?option=com_matukio&view=location&id=" . $locobj->id . ":" . JFilterOutput::stringURLSafe($locobj->title));
					$location = '<a href="' . $placelink . '">' . $locobj->location . '</a>';
				}

				if (MatukioHelperSettings::getSettings('show_timezone', '1'))
				{
				$begin .= " (GMT " . JHTML::_('date', $event->booked, 'P') . ")";
				}

				if ($event->webinar == 1)
				{
					$locimg = MatukioHelperUtilsBasic::getComponentImagePath() . "webinar.png";
					$html .= '<h4><img src="' . $locimg . '" title="' . JText::_("COM_MATUKIO_WEBINAR") . '" style="width: 22px; vertical-align:middle" /> '
						. $location . " " . JText::_("COM_MATUKIO_AT") . " " . $begin . '</h4>';
				}
				else
				{
					// TODO add map link
					$locimg = MatukioHelperUtilsBasic::getComponentImagePath() . "home.png";
					$html .= '<h4><img src="' . $locimg . '" title="' . JText::_("COM_MATUKIO_FIELDS_CITY") . '" style="width: 22px; vertical-align:middle" /> '
							. $location . " " . JText::_("COM_MATUKIO_AT") . " " . $begin . '</h4>';
				}

				$html .= '			</div>';
				$html .= '		</div>';
				$html .= '	</div>';
				$html .= '	<div class="mat_event_description">';
				$html .= $event->shortdesc;
				$html .= '</div>';
				$html .= '	<div class="mat_event_footer">';
				$html .= '		<div class="mat_event_footer_inner">';
				$html .= '			<div class="mat_event_infoline">';

				$catlink = JRoute::_(
					"index.php?option=com_matukio&view=eventlist&art=0&catid=" . $event->catid . ":" . JFilterOutput::stringURLSafe($event->category)
				);

				$html .= '<a href="' . $catlink . '">' . JTEXT::_($event->category) . '</a>';

				// Infoline
				$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($event);

				if (MatukioHelperSettings::getSettings('event_showinfoline', 1) == 1)
				{
					$html .= " | ";

					// Veranstaltungsnummer anzeigen
					if ($event->semnum != "")
					{
						$html .= JTEXT::_('COM_MATUKIO_NUMBER') . ": " . $event->semnum . " | ";
					}

					$html .= JTEXT::_('COM_MATUKIO_BOOKABLE') . ": " . $buchopt[4];
				}

				// Seminarleiter anzeigen
				if ($event->teacher != "")
				{
					$html .= " | " . $event->teacher;
				}

				// Fees
				if ($event->fees > 0)
				{
					$html .= " | ";

					$gebuehr = MatukioHelperUtilsEvents::getFormatedCurrency($event->fees);
					$currency = MatukioHelperSettings::getSettings('currency_symbol', '$');

					if ($currency == '€')
					{
						$html .= JTEXT::_('COM_MATUKIO_FEES') . ': ' . $gebuehr . " " . $currency;
					}
					else
					{
						$html .= JTEXT::_('COM_MATUKIO_FEES') . ': ' . $currency . " " . $gebuehr;
					}
				}

				$html .= '</div>';
				$html .= '			<div class="mat_event_footer_buttons" align="right">';

				// Detail Link
				$html .= " <a title=\"" . $event->title . "\" href=\"" . $link . "\">"
									. "<span class=\"mat_button\"><img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
									. "0012.png\" border=\"0\" align=\"absmiddle\">&nbsp;" . JTEXT::_('COM_MATUKIO_EVENT_DETAILS') . "</span></a> ";

				// Booking Link
				if (($user->id != 0 || (MatukioHelperSettings::getSettings('booking_unregistered', 1) == 1))
					&& MatukioHelperSettings::getSettings('oldbookingform', 0) != 1)
				{
					if ($event->nrbooked > 0)
					{
						$bookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $event->id . ":"
							. JFilterOutput::stringURLSafe($event->title)
						);

						$html .= " <a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink
											. "\"><span class=\"mat_button mat_book\" type=\"button\"><img src=\""
											. MatukioHelperUtilsBasic::getComponentImagePath()
											. "1116.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
											. JTEXT::_('COM_MATUKIO_BOOK') . "</span></a>";
					}
				}

				$html .= '				<br />';
				$results = $dispatcher->trigger('onContentAfterButton', array('com_matukio.upcomingevent', &$event, &$params, 0));
				$html .= trim(implode("\n", $results));
				$html .= '			</div>';
				$html .= '		</div>';
				$html .= '	</div>';
				$html .= ' </div>';
			}
		}

		return $html;
	}
}
