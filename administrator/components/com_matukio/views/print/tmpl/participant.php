<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       05.10.13
 *
 * @copyright  Copyright (C) 2008 - {YEAR} Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

$this->bookings = JFactory::getApplication()->input->get('bookings', '', 'string');

// Transform to array again.
if (!empty($this->bookings))
{
	$this->bookings = explode(",", $this->bookings);
}

$this->backend = true;
$this->art = 2;

include_once JPATH_ROOT . "/components/com_matukio/views/printeventlist/tmpl/participants.php";