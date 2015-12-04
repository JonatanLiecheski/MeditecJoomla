<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       28.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
jimport('joomla.application.component.controlleradmin');

/**
 * Class MatukioControllercoupons
 *
 * @since  2.0
 */
class MatukioControllerCoupons extends JControllerAdmin
{
	/**
	 * Register extra tasks
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('addCoupon', 'editCoupon');
		$this->registerTask('apply', 'save');
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Coupons', $prefix = 'MatukioModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Removes an coupon
	 *
	 * @throws Exception
	 * @return  void
	 */
	public function remove()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$db = JFactory::getDBO();

		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__matukio_booking_coupons where id IN ( $cids )";
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}
		}

		$this->setRedirect('index.php?option=com_matukio&view=coupons');
	}


	/**
	 * Toogles publish for the given coupon ids
	 *
	 * @throws  Exception - If db queries fail
	 * @return  void
	 */
	public function publish()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if ($this->task == 'publish')
		{
			$publish = 1;
		}
		else
		{
			$publish = 0;
		}

		$msg = "";
		$tilesTable = JTable::getInstance('coupons', 'Table');
		$tilesTable->publish($cid, $publish);

		$link = 'index.php?option=com_matukio&view=coupons';

		$this->setRedirect($link, $msg);
	}

	/**
	 * Edit coupon form
	 *
	 * @return  void
	 */
	public function editCoupon()
	{
		$document = JFactory::getDocument();
		$viewName = 'editcoupon';
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);

		$model = $this->getModel('editcoupon');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Saves the form
	 *
	 * @throws  exception - if query fails
	 * @return  void
	 */
	public function save()
	{
		$row = JTable::getInstance('coupons', 'Table');
		$postgal = JRequest::get('post');

		$id = JFactory::getApplication()->input->getInt('id', 0);


		if (!$row->bind($postgal))
		{
			throw new Exception($row->getError(), 42);

		}

		if (!isset($row->published))
		{
			$row->published = 1;
		}

		if (!$row->store())
		{
			throw new Exception($row->getError(), 42);
		}

		switch ($this->task)
		{
			case 'apply':
				$msg = JText::_('COM_MATUKIO_BOOKING_FIELD_APPLY');
				$link = 'index.php?option=com_matukio&controller=coupons&task=editCoupon&id=' . $row->id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_BOOKING_FIELD_SAVE');
				$link = 'index.php?option=com_matukio&view=coupons';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Cancels coupon list view
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=coupons';
		$this->setRedirect($link);
	}


	/**
	 * Duplicates an or multiple coupons
	 *
	 * @throws  Exception - If db queries fail
	 * @return  void
	 */
	public function duplicate()
	{
		$ids = JFactory::getApplication()->input->get('cid', array(), 'array');

		if (count($ids))
		{
			$db = JFactory::getDbo();
			$cids = implode(',', $ids);

			$db->setQuery("SELECT * FROM #__matukio_booking_coupons WHERE id IN (" . $cids . ")");
			$rows = $db->loadObjectList();

			if ($db->getErrorNum())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			foreach ($rows as $item)
			{
				$row = JTable::getInstance('Coupons', 'Table');

				if (!$row->bind($item))
				{
					throw new Exception($db->getErrorMsg(), 42);
				}

				// Reset values
				$row->id = null;
				$row->hits = 0;

				if (!$row->check())
				{
					throw new Exception($db->getErrorMsg(), 42);
				}

				if (!$row->store())
				{
					throw new Exception($db->getErrorMsg(), 42);
				}
			}
		}

		$msg = JText::_("COM_MATUKIO_COUPON_DUPLICATE_SUCCESS");
		$link = 'index.php?option=com_matukio&view=coupons';

		$this->setRedirect($link, $msg);
	}
}
