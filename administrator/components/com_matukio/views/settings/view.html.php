<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       29.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.view');

/**
 * Class MatukioViewSettings
 *
 * @since  1.0.0
 */
class MatukioViewSettings extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  - An opt template
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$items = $this->get('Data');

		for ($i = 0; $i < count($items); $i++)
		{
			$item = $items[$i];

			if ($item->catdisp == "basic")
			{
				$items_basic[$item->id] = $item;
			}
			elseif ($item->catdisp == "layout")
			{
				$items_layout[$item->id] = $item;
			}
			elseif ($item->catdisp == "advanced")
			{
				$items_advanced[$item->id] = $item;
			}
			elseif ($item->catdisp == "security")
			{
				$items_security[$item->id] = $item;
			}
			elseif ($item->catdisp == "payment")
			{
				$items_payment[$item->id] = $item;
			}
			elseif ($item->catdisp == "modernlayout")
			{
				$items_modernlayout[$item->id] = $item;
			}
			elseif ($item->catdisp == "defaults")
			{
				$items_defaults[$item->id] = $item;
			}
			elseif ($item->catdisp == "cronjobs")
			{
				$items_cronjobs[$item->id] = $item;
			}
		}

		$this->items = $items;

		$this->items_basic = $items_basic;
		$this->items_layout = $items_layout;
		$this->items_modernlayout = $items_modernlayout;
		$this->items_advanced = $items_advanced;
		$this->items_security = $items_security;
		$this->items_payment = $items_payment;
		$this->items_defaults = $items_defaults;
		$this->items_cronjobs = $items_cronjobs;

		$this->addToolbar();

		parent::display($tpl);
	}

	/**
	 * Adds the toolbar
	 *
	 * @return  void
	 */
	public function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_MATUKIO_SETTINGS'), 'config');
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::custom("reset", "options", "options", JText::_('COM_MATUKIO_SETTINGS_RESET'), false);
		JToolBarHelper::preferences("com_matukio");
		JToolBarHelper::help('screen.matukio', true);
	}
}
