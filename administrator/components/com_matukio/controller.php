<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


jimport('joomla.application.component.controller');

defined('_JEXEC') or die ('Restricted access');

/**
 * Class MatukioController
 *
 * @since  3.0.0
 */
class MatukioController extends JControllerLegacy
{
	protected $default_view = 'eventlist';
}
