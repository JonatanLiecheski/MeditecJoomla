<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       04.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

/**
 * Class MatukioModelEditFee
 *
 * @since  3.0.0
 */
class MatukioModelEditFee extends JModelLegacy
{
	/**
	 * Set id based on given id
	 */
	public function __construct()
	{
		parent::__construct();

		$this->setId = JFactory::getApplication()->input->getInt('id', 0);
	}

	/**
	 * Gets the fee rate
	 *
	 * @return  mixed
	 */
	public function getFEe()
	{
		$this->setId2 = JFactory::getApplication()->input->getInt('id', 0);
		$id = $this->setId2;

		if (empty($this->_data))
		{
			$query = $this->_buildQuery($id);
			$this->_db->setQuery($query);
			$this->_data = $this->_db->loadObject();
		}

		return $this->_data;
	}

	/**
	 * Builds the fee query (#__matukio_different_fees)
	 *
	 * @param   int  $id  - The fee id
	 *
	 * @return string
	 */
	private function _buildQuery($id)
	{
		$query = "SELECT * FROM #__matukio_different_fees WHERE id = '" . $id . "'";

		return $query;
	}
}
