<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       04.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die ('Restricted access');

/**
 * Class MatukioTableDifferentfees
 *
 * @since  3.0
 */
class MatukioTableDifferentfees extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   string  &$db  - the db obj
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__matukio_different_fees', 'id', $db);
	}
}
