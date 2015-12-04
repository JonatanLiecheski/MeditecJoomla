<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       13.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.tooltip');
jimport('joomla.filter.output');
JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

// Load the menu including bootstrap etc
echo CompojoomHtmlCtemplate::getHead(MatukioHelperUtilsBasic::getMenu(), 'bookings', 'COM_MATUKIO_CONTACT_PARTICIPANTS', 'COM_MATUKIO_SLOGAN_CONTACT_PARTICIPANTS');
?>
<form action="<?php echo JRoute::_("index.php?option=com_matukio&view=conactparticipants"); ?>" method="post" name="adminForm" id="adminForm">
	<div class="box-info full">
		<div class="table-responsive">
			<table class="table">
			<tr>
				<td width="150px"><?php echo JText::_("COM_MATUKIO_TEMPLATE_MAIL_SUBJECT"); ?></td>
				<td><input type="text" name="subject" id="subject" style="width: 200px;"/></td>
			</tr>
			<tr>
				<td colspan="2">
					<?php echo JText::_("COM_MATUKIO_TEXT"); ?><br />
					<br />
					<textarea name="text" id="text" style="width: 600px; height: 200px;"></textarea>
				</td>
			</tr>
		</table>
		</div>
	</div>

	<input type="hidden" name="option" value="com_matukio"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="event_id" value="<?php echo $this->event_id; ?>"/>
	<input type="hidden" name="cid" value="<?php echo implode(",", $this->booking_ids); ?>"/>

	<?php echo JHTML::_('form.token'); ?>
</form>
<?php
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
