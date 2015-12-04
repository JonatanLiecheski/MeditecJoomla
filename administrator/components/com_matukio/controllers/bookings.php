<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       24.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controlleradmin');

/**
 * Class MatukioControllerParticipants
 *
 * @since  3.0
 */
class MatukioControllerBookings extends JControllerAdmin
{
	/**
	 * Constructor to register extra tasks
	 */
	public function __construct()
	{
		parent::__construct();

		// Register Extra tasks
		$this->registerTask('unpublish', 'publish');
		$this->registerTask('uncertificate', 'certificate');
		$this->registerTask('unpaid', 'paid');

		$this->registerTask('apply', 'save');
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 */
	public function getModel($name = 'Participants', $prefix = 'MatukioModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Certicates / revokes certification of the booking
	 *
	 * @throws  Exception - if query fails
	 * @return  void
	 */
	public function certificate()
	{
		$input = JFactory::getApplication()->input;
		$booking_ids = $input->get("cid", array(), 'array');

		$database = JFactory::getDBO();

		if (count($booking_ids))
		{
			$uids = implode(',', $booking_ids);

			$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id IN ($uids)");
			$rows = $database->loadObjectList();

			if ($database->getErrorNum())
			{
				throw new Exception($database->stderr(), 42);
			}

			foreach ($rows as $row)
			{
				if ($this->task == "certificate")
				{
					$database->setQuery("UPDATE #__matukio_bookings SET certificated='1' WHERE id='$row->id'");
					$certmail = 6;
				}

				if ($this->task == "uncertificate")
				{
					$database->setQuery("UPDATE #__matukio_bookings SET certificated='0' WHERE id='$row->id'");
					$certmail = 7;
				}

				if (!$database->execute())
				{
					throw new Exception($database->stderr(), 42);
				}

				$event = MatukioHelperUtilsEvents::getEventRecurring($row->semid);

				MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $row->id, $certmail);
			}
		}

		$link = 'index.php?option=com_matukio&view=bookings';
		$this->setRedirect($link);
	}

	/**
	 * Toogle paid status
	 *
	 * @throws  Exception - if query fails
	 * @return  void
	 */
	public function paid()
	{
		$input = JFactory::getApplication()->input;
		$event_id = $input->getInt("event_id", 0);
		$booking_ids = $input->get("booking_id", array(), 'array');

		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();

		if (count($booking_ids))
		{
			$uids = implode(',', $booking_ids);

			$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id IN ($uids)");

			$rows = $database->loadObjectList();

			if ($database->getErrorNum())
			{
				throw new Exception($database->stderr(), 42);
			}

			foreach ($rows as $row)
			{
				if ($this->task == "paid")
				{
					$database->setQuery("UPDATE #__matukio_bookings SET paid='1',
						status = " . $database->quote(MatukioHelperUtilsBooking::$ACTIVE)
						. " WHERE id = " . $database->quote($row->id)
					);
				}

				if ($this->task == "unpaid")
				{
					$database->setQuery("UPDATE #__matukio_bookings SET paid='0' WHERE id = " . $database->quote($row->id));
				}

				if (!$database->execute())
				{
					throw new Exception($database->stderr(), 42);
				}
			}
		}

		$link = 'index.php?option=com_matukio&view=bookings';
		$this->setRedirect($link);
	}

	/**
	 * Removes an booking
	 *
	 * @throws  Exception
	 * @return  void
	 */
	public function remove()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		// Set db status to deleted @since 3.1
		MatukioHelperUtilsBooking::deleteBookings($cid);

		$this->setRedirect('index.php?option=com_matukio&view=bookings');
	}

	/**
	 * Cancels participants view
	 *
	 * @return  void
	 */
	public function cancel()
	{
		$link = 'index.php?option=com_matukio&view=bookings';
		$this->setRedirect($link);
	}

	/**
	 * Publishs / unpublish the booking
	 *
	 * @return  void
	 */
	public function publish()
	{
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');

		if ($this->task == 'publish')
		{
			$status = MatukioHelperUtilsBooking::$ACTIVE;
		}
		else
		{
			$status = MatukioHelperUtilsBooking::$PENDING;
		}

		MatukioHelperUtilsBooking::changeStatusBooking($cid, $status, true);

		$msg = "";
		$link = 'index.php?option=com_matukio&view=bookings';

		$this->setRedirect($link, $msg);
	}
}
