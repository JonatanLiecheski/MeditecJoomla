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

if (JVERSION >= 3)
{
	JHtmlBehavior::framework();
}
else
{
	JHTML::_('behavior.mootools');
}

JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
JHTML::_('stylesheet', 'media/com_matukio/css/calendar.css');
JHTML::_('stylesheet', 'media/com_matukio/css/mooECal.css');
//JHTML::_('stylesheet', 'media/com_matukio/css/mooECalLarge.css');
//JHTML::_('stylesheet', 'media/com_matukio/css/mooECalSmall.css');

JHTML::_('script', 'media/com_matukio/js/mooECal.js');
JHTML::_('script', 'media/com_matukio/js/mecPHPPlugin.js');
?>
<!-- Start Matukio by compojoom.com -->
<script type="text/javascript">
	window.addEvent('domready', function () {
		new Calendar({
			calContainer: 'mat_calendar_holder',
			newDate: '<?php echo Date("m/d/Y");//8/5/2009?>',
			weekStart: <?php echo $this->params->get('weekStart', 0); ?>,
			feedPlugin: 'new mecPHPPlugin()',
			dayNames: ['<?php echo JText::_("COM_MATUKIO_SUNDAY");?>', '<?php echo JText::_("COM_MATUKIO_MONDAY");?>', '<?php echo JText::_("COM_MATUKIO_TUESDAY");?>',
				'<?php echo JText::_("COM_MATUKIO_WEDNESDAY");?>', '<?php echo JText::_("COM_MATUKIO_THURSDAY");?>', '<?php echo JText::_("COM_MATUKIO_FRIDAY");?>',
				'<?php echo JText::_("COM_MATUKIO_SATURDAY");?>'],
			monthNames: ['<?php echo JText::_("COM_MATUKIO_JANUARY");?>', '<?php echo JText::_("COM_MATUKIO_FEBRUARY");?>', '<?php echo JText::_("COM_MATUKIO_MARCH");?>',
				'<?php echo JText::_("COM_MATUKIO_APRIL");?>',
				'<?php echo JText::_("COM_MATUKIO_MAY");?>', '<?php echo JText::_("COM_MATUKIO_JUNE");?>', '<?php echo JText::_("COM_MATUKIO_JULY");?>',
				'<?php echo JText::_("COM_MATUKIO_AUGUST");?>', '<?php echo JText::_("COM_MATUKIO_SEPTEMBER");?>', '<?php echo JText::_("COM_MATUKIO_OCTOBER");?>',
				'<?php echo JText::_("COM_MATUKIO_NOVEMBER");?>', '<?php echo JText::_("COM_MATUKIO_DECEMBER");?>'],
			dateFormat: '<?php echo $this->params->get('dateFormat', '%Y-%m-%d'); ?>',
			timeFormat: '<?php echo $this->params->get('timeFormat', '%I:%M'); ?>',
			dayText: '<?php echo JText::_("COM_MATUKIO_DAY");?>',
			weekText: '<?php echo JText::_("COM_MATUKIO_WEEK");?>',
			monthText: '<?php echo JText::_("COM_MATUKIO_MONTH");?>',
			timeTwelveHourFormat: '<?php echo $this->params->get('timeTwelveHourFormat', false); ?>'
		});
	});
</script>
<div class="componentheading">
	<h2><?php echo JText::_($this->title); ?></h2>
</div>

<div id="mat_holder">
	<div id="mat_calendar_holder">

	</div>
	<?php
	echo MatukioHelperUtilsBasic::getCopyright();
	?>
</div>
<!-- End Matukio by compojoom.com -->
