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
 * Class MatukioViewAgb
 *
 * @since  1.0.0
 */
class MatukioViewAgb extends JViewLegacy
{
	/**
	 * Displays the form
	 *
	 * @param   string  $tpl  - The tmpl
	 *
	 * @return mixed|void
	 */
	public function display($tpl = null)
	{
		$this->agb = nl2br(MatukioHelperSettings::getSettings('agb_text', ''));
		parent::display($tpl);
	}
}
