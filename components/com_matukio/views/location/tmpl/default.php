<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       12.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal');
JHTML::_('stylesheet', 'media/com_matukio/css/modern.css');

if (empty($this->location))
{
	echo JText::_("COM_MATUKIO_NO_LOCATION_PROFILE");
	echo MatukioHelperUtilsBasic::getCopyright();

	return;
}

?>
<!-- Start Matukio by compojoom.com -->
<div class="componentheading">
	<h2><?php echo JText::_($this->location->title); ?></h2>
</div>


<div id="mat_holder">
	<div id="mat_infobox">
		<table class="mat_infotable table table-bordered" border="0" width="100%">
			<?php if (!empty($this->location->phone)) : ?>
				<tr>
					<td><?php echo JText::_("COM_MATUKIO_PHONE"); ?></td>
					<td><?php echo $this->location->phone; ?></td>
				</tr>
			<?php endif; ?>
			<?php if (!empty($this->location->email)) : ?>
				<tr>
					<td><?php echo JText::_("COM_MATUKIO_EMAIL"); ?></td>
					<td><?php echo $this->location->email; ?></td>
				</tr>
			<?php endif; ?>
			<?php if (!empty($this->location->website)) : ?>
				<tr>
					<td><?php echo JText::_("COM_MATUKIO_WEBSITE"); ?></td>
					<td><?php echo $this->location->website; ?></td>
				</tr>
			<?php endif; ?>
			<?php if ($this->location != null && !empty($this->location->gmaploc)) : ?>
				<tr>
				<td colspan="2">
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

					$location = $this->location->gmaploc;

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
					JRoute::_('index.php?option=com_matukio&view=map&tmpl=component&event_id=0&location_id=' . $this->location->id);
					?>" rel="{handler: 'iframe', size: {x: 600, y: 400}}">
						<div id="map_canvas" style="width: 100%;height: 200px; border-radius: 0 0 0 15px"></div>
					</a>
				</div>
			<?php endif; ?>
			</td>
			</tr>
		</table>
	</div>
	<?php
	// Description
	echo JHtml::_('content.prepare', $this->location->description);

	// CComment and co
	echo $this->jevent->afterDisplayContent;

	// Since 3.1.0 - Show upcoming events with that location
	if (MatukioHelperSettings::getSettings("locations_show_upcoming", 1))
	{
	?>

	<div id="upcoming_events">
		<?php
		if (count($this->upcoming_events))
		{
			echo "<h3>" . JText::_("COM_MATUKIO_UPCOMING_EVENTS_AT_THIS_LOCATION") . "</h3>";

			echo MatukioHelperUpcoming::getUpcomingEventsHTML($this->upcoming_events, $this->user);
		}
		?>
	</div>

	<?php
	}

	// Footer
	echo MatukioHelperUtilsBasic::getCopyright();
	?>
</div>
<!-- End Matukio by compojoom.com -->
