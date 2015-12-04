<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       17.10.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @since      2.2.0
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal');
JHTML::_('stylesheet', 'media/com_matukio/css/modern.css');
?>
<!-- Start Matukio by compojoom.com -->
<div id="mat_holder" class="compojoom-bootstrap">
	<?php
	$pg_plugin = $this->booking->payment_method;

	$dispatcher = JDispatcher::getInstance();

	$vars = new stdClass;
	$vars->orderid = $this->booking->id;
	$vars->order_id = $this->booking->id;
	$vars->user_firstname = $this->booking->name;
	$vars->user_id = $this->booking->userid;
	$vars->user_email = $this->booking->email;

	if (empty($this->booking->email) && $this->booking->userid > 0)
	{
		$user = JFactory::getUser($this->booking->userid);
		$vars->user_email = $user->email;
		$vars->user_firstname = $user->name;
	}

	$vars->item_name = $this->event->title;

	// Link back to the form
	if (MatukioHelperSettings::getSettings("oldbooking_redirect_after", "bookingpage") == "bookingpage")
	{
		$vars->return = JURI::base() . substr(
				JRoute::_(MatukioHelperRoute::getEventRoute($this->event->id, $this->event->catid, 1, $this->booking->id, $this->uuid), false),
				strlen(JURI::base(true)) + 1
			);
	}
	elseif (MatukioHelperSettings::getSettings("oldbooking_redirect_after", "bookingpage") == "eventpage")
	{
		$vars->return = JURI::base() . substr(
				JRoute::_(MatukioHelperRoute::getEventRoute($this->event->id, $this->event->catid, 0, $this->booking->id, $this->uuid), false),
				strlen(JURI::base(true)) + 1
		);
	}
	else
	{
		// Eventlist overview
		$vars->return = JURI::base() . substr(JRoute::_("index.php?option=com_matukio&view=eventlist"), strlen(JURI::base(true)) + 1);
	}

	$vars->cancel_return = JRoute::_(
		JURI::root() . "index.php?option=com_matukio&view=ppayment&task=cancelPayment&pg_plugin=" . $pg_plugin . "&uuid=" . $this->uuid
	);
	$vars->notify_url = JRoute::_(
		JURI::root() . "index.php?option=com_matukio&view=ppayment&task=status&pg_plugin=" . $pg_plugin . "&uuid=" . $this->uuid
	);

	$vars->submiturl = JRoute::_("index.php?option=com_matukio&controller=ppayment&task=confirmpayment&processor={$pg_plugin}");

	// Not documented in payment api
	$vars->url = JRoute::_(
		JURI::root() . "index.php?option=com_matukio&view=ppayment&task=status&pg_plugin=" . $pg_plugin	. "&uid=" . $this->booking->id . "&uuid=" . $this->uuid
	);

	$vars->currency_code = MatukioHelperSettings::getSettings("paypal_currency", 'EUR');
	$vars->amount = $this->booking->payment_brutto;

	// Import the right plugin here!
	JPluginHelper::importPlugin('payment', $pg_plugin);

	if ($pg_plugin == 'paypal')
	{
		$vars->cmd = '_xclick';
	}

	$html = $dispatcher->trigger('onTP_GetHTML', array($vars));

	if ($pg_plugin == 'paypal')
	{
	?>
	<?php
	$t1 = JText::_('COM_MATUKIO_LEVEL_REDIRECTING_HEADER');
	$t2 = JText::_('COM_MATUKIO_LEVEL_REDIRECTING_BODY');
	?>
	<h3><?php echo JText::_('COM_MATUKIO_LEVEL_REDIRECTING_HEADER') ?></h3>

	<p><?php echo JText::_('COM_MATUKIO_LEVEL_REDIRECTING_BODY') ?></p>

	<p align="center">
	<?php
	}

	echo $html[0];
	echo "<br /><br />";
	echo MatukioHelperUtilsBasic::getCopyright();
	?>
</div>
<!-- End Matukio by compojoom.com -->
