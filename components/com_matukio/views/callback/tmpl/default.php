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
			window.location = "<?php echo JURI::root(); ?>"
		}, 10000);
	});
</script>
<?php
$t1 = JText::_('COM_MATUKIO_THANK_YOU');
$t2 = JText::_('COM_MATUKIO_LEVEL_REDIRECTING_BODY');
?>
<h3><?php echo JText::_('COM_MATUKIO_THANK_YOU') ?></h3>
<p><?php echo JText::_('COM_MATUKIO_THANK_YOU_TEXT') ?></p>
<p>
</p>