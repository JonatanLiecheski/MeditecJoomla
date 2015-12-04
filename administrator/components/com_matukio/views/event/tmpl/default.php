<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       25.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

if (JVERSION > 3)
{
	// TODO change tooltips to bootstrap
	JHtml::_('formbehavior.chosen', 'select');
}

echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'eventlist', 'COM_MATUKIO_EDIT_EVENT', 'COM_MATUKIO_SLOGAN_EDIT_EVENT');

JFilterOutput::objectHTMLSafe($this->event);
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || jQuery('#adminForm').validationEngine('validate'))
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>
	<div class="box-info full">
		<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal" enctype="multipart/form-data" role="form">

			<?php
			// Use the function, the code should be moved here some time
			echo MatukioHelperUtilsEvents::getEventEdit($this->event, 2);
			?>
			<div class="clr clear"></div>

			<input type="hidden" name="option" value="com_matukio" />

			<input type="hidden" name="controller" value="eventlist" />
			<input type="hidden" name="model" value="event" />
			<input type="hidden" name="view" value="event" />
			<input type="hidden" name="task" value="" />

			<input type="hidden" name="published" value="<?php echo $this->event->published; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->event->id; ?>" />

			<?php echo JHTML::_('form.token'); ?>
			<?php echo JHTML::_('behavior.keepalive'); ?>
		</form>
	</div>
<div class="clr"></div>
<?php
// Show Footer
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
