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

JHTML::_('behavior.modal');
JHTML::_('stylesheet', 'media/com_matukio/css/modern.css');
?>
<!-- Start Matukio by compojoom.com -->
<div id="mat_holder">
	<?php
	$dispatcher = JDispatcher::getinstance();
	JPluginHelper::importPlugin('payment');

	$data = $dispatcher->trigger('onTP_Processpayment', array($_POST));
	echo $data;

	echo MatukioHelperUtilsBasic::getCopyright();
	?>
</div>
<!-- End Matukio by compojoom.com -->
