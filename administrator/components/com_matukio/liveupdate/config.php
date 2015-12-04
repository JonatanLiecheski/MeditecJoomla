<?php
/**
 * @package    Hotspots
 * @author     DanielDimitrov <daniel@compojoom.com>
 * @date       14.07.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 *
 * @since  3.5
 **/
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
	var $_extensionName = 'com_matukio';
	var $_extensionTitle = 'Matukio - Events for Joomla!';
	var $_versionStrategy = 'vcompare';
	var $_updateURL = 'https://compojoom.com/index.php?option=com_ars&view=update&format=ini&id=9';
	var $_requiresAuthorization = true;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Populate downloadID as liveupdate cannot find the download id in the unknown for it scope
		$this->_downloadID = JComponentHelper::getParams('com_matukio')->get('global.downloadid');

		// Dev releases use the "newest" strategy
		if (substr($this->_currentVersion, 1, 2) == 'ev')
		{
			$this->_versionStrategy = 'newest';
		}

		parent::__construct();
	}
}
