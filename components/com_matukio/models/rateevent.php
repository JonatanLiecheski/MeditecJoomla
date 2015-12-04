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
jimport('joomla.application.component.model');

/**
 * Class MatukioModelRateEvent
 *
 * @since  1.0.0
 */
class MatukioModelRateEvent extends JModelLegacy
{
	/**
	 * Gets the event (we just reference to the event model -- update sometime)
	 *
	 * @param   int  $id  - The id
	 *
	 * @return  mixed
	 */
	public function getEvent($id)
	{
		$model = JModelLegacy::getInstance('Event', 'MatukioModel');
		$event = $model->getItem($id);

		return $event;
	}
}
