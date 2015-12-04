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
 * Class MatukioControllerBookingfields
 *
 * @since  2.0.0
 */
class MatukioControllerBookingfields extends JControllerAdmin
{
	/**
	 * Register extra tasks
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('addBookingfield', 'editBookingfield');
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
	 * Removes a booking field
	 *
	 * @throws Exception
	 * @return  void
	 */
	public function remove()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');

		$db = JFactory::getDBO();

		if (count($cid))
		{
			$cids = implode(',', $cid);
			$query = "DELETE FROM #__matukio_booking_fields where id IN ( $cids )";
			$db->setQuery($query);

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}
		}

		$this->setRedirect('index.php?option=com_matukio&view=bookingfields');
	}

	/**
	 * Toogles publish for the given event ids
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
		$table = JTable::getInstance('bookingfields', 'Table');
		$table->publish($cid, $publish);

		$link = 'index.php?option=com_matukio&view=bookingfields';

		$this->setRedirect($link, $msg);
	}

	/**
	 * Edit Bookingfield
	 *
	 * @return  void
	 */

	public function editBookingfield()
	{
		$document = JFactory::getDocument();
		$viewName = 'editbookingfield';
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);

		$model = $this->getModel('editbookingfield');
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
		$row = JTable::getInstance('bookingfields', 'Table');
		$postgal = JRequest::get('post');

		$id = JFactory::getApplication()->input->getInt('id', 0);

		// Let's filter the title for slashes, spaces etc.
		$field_name = JFactory::getApplication()->input->getCmd('field_name', '');

		if (!$row->bind($postgal))
		{
			throw new Exception($row->getError(), 42);
		}

		$row->field_name = $field_name;

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
				$link = 'index.php?option=com_matukio&controller=bookingfields&task=editBookingfield&id=' . $row->id;
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_BOOKING_FIELD_SAVE');
				$link = 'index.php?option=com_matukio&view=bookingfields';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Resets all bookingfields (Drops table bookingfields)
	 * and reinits them
	 *
	 * @return  void
	 */
	public function reset()
	{
		// First let us drop all bookingfields
		$db = JFactory::getDbo();

		$query = "TRUNCATE TABLE #__matukio_booking_fields";

		$db->setQuery($query);
		$db->execute();

		// Include script.php
		require_once JPATH_COMPONENT_ADMINISTRATOR . "/script.php";
		$script = new Com_MatukioInstallerScript;

		$status = $script->bookingfieldsContent(false);
		$msg = JText::_("COM_MATUKIO_BOOKINGFIELD_RESET_SUCCESS") . " " . $status;

		$this->setRedirect('index.php?option=com_matukio&view=bookingfields', $msg);
	}

	/**
	 * Cancels the edit
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=bookingfields';
		$this->setRedirect($link);
	}
}
