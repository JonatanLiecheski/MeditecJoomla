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

?>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">


	<input type="hidden" name="option" value="com_matukio"/>
	<input type="hidden" name="task" value="import_seminar"/>
	<input type="hidden" name="view" value="import"/>
	<input type="hidden" name="controller" value="import"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
