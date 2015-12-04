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

MatukioHelperUtilsBasic::bootstrap(true);

JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
JHTML::_('stylesheet', 'media/com_matukio/css/fullcalendar.css');

JHTML::_('script', 'media/com_matukio/js/fullcalendar.min.js');
?>
<!-- Start Matukio by compojoom.com -->
<script type="text/javascript">
	jQuery( document ).ready(function( $ ) {
		$("#mat_calendar_holder").fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			titleFormat: {
				month: '<?php echo $this->params->get('titleFormatMonth', 'MMMM yyyy'); ?>',
				week: "<?php echo $this->params->get('titleFormatWeek', 'MMM d[ yyyy]{ \'&#8212;\'[ MMM] d yyyy}'); ?>",
				day: '<?php echo $this->params->get('titleFormatDay', 'dddd, MMM d, yyyy'); ?>'
			},

			timeFormat: {
				agenda: '<?php echo $this->params->get('timeFormatAgenda', 'H:mm{ - H:mm}'); ?>',
				'': '<?php echo $this->params->get('timeFormat', 'H:mm'); ?>'
			},

			firstHour: <?php echo $this->params->get('firstHour', '8'); ?>,

			firstDay: <?php echo $this->params->get('weekStart', '0'); ?>,

			eventSources: [
				{
					url: 'index.php?option=com_matukio&view=requests&format=raw&task=getcalendarjquery'
				}
			],

			columnFormat: {
				month: '<?php echo $this->params->get('columnMonth', 'ddd'); ?>',
				week: '<?php echo $this->params->get('columnWeek', 'ddd M/d'); ?>',
				day: '<?php echo $this->params->get('columnDay', 'dddd M/d'); ?>'
			},

			axisFormat: '<?php echo $this->params->get('axisFormat', 'H:mm'); ?>',

			dayNames: ['<?php echo JText::_("COM_MATUKIO_SUNDAY");?>', '<?php echo JText::_("COM_MATUKIO_MONDAY");?>', '<?php echo JText::_("COM_MATUKIO_TUESDAY");?>',
				'<?php echo JText::_("COM_MATUKIO_WEDNESDAY");?>', '<?php echo JText::_("COM_MATUKIO_THURSDAY");?>', '<?php echo JText::_("COM_MATUKIO_FRIDAY");?>',
				'<?php echo JText::_("COM_MATUKIO_SATURDAY");?>'],

			dayNamesShort: ['<?php echo JText::_("COM_MATUKIO_SUNDAY_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_MONDAY_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_TUESDAY_SHORT");?>',
				'<?php echo JText::_("COM_MATUKIO_WEDNESDAY_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_THURSDAY_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_FRIDAY_SHORT");?>',
				'<?php echo JText::_("COM_MATUKIO_SATURDAY_SHORT");?>'],

			monthNames: ['<?php echo JText::_("COM_MATUKIO_JANUARY");?>', '<?php echo JText::_("COM_MATUKIO_FEBRUARY");?>', '<?php echo JText::_("COM_MATUKIO_MARCH");?>',
				'<?php echo JText::_("COM_MATUKIO_APRIL");?>',
				'<?php echo JText::_("COM_MATUKIO_MAY");?>', '<?php echo JText::_("COM_MATUKIO_JUNE");?>', '<?php echo JText::_("COM_MATUKIO_JULY");?>',
				'<?php echo JText::_("COM_MATUKIO_AUGUST");?>', '<?php echo JText::_("COM_MATUKIO_SEPTEMBER");?>', '<?php echo JText::_("COM_MATUKIO_OCTOBER");?>',
				'<?php echo JText::_("COM_MATUKIO_NOVEMBER");?>', '<?php echo JText::_("COM_MATUKIO_DECEMBER");?>'],

			monthNamesShort: ['<?php echo JText::_("COM_MATUKIO_JANUARY_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_FEBRUARY_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_MARCH_SHORT");?>',
				'<?php echo JText::_("COM_MATUKIO_APRIL_SHORT");?>',
				'<?php echo JText::_("COM_MATUKIO_MAY_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_JUNE_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_JULY_SHORT");?>',
				'<?php echo JText::_("COM_MATUKIO_AUGUST_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_SEPTEMBER_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_OCTOBER_SHORT");?>',
				'<?php echo JText::_("COM_MATUKIO_NOVEMBER_SHORT");?>', '<?php echo JText::_("COM_MATUKIO_DECEMBER_SHORT");?>'],

			buttonText: {
				today:    '<?php echo JText::_("COM_MATUKIO_TODAY");?>',
				month:    '<?php echo JText::_("COM_MATUKIO_MONTH");?>',
				week:     '<?php echo JText::_("COM_MATUKIO_WEEK");?>',
				day:      '<?php echo JText::_("COM_MATUKIO_DAY");?>'
			},

			editable: false
		})
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
