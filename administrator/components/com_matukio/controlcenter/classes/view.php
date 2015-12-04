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

jimport('joomla.application.component.view');
jimport('joomla.application.module.helper');

class ControlCenterView extends JViewLegacy
{
	public function display($tpl = null)
	{
		$config = ControlCenterConfig::getInstance();

		$task = JFactory::getApplication()->input->get('task', 'overview');

		if ($task == "information")
		{
			JToolBarHelper::title(JText::_($config->_extensionTitle) . ' &ndash; ' . JText::_('COMPOJOOM_CONTROLCENTER_TASK_INFORMATION'), 'user-profile');
		} else
		{
			JToolBarHelper::title(JText::_($config->_extensionTitle) . ' &ndash; ' . JText::_('COMPOJOOM_CONTROLCENTER_TASK_OVERVIEW'), 'controlcenter');
		}

		JToolBarHelper::help('screen.' . $config->_extensionTitle);

		$this->assign('config', $config);

		switch ($task)
		{
			case 'information':
				$this->setLayout('information');

				break;

			case 'overview':
			default:
				$this->setLayout('overview');
				break;
		}

		parent::display($tpl);
	}

}