<?php
/**
 * Matukio
 * @package Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 0.9.0 beta $
 **/

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');

/**
 * Class MatukioControllercoupons
 *
 * @since  2.2
 */
class MatukioControllerOrganizers extends JControllerAdmin
{
	/**
	 * Register extra tasks
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('addOrganizer', 'editOrganizer');
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
	 * Removes an organizer
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
			$query = "DELETE FROM #__matukio_organizers where id IN ( $cids )";
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}
		}

		$msg = JText::_("COM_MATUKIO_ORGANIZERS_SUCCESSFULLY_DELETED");

		$this->setRedirect('index.php?option=com_matukio&view=organizers', $msg);
	}

	/**
	 * Toogles publish for the given organizer ids
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
		$tilesTable = JTable::getInstance('organizers', 'Table');
		$tilesTable->publish($cid, $publish);

		$link = 'index.php?option=com_matukio&view=organizers';

		$this->setRedirect($link, $msg);
	}

	/**
	 * Edit organizer form
	 *
	 * @return  void
	 */
	public function editOrganizer()
	{
		$document = JFactory::getDocument();

		// Hardcoded
		$viewName = 'editorganizer';
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('editorganizer');
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
		$row = JTable::getInstance('organizers', 'Table');
		$postgal = JRequest::get('post');
		$postgal['description'] = JRequest::getVar('description', '', 'post', 'html', JREQUEST_ALLOWHTML);


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
				$msg = JText::_('COM_MATUKIO_ORAGANIZER_APPLY');
				$link = 'index.php?option=com_matukio&view=editOrganizer&id=' . $row->id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_ORGANIZER_SAVE');
				$link = 'index.php?option=com_matukio&view=organizers';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Cancels organizer list view
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=organizers';
		$this->setRedirect($link);
	}
}
