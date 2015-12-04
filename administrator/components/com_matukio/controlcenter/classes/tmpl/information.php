<?php
/**
 * Compojoom ControlCenter
 * @package Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 0.9.0 beta $
 **/

defined('_JEXEC') or die();

// Loading css and js
JHTML::_('behavior.tooltip');
JHTML::_('stylesheet', 'media/com_matukio/ccc/css/ccc.css');
JHTML::_('script', 'media/com_matukio/ccc/js/ccc.js');
JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

$modules = JModuleHelper::getModules('ccc_' . $this->config->extensionPosition . '_promotion');

echo CompojoomHtmlCtemplate::getHead(
	MatukioHelperUtilsBasic::getMenu(), 'information', 'COM_MATUKIO_INFORMATIONS', 'COM_MATUKIO_SLOGAN_INFORMATIONS'
);
?>
<div class="box-info">
<div id="ccc_information">
	<div id="ccc_information_inner">
		<h2><?php echo JText::_('COMPOJOOM_CONTROLCENTER_VERSION'); ?></h2>

		<p>
			<?php echo $this->config->version; ?>
			<br /><br />
		</p>

		<h2><?php echo JText::_('COMPOJOOM_CONTROLCENTER_COPYRIGHT'); ?></h2>

		<p>
			<?php echo $this->config->copyright; ?>
			<br /><br />
		</p>

		<h2><?php echo JText::_('COMPOJOOM_CONTROLCENTER_LICENSE'); ?></h2>

		<p>
			<?php echo $this->config->license; ?>
			<br /><br />
		</p>

		<h2><?php echo JText::_('COMPOJOOM_CONTROLCENTER_TRANLATION'); ?></h2>

		<p>
			<?php echo $this->config->translation; ?>
			<br /><br />
		</p>

		<h2>Thank you</h2>

		<p> This software would not have been possible without the help of those listed here.
			THANK YOU for your continuous help, support and inspiration!
		</p>
		<ul>
			<?php echo JText::_($this->config->thankyou); ?>
		</ul>

	</div>
	<div id="ccc_information_modules">
		<br />
		<p></p>
		<?php
		foreach ($modules as $module)
		{
			$output = JModuleHelper::renderModule($module);
			echo $output;
		}
		?>
	</div>
</div>
</div>
<?php
echo CompojoomHtmlCTemplate::getFooter(MatukioHelperUtilsBasic::getCopyright(false));
