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

/**
 * Class MatukioModelSettings
 *
 * @since  1.0
 */
class MatukioModelSettings extends JModelLegacy
{
	private $_data = null;

	private $_id = null;

	public function __construct()
	{
		parent::__construct();
	}

	public function getData()
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query);
		}

		return $this->_data;
	}

	public function isCheckedOut($uid = 0)
	{
		if ($this->_loadData())
		{
			if ($uid)
			{
				return ($this->_data->checked_out && $this->_data->checked_out != $uid);
			}
			else
			{
				return $this->_data->checked_out;
			}
		}
	}

	public function checkin()
	{
		if ($this->_id)
		{
			$hotspots = & $this->getTable();

			if (!$hotspots->checkin($this->_id))
			{
				JError::raise(E_ERROR, 500, $this->_db->getErrorMsg());

				return false;
			}
		}

		return false;
	}

	public function checkout($uid = null)
	{
		if ($this->_id)
		{
			if (is_null($uid))
			{
				$user =& JFactory::getUser();
				$uid = $user->get('id');
			}

			$hotspots = & $this->getTable();

			if (!$hotspots->checkout($uid, $this->_id))
			{
				JError::raise(E_ERROR, 500, $this->_db->getErrorMsg());

				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Saves (all) settings
	 *
	 * @param   array  $dataArray  - THe array of settings
	 *
	 * @return  bool
	 */
	function store($dataArray)
	{
		$row = $this->getTable('Settings', 'Table');

		if (!empty($dataArray))
		{
			foreach ($dataArray as $key => $value)
			{
				$data['id'] = $key;
				$data['value'] = $value;

				if (!$row->bind($data))
				{
					JError::raise(E_ERROR, 500, $this->_db->getErrorMsg());
				}

				if (!$row->check())
				{
					JError::raise(E_ERROR, 500, $this->_db->getErrorMsg());
				}

				if (!$row->store())
				{
					JError::raise(E_ERROR, 500, $this->_db->getErrorMsg());
				}
			}
		}

		return true;
	}


	/**
	 * Builds the query
	 *
	 * @return  string
	 */
	function _buildQuery()
	{
		// Update 3.0 order by ordering!
		$query = ' SELECT st.*'
			. ' FROM #__matukio_settings AS st'
			. ' ORDER BY st.id';

		return $query;
	}
}
