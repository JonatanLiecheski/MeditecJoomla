<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       13.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * Class MatukioViewContactpart
 *
 * @since  2.2.4
 */
class MatukioViewContactparticipants extends JViewLegacy
{

	/**
	 * Displays the form
	 * since 3.0 -> we can also use bookings instead of event ids
	 *
	 * @param   object  $tpl  - The template
	 *
	 * @return  void
	 */

	public function display($tpl = null)
	{
		$appl = JFactory::getApplication();
		$uri = JFactory::getURI();
		$model = $this->getModel();

		$this->participants = $model->getParticipants();
		$this->event_id = JFactory::getApplication()->input->getInt('event_id', 0);

		if (empty($this->event_id))
		{
			$this->booking_ids = JFactory::getApplication()->input->get('cid', array(), 'array');
		}

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
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_MATUKIO_CONTACT_PARTICIPANTS'), 'massmail');
		JToolbarHelper::custom("contactparticipants.send", 'send', 'send', JText::_("COM_MATUKIO_SEND"), false);
		JToolBarHelper::help('screen.matukio', true);
	}
}
