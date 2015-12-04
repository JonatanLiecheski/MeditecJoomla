<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       02.04.14
 *
 * @copyright  Copyright (C) 2008 - 2014 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

MatukioHelperUtilsBasic::bootstrap(true);

JHTML::_('stylesheet', 'media/com_matukio/css/modern.css');
?>
<!-- Start Matukio by compojoom.com -->
<div class="componentheading">
	<h2><?php echo JText::_("COM_MATUKIO_PPAYMENT_ERROR"); ?></h2>
</div>

<div id="mat_holder">
	<?php echo JText::_("COM_MATUKIO_PPAYMENT_ERROR_INTRO"); ?>:
	<br /><br />
	<div id="ppayment_error">
		<?php
		echo $this->data[0]['error']['desc'] . " (" . $this->data[0]['error']['code'] . ")";
		?>
	</div>
	<br />
	<button id="btn_back" class="btn btn-success"><?php echo JText::_("COM_MATUKIO_BACK"); ?></button>
	<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery('#btn_back').click(function(){
				parent.history.back();
				return false;
			});
		});
	</script>
	<?php
	echo MatukioHelperUtilsBasic::getCopyright();
	?>
</div>
<!-- End Matukio by compojoom.com -->
