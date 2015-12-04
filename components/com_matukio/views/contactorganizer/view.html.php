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
 * Class MatukioViewContactorganizer
 *
 * @since  1.0
 */
class MatukioViewContactorganizer extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   object  $tpl  - The tmpl
	 *
	 * @throws Exception on no id given
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		// Should be 1, else it's messages to participants
		$art = JFactory::getApplication()->input->get('art', 1);
		$cid = JFactory::getApplication()->input->getInt('cid', 0);

		if (empty($cid) && $art != "organizer")
		{
			throw new Exception(JText::_("COM_MATUKIO_NO_ID"));
		}

		if ($art != "organizer")
		{
			$model = JModelLegacy::getInstance('Event', 'MatukioModel');
			$this->event = $model->getItem($cid);
		}
		else
		{
			$this->organizer = MatukioHelperOrganizer::getOrganizerId($cid);
		}

		$this->art = $art;

		parent::display($tpl);
	}
}
