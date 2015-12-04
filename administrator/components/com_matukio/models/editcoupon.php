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
jimport('joomla.application.component.model');

class MatukioModelEditcoupon extends JModelLegacy
{

	public function __construct()
	{
		parent::__construct();
		//$array = JFactory::getApplication()->input->get('id', 0);
		$this->setId = JFactory::getApplication()->input->getInt('id', 0);
	}

	public function getCoupon()
	{
		//$array2 = JFactory::getApplication()->input->get('id', 0, '', 'array');
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

	private function _buildQuery($id)
	{
		$query = "SELECT * FROM #__matukio_booking_coupons WHERE id = '" . $id . "'";
		return $query;
	}

}