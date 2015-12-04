<?php
/**
 * ControlCenter
 * @package Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All rights reserved
 * @Joomla! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version $Revision: 0.9.0 beta $
 **/

defined('_JEXEC') or die();

/**
 * Class ControlCenterConfig
 *
 * @since  2.0.0
 */
class ControlCenterConfig
{
	public $version = "4.5.0";

	public $copyright = "Copyright (C) 2011 - 2014 Yves Hoppe - compojoom.com";

	public $license = "GPL v2 or later";

	public $translation = "English: compojoom.com <br />German: compojoom.com";

	public $description = "COM_MATUKIO_XML_DESCRIPTION";

	public $thankyou = "<li><a href='http://seminar.vollmar.ws'>Dirk Vollmar</a> - For writing the extension Seminar for Joomla 1.5,
                        on which Version 1.0 of this extension was originally based</li>
                        <li>Sebastiaan - For his continous help finding bugs and improving Matukio</li>
                        <li>Rob Swart - For his continous help finding bugs and improving Matukio</li>
                        <li>Hubert Beck - For his continous help finding bugs and improving Matukio</li>
                        <li>Bernd Seifert - For his continous help finding bugs and improving Matukio</li>
                        ";

	public $_extensionTitle = "com_matukio";

	// E.G. ccc_extensionPostion_left
	public $extensionPosition = "matukio";

	public $_logopath = '/media/com_matukio/backend/images/logo.png';

	/**
	 * Gets the config instance
	 *
	 * @return  ControlCenterConfig
	 */
	public static function &getInstance()
	{
		static $instance = null;

		if (!is_object($instance))
		{
			$instance = new ControlCenterConfig;
		}

		return $instance;
	}
}
