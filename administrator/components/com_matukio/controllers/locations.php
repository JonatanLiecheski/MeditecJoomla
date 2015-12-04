<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       11.11.13
 *
 * @copyright  Copyright (C) 2008 - 2013 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');

/**
 * Class MatukioControllerLocations
 *
 * @since  3.0.0
 */
class MatukioControllerLocations extends JControllerAdmin
{
	/**
	 * Register extra tasks
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('addLocation', 'editLocation');
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
	public function getModel($name = 'Organizers', $prefix = 'MatukioModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Removes an Location
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
			$query = "DELETE FROM #__matukio_locations where id IN ( $cids )";
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}
		}

		$msg = JText::_("COM_MATUKIO_LOCATION_SUCCESSFULLY_DELETED");

		$this->setRedirect('index.php?option=com_matukio&view=locations', $msg);
	}

	/**
	 * Toogles publish for the given location ids
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
		$tilesTable = JTable::getInstance('Locations', 'MatukioTable');
		$tilesTable->publish($cid, $publish);

		$link = 'index.php?option=com_matukio&view=locations';

		$this->setRedirect($link, $msg);
	}

	/**
	 * Edit location form
	 *
	 * @return  void
	 */
	public function editLocation()
	{
		$document = JFactory::getDocument();

		// Hardcoded
		$viewName = 'editLocation';
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('editLocation');
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
		$row = JTable::getInstance('Locations', 'MatukioTable');
		$postgal = JRequest::get('post');
		$postgal['description'] = JRequest::getVar('description', '', 'post', 'html', JREQUEST_ALLOWHTML);
		$postgal['location'] = JRequest::getVar('location', '', 'post', 'html', JREQUEST_ALLOWHTML);


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
				$msg = JText::_('COM_MATUKIO_LOCATION_APPLY');
				$link = 'index.php?option=com_matukio&view=editLocation&id=' . $row->id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_LOCATION_SAVE');
				$link = 'index.php?option=com_matukio&view=locations';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Cancels location edit view
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=locations';
		$this->setRedirect($link);
	}
}
