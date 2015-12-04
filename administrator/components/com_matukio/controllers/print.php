<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       05.10.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.controlleradmin');

/**
 * Class MatukioControllerCoupons
 *
 * @since  3.0.0
 */
class MatukioControllerPrint extends JControllerAdmin
{
	/**
	 * Prints the signature list
	 * Redirects if tmpl = component is not set -> hack because document->setType is not working!
	 *
	 * @return  void | null
	 */
	public function signature()
	{
		$tmpl = JFactory::getApplication()->input->get('tmpl', '');

		if (empty($tmpl))
		{
			$bookings = JFactory::getApplication()->input->get('cid', array(), 'array');
			$link = "index.php?option=com_matukio&tmpl=component&controller=print&task=signature&bookings=" . implode(",", $bookings);

			return $this->setRedirect($link);
		}


		$bookings = JFactory::getApplication()->input->get('bookings', '', 'string');

		if (empty($bookings))
		{
			$link = "index.php?option=com_matukio&view=bookings";

			return $this->setRedirect($link, "Printing failed");
		}

		$viewName = 'print';
		$view = $this->getView($viewName, "html");
		$view->setLayout('signature');
		$view->display();
	}

	/**
	 * Prints the participant list
	 * Redirects if tmpl = component is not set -> hack because document->setType is not working!
	 *
	 * @return  void | null
	 */
	public function participant()
	{
		$tmpl = JFactory::getApplication()->input->get('tmpl', '');

		if (empty($tmpl))
		{
			$bookings = JFactory::getApplication()->input->get('cid', array(), 'array');
			$link = "index.php?option=com_matukio&tmpl=component&controller=print&task=participant&bookings=" . implode(",", $bookings);

			return $this->setRedirect($link);
		}

		$bookings = JFactory::getApplication()->input->get('bookings', '', 'string');

		if (empty($bookings))
		{
			$link = "index.php?option=com_matukio&view=bookings";

			return $this->setRedirect($link, "Printing failed");
		}

		$viewName = 'print';
		$view = $this->getView($viewName, "html");
		$view->setLayout('participant');
		$view->display();
	}

	/**
	 * Downloads the csv export list
	 * Redirects if format raw is not set -> hack because document->setType is not working!
	 *
	 * @return  void | null
	 */
	public function csv()
	{
		$doc = JFactory::getDocument();
		$type = $doc->getType();

		if ($type != "raw")
		{
			$bookings = JFactory::getApplication()->input->get('cid', array(), 'array');
			$link = "index.php?option=com_matukio&format=raw&controller=print&task=csv&bookings=" . implode(",", $bookings);

			return $this->setRedirect($link);
		}

		$bookings = JFactory::getApplication()->input->get('bookings', '', 'string');

		if (empty($bookings))
		{
			$link = "index.php?option=com_matukio&view=bookings";

			return $this->setRedirect($link, "CSV Download failed");
		}

		$viewName = 'print';
		$view = $this->getView($viewName, 'raw');
		$view->setLayout('csv');
		$view->display();
	}
}
