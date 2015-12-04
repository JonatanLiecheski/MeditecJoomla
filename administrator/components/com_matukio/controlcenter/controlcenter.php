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

require_once dirname(__FILE__) . '/config.php';

jimport('joomla.application.component.controller');

/**
 * Class CompojoomControlCenter
 *
 * @since  1.0.0
 */
class CompojoomControlCenter
{
	public static $version = '1.0.1';

	/**
	 * Loads the translation strings -- this is an internal function, called automatically
	 *
	 * @return  void
	 */
	private static function loadLanguage()
	{
		// Load translations
		$basePath = dirname(__FILE__);
		$jlang = JFactory::getLanguage();

		// Load English (British)
		$jlang->load('compojoomcontrolcenter', $basePath, 'en-GB', true);

		// Load the site's eventlist language
		$jlang->load('compojoomcontrolcenter', $basePath, $jlang->getDefault(), true);

		// Load the currently selected language
		$jlang->load('compojoomcontrolcenter', $basePath, null, true);
	}

	/**
	 * Handles requests to the "liveupdate" view which is used to display
	 * update information and perform the live updates
	 *
	 * @param   string  $task  - the task
	 *
	 * @return  void;
	 */
	public static function handleRequest($task = 'overview')
	{
		// Load language strings
		self::loadLanguage();

		if ($task == 'overview')
		{
			// Load the controller and let it run the show
			require_once dirname(__FILE__) . '/classes/controller.php';

			$controller = new ControlCenterController;
			$controller->execute(JFactory::getApplication()->input->get('task', 'overview'));
			$controller->redirect();
		}
		else
		{
			JFactory::getApplication()->input->set('task', $task);

			// Load the controller and let it run the show
			require_once dirname(__FILE__) . '/classes/controller.php';
			$controller = new ControlCenterController;
			$controller->execute($task);
			$controller->redirect();
		}
	}
}
