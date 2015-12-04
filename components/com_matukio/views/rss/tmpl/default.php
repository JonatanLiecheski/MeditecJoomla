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

$config = JFactory::getConfig();
header("Content-Type: application/rss+xml; charset=UTF-8");
$mainconfig = JFactory::getConfig();
$sprache = JFactory::getLanguage();

header("Content-Type: application/rss+xml");
echo('<?xml version="1.0" encoding="UTF-8"?>');

$html = "\n<rss version=\"2.0\">";
$html .= "\n<channel>";
$html .= "\n<title>" . $mainconfig->get('config.sitename') . " - " . JTEXT::_('COM_MATUKIO_EVENTS') . "</title>";
$html .= "\n<link>" . JURI::root() . "index.php?tmpl=component&amp;option=" . JFactory::getApplication()->input->get('option')
	. "&amp;view=eventlist</link>";
$html .= "\n<description>" . $mainconfig->get('config.sitename') . " - Events" . "</description>";
$html .= "\n<language>" . $sprache->getTag() . "</language>";
$html .= "\n<copyright>" . $mainconfig->get('config.fromname') . "</copyright>";
$html .= "\n<ttl>60</ttl>";
$html .= "\n<pubDate>" . date("r") . "</pubDate>";

foreach ($this->rows AS $row)
{
	$user = JFactory::getuser($row->publisher);
	$cancelled = "";

	if ($row->cancelled == 1)
	{
		$cancelled = " - " . JTEXT::_('COM_MATUKIO_CANCELLED');
	}

	$html .= "\n<item>";
	$html .= "\n<title>" . $row->title . $cancelled . "</title>";
	$html .= "\n<description>" . JTEXT::_('COM_MATUKIO_BEGIN') . ": " . JHTML::_('date', $row->begin,
			MatukioHelperSettings::getSettings('date_format_small', 'd-m-Y, H:i')
		) . " - " . $row->shortdesc . "</description>";

	$eventid_l = $row->id . ':' . JFilterOutput::stringURLSafe($row->title);
	$catid_l = $row->catid . ':' . JFilterOutput::stringURLSafe(MatukioHelperCategories::getCategoryAlias($row->catid));

	$link = JRoute::_(MatukioHelperRoute::getEventRoute($eventid_l, $catid_l), true, 2);

	$html .= "\n<link>" . $link . "</link>";

	if (MatukioHelperSettings::getSettings('frontend_showownerdetails', 1) > 0)
	{
		$html .= "\n<author>" . $user->name . ", " . $user->email . "</author>";
	}

	$html .= "\n<guid>" . MatukioHelperUtilsBooking::getBookingId($row->id) . "</guid>";
	$html .= "\n<category>" . $row->category . "</category>";
	$html .= "\n<pubDate>" . date("r", strtotime($row->publishdate)) . "</pubDate>";
	$html .= "\n</item>";
}
$html .= "\n</channel>";
$html .= "\n</rss>";

echo $html;
