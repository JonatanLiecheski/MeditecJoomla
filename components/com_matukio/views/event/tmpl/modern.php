<?php
/**
 * Matukio
 *
 * @package  Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All      rights reserved
 * @Joomla   ! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version  $Revision: 1.0.0 $
 **/
defined('_JEXEC') or die ('Restricted access');

global $mainframe;
$document = JFactory::getDocument();
$database = JFactory::getDBO();
$my = JFactory::getuser();

JHTML::_('behavior.modal');
JHTML::_('stylesheet', 'media/com_matukio/css/modern.css');

// Backward compatibilty
$buchopt = MatukioHelperUtilsEvents::getEventBookableArray($this->art, $this->event, $my->id, $this->uuid);

$nametemp = "";
$htxt = 2;

$bezahlt = 0;

if ($this->art == 1)
{
	$bezahlt = $this->booking->paid;
}

if ($this->art > 2)
{
	if ($usrid == 0)
	{
		$nametemp = MatukioHelperUtilsBasic::getBookedUserList($this->event);
	}
	elseif ($usrid > 0)
	{
		$nametemp = JFactory::getuser($usrid);
		$nametemp = $nametemp->name;
	}

	if ($nametemp == "")
	{
		$htxt = 2.2;
	}
}

// Status für Parser festlegen
$parse = "sem_unregistered";
if ($my->id > 0)
{
	$parse = "sem_registered";
}

if ($buchopt[0] == 2)
{
	$parse = "sem_booked";

	if ($buchopt[2][0]->paid > 0)
	{
		$parse = "sem_paid";
	}

	if ($buchopt[2][0]->certificated > 0)
	{
		$parse = "sem_certifcated";
	}
}
?>
<!-- Start Matukio by compojoom.com -->
<div id="matukio_holder" style="clear: both;">
	<?php if (MatukioHelperSettings::getSettings('show_event_title', 1)) : ?>
		<div class="componentheading">
			<h2><?php echo JText::_($this->event->title); ?></h2>
		</div>
	<?php endif; ?>

	<div id="mat_holder" class="clearfix">
	<div id="mat_topmenu">

	</div>
	<div id="mat_infobox">
	<table class="mat_infotable" border="0" width="100%">
	<tr>
		<td class="key" width="80px">
			<?php echo JTEXT::_('COM_MATUKIO_NUMBER'); ?>
		</td>
		<td>
			<?php echo $this->event->semnum; ?>
		</td>
	</tr>
	<tr>
		<td class="key" width="80px">
			<?php echo JTEXT::_('COM_MATUKIO_STATUS'); ?>
		</td>
		<td>
			<?php
			// Status anzeigen
			$htxt = $buchopt[1];

			if ($this->event->nrbooked < 1)
			{
				$htxt = JTEXT::_('COM_MATUKIO_CANNOT_BOOK_ONLINE');
			}

			echo $htxt;
			?>
		</td>
	</tr>

	<?php
	$htx1 = "";
	$htx2 = "";

	if ($this->event->cancelled == 1)
	{
		$htx1 = "\n<span class=\"sem_cancelled\">" . JTEXT::_('COM_MATUKIO_CANCELLED') . " </span>(<del>";
		$htx2 = "</del>)";
	}

	if ($this->event->showbegin > 0) :
	?>
		<tr>
			<td class="key" width="80px">
				<?php echo JTEXT::_('COM_MATUKIO_BEGIN'); ?>
			</td>
			<td>
				<?php
				$cltimezone = "";

				if (MatukioHelperSettings::getSettings('show_timezone', '1'))
				{
					$cltimezone = " (GMT " . JHTML::_('date', $this->event->begin, 'P') . ")";
				}

				echo $htx1 . JHTML::_('date', $this->event->begin, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')) . $cltimezone . $htx2;
				?>
			</td>
		</tr>
	<?php endif; ?>
	<?php if ($this->event->showend > 0) : ?>
		<tr>
			<td class="key" width="80px">
				<?php echo JTEXT::_('COM_MATUKIO_END'); ?>
			</td>
			<td>
				<?php
				$cltimezone = "";

				if (MatukioHelperSettings::getSettings('show_timezone', '1'))
				{
					$cltimezone = " (GMT " . JHTML::_('date', $this->event->end, 'P') . ")";
				}

				echo $htx1 . JHTML::_('date', $this->event->end, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')) . $cltimezone . $htx2;
				?>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	if ($this->event->showbooked > 0)
	{
	?>
		<?php if ($this->art == 0 OR ($this->art == 3 AND $usrid == 0)): ?>
		<tr>
			<td class="key" width="80px">
				<?php echo JTEXT::_('COM_MATUKIO_CLOSING_DATE'); ?>
			</td>
			<td>
				<?php
				$cltimezone = "";

				if (MatukioHelperSettings::getSettings('show_timezone', '1'))
				{
					$cltimezone = " (GMT " . JHTML::_('date', $this->event->booked, 'P') . ")";
				}

				echo $htx1 . JHTML::_('date', $this->event->booked, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')) . $cltimezone . $htx2;
				?>
			</td>
		</tr>
	<?php else: ?>
		<tr>
			<td class="key" width="80px">
				<?php echo JTEXT::_('COM_MATUKIO_DATE_OF_BOOKING'); ?>
			</td>
			<td>
				<?php
					$cltimezone = "";

					if (!empty($buchopt[2][0]->bookingdate))
					{
						if (MatukioHelperSettings::getSettings('show_timezone', '1'))
						{
							$cltimezone = " (GMT " . JHTML::_('date', $buchopt[2][0]->bookingdate, 'P') . ")";
						}

						echo JHTML::_('date', $buchopt[2][0]->bookingdate, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')) . $cltimezone;
					}
					else
					{
						if (MatukioHelperSettings::getSettings('show_timezone', '1'))
						{
							$cltimezone = " (GMT " . JHTML::_('date', 'now', 'P') . ")";
						}

						echo JHTML::_('date', 'now', MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')) . $cltimezone;
					}
				?>
			</td>
		</tr>
	<?php
	endif;
	}

	if ($this->event->teacher != "") : ?>
		<tr>
			<td class="key" width="80px">
				<?php echo JTEXT::_('COM_MATUKIO_TUTOR'); ?>
			</td>
			<td>
				<?php
				echo $this->event->teacher;
				?>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	if (MatukioHelperSettings::getSettings('organizer_pages', 1))
	{
		$organizer = MatukioHelperOrganizer::getOrganizer($this->event->publisher);

		if (!empty($organizer))
		{
		$link = JRoute::_("index.php?option=com_matukio&view=organizer&id=" . $organizer->id . ":" . JFilterOutput::stringURLSafe($organizer->name));
		?>
			<tr>
				<td class="key" width="80px">
					<?php echo JTEXT::_('COM_MATUKIO_ORGANIZER'); ?>
				</td>
				<td>
					<?php
					echo "<a href=\"" . $link . "\" title=\"" . $organizer->name . "\">";
					echo $organizer->name;
					echo "</a>";
					?>
				</td>
			</tr>
		<?php
		}
	}
	?>
	<?php if ($this->event->target != "") : ?>
		<tr>
			<td class="key" width="80px">
				<?php echo JTEXT::_('COM_MATUKIO_TARGET_GROUP'); ?>
			</td>
			<td>
				<?php echo $this->event->target; ?>
			</td>
		</tr>
	<?php endif; ?>
	<?php if ($this->event->webinar != 1) : ?>
		<tr>
			<td class="key" width="80px">
				<?php echo JTEXT::_('COM_MATUKIO_CITY'); ?>
			</td>
			<td>
				<?php
				if (empty($this->event->place_id))
				{
					echo $this->event->place;
				}
				else
				{
					if (!empty($this->location))
					{
						$link = JRoute::_("index.php?option=com_matukio&view=location&id=" . $this->location->id);
						echo '<a href="' . $link . '" title="' . $this->location->title . '">' . $this->location->location . '</a>';
					}
				}
				?>
			</td>
		</tr>
	<?php endif; ?>
	<?php if ($this->event->nrbooked > 0 AND MatukioHelperSettings::getSettings('event_showinfoline', 1)) : ?>
		<tr>
			<td class="key" width="80px">
				<?php echo JTEXT::_('COM_MATUKIO_BOOKABLE'); ?>
			</td>
			<td>
				<?php echo $buchopt[4]; ?>
			</td>
		</tr>
	<?php endif; ?>
	<?php if ($this->event->fees > 0) : ?>
		<tr>
			<td class="key" width="80px">
				<?php echo JTEXT::_('COM_MATUKIO_FEES'); ?>
			</td>
			<td>
				<?php
				$tmp = MatukioHelperUtilsEvents::getFormatedCurrency($this->event->fees, MatukioHelperSettings::getSettings('currency_symbol', '$'));

				if ($buchopt[0] == 2)
				{
					if ($buchopt[2][0]->paid == 1)
					{
						$tmp .= " - " . JTEXT::_('COM_MATUKIO_PAID');
					}
				}

				echo $tmp . " " . JTEXT::_('COM_MATUKIO_PRO_PERSON');

				if (MatukioHelperSettings::getSettings('show_different_fees', 1) && $this->event->different_fees)
				{
					echo MatukioHelperFees::getFeesShow($this->event);
				}
				?>
			</td>
		</tr>
	<?php endif; ?>
	<?php
	// Files:

	$datfeld = MatukioHelperUtilsEvents::getEventFileArray($this->event);
	$htxt = array();

	for ($i = 0; $i < count($datfeld[0]); $i++)
	{
		if (    $datfeld[0][$i] != "" AND ($datfeld[2][$i] == 0 OR ($my->id > 0	AND $datfeld[2][$i] == 1) OR ($buchopt[0] == 2 AND $datfeld[2][$i] == 2)
			OR ($buchopt[2][0]->paid == 1 AND $datfeld[2][$i] == 3)))
		{
			// Still a joke
			$filelink = JRoute::_("index.php?option=com_matukio&view=matukio&task=downloadfile&a6d5dgdee4cu7eho8e7fc6ed4e76z="
				. sha1(md5($datfeld[0][$i])) . $this->event->id
			);

			$htxt[] = "<tr>
			                 <td style=\"white-space:nowrap;vertical-align:top;\">
	                             <span style=\"background-image:url(" . MatukioHelperUtilsBasic::getComponentImagePath()
				. "0002.png);background-repeat:no-repeat; background-position:2px;padding-left:18px;vertical-align:middle;\">
	                    <a href=\"" . $filelink . "\" target=\"_blank\">" . $datfeld[0][$i]
				. "</a>
	                                            </span>
	                                        <br />"
				. $datfeld[1][$i]
				. "</td>
	                 </tr>";

		}
	}

	if (count($htxt) > 0)
	{
		echo "<tr>";
		echo "<td colspan='2'>";
		echo JTEXT::_('COM_MATUKIO_FILES');
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td colspan='2'>";
		echo '<table width="100%" border="0">';
		echo implode($htxt);
		echo "</table>";
		echo "</td>";
		echo "</tr>";
	}
	?>
	</table>
	<?php if ($this->event->webinar != 1 && ($this->event->gmaploc != "" || ($this->location != null && !empty($this->location->gmaploc)))) : ?>
		<div id="mat_map">
			<?php
			Jhtml::_('behavior.framework');
			$api = 'http://maps.googleapis.com/maps/api/js?sensor=false';

			$uri = JURI::getInstance();

			if ($uri->isSSL())
			{
				$api = 'https://maps.googleapis.com/maps/api/js?sensor=false';
			}

			$document = JFactory::getDocument();
			$document->addScript($api);

			$location = $this->event->gmaploc;

			if (empty($location) && ($this->location != null && !empty($this->location->gmaploc)))
			{
				$location = $this->location->gmaploc;
			}

			$script = "window.addEvent('domready', function() {

	                    geocoder = new google.maps.Geocoder();
	                    var myOptions = {
	                        zoom:8,
	                        mapTypeId:google.maps.MapTypeId.ROADMAP
	                    };
	                    var map = new google.maps.Map(document.getElementById('map_canvas'),
	                              myOptions);
	                    var address = '" . preg_replace("#\n|\r#", ' ', str_replace('<br />', ',', $location)) . "';
	                    geocoder.geocode( { 'address': address}, function(results, status) {
	                    if (status == google.maps.GeocoderStatus.OK) {
	                        map.setCenter(results[0].geometry.location);
	                        var marker = new google.maps.Marker({
	                        map: map,
	                        position: results[0].geometry.location
	                    });

	                    var infowindow = new google.maps.InfoWindow({
	                        content: address
	                    });
	                    google.maps.event.addListener(marker, 'click', function() {
	                        infowindow.open(map,marker);
	                    });

	                    } else {
	                        alert('Geocode was not successful for the following reason: ' + status);
	                    }
	                    });

	                    });";

			$document->addScriptDeclaration($script);
			?>
			<a title="<?php JTEXT::_('COM_MATUKIO_MAP'); ?>" class="modal cjmodal" href="<?php echo
			JRoute::_('index.php?option=com_matukio&view=map&tmpl=component&event_id=' . $this->event->id);
			?>" rel="{handler: 'iframe', size: {x: 600, y: 400}}" style="position: relative; display: block !important;">
				<div id="map_canvas" style="width: 100%;height: 200px; border-radius: 0 0 0 15px"></div>
			</a>
		</div>
	<?php endif; ?>
	</div>
	<div id="mat_description">
		<div id="mat_description_inner">
			<?php if (MatukioHelperSettings::getSettings('social_media', 1)) : ?>
				<div id="mat_social">

					<!-- Facebook -->
					<script>(function (d, s, id) {
							var js, fjs = d.getElementsByTagName(s)[0];
							if (d.getElementById(id)) return;
							js = d.createElement(s);
							js.id = id;
							js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
							fjs.parentNode.insertBefore(js, fjs);
						}(document, 'script', 'facebook-jssdk'));
					</script>

					<div class="fb-like" data-href="<?php echo JURI::current(); ?>" data-send="false"
					     data-layout="button_count"
					     data-width="100" data-show-faces="false" data-action="recommend" style="margin-right: 25px;"></div>

					<!-- Twitter -->
					<div class="twitter-btn">
						<a href="https://twitter.com/share" class="twitter-share-button" data-lang="en">Tweet</a>
						<script>
							!function (d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (!d.getElementById(id)) {
									js = d.createElement(s);
									js.id = id;
									js.src = "//platform.twitter.com/widgets.js";
									fjs.parentNode.insertBefore(js, fjs);
								}
							}
								(document, "script", "twitter-wjs");
						</script>
					</div>

					<!-- Google plus one -->
					<div class="g-plusone" data-size="medium"></div>

					<script type="text/javascript">
						(function () {
							var po = document.createElement('script');
							po.type = 'text/javascript';
							po.async = true;
							po.src = 'https://apis.google.com/js/plusone.js';
							var s = document.getElementsByTagName('script')[0];
							s.parentNode.insertBefore(po, s);
						})();
					</script>
					<!-- Plus One End -->
				</div>
			<?php endif; ?>

			<?php
			// Show description
			if ($this->event->description != "")
			{
				echo MatukioHelperUtilsBasic::parseOutput(JHtml::_('content.prepare', JText::_($this->event->description)), $parse);
			}
			?>
		</div>
	</div>
	<div id="mat_bottom">
		<?php
		// Kontaktformular
		if (MatukioHelperSettings::getSettings("sendmail_contact", 1))
		{
			echo MatukioHelperUtilsEvents::getEmailWindow(MatukioHelperUtilsBasic::getComponentImagePath(), $this->event->id, 1, "modern");
		}

		// Kalender
		if (MatukioHelperSettings::getSettings('frontend_usericsdownload', 1) > 0)
		{
			echo MatukioHelperUtilsEvents::getCalendarButton($this->event);
		}

		// Print Overview (normally always allowed)
		echo MatukioHelperUtilsEvents::getPrintWindow(2, $this->event->id, '', 'b', "modern");

		// Participants (if allowed)
		if ((MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 2 AND $this->user->id > 0) // Falls registrierte sehen dürfen und user registriert ist und art 0 ist
			OR (MatukioHelperSettings::getSettings('frontend_userviewteilnehmer', 0) == 1)) //    ODER Jeder (auch unregistrierte die Teilnehmer sehen dürfen und art 0 ist
		{
			$htxt = "&nbsp";

			if ($this->event->nrbooked > 0)
			{
				$viewteilnehmerlink = JRoute::_("index.php?option=com_matukio&view=participants&cid=" . $this->event->id . "&art=0");

				echo " <a href=\"" . $viewteilnehmerlink . "\"><span class=\"mat_button\" style=\"cursor:pointer;\"
                                        title=\"" . JTEXT::_('COM_MATUKIO_BOOKINGS') . "\">"
					. "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "0004.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
					. JTEXT::_('COM_MATUKIO_PARTICIPANTS') . "</span></a>";
			}
		}

		// Book
		if (($this->user->id OR MatukioHelperSettings::getSettings('booking_unregistered', 1) == 1)
			AND $this->event->cancelled == 0
			AND $this->event->nrbooked > 0
			AND (count($buchopt[2]) == 0 OR (count($buchopt[2]) > 0 && MatukioHelperSettings::getSettings('frontend_usermehrereplaetze', 1) != 0)))
		{
			$bookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&cid=" . $this->event->id . ":"
				. JFilterOutput::stringURLSafe($this->event->title)
			);

			echo " <a title=\"" . JTEXT::_('COM_MATUKIO_BOOK') . "\" href=\"" . $bookinglink
				. "\"><span class=\"mat_book\" type=\"button\"><img src=\""
				. MatukioHelperUtilsBasic::getComponentImagePath()
				. "1116.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
				. JTEXT::_('COM_MATUKIO_BOOK') . "</span></a>";
		}

		// Aenderungen speichern Veranstalter  , not really implemented here
		if ($this->art == 3 And $usrid != 0 AND ($this->event->nrbooked > 1 OR $zfleer == 0))
		{
			echo ' <input type="submit" class="button" value="' . JTEXT::_('COM_MATUKIO_SAVE_CHANGES') . '">';
		}

		// Aenderungen speichern Benutzer falls noch nicht gezahlt
		if ($this->art == 1 AND strtotime($this->event->booked) -
			time() >= (MatukioHelperSettings::getSettings('booking_stornotage', 1) * 24 * 60 * 60)
			AND $bezahlt == 0)
		{
			if ($this->user->id > 0)
			{
				$unbookinglink = JRoute::_("index.php?option=com_matukio&view=bookevent&task=cancelBooking&cid=" . $this->event->id);

				if (MatukioHelperSettings::getSettings('booking_stornotage', 1) > -1)
				{
					echo " <a border=\"0\" href=\"" . $unbookinglink
						. "\" ><span class=\"mat_book\" type=\"button\"><img src=\""
						. MatukioHelperUtilsBasic::getComponentImagePath() . "1532.png\" border=\"0\" align=\"absmiddle\" style=\"width: 16px; height: 16px;\">&nbsp;"
						. JTEXT::_('COM_MATUKIO_BOOKING_CANCELLED') . "</span></a>";
				}
			}
		}

		// Booking details to first booking
		if (count($buchopt[2]) > 0)
		{
			$blink = JRoute::_("index.php?option=com_matukio&view=booking&uuid=" . $buchopt[2][0]->uuid);

			echo " <a href=\"" . $blink . "\"><span class=\"mat_button\" style=\"cursor:pointer;\"
                                        title=\"" . JTEXT::_('COM_MATUKIO_BOOKING_DETAILS') . "\">"
				. "<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "0004.png\" border=\"0\" align=\"absmiddle\">&nbsp;"
				. JTEXT::_('COM_MATUKIO_BOOKING_DETAILS') . "</span></a>";

		}
	?>
	</div>
	</div>
	<?php echo $this->jevent->afterDisplayContent; ?>
	<?php
	echo MatukioHelperUtilsBasic::getCopyright();
	?>
</div>
