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

/**
 * Class TableNumber
 *
 * @since  1.0.0
 */
class TableNumber extends JTable
{
	/**
	 * The constructor
	 *
	 * @param   string  &$db  - The database
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__matukio_number', 'id', $db);
	}
}
