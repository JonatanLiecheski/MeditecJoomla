<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       11.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      2.0.0
 */

defined('_JEXEC') or die('Restricted access');
?>
<div class="mat_events">
<div class="mat_events_holder clearfix">
<?php
for ($i = 0; $i < count($this->myofferEvents); $i++)
{
$event = $this->myofferEvents[$i];

// Check if we are able to book
$buchopt = MatukioHelperUtilsEvents::getEventBookableArray(2, $event, $user->id);

$eventid_l = $event->id . ':' . JFilterOutput::stringURLSafe($event->title);
$catid_l = $event->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($event->catid));

// Edit event
$link = JRoute::_("index.php?option=com_matukio&view=createevent&cid=" . $event->eventid);

// Image
$zusimage = "";
$zusbild = 0;

$linksbild = MatukioHelperUtilsBasic::getComponentImagePath() . "2801.png";

if ($event->publisher == $user->id)
{
	$zusimage = MatukioHelperUtilsBasic::getComponentImagePath() . "2607.png";
}

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
						echo '<strong> ' . $img . $event->place . " " . JText::_("COM_MATUKIO_AT") . " " . $begin . '</strong>';
					}
					else
					{
						if ($event->gmaploc != "")
						{
							echo '<a title="' . JTEXT::_('COM_MATUKIO_MAP') . '" class="modal cjmodal" href="' . JRoute::_('index.php?option=com_matukio&view=map&tmpl=component&event_id=' . $event->id)
								. '" rel="{handler: \'iframe\', size: {x: 600, y: 400}}">' . $img . '</a>';
						}
						else
						{
							echo $img;
						}

						echo '<strong> ' . $event->place . " " . JText::_("COM_MATUKIO_AT") . " " . $begin . '</strong>';
					}
					?>
				</div>

				<div class="mat_event_short_description">
					<span class="mat_shortdesc"><?php echo JText::_($event->shortdesc); ?></span>
				</div>

				<?php
				// Show closing date
				if ($event->nrbooked < 1)
				{
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

						echo "<span class=\"mat_small\">" . JTEXT::_('COM_MATUKIO_DATE_OF_BOOKING') . ": " . JHTML::_('date', $buchopt[2][0]->bookingdate,
								MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
							) . $cltimezone . "</span>";
					}
					else
					{
						$cltimezone = "";

						if (MatukioHelperSettings::getSettings('show_timezone', '1'))
						{
							$cltimezone = " (GMT " . JHTML::_('date', $event->booked, 'P') . ")";
						}

						if ($event->cancelled == 1)
						{
							echo "<span class=\"mat_small\">" . JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ": <del>" . JHTML::_('date', $event->booked,
									MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
							) . $cltimezone . "</del></span>";
						}
						else
						{
							echo "<span class=\"mat_small\">" . JTEXT::_('COM_MATUKIO_CLOSING_DATE') . ": " . JHTML::_('date', $event->booked,
									MatukioHelperSettings::getSettings('date_format', 'd-m-Y, H:i')
							) . $cltimezone . "</span>";
						}
					}
				} ?>

				<?php
				// Infoline
				$gebucht = MatukioHelperUtilsEvents::calculateBookedPlaces($event);

				if (MatukioHelperSettings::getSettings('event_showinfoline', 1) == 1) : ?>
					<div class="mat_event_infoline">
						<span class="mat_small">
							<?php echo JTEXT::_('COM_MATUKIO_CATEGORY') . ": " . $event->category; ?>

							<?php
							if ($event->nrbooked > 0)
							{
								echo " - " . JText::_("COM_MATUKIO_ORGANISER") . ": " . $event->teacher . " - " . JTEXT::_('COM_MATUKIO_BOOKED_PLACES')
									. ": " . $gebucht->booked . " - " . JTEXT::_('COM_MATUKIO_BOOKABLE') . ": " . $buchopt[4]
									. " - " . JTEXT::_('COM_MATUKIO_HITS') . ": " . $event->hits;
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
				if ((MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 2 AND $user->id > 0) // Falls registrierte sehen dürfen und user registriert ist und art 0 ist
					OR (MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 1) // ODER Jeder (auch unregistrierte die Teilnehmer sehen dürfen und art 0 ist
					OR (MatukioHelperSettings::getSettings('frontend_teilnehmerviewteilnehmer', 0) > 0 AND $user->id > 0) // Wenn  Teilnehmer Teilnehmer sehen dürfen (wtf ist der check ob er teilnehmer ist?? nur mit art = 1??)
					OR (MatukioHelperSettings::getSettings('frontend_ownereditevent', 1) > 0)) // Falls Frontendedit event 1 ist und art = 2
				{
					$htxt = "&nbsp";

					if ($event->nrbooked > 0)
					{
						$viewteilnehmerlink = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $event->id . "&art=2");

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
						echo "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "240"
							. $event->grade . ".png\" alt=\"" . JTEXT::_('COM_MATUKIO_RATING') . "\">";
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
						// Barchart
							echo "<div class=\"mat_event_status_column\">"
								. MatukioHelperUtilsEvents::getProcentBar($event->maxpupil, $buchopt[4], $buchopt[3])
								. "</div>";
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

		if (count($this->myofferEvents) == 0)
		{
			echo JTEXT::_('COM_MATUKIO_NO_EVENT_FOUND');
		}

		// Pagination TODO include sometime
		// Color descriptions / traffic lights status
		if (count($this->myofferEvents) > 0 AND MatukioHelperSettings::getSettings('sem_hide_ampel', '') == 0 AND MatukioHelperSettings::getSettings('event_statusgraphic', 2) > 0)
		{
			$dots = array(JTEXT::_('COM_MATUKIO_EVENT_HAS_NOT_STARTED_YET'), JTEXT::_('COM_MATUKIO_EVENT_IS_RUNNING'), JTEXT::_('COM_MATUKIO_EVENT_HAS_ENDED'));
			echo MatukioHelperUtilsEvents::getColorDescriptions($dots[0], $dots[1], $dots[2]);
		}
		?>
	</div>
</div>
<?php // Buttons ?>
<div class="mat_buttons">
	<div class="mat_buttons_inner">
		<?php
		// Print Button
		echo MatukioHelperUtilsEvents::getPrintWindow((2 + 2), '', '', 'b');
		?>

		<?php
		/* New event button */
		if (JFactory::getUser()->authorise('core.create', 'com_matukio')):
			?>
			<a href="<?php echo JRoute::_("index.php?option=com_matukio&view=createevent"); ?>"><span class="mat_button"
			                                                                                          style="cursor:pointer;"><?php
					echo JHTML::_('image', MatukioHelperUtilsBasic::getComponentImagePath() . '1816.png', null, array('border' => '0', 'align' => 'absmiddle')); ?>
					<?php echo JTEXT::_('COM_MATUKIO_NEW_EVENT') ?></span></a>
		<?php endif; ?>
	</div>
</div>
