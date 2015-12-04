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

JHTML::_('behavior.modal');
JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
$user = JFactory::getUser();

if (isset($this->art) && $this->art == 1)
{
	$htxt = str_replace("SEM_TITLE", $this->event->title, JTEXT::_('COM_MATUKIO_LEAVE_MESSAGE_TO_ORGANISER'));
}
elseif ($this->art == "organizer")
{
	$htxt = JTEXT::_('COM_MATUKIO_CONTACT_ORGANIZER');
}
else
{
	$htxt = str_replace("SEM_TITLE", $this->event->title, JTEXT::_('COM_MATUKIO_PLEASE_ENTER_MESSAGE_TO_PARTICIPANTS'));
}
?>
<body onload="parent.sbox-window.focus();">

<form action="index.php" method="post" name="FrontForm">

	<div class="sem_cat_title"><?php echo JTEXT::_('COM_MATUKIO_CONTACT') ?></div>
	<br/>

	<div id="loader" style="position: absolute; top:113; left:188; width:124px; height:124px; z-Index:10001; display: none;">
		<img src="<?php echo MatukioHelperUtilsBasic::getComponentImagePath(); ?>loader.gif"
		     width="124px" height="124px" style="width:124px; height:124px;"></div>


	<div class="sem_shortdesc"><?php echo $htxt ?></div>
	<br/>
	<center>
		<table border="0" cellpadding="4" class="mat_contact">
			<tr>
				<td width="60px">
					<?php echo JTEXT::_('COM_MATUKIO_NAME') ?>
				</td>
				<td>
					<input type="text" id="name" name="name" value="<?php echo $user->name; ?>" size="30"
					       maxlength="255">
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JTEXT::_('COM_MATUKIO_EMAIL') ?>
				</td>
				<td>
					<input type="text" id="email" name="email" value="<?php echo $user->email; ?>" size="30"
					       maxlength="255">
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<textarea name="text" rows="10" cols="50"></textarea>
				</td>
			</tr>
		</table>
	</center>

	<input type="hidden" name="option" value="com_matukio"/>
	<input type="hidden" name="view" value="contactorganizer"/>
	<input type="hidden" name="controller" value="contactorganizer"/>
	<input type="hidden" name="task" value="sendEmail"/>

	<?php if (isset($this->art) && $this->art != "organizer"): ?>
		<input type="hidden" name="event_id" value="<?php echo $this->event->id; ?>"/>
		<input type="hidden" name="organizer_id" value="0"/>
	<?php else: ?>
		<input type="hidden" name="event_id" value="0"/>
		<input type="hidden" name="organizer_id" value="<?php echo $this->organizer->id; ?>"/>
	<?php endif; ?>

	<input type="hidden" name="art" value="<?php echo $this->art ?>"/>
	<br/>
	<center><input type="submit" class="btn" style="cursor:pointer;" type="button" value="<?php echo JTEXT::_('COM_MATUKIO_SEND') ?>"</center>
</form>
</body>
</html>
