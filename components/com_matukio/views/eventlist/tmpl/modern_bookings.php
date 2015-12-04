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
?>

<div class="mat_events">
<div class="mat_events_holder clearfix">
<?php
$cureventid = 0;
$bcount = 0;

for ($i = 0; $i < count($this->mybookedEvents); $i++)
{
	$event = $this->mybookedEvents[$i];

	// Hack for multiple bookings with the same event
	if (empty($cureventid))
	{
		$cureventid = $event->id;
	}
	else
	{
		if ($cureventid != $event->id)
		{
			// Reset
			$bcount = 0;
			$cureventid = $event->id;
		}
	}

	// Check if we are able to book
	$buchopt = MatukioHelperUtilsEvents::getEventBookableArray(1, $event, $user->id);

	$link = JRoute::_("index.php?option=com_matukio&view=booking&uuid=" . $buchopt[2][$bcount]->uuid);

	$booking_id = MatukioHelperUtilsBooking::getBookingId($buchopt[2][$bcount]->id);

	// Image
	// Todo Update Sometime
	$zusimage = "";
	$zusbild = 0;

	$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2701.png";
	$zusimage = MatukioHelperUtilsBasic::getComponentImagePath() . "2606.png";

	if ($user->id == 0)
	{
		$zusimage = "";
	}

	if ($event->cancelled == 1)
	{
		$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2604.png";
		$zusimage = MatukioHelperUtilsBasic::getComponentImagePath() . "2200.png";
	}

	if ($event->image != "" AND  MatukioHelperSettings::getSettings('event_image', 1) == 1)
	{
		$linksbild = MatukioHelperUtilsBasic::getEventImagePath(1) . $event->image;
		$zusbild = 1;
	}

	$class_even = ($i % 2 == 0) ? " mat_single_even" : "";
?>
<div class="mat_single_event_holder<?php echo $class_even ?>">
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
			       title="<?php echo $event->title; ?>"><?php echo JText::_($event->title); ?></a></h3>

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
						$img = '<img src="' . $locimg . '" title="' . JText::_("COM_MATUKIO_FIELDS_CITY") . '" style="width: 18px; vertical-align:middle" />';
					}
					else
					{
						$locimg = MatukioHelperUtilsBasic::getComponentImagePath() . "home.png";
						$img = '<img src="' . $locimg . '" title="' . JText::_("COM_MATUKIO_FIELDS_CITY") . '" style="width: 18px; vertical-align:middle" />';
					}
				}

				if ($event->webinar == 1)
				{
					echo '<strong> ' . $img . $event->place . " ";

					if ($event->showbegin)
					{
						echo JText::_("COM_MATUKIO_AT") . " " . $begin;
					}

					echo '</strong>';
				}
				else
				{
					if ($event->gmaploc != "")
					{
						echo '<a title="' . JTEXT::_('COM_MATUKIO_MAP') . '" class="modal cjmodal" href="'
							. JRoute::_('index.php?option=com_matukio&view=map&tmpl=component&event_id=' . $event->id)
							. '" rel="{handler: \'iframe\', size: {x: 600, y: 400}}">' . $img . '</a>';
					}
					else
					{
						echo $img;
					}

					echo '<strong> ' . $event->place . " ";

					if ($event->showbegin)
					{
						echo JText::_("COM_MATUKIO_AT") . " " . $begin;
					}

					echo '</strong>';

				}
				?>
			</div>

			<div class="mat_event_short_description">
				<span class="mat_shortdesc"><?php echo JText::_($event->shortdesc); ?></span>
			</div>

			<?php
			// Booking id
			echo " <span class=\"mat_small mat_booking_id\">" . JTEXT::_('COM_MATUKIO_BOOKING_ID') . ": "
				. $booking_id . "</span>";

			// Show closing date
			if ($event->nrbooked < 1)
			{
			?>
				<div class="mat_event_cannot_book"> - <?php echo JTEXT::_('COM_MATUKIO_CANNOT_BOOK_ONLINE') ?></div>
			<?php
			}
			elseif ($event->showbooked > 0)
			{
				if ($buchopt[0] == 2)
				{
					$cltimezone = "";

					if (MatukioHelperSettings::getSettings('show_timezone', '1'))
					{
						$cltimezone = " (GMT " . JHTML::_('date', $buchopt[2][$bcount]->bookingdate, 'P') . ")";
					}

					echo " - <span class=\"mat_small mat_date_of_booking\">" . JTEXT::_('COM_MATUKIO_DATE_OF_BOOKING') . ": " . JHTML::_('date', $buchopt[2][0]->bookingdate,
							MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
						) . $cltimezone . "</span>";
				}
				else
				{
					if ($event->cancelled == 1)
					{
						$cltimezone = "";

						if (MatukioHelperSettings::getSettings('show_timezone', '1'))
						{
							$cltimezone = " (GMT " . JHTML::_('date', $event->booked, 'P') . ")";
						}

						echo " - <span class=\"mat_small mat_closing_date\">" . JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ": <del>" . JHTML::_('date', $event->booked,
								MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
							) . $cltimezone . "</del></span>";
					}
					else
					{
						$cltimezone = "";

						if (MatukioHelperSettings::getSettings('show_timezone', '1'))
						{
							$cltimezone = " (GMT " . JHTML::_('date', $event->booked, 'P') . ")";
						}

						echo " - <span class=\"mat_small mat_closing_date\">" . JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ": " . JHTML::_('date', $event->booked,
								MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
							) . $cltimezone . "</span>";
					}
				}
			}

			if (MatukioHelperSettings::getSettings('participant_grading_system', 0) && $buchopt[2][$bcount]->mark != 0)
			{
				echo " - " . JText::_("COM_MATUKIO_PARTICIPANT_MARK") . ": " . $buchopt[2][$bcount]->mark;
			}

			// infoline
			$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($event);
			if (MatukioHelperSettings::getSettings('event_showinfoline', 1) == 1) : ?>
				<div class="mat_event_infoline">
                                    <span class="mat_small">
                                        <?php echo JTEXT::_('COM_MATUKIO_CATEGORY') . ": " . $event->category; ?>
                                        <?php if ($event->nrbooked > 0)
                                        {
	                                        echo " - " . JText::_("COM_MATUKIO_ORGANISER") . ": " . $event->teacher . " - " . JTEXT::_('COM_MATUKIO_BOOKED_PLACES') . ": " . $gebucht->booked . " - " . JTEXT::_('COM_MATUKIO_BOOKABLE')
		                                        . ": " . $buchopt[4] . " - " . JTEXT::_('COM_MATUKIO_HITS') . ": " . $event->hits;
										}
?>
                                    </span>
				</div>
			<?php endif; ?>
			<?php
			// Fees
			if ($event->fees > 0)
			{
				echo '<div class="mat_event_fee">';
				$fee = MatukioHelperUtilsEvents::getFormatedCurrency($event->fees);
				$klasse = "mat_fees";
				if ($buchopt[0] == 2)
				{
					if (count($buchopt[2]) > 0)
					{
						if ($buchopt[2][0]->paid == 1)
						{
							$klasse = "mat_fees_paid";
						}
						else
						{
							$klasse = "mat_fees_not_paid";
						}

						if ($buchopt[2][0]->nrbooked > 1)
						{
							$fee = MatukioHelperUtilsEvents::getFormatedCurrency($buchopt[2][0]->payment_brutto);
						}
					}
				}
				echo "<span class=\"" . $klasse . "\">" . MatukioHelperSettings::getSettings('currency_symbol', '$')
					. " " . $fee . "</span>";
				echo "</div>";
			}
			?>
		</div>
	</div>
	<div class="mat_event_right">
		<div class="mat_event_right_inner">
			<?php
			// Show participants (if allowed)
			if ((MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 2 AND $user->id > 0
				) OR (MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 1)
				OR (MatukioHelperSettings::getSettings('frontend_teilnehmerviewteilnehmer', 0) > 0 AND $user->id > 0)
			)
			{
				$htxt = "&nbsp";

				if ($event->nrbooked > 0)
				{
					$viewteilnehmerlink = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $event->id . "&art=1");

					echo "<div class=\"mat_event_show_bookings\"><a href=\"" . $viewteilnehmerlink . "\"><span class=\"mat_button\" style=\"cursor:pointer;\"
                                        title=\"" . JTEXT::_('COM_MATUKIO_BOOKINGS') . "\">" . $gebucht->booked . "</span></a></div>";
				}
			}

			// Rating System
			if (MatukioHelperSettings::getSettings('frontend_ratingsystem', 0) > 0)
			{
				$htxt = "&nbsp";

				if ($current_date > $event->end AND $event->nrbooked > 0)
				{
					echo MatukioHelperUtilsEvents::getRatingPopup(
						MatukioHelperUtilsBasic::getComponentImagePath(),
						$event->id, $buchopt[2][0]->grade
					);
					$htbr = 30;
				}
				else
				{
					$htxt = "&nbsp;";
					$htbr = "";
				}
			}

			// Certification
			if (MatukioHelperSettings::getSettings('frontend_certificatesystem', 0) > 0)
			{
				if ($buchopt[2][0]->certificated == 1 AND $event->nrbooked > 0)
				{
					echo '<div class="mat_event_certificate">';
					echo MatukioHelperUtilsEvents::getPrintWindow(1, $event->sid, $buchopt[2][0]->id, '');
					echo '</div>';
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

// Increase the position
$bcount++;
}

if (count($this->mybookedEvents) == 0)
{
	echo JTEXT::_('COM_MATUKIO_NO_EVENT_FOUND');
}

// Color descriptions / traffic lights status
if (count($this->mybookedEvents) > 0 AND MatukioHelperSettings::getSettings('sem_hide_ampel', '') == 0 AND MatukioHelperSettings::getSettings('event_statusgraphic', 2) > 0)
{
	$dots = array(JTEXT::_('COM_MATUKIO_PARTICIPANT_ASSURED'), JTEXT::_('COM_MATUKIO_WAITLIST'), JTEXT::_('COM_MATUKIO_NO_SPACE_AVAILABLE'));
	echo MatukioHelperUtilsEvents::getColorDescriptions($dots[0], $dots[1], $dots[2], 1);
}
?>
</div>
</div>
<?php // Buttons ?>
	<div class="mat_buttons">
		<div class="mat_buttons_inner">
			<?php if (MatukioHelperSettings::getSettings('rss_feed', 1) == 1) : // RSS Feed
				$href = JURI::ROOT() . "index.php?tmpl=component&option=com_matukio&view=rss&format=raw";
				?>
				<span class="mat_button" style="cursor:pointer;" type="button"
				      onClick="window.open('<?php echo $href ?>');"><img src="<?php
					echo MatukioHelperUtilsBasic::getComponentImagePath() ?>3116.png" border="0" align="absmiddle"> <?php echo JTEXT::_('COM_MATUKIO_RSS_FEED') ?>
            </span>
			<?php endif; ?>

			<?php if (MatukioHelperSettings::getSettings('frontend_usericsdownload', 1) == 1) : // ICS Download
				$href = JURI::ROOT() . "index.php?tmpl=component&option=" . JFactory::getApplication()->input->get('option') . "&view=ics&format=raw";
				?>
				<span class="mat_button" style="cursor:pointer;" type="button"
				      onClick="window.open('<?php echo $href ?>');"><img src="<?php
					echo MatukioHelperUtilsBasic::getComponentImagePath() ?>3316.png" border="0"
				                                                         align="absmiddle"> <?php echo JTEXT::_('COM_MATUKIO_DOWNLOAD_CALENDER_FILE') ?>
             </span>
			<?php endif; ?>

			<?php
			// Print Button
			echo MatukioHelperUtilsEvents::getPrintWindow((1 + 2), '', '', 'b');
			?>
	</div>
</div>
