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
 * Class MatukioControllerTaxes
 *
 * @since  3.0
 */
class MatukioControllerTaxes extends JControllerAdmin
{
	/**
	 * Register extra tasks
	 */

	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('addTax', 'editTax');
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
	public function getModel($name = 'Taxes', $prefix = 'MatukioModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Removes a tax rate
	 *
	 * @throws  Exception
	 * @return  void
	 */
	public function remove()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$db = JFactory::getDBO();

		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__matukio_taxes where id IN ( $cids )";
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}
		}

		$this->setRedirect('index.php?option=com_matukio&view=taxes');
	}


	/**
	 * Toogles publish for the given tax ids
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
		$tilesTable = JTable::getInstance('taxes', 'MatukioTable');
		$tilesTable->publish($cid, $publish);

		$link = 'index.php?option=com_matukio&view=taxes';

		$this->setRedirect($link, $msg);
	}

	/**
	 * Edit tax form
	 *
	 * @return  void
	 */
	public function editTax()
	{
		$document = JFactory::getDocument();
		$viewName = 'edittax';
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('edittax');
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
		$row = JTable::getInstance('taxes', 'MatukioTable');
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
				$msg = JText::_('COM_MATUKIO_TAXES_APPLY');
				$link = 'index.php?option=com_matukio&controller=taxes&task=editTax&id=' . $row->id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_TAXES_SAVE');
				$link = 'index.php?option=com_matukio&view=taxes';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Cancels tax edit
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=taxes';
		$this->setRedirect($link);
	}
}
