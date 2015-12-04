<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */
defined('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.view');

/**
 * Class MatukioViewPrintEventlist
 *
 * @since  1.0.0
 */
class MatukioViewPrintEventlist extends JViewLegacy
{
	/**
	 * Displays the eventlist print window
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @return  mixed
	 */
	public function display($tpl = null)
	{
		$input = JFactory::getApplication()->input;

		$cid = $input->getInt('cid', 0);
		$todo = $input->get('todo', '');

		if (empty($cid))
		{
			JError::_raiseError("COM_MATUKIO_NO_ID");

			return;
		}

		switch ($todo)
		{
			default:
			case "csvlist":
				// TODO implement userchecking
				$this->art = $input->getInt('art', 0);
				$this->cid = $cid;

				$this->setLayout("csv");
				break;

			case "invoice":
				// TODO implement userchecking
				$this->art = $input->getInt('art', 0);
				$this->uid = $input->getInt('uid', 0);
				$this->uuid = $input->get('uuid', 0);

				$this->setLayout("invoice");
				break;

			case "ticket":
				$this->art = $input->getInt('art', 0);
				$this->uid = $input->getInt('uid', 0);
				$this->uuid = $input->get('uuid', 0);

				$this->setLayout("ticket");
				break;
		}

		parent::display($tpl);
	}
}
