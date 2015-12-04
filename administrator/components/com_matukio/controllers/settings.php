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

/**
 * Class MatukioControllerSettings
 *
 * @since  1.0
 */
class MatukioControllerSettings extends MatukioController
{
	/**
	 * Constructor
	 *
	 * Register extra task apply
	 */
	public function __construct()
	{
		parent::__construct();
		$this->registerTask('apply', 'save');
	}

	/**
	 * Saves the array of settings
	 *
	 * @return  void
	 */
	public function save()
	{
		$matukioSet = JRequest::getVar('matukioset', array(0), 'post', 'array');

		require_once JPATH_COMPONENT . '/models/settings.php';

		$model = new MatukioModelSettings;

		switch (JFactory::getApplication()->input->get('task'))
		{
			case 'apply':
				if ($model->store($matukioSet))
				{
					$msg = JText::_('COM_MATUKIO_CHANGES_TO_SETTINGS_SAVED');
				}
				else
				{
					$msg = JText::_('COM_MATUKIO_ERROR_SAVING_SETTINGS');
				}

				$this->setRedirect('index.php?option=com_matukio&view=settings', $msg);
				break;

			case 'save':
			default:
				if ($model->store($matukioSet))
				{
					$msg = JText::_('COM_MATUKIO_SETTINGS_SAVED');
				}
				else
				{
					$msg = JText::_('COM_MATUKIO_ERROR_SAVING_SETTINGS');
				}

				$this->setRedirect('index.php?option=com_matukio');
				break;
		}

		$model->checkin();
	}

	/**
	 * Displays the settings form
	 *
	 * @param   bool  $cachable   - Is cachable?
	 * @param   bool  $urlparams  - The Params
	 *
	 * @return  JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->get('view', 'settings');

		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);

		require_once JPATH_COMPONENT . '/models/settings.php';

		$model = new MatukioModelSettings;

		$view->setModel($model, true);

		$view->setLayout('default');
		$view->display();
	}

	/**
	 * Returns to the default overview
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_matukio');
	}

	/**
	 * Resets all settings (Drops table settings)
	 * and reinitializes them
	 *
	 * @return  void
	 */
	public function reset()
	{
		// First let us drop all settings
		$db = JFactory::getDbo();

		$query = "TRUNCATE TABLE #__matukio_settings";

		$db->setQuery($query);
		$db->execute();

		// Include script.php
		require_once JPATH_COMPONENT_ADMINISTRATOR . "/script.php";
		$script = new Com_MatukioInstallerScript;

		$status = $script->settingsContent(false);
		$msg = JText::_("COM_MATUKIO_SETTINGS_RESET_SUCCESS") . " " . $status;

		$this->setRedirect('index.php?option=com_matukio&view=settings', $msg);
	}
}
