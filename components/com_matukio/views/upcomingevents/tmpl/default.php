<?php
/**
 * Matukio
 * @package Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 2.1.0 $
 **/

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal');

JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
JHTML::_('stylesheet', 'media/com_matukio/css/upcoming.css');
?>
<!-- Start Matukio by compojoom.com -->
<div class="componentheading">
	<h2><?php echo JText::_($this->title); ?></h2>
</div>

<div id="mat_holder">
	<?php
	// Starting event output
	echo MatukioHelperUpcoming::getUpcomingEventsHTML($this->events, $this->user);
	echo MatukioHelperUtilsBasic::getCopyright();
	?>
</div>
<!-- End Matukio by compojoom.com -->
