<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       10.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die('Restricted access');
$input = JFactory::getApplication()->input;
$task = $input->get('task', null);

// Checking if task is set
if (!$task)
{
	echo "No task specified";

	return;
}

// URL index.php?option=com_matukio&format=raw&view=requests&task=get_override_fee_edit_row
if ($task == 'get_override_fee_edit_row')
{
	MatukioHelperFees::printDifferentFeesRow();
}
elseif ($task == 'generate_recurring')
{
	MatukioHelperRecurring::printGenerateRecurring();
}

jexit();
