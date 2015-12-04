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
jimport('joomla.application.component.view');

/**
 * Class MatukioViewBookings
 *
 * @since  3.0.0
 */
class MatukioViewBookings extends JViewLegacy
{
	/**
	 * Displays the eventlist
	 *
	 * @param   string  $tpl  - The template
	 *
	 * @return  mixed|void
	 */
	public function display($tpl = null)
	{
		$this->state = $this->get('state');

		$event_id = JFactory::getApplication()->input->getInt("event_id", 0);

		if (!empty($event_id))
		{
			$this->state->set('filter.events', $event_id);
		}

		$this->items = $this->get('Items');
		$this->status = $this->get('status');
		$this->pagination = $this->get('Pagination');

		$db = JFactory::getDbo();

		$events[] = JHTML::_('select.option', '0', JTEXT::_('COM_MATUKIO_ALL_EVENTS'));

		// TODO add the possibility to filter for current / old / archived etc. events
		$db->setQuery("SELECT r.id AS value, CONCAT(a.title, ' ', r.begin) AS text FROM #__matukio_recurring AS r
			LEFT JOIN #__matukio AS a ON r.event_id = a.id
		    ORDER BY r.begin DESC
		 ");

		$events = array_merge($events, (array) $db->loadObjectList());

		$this->events = JHtml::_('select.genericlist', $events, 'filter_events', 'onchange="jQuery(\'#task\').val(\'\'); this.form.submit()"',
				'value', 'text', $this->state->get('filter.events')
		);

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Ads the toolbar buttons
	 *
	 * @return void
	 */
	public function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_MATUKIO_BOOKINGS'), 'generic');

		JToolBarHelper::addNew('editbooking.editbooking');

		JToolBarHelper::publishList("bookings.publish", JText::_('COM_MATUKIO_ACTIVATE'));
		JToolBarHelper::unpublishList("bookings.unpublish", JText::_('COM_MATUKIO_SET_PENDING'));

		// Delete participants (set status = deleted)
		JToolBarHelper::deleteList('COM_MATUKIO_REALLY_DELETE_THIS_PARTICIPANT', 'bookings.remove');

		JToolBarHelper::custom("bookings.certificate", "publish", "publish", JText::_("COM_MATUKIO_CERTIFICATE"));
		JToolBarHelper::custom("bookings.uncertificate", "unpublish", "unpublish", JText::_("COM_MATUKIO_WITHDREW_CERTIFICATE"));

		JToolbarHelper::custom("contactparticipants.display", "send", "send", JText::_("COM_MATUKIO_CONTACT_PARTICIPANTS"));

		// Tmpl = component
		JToolbarHelper::custom("print.signature", "print", "print", JText::_("COM_MATUKIO_PRINT_SIGNATURE_LIST"));
		JToolbarHelper::custom("print.participant", "print", "print", JText::_("COM_MATUKIO_PRINT_PARTICIPANTS_LIST"));

		// Raw format
		JToolbarHelper::custom("print.csv", "checkin", "checkin", JText::_("COM_MATUKIO_DOWNLOAD_CSV_FILE"));

		JToolBarHelper::help('screen.matukio', true);
	}
}
