<?php
/**
 * @author Daniel Dimitrov
 * @date: 29.03.12
 *
 * @copyright  Copyright (C) 2008 - 2012 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.framework');

$api = 'http://maps.googleapis.com/maps/api/js?sensor=false';

$uri = JURI::getInstance();

if ($uri->isSSL())
{
	$api = 'https://maps.googleapis.com/maps/api/js?sensor=false';
}

$document = JFactory::getDocument();
$document->addScript($api);

$location = null;

if (!empty($this->event))
{
	$locobj = null;

	if ($this->event->place_id > 0)
	{
		$locobj = MatukioHelperUtilsEvents::getLocation($this->event->place_id);
	}

	$location = $this->event->gmaploc;

	if (empty($location) && ($locobj != null && !empty($locobj->gmaploc)))
	{
		$location = $locobj->gmaploc;
	}
}
elseif (!empty($this->location))
{
	$location = $this->location->gmaploc;
}
else
{
	throw new Exception("No location found");
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

<div id="map_canvas" style="width:570px; height:370px"></div>
