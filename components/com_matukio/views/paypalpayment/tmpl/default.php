<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die();
?>
<script type="text/javascript">
	window.addEvent('domready', function () {
		window.setTimeout(function () {
			$('paymentForm').submit()
		}, 10000);
	});
</script>
<?php
$t1 = JText::_('COM_MATUKIO_LEVEL_REDIRECTING_HEADER');
$t2 = JText::_('COM_MATUKIO_LEVEL_REDIRECTING_BODY');
?>
<h3><?php echo JText::_('COM_MATUKIO_LEVEL_REDIRECTING_HEADER') ?></h3>
<p><?php echo JText::_('COM_MATUKIO_LEVEL_REDIRECTING_BODY') ?></p>
<p align="center">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="paymentForm">
	<input type="hidden" name="cmd" value="_xclick"/>
	<input type="hidden" name="business" value="<?php echo $this->merchant_address ?>"/>  <? // Paypal Adress ?>
	<input type="hidden" name="return" value="<?php echo $this->success_url ?>"/>  <? // Success url ?>
	<input type="hidden" name="cancel_return" value="<?php echo $this->cancel_url ?>"/>
	<input type="hidden" name="notify_url" value="<?php echo $this->success_url ?>"/>

	<input type="hidden" name="custom"
	       value="<?php echo MatukioHelperUtilsBooking::getBookingId($this->booking->id) ?>"/>

	<input type="hidden" name="item_number" value="<?php echo $this->item_number ?>"/>
	<input type="hidden" name="item_name" value="<?php echo $this->event->title . ' - ['
		. MatukioHelperUtilsBooking::getBookingId($this->booking->id) . ']' ?>"/>

	<input type="hidden" name="currency_code" value="<?php echo $this->currency ?>"/>

	<?php if (!empty($this->tax_amount)): ?>  */ ?>
		<input type="hidden" name="amount" value="<?php echo $this->net_amount ?>"/>
		<input type="hidden" name="tax" value="<?php echo $this->tax_amount ?>"/>
	<?php else: ?>
		<input type="hidden" name="amount" value="<?php echo $this->net_amount ?>"/>
	<?php endif; ?>
	<?php /* else: ?>
    <input type="hidden" name="a3" value="<?php echo $subscription->gross_amount ?>" />
    <input type="hidden" name="p3" value="<?php echo $data->p3 ?>" />
    <input type="hidden" name="t3" value="<?php echo $data->t3 ?>" />
    <input type="hidden" name="src" value="1" />
    <input type="hidden" name="sra" value="1" />
    <?php endif; */
	?>

	<?php
	/*<input type="hidden" name="first_name" value="<?php echo $data->firstname ?>" />
	<input type="hidden" name="last_name" value="<?php echo $data->lastname ?>" />

	<input type="hidden" name="address_override" value="0">
	<input type="hidden" name="address1" value="<?php echo $kuser->address1 ?>">
	<input type="hidden" name="address2" value="<?php echo $kuser->address2 ?>">
	<input type="hidden" name="city" value="<?php echo $kuser->city ?>">
	<input type="hidden" name="state" value="<?php echo $kuser->state ?>">
	<input type="hidden" name="zip" value="<?php echo $kuser->zip ?>">
	<input type="hidden" name="country" value="<?php echo $kuser->country ?>">
	*/
	?>

	<input type="hidden" name="no_note" value="1"/>
	<input type="hidden" name="no_shipping" value="1"/>
	<?php if ($cbt = MatukioHelperSettings::getSettings('cbt', '')): // Text for return to merchant button?>
		<input type="hidden" name="cbt" value="<?php echo $cbt ?>"/>
	<?php endif; ?>
	<?php if ($cpp_header_image = MatukioHelperSettings::getSettings('cpp_header_image', '')): ?>
		<input type="hidden" name="cpp_header_image" value="<?php echo $cpp_header_image ?>"/>
	<?php endif; ?>
	<?php if ($cpp_headerback_color = MatukioHelperSettings::getSettings('cpp_headerback_color', '')): ?>
		<input type="hidden" name="cpp_headerback_color" value="<?php echo $cpp_headerback_color ?>"/>
	<?php endif; ?>
	<?php if ($cpp_headerborder_color = MatukioHelperSettings::getSettings('cpp_headerborder_color', '')): ?>
		<input type="hidden" name="cpp_headerborder_color" value="<?php echo $cpp_headerborder_color ?>"/>
	<?php endif; ?>

	<input type="image" src="https://www.paypalobjects.com/WEBSCR-640-20110306-1/en_US/i/btn/btn_paynowCC_LG.gif"
	       border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!" id="paypalsubmit"/>
	<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"/>
</form>
</p>