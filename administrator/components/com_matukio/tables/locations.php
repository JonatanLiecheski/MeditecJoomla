<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       11.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die ('Restricted access');

/**
 * Class MatukioTableLocations
 *
 * @since  3.0.0
 */
class MatukioTableLocations extends JTable
{
	/**
	 * Gets the table obj
	 *
	 * @param   object  &$db  - The db
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__matukio_locations', 'id', $db);
	}
}
