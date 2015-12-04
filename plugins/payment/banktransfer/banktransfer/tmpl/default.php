<?php 
/**
 * @package Social Ads
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/defines.php';

JLoader::register('MatukioHelperSettings', JPATH_ADMINISTRATOR . '/components/com_matukio/helpers/settings.php');

// Load banktransfer data
$account = MatukioHelperSettings::getSettings('banktransfer_account', '');
$blz = MatukioHelperSettings::getSettings('banktransfer_blz', '');
$bank = MatukioHelperSettings::getSettings('banktransfer_bank', '');
$account_holder = MatukioHelperSettings::getSettings('banktransfer_accountholder', '');
$iban = MatukioHelperSettings::getSettings('banktransfer_iban', '');
$bic = MatukioHelperSettings::getSettings('banktransfer_bic', '');


// no direct access

JHTML::_('behavior.formvalidation');
$document =JFactory::getDocument();
	if($vars->custom_email=="")
		$email = JText::_('NO_ADDRS');
	else
		$email = $vars->custom_email;

?>

<div class="akeeba-bootstrap">
<form action="<?php echo $vars->url; ?>" name="adminForm" id="adminForm" onSubmit="return myValidate(this);" class="form-validate form-horizontal"  method="post">			
	<div>
		<div class="control-group">
			<label for="cardfname" class="control-label"><?php  echo JText::_( 'ORDER_INFO_LABEL' );?></label>
			<div class="controls">	<?php  echo JText::sprintf( 'ORDER_INFO', $vars->custom_name);?><br /><br />

            <table border="0">
                <tr>
                    <td style="width: 150px"><?php echo JText::_('ACCOUNT_HOLDER');?>:</td>
                    <td><?php echo $account_holder; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('ACCOUNT'); ?>:</td>
	                <td><?php echo $account; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('BLZ'); ?>:</td>
                    <td><?php echo $blz; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('IBAN');?>:</td>
                    <td><?php echo $iban; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('BIC');?>:</td>
                    <td><?php echo $bic; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_('BANK');?>:</td>
                    <td><?php echo $bank; ?></td>
                </tr>
            </table>
            </div>
		</div>
		<div class="control-group">
		</div>
		<div class="control-group">
			<label for="cardaddress1" class="control-label"><?php echo JText::_( 'CON_PAY_PRO' ) ?></label>
			<div class="controls">
					<input type='hidden' name='mail_addr' value="<?php echo $email;?>" />
			</div>
		</div>
		<div class="form-actions">
				<input type='hidden' name='order_id' value="<?php echo $vars->order_id;?>" />
				<input type='hidden' name="total" value="<?php echo sprintf('%02.2f',$vars->amount) ?>" />
				<input type="hidden" name="user_id" size="10" value="<?php echo $vars->user_id;?>" />
				<input type='hidden' name='return' value="<?php echo $vars->return;?>" >
				<input type="hidden" name="plugin_payment_method" value="onsite" />
				<input type='submit' name='btn_check' id='btn_check' class="btn btn-success btn-large"  value="<?php echo JText::_('NEXT'); ?>">
		</div>
	</div>
</form>
</div>
