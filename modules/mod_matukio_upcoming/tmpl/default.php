<?php
/**
 * Matukio
 * @package  Joomla!
 * @Copyright (C) 2013 - Yves Hoppe - compojoom.com
 * @All      rights reserved
 * @Joomla   ! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version  $Revision: 2.2.0 $
 **/
defined('_JEXEC') or die('Restricted access');

JHTML::_('stylesheet', 'media/mod_matukio_upcoming/tmpl/default/css/tmpl.css');

// Always an array
$catids = $params->get("catid", array());

$number = $params->get("number", 3);
$orderby = $params->get("orderby", "begin ASC");

$events = ModMatukioUpcomingHelper::getEvents($catids, $number, $orderby);

foreach ($events as $event)
{
	// Link
	$eventid_l = $event->id . ':' . JFilterOutput::stringURLSafe($event->title);
	$catid_l = $event->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($event->catid));

	$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l), false);

	$begin = JHTML::_('date', $event->begin, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i'));
	$end = JHTML::_('date', $event->end, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i'));
	$booked = JHTML::_('date', $event->booked, MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i'));

	$location = $event->place;

	if ($event->webinar == 1)
	{
		$location = JText::_("COM_MATUKIO_WEBINAR");
	}
	elseif ($event->place_id != 0)
	{
		$locobj = MatukioHelperUtilsEvents::getLocation($event->place_id);
		$location = $locobj->location;
	}
	else
	{
		$location = $event->place;
	}
	?>
	<div class="mmat_event_holder">
		<div class="mmat_event_holder_inner">
			<h3><a href="<?php echo $link; ?>" title="<?php echo $event->title; ?>"><?php echo $event->title; ?></a>
			</h3>
			<?php
			if ($event->showbegin > 0)
			{
				echo JText::_("COM_MATUKIO_BEGIN") . ": " . $begin . "<br />";
			}

			if ($params->get("showEnd", 1) && $event->showend > 0)
			{
				echo JText::_("COM_MATUKIO_END") . ": " . $end . "<br />";
			}

			if ($params->get("showBooked", 1))
				echo JText::_("COM_MATUKIO_CLOSING_DATE") . ": " . $booked . "<br />";

			if ($params->get("showLocation", 1))
				echo JText::_("COM_MATUKIO_CITY") . ": " . $location . "<br />";

			if ($params->get("showHits", 0))
				echo JText::_("COM_MATUKIO_HITS") . ": " . $event->hits . "<br />";

			if ($params->get("showShortDescription", 0))
				echo $event->shortdesc . "<br />";

			if ($params->get("showReadMore", 1))
				echo "<div class=\"mmat_readon\"><a href=\"" . $link . "\" title=\"" . $event->title . "\" class=\"readon\"><span class=\"mmat_button\">"
					. JText::_("COM_MATUKIO_READ_MORE") . "</span></a></div>";
			?>
		</div>
	</div>
<?php
}


