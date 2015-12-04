<?php
/**
 * Matukio
 * @package Joomla!
 * @Copyright (C) 2013 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 2.2 $
 **/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filter.output');
jimport('joomla.html.pane');

JHTML::_('behavior.tooltip');

$doc = JFactory::getDocument();
$doc->addStyleSheet('../media/com_matukio/backend/css/settings.css');
$doc->addScript('../media/com_matukio/backend/js/Form.Check.js');
$doc->addScript('../media/com_matukio/backend/js/Form.CheckGroup.js');
$doc->addScript('../media/com_matukio/backend/js/Form.Dropdown.js');
$doc->addScript('../media/com_matukio/backend/js/Form.Radio.js');
$doc->addScript('../media/com_matukio/backend/js/Form.RadioGroup.js');
$doc->addScript('../media/com_matukio/backend/js/Form.SelectOption.js');

JHTML::_('stylesheet', '../media/com_matukio/backend/css/matukio.css');
?>
<div class="mat_content_holder">

	Coming soon with Matukio 2.2 Stable .. :)

	<?php echo MatukioHelperUtilsBasic::getCopyright(); ?>

</div>
