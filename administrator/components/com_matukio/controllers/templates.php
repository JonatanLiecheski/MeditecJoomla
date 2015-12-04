<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

/**
 * Class MatukioControllerTemplates
 *
 * @since  2.2.0
 */
class MatukioControllerTemplates extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('apply', 'save');
	}

	/**
	 * Displays the form
	 *
	 * @param   bool  $cachable   - Cache
	 * @param   bool  $urlparams  - Params
	 *
	 * @return  JControllerLegacy|void
	 */

	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'templates');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$model = $this->getModel('Templates', 'MatukioModel');
		$view->setModel($model, true);
		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Saves the templates
	 *
	 * @return  void|bool
	 */

	public function save()
	{
		$subjectArray = JRequest::getVar('subject', array(0), 'post', 'array');
		$valueArray = JRequest::getVar('value', array(0), 'post', 'array');
		$value_textArray = JRequest::getVar('value_text', array(0), 'post', 'array');

		$row = JTable::getInstance('templates', 'Table');

		foreach ($subjectArray as $key => $subject)
		{
			$data['id'] = $key;
			$data['subject'] = $subject;
			$data['value'] = $valueArray[$key];
			$data['value_text'] = $value_textArray[$key];

			if (!$row->bind($data))
			{
				$this->setError($this->_db->getErrorMsg());

				return false;
			}

			if (!$row->check())
			{
				$this->setError($this->_db->getErrorMsg());

				return false;
			}

			if (!$row->store())
			{
				$this->setError($this->_db->getErrorMsg());

				return false;
			}
		}

		switch ($this->task)
		{
			case 'apply':
				$msg = JText::_('COM_MATUKIO_TEMPLATES_FIELD_APPLY');
				$link = 'index.php?option=com_matukio&view=templates';
				break;

			case 'save':
			default:
				$msg = JText::_('COM_MATUKIO_BOOKING_FIELD_SAVE');
				$link = 'index.php?option=com_matukio&view=templates';
				break;
		}

		$this->setRedirect($link, $msg);
	}

	/**
	 * Resets all templates (Drops table templates)
	 * and reinits them
	 *
	 * @return  void
	 */
	public function reset()
	{
		// First let us drop all settings
		$db = JFactory::getDbo();

		$query = "TRUNCATE TABLE #__matukio_templates";

		$db->setQuery($query);
		$db->execute();

		// Include script.php
		require_once JPATH_COMPONENT_ADMINISTRATOR . "/script.php";
		$script = new Com_MatukioInstallerScript;

		$status = $script->templatesContent(false);
		$msg = JText::_("COM_MATUKIO_TEMPLATE_RESET_SUCCESS") . " " . $status;

		$this->setRedirect('index.php?option=com_matukio&view=templates', $msg);
	}

	/**
	 * Return to overview
	 *
	 * @return  void
	 */

	public function cancel()
	{
		$link = 'index.php?option=com_matukio';
		$this->setRedirect($link);
	}
}
