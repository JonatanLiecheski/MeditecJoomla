<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       04.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
jimport('joomla.application.component.controlleradmin');

/**
 * Class MatukioControllerDifferentfees
 *
 * @since  3.0
 */
class MatukioControllerDifferentfees extends JControllerAdmin
{
	/**
	 * Register extra tasks
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('addFee', 'editFee');
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
	public function getModel($name = 'Differentfees', $prefix = 'MatukioModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Removes a fee
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
			$query = "DELETE FROM #__matukio_different_fees where id IN ( $cids )";
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}
		}

		$this->setRedirect('index.php?option=com_matukio&view=differentfees');
	}


	/**
	 * Toogles publish for the given fee ids
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
		$tilesTable = JTable::getInstance('Differentfees', 'MatukioTable');
		$tilesTable->publish($cid, $publish);

		$link = 'index.php?option=com_matukio&view=differentfees';

		$this->setRedirect($link, $msg);
	}

	/**
	 * Edit tax form
	 *
	 * @return  void
	 */
	public function editFee()
	{
		$document = JFactory::getDocument();
		$viewName = 'editFee';
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('editFee');
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
		$row = JTable::getInstance('Differentfees', 'MatukioTable');
		$postgal = JRequest::get('post');

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
				$msg = JText::_('COM_MATUKIO_DIFFERENT_FEES_APPLY');
				$link = 'index.php?option=com_matukio&controller=differentfees&task=editFee&id=' . $row->id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_DIFFERENT_FEES_SAVE');
				$link = 'index.php?option=com_matukio&view=differentfees';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Cancels fee view
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=differentfees';
		$this->setRedirect($link);
	}
}
