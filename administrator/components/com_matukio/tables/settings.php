<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die ('Restricted access');

// Include library dependencies
jimport('joomla.filter.input');

/**
 * Class TableSettings
 *
 * @since  1.0
 */
class TableSettings extends JTable
{
	var $id = null;

	var $title = null;

	var $value = null;

	/**
	 * Construct
	 *
	 * @param   string  &$db  - The db
	 */
	function __construct(&$db)
	{
		parent::__construct('#__matukio_settings', 'id', $db);
	}
}
