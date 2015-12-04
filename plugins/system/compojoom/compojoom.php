<?php
/**
 * Compojoom System Plugin
 * @package  Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All      rights reserved
 * @Joomla   ! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version  $Revision: 1.0.0 $
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('COMPOJOOM_PLUGIN'))
{
	define('COMPOJOOM_PLUGIN', '1.0.1');
}

if (!defined('CDEBUG'))
{
	define('CDEBUG', false);
}

// import libaries
jimport('joomla.event.plugin');

/**
 * Class plgSystemCompojoom
 *
 * @since       2.2.0
 * @deprecated  Should be deleted sometime
 */
class plgSystemCompojoom extends JPlugin
{
	protected $_extensions = array(
		/*'com_hotspots',*/
		'com_matukio', 'com_ffgate', 'com_tiles', 'com_cadvancedslideshow',
		/*'com_ccomment', 'com_cmigrator', 'com_cmc', 'com_mandrill'*/
	);

	public function onAfterDispatch()
	{
		$app = JFactory::getApplication();

		// This plugin is currently only intended for the administration area
		if (!$app->isAdmin())
		{
			return true;
		}

		if (!in_array(JFactory::getApplication()->input->get("option"), $this->_extensions)
			&& !in_array(JFactory::getApplication()->input->get("extension"), $this->_extensions))
		{
			return true;
		}

		$doc = JFactory::getDocument();

		// This plugin is only for html
		if ($doc->getType() != 'html')
		{
			return true;
		}

		// Load Bootstrap
		if (JVERSION < 3.0)
		{
			// Load cstyle
			// $doc->addStyleSheet(JUri::root() . 'media/plg_system_compojoom/css/cstyle.css');
		}
	}
}
