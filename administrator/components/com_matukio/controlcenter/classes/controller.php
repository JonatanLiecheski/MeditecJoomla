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

jimport('joomla.application.component.controller');

/**
 * Class ControlCenterController
 *
 * @since  1.0.0
 */
class ControlCenterController extends JControllerLegacy
{
	private $jversion = '15';

	/**
	 * Object contructor
	 * @param array $config
	 *
	 * @return ControlCenterController
	 */
	public function __construct($config = array())
	{
		parent::__construct();

		// Do we have Joomla! 1.6?
		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$this->jversion = '16';
		}

		$basePath = dirname(__FILE__);
		if ($this->jversion == '15')
		{
			$this->_basePath = $basePath;
		} else
		{
			$this->basePath = $basePath;
		}

		$this->registerDefaultTask('overview');
	}

	/**
	 * Runs the eventlist page task
	 */
	public function overview()
	{
		$this->display();
	}

	/**
	 * Displays the current view
	 * @param bool $cachable Ignored!
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$viewLayout = JFactory::getApplication()->input->get('layout', 'overview');

		$view = $this->getThisView();

		// Set the layout
		$view->setLayout($viewLayout);

		// Display the view
		$view->display();
	}

	/**
	 * Gets the control view
	 *
	 * @return  ControlCenterView
	 */
	public final function getThisView()
	{
		static $view = null;

		if (is_null($view))
		{
			$basePath = ($this->jversion == '15') ? $this->_basePath : $this->basePath;
			$tPath = dirname(__FILE__) . '/tmpl';

			require_once 'view.php';
			$view = new ControlCenterView(array('base_path' => $basePath, 'template_path' => $tPath));
		}

		return $view;
	}
}
