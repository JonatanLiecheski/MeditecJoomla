<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       11.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
?>
<!-- Begin Matukio by compojoom.com -->
<div class="mat_events">
<div class="mat_events_holder clearfix">
<?php
for ($i = 0; $i < count($this->allEvents); $i++)
{
$event = $this->allEvents[$i];

// Check if we are able to book
$buchopt = MatukioHelperUtilsEvents::getEventBookableArray(0, $event, $user->id);

$eventid_l = $event->id . ':' . JFilterOutput::stringURLSafe($event->title);
$catid_l   = $event->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($event->catid));

$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l), false);

// Image
// TODO Update Sometime
$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2601.png";
$zusimage  = "";
$zusbild   = 0;

if ($user->id == $event->publisher)
{
	$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2603.png";
	$zusimage  = MatukioHelperUtilsBasic::getComponentImagePath() . "2607.png";
}

if ($buchopt[0] == 2)
{
	$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2602.png";
	$zusimage  = MatukioHelperUtilsBasic::getComponentImagePath() . "2606.png";
}

// Category image
$cat_params = json_decode($event->catparams);

if (!empty($cat_params->image))
{
	$linksbild = $cat_params->image;
}

if ($user->id == 0)
{
	$zusimage = "";
}

if ($event->cancelled == 1)
{
	$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2604.png";
	$zusimage  = MatukioHelperUtilsBasic::getComponentImagePath() . "2200.png";
}

if ($event->image != "" AND MatukioHelperSettings::getSettings('event_image', 1) == 1)
{
	$linksbild = MatukioHelperUtilsBasic::getEventImagePath(1) . $event->image;
	$zusbild   = 1;
}

$class_even = ($i % 2 == 0) ? " mat_single_even" : "";

$hot_event      = ($event->hot_event) ? " mat_hot_event" : "";
$hot_event_text = ($event->hot_event) ? JText::_("COM_MATUKIO_HOT") : "";
$top_event      = ($event->top_event) ? " mat_top_event" : "";
?>
<div class="mat_single_event_holder<?php echo $class_even . $hot_event . $top_event ?>">
<div class="mat_single_event_holder_inner">
<div class="mat_event_image">
	<div class="mat_event_image_inner">
		<a title="<?php echo JText::_($event->title) ?>" href="<?php echo $link ?>">
			<img src="<?php echo $linksbild ?>" border="0"/>
		</a>
		<?php if ($zusbild == 1 AND $zusimage != "" AND MatukioHelperSettings::getSettings('event_image', 1) > 0) : ?>
			<div class="mat_event_add_image">
				<img src="<?php echo $zusimage ?>"/>
			</div>
		<?php endif; ?>
	</div>
</div>
<div class="mat_event_content">
<div class="mat_event_content_inner">
<h3><a href="<?php echo $link; ?>"
       title="<?php echo $event->title; ?>"><?php echo JText::_($event->title); ?></a> <?php echo $hot_event_text ?>
</h3>

<div class="mat_event_location">
	<?php
	// Location & Begin
	$begin = JHTML::_('date', $event->begin, MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i'));

	if (MatukioHelperSettings::getSettings('show_timezone', '1'))
	{
		$begin .= " (GMT " . JHTML::_('date', $event->begin, 'P') . ")";
	}

	$img = "";

	if (MatukioHelperSettings::getSettings('location_image', 1))
	{
		if ($event->webinar == 1)
		{
			$locimg = MatukioHelperUtilsBasic::getComponentImagePath() . "webinar.png";
			$img    = '<img src="' . $locimg . '" title="' . JText::_("COM_MATUKIO_WEBINAR") . '" style="width: 18px; vertical-align:middle" /> ';
		}
		else
		{
			$locimg = MatukioHelperUtilsBasic::getComponentImagePath() . "home.png";
			$img    = '<img src="' . $locimg . '" title="' . JText::_("COM_MATUKIO_FIELDS_CITY") . '" style="width: 18px; vertical-align:middle" />';
		}
	}

	if ($event->webinar == 1)
	{
		echo '<strong> ' . $img . " " . $begin . '</strong>';
	}
	else
	{
		$locobj = null;

		if ($event->place_id > 0)
		{
			$locobj = MatukioHelperUtilsEvents::getLocation($event->place_id);
		}

		if ($event->gmaploc != "")
		{
			echo '<a title="' . JTEXT::_('COM_MATUKIO_MAP') . '" class="modal cjmodal" href="'
				. JRoute::_('index.php?option=com_matukio&view=map&tmpl=component&event_id=' . $event->id)
				. '" rel="{handler: \'iframe\', size: {x: 600, y: 400}}">' . $img . '</a>';
		}
		elseif ($event->place_id > 0)
		{
			if (!empty($locobj) && !empty($locobj->gmaploc))
			{
				echo '<a title="' . JTEXT::_('COM_MATUKIO_MAP') . '" class="modal cjmodal" href="'
					. JRoute::_('index.php?option=com_matukio&view=map&tmpl=component&event_id=' . $event->id)
					. '" rel="{handler: \'iframe\', size: {x: 600, y: 400}}">' . $img . '</a> ';
			}
		}
		else
		{
			echo $img;
		}

		if ($event->place_id == 0)
		{
			// Custom location
			echo '<strong> ' . $event->place . " ";

			if ($event->showbegin)
			{
				echo JText::_("COM_MATUKIO_AT") . " " . $begin;
			}


			echo '</strong>';
		}
		else
		{
			if (!empty($locobj))
			{
				$link = JRoute::_("index.php?option=com_matukio&view=location&id=" . $locobj->id . ":" . JFilterOutput::stringURLSafe($locobj->title));

				echo '<strong><a href="' . $link . '" >' . JText::_($locobj->title) . '</a>' . " ";

				if ($event->showbegin)
				{
					echo JText::_("COM_MATUKIO_AT") . " " . $begin;
				}

				echo '</strong>';
			}
			else
			{
				throw new Exception("COM_MATUKIO_ERROR_LOADING_LOCATION_WITH_ID" . $locobj->id);
			}
		}
	}
	?>
</div>

<div class="mat_event_short_description">
	<span class="mat_shortdesc"><?php echo JText::_($event->shortdesc); ?></span>
</div>

<?php
if ($event->nrbooked < 1)
{
	// Show closing date
	?>
	<div class="mat_event_cannot_book"><?php echo JTEXT::_('COM_MATUKIO_CANNOT_BOOK_ONLINE') ?></div>
<?php
}
elseif ($event->showbooked > 0)
{
	if ($buchopt[0] == 2)
	{
		$cltimezone = "";

		if (MatukioHelperSettings::getSettings('show_timezone', '1'))
		{
			$cltimezone = " (GMT " . JHTML::_('date', $buchopt[2][0]->bookingdate, 'P') . ")";
		}

		echo "<span class=\"mat_small mat_bookingdate\">" . JTEXT::_('COM_MATUKIO_DATE_OF_BOOKING') . ": " . JHTML::_('date', $buchopt[2][0]->bookingdate,
				MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
			) . $cltimezone . "</span>";
	}
	else
	{
		$tzbooked = "";

		if (MatukioHelperSettings::getSettings('show_timezone', '1'))
		{
			$tzbooked = " (GMT " . JHTML::_('date', $event->booked, 'P') . ")";
		}

		if ($event->cancelled == 1)
		{
			echo "<span class=\"mat_small mat_booked\">" . JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ": <del>" . JHTML::_('date', $event->booked,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
				) . $tzbooked . "</del></span>";
		}
		else
		{
			echo "<span class=\"mat_small mat_booked\">" . JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ": " . JHTML::_('date', $event->booked,
					MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
				) . $tzbooked . "</span>";
		}
	}
}

// Infoline
$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($event);

if (MatukioHelperSettings::getSettings('event_showinfoline', 1) == 1)
{
	?>
	<div class="mat_event_infoline">
				<span class="mat_small">
					<?php
					echo JTEXT::_('COM_MATUKIO_CATEGORY') . ": " . $event->category;

					$teacher_show = "";

					if (MatukioHelperSettings::getSettings('organizer_pages', 1))
					{
						$organizer = MatukioHelperOrganizer::getOrganizer($event->publisher);

						if (!empty($organizer))
						{
							$link         = JRoute::_("index.php?option=com_matukio&view=organizer&id=" . $organizer->id . ":" . JFilterOutput::stringURLSafe($organizer->name));
							$teacher_show = "<a href=\"" . $link . "\" title=\"" . $organizer->name . "\">";
							$teacher_show .= $organizer->name;
							$teacher_show .= "</a>";
						}
						else
						{
							$teacher_show = $event->teacher;
						}
					}
					else
					{
						$teacher_show = $event->teacher;
					}

					echo "<span class=\"mat_organizer\"> - " . JText::_("COM_MATUKIO_ORGANISER") . ": " . $teacher_show . "</span>";

					if ($event->nrbooked > 0)
					{
						echo "<span class=\"mat_booked_places\"> - " . JTEXT::_('COM_MATUKIO_BOOKED_PLACES') . ": " . $gebucht->booked . "</span>"
							. "<span class=\"mat_bookable\"> - " . JTEXT::_('COM_MATUKIO_BOOKABLE') . ": " . $buchopt[4] . "</span>";
					}

					echo "<span class=\"mat_hits\"> - " . JTEXT::_('COM_MATUKIO_HITS') . ": " . $event->hits . "</span>";
					?>
				</span>
	</div>
<?php
}

// Fees
if ($event->fees > 0 && MatukioHelperSettings::getSettings('modern_eventlist_show_fee', 1))
{
	echo '<div class="mat_event_fee">';
	echo MatukioHelperUtilsEvents::getFormatedCurrency($event->fees, MatukioHelperSettings::getSettings('currency_symbol', '$'));
	echo "</div>";
}
?>
</div>
</div>
<div class="mat_event_right">
	<div class="mat_event_right_inner">
		<?php
		// Show participants (if allowed)
		if ((MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 2 AND $user->id > 0) // Falls registrierte sehen dürfen und user registriert ist und art 0 ist
			OR (MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 1)
		) //    ODER Jeder (auch unregistrierte die Teilnehmer sehen dürfen und art 0 ist
		{
			$htxt = "&nbsp";

			if ($event->nrbooked > 0)
			{
				$viewteilnehmerlink = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $event->id . "&art=0");

				echo "<div class=\"mat_event_show_bookings\"><a href=\"" . $viewteilnehmerlink . "\"><span class=\"mat_button\" style=\"cursor:pointer;\"
                                        title=\"" . JTEXT::_('COM_MATUKIO_BOOKINGS') . "\">" . $gebucht->booked . "</span></a></div>";
			}
		}

		// Status image
		if (MatukioHelperSettings::getSettings('event_statusgraphic', 2) > 0)
		{
			// Ampel
			if (MatukioHelperSettings::getSettings('event_statusgraphic', 2) == 1 AND $event->nrbooked > 0)
			{
				echo " <div class=\"mat_event_status_lights\"><img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "230" . $buchopt[3]
					. ".png\" alt=\"" . $buchopt[1] . "\" class=\"hasTip\" title=\"" . $buchopt[1] . "\" /></div>";
			}
			elseif (MatukioHelperSettings::getSettings('event_statusgraphic', 2) == 2 AND $event->nrbooked > 0)
			{
				// Säule
				echo "<div class=\"mat_event_status_column\">" . MatukioHelperUtilsEvents::getProcentBar($event->maxpupil, $buchopt[4], $buchopt[3]) . "</div>";
			}
		}

		?>
	</div>
</div>
<?php
echo "<div style=\"clear:both\"></div>";
echo "</div>"; // Inner
echo "</div>"; // End Single Event holder
} // End for

if (count($this->allEvents) == 0)
{
	echo JTEXT::_('COM_MATUKIO_NO_EVENT_FOUND');
}

// Pagination
if (count($this->allEvents) < $this->total)
{
	echo $this->pageNavAllEvents;
}

// Color descriptions / traffic lights status
if (count($this->allEvents) > 0 AND MatukioHelperSettings::getSettings('sem_hide_ampel', '') == 0
	AND MatukioHelperSettings::getSettings('event_statusgraphic', 2) > 0)
{
	$dots = array(JTEXT::_('COM_MATUKIO_NOT_EXCEEDED'), JTEXT::_('COM_MATUKIO_BOOKING_ON_WAITLIST'), JTEXT::_('COM_MATUKIO_UNBOOKABLE'));
	echo MatukioHelperUtilsEvents::getColorDescriptions($dots[0], $dots[1], $dots[2]);
}
?>
</div>
</div>
<?php // Buttons ?>
<div class="mat_buttons">
	<div class="mat_buttons_inner">
		<?php
		if (MatukioHelperSettings::getSettings('rss_feed', 1) == 1) : // RSS Feed
			$href = JURI::ROOT() . "index.php?tmpl=component&option=com_matukio&view=rss&format=raw";
			?>
			<span class="mat_button" style="cursor:pointer;" type="button"
			      onClick="window.open('<?php echo $href ?>');"><img src="<?php
				echo MatukioHelperUtilsBasic::getComponentImagePath() ?>3116.png" border="0"
			                                                         align="absmiddle"> <?php echo JTEXT::_('COM_MATUKIO_RSS_FEED') ?>
            </span>
		<?php endif; ?>

		<?php if (MatukioHelperSettings::getSettings('frontend_usericsdownload', 1) == 1) : // ICS Download
			$href = JURI::ROOT() . "index.php?tmpl=component&option=" . JFactory::getApplication()->input->get('option') . "&view=ics&format=raw";
			?>
			<span class="mat_button" style="cursor:pointer;" type="button"
			      onClick="window.open('<?php echo $href ?>');"><img src="<?php
				echo MatukioHelperUtilsBasic::getComponentImagePath() ?>3316.png" border="0" align="absmiddle">
				<?php echo JTEXT::_('COM_MATUKIO_DOWNLOAD_CALENDER_FILE') ?>
             </span>
		<?php endif; ?>

		<?php
		// Print Button
		echo MatukioHelperUtilsEvents::getPrintWindow((0 + 2), '', '', 'b');
		?>
	</div>
</div>
<!-- End Matukio by compojoom.com -->
