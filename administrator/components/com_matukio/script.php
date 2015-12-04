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

/**
 * Script file of Matukio component
 * Com_MatukioInstallerScript
 *
 * @since  1.0.0
 */
class Com_MatukioInstallerScript
{
	/*
	 * The release value to be displayed and checked against throughout this file.
	 */
	private $release = '1.0';

	private $minimum_joomla_release = '2.5.6';

	private $extension = "COM_MATUKIO";

	private $matVersion = '4.5.0';

	public $isUpdate = "No";

	private $installationQueue = array(
		'modules' => array(
			'site' => array(
				'mod_matukio' => array('left', 0),
				'mod_matukio_booking' => array('left', 0),
				'mod_matukio_calendar' => array('left', 0),
				'mod_matukio_upcoming' => array('left', 0),
			),
			'admin' => array(
				"mod_ccc_matukio_icons" => array('ccc_matukio_left', 1),
				"mod_ccc_matukio_newsfeed" => array('ccc_matukio_slider', 1),
				"mod_ccc_matukio_update" => array('ccc_matukio_slider', 1),
				"mod_ccc_matukio_overview" => array('ccc_matukio_slider', 1),
				"mod_ccc_matukio_promotion" => array('ccc_matukio_promotion', 1),
			),
		),

		'plugins' => array(
			'plg_search_matukio' => 1,
			'plg_system_compojoom' => 1,
			'plg_payment_alphauserpoints' => 1,
			'plg_payment_amazon' => 1,
			'plg_payment_cash' => 1,
			'plg_payment_banktransfer' => 1,
			'plg_payment_authorizenet' => 1,
			'plg_payment_bycheck' => 1,
			'plg_payment_byorder' => 1,
			'plg_payment_ccavenue' => 1,
			'plg_payment_jomsocialpoints' => 1,
			'plg_payment_linkpoint' => 1,
			'plg_payment_paypal' => 1,
			'plg_payment_paypalpro' => 1,
			'plg_payment_payu' => 1,
			'plg_payment_debit' => 1,
			// New since 4.4.2
			'plg_payment_epaydk' => 1,
			'plg_payment_payfast' => 1,
			'plg_payment_paymill' => 1
		),

		// Key is the name without the lib_ prefix, value if the library should be autopublished
		'libraries' => array(
			'compojoom' => 1
		)
	);

	/**
	 * method to install the component
	 *
	 * @param   string  $parent  - The parent
	 *
	 * @return void
	 */
	public function install($parent)
	{
		$this->parent = $parent;
	}

	/**
	 * The joomla framework doesn't tell us if the component has tables filled with data
	 *
	 * @return  boolean
	 */
	private function newInstall()
	{
		$db = JFactory::getDbo();
		$query = 'SELECT * FROM ' . $db->quoteName('#__matukio_settings') . ' WHERE title = ' . $db->Quote('db_version');
		$db->setQuery($query);

		try
		{
			$result = $db->loadObject();

			if (empty($result))
			{
				return true;
			}

			return false;
		}
		catch (Exception $e)
		{
			// Error
			return true;
		}
	}

	/**
	 * method to uninstall the component
	 *
	 * @param   string  $parent  - The parent
	 *
	 * @return void
	 */
	public function uninstall($parent)
	{
		$this->parent = $parent;
		$this->uninstallModules();
		$this->status->plugins = $this->uninstallPlugins($this->installationQueue['plugins']);

		echo $this->displayInfoUninstallation();
	}

	/**
	 * method to update the component
	 *
	 * @param   string  $parent  - The parent
	 *
	 * @throws  Exception  - if an uknown database is found
	 * @return  void
	 */
	public function update($parent)
	{
		$this->parent = $parent;

		if (!$this->newInstall())
		{
			$this->isUpdate = "Yes";

			$db = JFactory::getDbo();

			// Check which db version
			$query = 'SELECT * FROM ' . $db->quoteName('#__matukio_settings') . ' WHERE title = ' . $db->Quote('db_version');

			$db->setQuery($query);
			$row = $db->loadObject();

			$update = $row->value;

			if (empty($update))
			{
				// Set version to 1.0 then..
				$update = '1.0.0';
			}

			switch ($update)
			{
				case '1.0.0':
					$this->dummyContent();
					$this->update100();
				case '2.0.0':
				case '2.0.1':
					$this->update202();
				case '2.0.2':
				case '2.1.0':
				case '2.1.1':
				case '2.1.2':
				case '2.1.3':
				case '2.1.4':
				case '2.1.5':
				case '2.1.6':
				case '2.1.7':
				case '2.1.8':
				case '2.1.9':
				case '2.1.10':
					$this->update220();
				case '2.2.0':
				case '2.2.1':
					$this->update222();
				case '2.2.2':
				case '2.2.3':
				case '2.2.4':
					$this->update300();
				case '3.0.0':
				case '3.0.1':
					$this->update302();
				case '3.0.2':
				case '3.0.3':
				case '3.0.4':
				case '3.0.5':
				case '3.0.6':
				case '3.0.7':
				case '3.0.8':
					$this->update310();
				case '3.1.0':
					$this->update311();
				case '3.1.1':
				case '4.0.0':
				case '4.0.1':
				case '4.0.2':
				case '4.0.3':
				case '4.0.4':
				case '4.0.5':
					$this->update406();
				case '4.0.6':
				case '4.0.7':
				case '4.0.8':
				case '4.0.9':
				case '4.1.0':
					$this->update420();
				case '4.2.0':
					$this->update421();
				case '4.2.1':
				case '4.2.2':
				case '4.2.3':
				case '4.2.4':
					$this->update430();
				case '4.3.0':
				case '4.3.1':
					$this->update440();
				case '4.4.0':
				case '4.4.1':
				case '4.4.2':
					$this->update450();
					$this->updateDBVersion();
				case '4.5.0':
					// Current release;
					break;
				default:
				case 'new':
					// We break here.. before ruining the database
					throw new Exception("Unknown Database version - are you updating from a development release?");
					break;
			}
		}
	}

	/**
	 * Updates the database version to the current one
	 *
	 * @return  void
	 */
	public function updateDBVersion()
	{
		$db = JFactory::getDbo();
		$query = 'UPDATE ' . $db->quoteName('#__matukio_settings') . ' SET value = "' . $this->matVersion . '" WHERE title = ' . $db->quote('db_version');

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates the database to the initial version
	 *
	 * @return  void
	 */
	public function update100()
	{
		$db = JFactory::getDbo();

		$query = "ALTER TABLE  `#__matukio` ADD  `created_by` INT( 10 ) NOT NULL DEFAULT  '0',
                ADD  `created_by_alias` VARCHAR( 255 ) NOT NULL DEFAULT  '',
                ADD  `created` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
                ADD  `modified` DATETIME NOT NULL DEFAULT  '0000-00-00 00:00:00',
                ADD  `modified_by` INT( 10 ) NOT NULL DEFAULT  '0',
                ADD  `group_id` INT( 11 ) NOT NULL DEFAULT  '0',
                ADD  `webinar` TINYINT( 1 ) DEFAULT '0';
                ";

		$db->setQuery($query);
		$db->execute();

		// DB 2.0.0
		$query = "ALTER TABLE  #__matukio_bookings ADD  `newfields` TEXT NULL,
                          ADD  `uuid` VARCHAR(255) NULL DEFAULT  '',
                          ADD  `payment_method` VARCHAR(255) NULL DEFAULT  '',
                          ADD  `payment_number` VARCHAR(255) NULL DEFAULT  '',
                          ADD  `payment_netto` FLOAT( 11, 2 ) NULL DEFAULT  '0.00',
                          ADD  `payment_tax` FLOAT( 11, 2 ) NULL DEFAULT  '0.00',
                          ADD  `payment_brutto` FLOAT( 11, 2 ) NULL DEFAULT  '0.00',
                          ADD  `coupon_code` VARCHAR(255) NULL DEFAULT '',
                          ADD  `checked_in` TINYINT(1) DEFAULT '0';
                ";

		$db->setQuery($query);
		$db->execute();

		$query = "CREATE TABLE IF NOT EXISTS `#__matukio_booking_coupons` (
                              `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                              `code` VARCHAR( 255 ) NOT NULL ,
                              `value` FLOAT( 11.2 ) NOT NULL DEFAULT  '0.00',
                              `procent` TINYINT( 1 ) NOT NULL DEFAULT  '1',
                              `published_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                              `published_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
                              `published` TINYINT( 1 ) NOT NULL DEFAULT  '0'
                            ) COMMENT='Coupons';
                ";

		$db->setQuery($query);
		$db->execute();

		$query = "CREATE TABLE IF NOT EXISTS `#__matukio_booking_fields` (
                              `id` int(11) NOT NULL AUTO_INCREMENT,
                              `field_name` varchar(255) NOT NULL,
                              `label` varchar(255) NOT NULL,
                              `default` text,
                              `values` text,
                              `page` tinyint(3) NOT NULL DEFAULT '1',
                              `type` varchar(255) NOT NULL DEFAULT 'text',
                              `required` tinyint(1) NOT NULL DEFAULT '0',
                              `ordering` int(11) NOT NULL DEFAULT '0',
                              `style` text,
                              `published` tinyint(1) NOT NULL DEFAULT '0',
                              PRIMARY KEY (`id`)
                            ) COMMENT='Fields';
                ";

		$db->setQuery($query);
		$db->execute();

		// Settings Reset for version 2.0 and 2.0.1
		$query = "TRUNCATE #__matukio_settings";
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates the table to Matukio 2.0.2
	 *
	 * @return  void
	 */
	public function update202()
	{
		$db = JFactory::getDbo();
		$query = "ALTER TABLE  " . $db->quoteName('#__matukio') . " ADD `language` VARCHAR( 255 ) NOT NULL DEFAULT '*'";

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates the database to version  2.2.2
	 * And at ALSO to 3.0.0
	 *
	 * @return  void
	 */
	public function update222()
	{
		$db = JFactory::getDbo();
		$query = 'INSERT INTO  ' . $db->quoteName('#__matukio_settings') . " (
                                                   `title` , `value`, `values`, `type`, `catdisp`)
                                                    VALUES (
                                                    'contact_organizer',  '1',  '',  'bool',  'advanced'
                                                    );";

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates the database to version 2.2
	 *
	 * @return  void
	 */
	public function update220()
	{
		$db = JFactory::getDbo();

		$query = "CREATE TABLE IF NOT EXISTS `#__matukio_templates` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `tmpl_name` varchar(255) NOT NULL,
                  `category` tinyint(4) NOT NULL DEFAULT '0',
                  `subject` text NOT NULL,
                  `value` text NOT NULL,
                  `value_text` text NOT NULL,
                  `default` text NOT NULL,
                  `modified_by` int(11) NOT NULL DEFAULT '0',
                  `published` tinyint(1) NOT NULL DEFAULT '1',
                  PRIMARY KEY (`id`)
                ) COMMENT='Templates'";

		$db->setQuery($query);
		$db->execute();

		$query = "CREATE TABLE IF NOT EXISTS `#__matukio_organizers` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `userId` int(11) NOT NULL,
                  `name` varchar(255) NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `website` varchar(255) NOT NULL,
                  `phone` varchar(255) NOT NULL,
                  `description` text NOT NULL,
                  `comments` text NOT NULL,
                  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  `image` varchar(255) NOT NULL,
                  `created_by` varchar(11) NOT NULL,
                  `modified_by` varchar(11) NOT NULL,
                  `published` varchar(1) NOT NULL,
                  PRIMARY KEY (`id`)
                ) COMMENT='Organizer';";

		$db->setQuery($query);
		$db->execute();

		$query = "ALTER TABLE `#__matukio` ADD `booking_mail` TEXT NOT NULL DEFAULT '',
                                             ADD  `certificate_code` TEXT NOT NULL DEFAULT '',
                                             ADD  `top_event` TINYINT( 1 ) NOT NULL DEFAULT  '0' ,
                                             ADD  `hot_event` TINYINT( 1 ) NOT NULL DEFAULT  '0' ,
                                             ADD  `asset_id` INT( 10 ) NOT NULL DEFAULT  '0' ,
                                             ADD  `status` TINYINT NOT NULL DEFAULT  '0'";

		$db->setQuery($query);
		$db->execute();

		$this->templatesContent();

		$query = "INSERT INTO " . $db->quoteName('#__matukio_settings') . " (`title`, `value`, `values`, `type`, `catdisp`) VALUES
                          ('mat_signature', '<strong>Please do not answer this E-Mail</strong>', '', 'text', 'layout'),
                          ('email_html', '1', '', 'bool', 'layout'),
                          ('export_csv_separator',  ';',  '',  'text',  'advanced'),
                          ('location_image', '1', '', 'bool', 'modernlayout'),
                          ('bookingfield_desc', '0', '', 'bool', 'advanced'),
                          ('navi_eventlist_number', '1', '', 'bool', 'modernlayout'),
                          ('navi_eventlist_search', '1', '', 'bool', 'modernlayout'),
                          ('navi_eventlist_categories', '1', '', 'bool', 'modernlayout'),
                          ('navi_eventlist_types', '1', '', 'bool', 'modernlayout'),
                          ('navi_eventlist_reset', '1', '', 'bool', 'modernlayout');";

		$db->setQuery($query);
		$db->execute();

		$query = "ALTER TABLE " . $db->quoteName('#__matukio_bookings')
			. " ADD `payment_status` VARCHAR( 255 ) NOT NULL DEFAULT 'P' ,
                        ADD `status` TINYINT NOT NULL DEFAULT '0'";
		$db->setQuery($query);
		$db->execute();

		$settings_del = array("certificate_htmlcode", "payment_cash", "payment_banktransfer", "payment_paypal",
			"payment_invoice", "cbt", "cpp_header_image", "cpp_headerback_color", "cpp_headerborder_color", "paypal_address");

		foreach ($settings_del as $set)
		{
			// Delete old no more needed settings
			$query = "DELETE FROM " . $db->quoteName('#__matukio_settings') . " WHERE title = " . $db->quote($set);
			$db->setQuery($query);
			$db->execute();
		}

		$query = "UPDATE " . $db->quoteName('#__matukio_settings') . ' SET `values` = "{0=START}{1=END}{2=ANMELDESCHLUSS}{3=NEVER}" WHERE title = ' . $db->quote('event_stopshowing');

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates the Matukio database to 3.0
	 *
	 * @return  void
	 */
	public function update300()
	{
		$db = JFactory::getDbo();

		// New tables
		$query = "CREATE TABLE IF NOT EXISTS `#__matukio_taxes` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `title` varchar(255) NOT NULL,
					  `value` double NOT NULL DEFAULT '0',
					  `published` tinyint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) COMMENT='Taxes';";

		$db->setQuery($query);
		$db->execute();

		$query = "CREATE TABLE IF NOT EXISTS `#__matukio_different_fees` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `title` varchar(255) NOT NULL,
					  `value` double(11,2) NOT NULL DEFAULT '0.00',
					  `percent` tinyint(1) NOT NULL DEFAULT '1',
					  `discount` tinyint(1) NOT NULL DEFAULT '1',
					  `published_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `published_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `published` tinyint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) Comment 'Fees';";

		$db->setQuery($query);
		$db->execute();

		$query = "CREATE TABLE IF NOT EXISTS `#__matukio_locations` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `title` varchar(1000) NOT NULL,
					  `gmaploc` varchar(1000) NOT NULL,
					  `location` varchar(1000) NOT NULL,
					  `phone` varchar(150) NOT NULL,
					  `email` varchar(255) NOT NULL,
					  `website` varchar(255) NOT NULL,
					  `description` text NOT NULL,
					  `image` varchar(255) NOT NULL,
					  `comments` text NOT NULL,
					  `created_by` int(11) NOT NULL,
					  `modified_by` int(11) NOT NULL,
					  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
					  `published` tinyint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) Comment 'Locations';";

		$db->setQuery($query);
		$db->execute();

		// Alter tables
		$query = "ALTER TABLE  `#__matukio` ADD  `tax_id` INT NOT NULL DEFAULT  '0' AFTER  `hot_event` ,
					ADD  `different_fees` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `tax_id` ,
					ADD  `different_fees_override` TEXT NOT NULL AFTER  `different_fees`";

		$db->setQuery($query);
		$db->execute();

		$query = "ALTER TABLE  `#__matukio_bookings` ADD  `different_fees` VARCHAR( 255 ) DEFAULT '' AFTER  `payment_brutto`";

		$db->setQuery($query);
		$db->execute();

		$query = "ALTER TABLE  `#__matukio` ADD  `place_id` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `description` ;";
		$db->setQuery($query);
		$db->execute();

		// New settings - we drop the old one .. clean up the mess
		$query = "TRUNCATE TABLE #__matukio_settings";

		$db->setQuery($query);
		$db->execute();

		$this->settingsContent();
	}

	/**
	 * Updates Matukio to 3.0.2
	 *
	 * @return  void
	 */
	public function update302()
	{
		$db = JFactory::getDbo();

		$query = "ALTER TABLE  `#__matukio_booking_coupons` ADD  `max_hits` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `value`,
		         ADD  `hits` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `max_hits`
		 ;";

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates Database to 3.1
	 *
	 * @throws  Exception - if queries fail
	 * @return  void
	 */
	public function update310()
	{
		$db = JFactory::getDbo();

		$query = <<<'EOT'
		INSERT INTO `#__matukio_templates` (`id`, `tmpl_name`, `category`, `subject`, `value`, `value_text`, `default`, `modified_by`, `published`) VALUES
		(8, 'invoice', 3, 'E', '<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td align="left" width="100%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td width="100%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td align="left" valign="top" width="50%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td align="left">Your Company</td>\r\n</tr>\r\n<tr>\r\n<td align="left">Your Company Address</td>\r\n</tr>\r\n<tr>\r\n<td align="left">Your Tax Number</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td align="center" valign="middle" width="50%">Your Logo</td>\r\n</tr>\r\n<tr>\r\n<td colspan="2" align="left" width="100%"><br /><br />\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td align="left" valign="top" width="50%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td style="background-color: #d6d6d6;" align="left">\r\n<h4 style="margin: 0px;">Customer Information</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td align="left">MAT_BOOKING_NAME</td>\r\n</tr>\r\n<tr>\r\n<td align="left">MAT_BOOKING_STREET</td>\r\n</tr>\r\n<tr>\r\n<td align="left">MAT_BOOKING_ZIP MAT_BOOKING_CITY</td>\r\n</tr>\r\n<tr>\r\n<td align="left">MAT_BOOKING_COUNTRY</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td align="left" valign="top" width="50%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td style="background-color: #d6d6d6;" colspan="2" align="left">\r\n<h4 style="margin: 0px;">Invoice Information</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td align="left" width="50%">Invoice Number:</td>\r\n<td align="left">MAT_INVOICE_NUMBER</td>\r\n</tr>\r\n<tr>\r\n<td align="left" width="50%">Invoice Date:</td>\r\n<td align="left">MAT_INVOICE_DATE</td>\r\n</tr>\r\n<tr>\r\n<td align="left" width="50%">Booking Number:</td>\r\n<td align="left">MAT_BOOKING_NUMBER</td>\r\n</tr>\r\n<tr>\r\n<td align="left" width="50%">Payment method:</td>\r\n<td align="left">MAT_BOOKING_PAYMENT_METHOD</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<br /><br /></td>\r\n</tr>\r\n<tr>\r\n<td style="background-color: #d6d6d6;" colspan="2" align="left">\r\n<h4 style="margin: 0px;">Order Items</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td colspan="2" align="left" width="100%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td align="left" valign="top" width="10%">#</td>\r\n<td align="left" valign="top" width="50%">Event</td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n</tr>\r\n<tr>\r\n<td align="left" valign="top" width="10%">MAT_BOOKING_NRBOOKED</td>\r\n<td align="left" valign="top" width="50%">MAT_EVENT_TITLE </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%">MAT_BOOKING_PAYMENT_NETTO</td>\r\n</tr>\r\n<tr>\r\n<td colspan="5" align="right" valign="top" width="90%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n</tr>\r\n<tr>\r\n<td colspan="5" align="right" valign="top" width="90%">Net total:</td>\r\n<td align="left" valign="top" width="10%">MAT_BOOKING_PAYMENT_NETTO</td>\r\n</tr>\r\n<tr>\r\n<td colspan="5" align="right" valign="top" width="90%">Tax total:</td>\r\n<td align="left" valign="top" width="10%">MAT_BOOKING_PAYMENT_TAX</td>\r\n</tr>\r\n<tr>\r\n<td colspan="5" align="right" valign="top" width="90%">Total:</td>\r\n<td align="left" valign="top" width="10%">MAT_BOOKING_PAYMENT_BRUTTO</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="background-color: #d6d6d6;" colspan="2" align="left">\r\n<h4 style="margin: 0px;">Invoice Note</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td colspan="2" align="left" width="100%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td> Your notes</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p> </p>', '', '', 0, 1),
		(9, 'invoice_email', 3, '##COM_MATUKIO_INVOICE_SUBJECT## MAT_BOOKING_NUMBER', '<div id="mat-invoice-mail">\r\n<p><span style="line-height: 1.3em;">##COM_MATUKIO_EMAIL_GREETING## MAT_BOOKING_NAME,<br /><br /></span>##COM_MATUKIO_EMAIL_INVOICE_ATTACHED##</p>\r\n<p>MAT_SIGNATURE</p>\r\n</div>', '', '', 0, 1),
		(10, 'ticket', 4, 'E', '<table style="width: 100%;">\r\n<tbody>\r\n<tr>\r\n<td>MAT_EVENT_TITLE - MAT_EVENT_BEGIN</td>\r\n</tr>\r\n<tr>\r\n<td>MAT_BOOKING_NRBOOKED - MAT_BOOKING_TICKETS </td>\r\n</tr>\r\n<tr>\r\n<td>MAT_BOOKING_NUMBER</td>\r\n</tr>\r\n<tr>\r\n<td><span style="font-family: Tahoma, Helvetica, Arial, sans-serif; font-size: 12px;">MAT_BOOKING_QRCODE</span></td>\r\n</tr>\r\n<tr>\r\n<td>MAT_BOOKING_PAYMENT_BRUTTO</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p><br /><br /><br /><br /></p>', '', '', 0, 1);
EOT;

		$db->setQuery($query);
		$db->execute();

		$query = "CREATE TABLE IF NOT EXISTS `#__matukio_invoice_number` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `number` int(11) NOT NULL,
				  `year` int(4) NOT NULL DEFAULT '2014',
				  `booking_id` int(11) NOT NULL DEFAULT '-1',
				  `published` tinyint(1) NOT NULL DEFAULT '0',
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8 Comment 'Invoices';";

		$db->setQuery($query);
		$db->execute();

		$query = "ALTER TABLE  `#__matukio` ADD `recurring` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `different_fees_override`,
				ADD  `recurring_count` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `recurring` ,
				ADD  `recurring_type` VARCHAR( 20 ) NOT NULL DEFAULT  'daily' AFTER  `recurring_count` ,
				ADD  `recurring_week_day` VARCHAR( 20 ) NOT NULL DEFAULT  '1' AFTER  `recurring_type`,
				ADD  `recurring_month_week` VARCHAR( 30 ) NOT NULL DEFAULT  'week1' AFTER  `recurring_week_day` ,
				ADD  `recurring_until` DATE NOT NULL DEFAULT  '0000-00-00' AFTER  `recurring_month_week`,
				ADD  `recurring_created` TINYINT( 2 ) NOT NULL DEFAULT  '0' AFTER  `recurring_until`;";

		$db->setQuery($query);
		$db->execute();

		$query = "CREATE TABLE IF NOT EXISTS `#__matukio_recurring` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `event_id` int(11) NOT NULL,
				  `semnum` varchar(255) NOT NULL,
				  `begin` datetime NOT NULL,
				  `end` datetime NOT NULL,
				  `booked` datetime NOT NULL,
				  `hits` INT( 11 ) NOT NULL DEFAULT '0',
				  `grade` TINYINT( 1 ) NOT NULL DEFAULT '0',
				  `cancelled` TINYINT( 1 ) NOT NULL DEFAULT '0',
				  `published` tinyint(1) NOT NULL DEFAULT '1',
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8 Comment 'Recurring';";

		$db->setQuery($query);
		$db->execute();

		$query = "INSERT INTO `#__matukio_settings` (`title`, `value`, `values`, `type`, `catdisp`) VALUES
					('different_fees_absolute', '0', '', 'bool', 'layout'),
					('recaptcha', '0', '', 'bool', 'security'),
					('recaptcha_private_key', '', '', 'text', 'security'),
					('recaptcha_public_key', '', '', 'text', 'security'),
					('booking_always_active', '0', '', 'bool', 'advanced'),
					('event_default_recurring', '0', '', 'bool', 'defaults'),
					('event_default_recurring_type', 'daily', '', 'text', 'defaults'),
					('event_default_recurring_until', '0000', '', 'text', 'defaults'),
					('event_default_recurring_week_day', '1', '', 'text', 'defaults'),
					('event_default_recurring_count', '0', '', 'text', 'defaults')
					;";

		$db->setQuery($query);
		$db->execute();

		$query = "ALTER TABLE `#__matukio_bookings` ADD `payment_plugin_data` TEXT NOT NULL AFTER `payment_brutto`;";
		$db->setQuery($query);
		$db->execute();

		// Update all old bookings to status 1 (active)
		$query = "UPDATE #__matukio_bookings SET status = 1 WHERE status = 0";
		$db->setQuery($query);
		$db->execute();

		// Create dates for the old events
		$query = "SELECT * FROM #__matukio";
		$db->setQuery($query);
		$events = $db->loadObjectList();

		// Insert old event dates into recurring table
		foreach ($events as $e)
		{
			// Add to dates
			$robj = new stdClass;
			$robj->id = $e->id;
			$robj->event_id = $e->id;
			$robj->semnum = $e->semnum;
			$robj->begin = $e->begin;
			$robj->end = $e->end;
			$robj->booked = $e->booked;
			$robj->hits = $e->hits;
			$robj->grade = $e->grade;
			$robj->cancelled = $e->cancelled;
			$robj->published = $e->published;

			// Ińsert into recurring table
			$result = $db->insertObject("#__matukio_recurring", $robj);

			if (!$result)
			{
				throw new Exception(print_r($db->error_get_last()), 500);
			}
		}
	}

	/**
	 * Updates Database to 3.1.1
	 *
	 * @throws  Exception - if queries fail
	 * @return  void
	 */
	public function update311()
	{
		$db = JFactory::getDbo();

		$query = "INSERT INTO `#__matukio_settings` (`title`, `value`, `values`, `type`, `catdisp`) VALUES
					('sendmail_ticket', '1', '', 'bool', 'payment')
					;";

		$db->setQuery($query);
		$db->execute();
	}


	/**
	 * Updates Database to 4.0.6
	 *
	 * @throws  Exception - if queries fail
	 * @return  void
	 */
	public function update406()
	{
		$db = JFactory::getDbo();

		$query = "INSERT INTO `#__matukio_settings` (`title`, `value`, `values`, `type`, `catdisp`) VALUES
					('sendmail_ticket', '1', '', 'bool', 'basic')
					;";

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates Database to 4.2.0
	 *
	 * @throws  Exception - if queries fail
	 * @return  void
	 */
	public function update420()
	{
		$db = JFactory::getDbo();

		$query = "INSERT INTO `#__matukio_settings` (`title`, `value`, `values`, `type`, `catdisp`) VALUES
			('download_invoice', '1', '', 'bool', 'payment'),
	        ('download_ticket', '1', '', 'bool', 'basic')";

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates Database to 4.2.1
	 *
	 * @throws  Exception - if queries fail
	 * @return  void
	 */
	public function update421()
	{
		$db = JFactory::getDbo();

		$query = "INSERT INTO `#__matukio_settings` (`title`, `value`, `values`, `type`, `catdisp`) VALUES
			('booking_always_inactive', '0', '', 'bool', 'advanced')";

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates Database to 4.3.0
	 *
	 * @throws  Exception - if queries fail
	 * @return  void
	 */
	public function update430()
	{
		// Add field min-people
		$db = JFactory::getDbo();

		$query = "ALTER TABLE `#__matukio` ADD `minpupil` INT(11) NOT NULL DEFAULT '0' AFTER `maxpupil`;";

		$db->setQuery($query);
		$db->execute();

		// Add the two new templates
		$db = JFactory::getDbo();

		$query = <<<'EOT'
		INSERT INTO `#__matukio_templates` (`id`, `tmpl_name`, `category`, `subject`, `value`, `value_text`, `default`, `modified_by`, `published`) VALUES
		(11, 'mail_cron_reminder', 0, 'Reminder free places MAT_EVENT_SEMNUM: MAT_EVENT_TITLE', '<p>##COM_MATUKIO_EMAIL_GREETING##,<br /><br />##COM_MATUKIO_FREE_PLACES_LEFT##<br /><br /> ##COM_MATUKIO_EVENT_DETAILS##:<br />MAT_EVENT_ALL_DETAILS_HTML<br /><br /> MAT_SIGNATURE</p>', '##COM_MATUKIO_EMAIL_GREETING##,\r\n\r\n##COM_MATUKIO_FREE_PLACES_LEFT##\r\n\r\n##COM_MATUKIO_EVENT_DETAILS##:\r\nMAT_EVENT_ALL_DETAILS_TEXT\r\n\r\nMAT_SIGNATURE', '', 0, 1),
		(12, 'mail_newevent', 0, 'New event MAT_EVENT_SEMNUM: MAT_EVENT_TITLE', '<p>##COM_MATUKIO_EMAIL_GREETING##,<br /><br />##COM_MATUKIO_NEW_EVENT_CREATED##<br /><br />##COM_MATUKIO_EVENT_DETAILS##:<br />MAT_EVENT_ALL_DETAILS_HTML<br /><br /> MAT_SIGNATURE</p>', '##COM_MATUKIO_EMAIL_GREETING##,\r\n\r\n##COM_MATUKIO_NEW_EVENT_CREATED##\r\n\r\n##COM_MATUKIO_EVENT_DETAILS##:\r\n\r\nMAT_EVENT_ALL_DETAILS_TEXT\r\n\r\nMAT_SIGNATURE', '', 0, 1);
EOT;

		$db->setQuery($query);
		$db->execute();

		// Add the new settings
		$query = "INSERT INTO `#__matukio_settings` (`title`, `value`, `values`, `type`, `catdisp`) VALUES
				('cron_confirmationcheck', '0', '', 'bool', 'cronjobs'),
				('cron_confirmationcheck_days', '30', '', 'text', 'cronjobs'),
				('cron_freeplaces_reminder', '0', '', 'bool', 'cronjobs'),
				('cron_freeplaces_days', '60', '', 'text', 'cronjobs'),
				('cron_usergroup', '0', '', 'groupselect', 'cronjobs'),
				('cron_invoice_afterevent', '0', '', 'bool', 'cronjobs'),
				('cron_invoice_days', '7', '', 'text', 'cronjobs'),
		        ('sendmail_newevent', '1', '', 'bool', 'advanced'),
		        ('sendmail_newevent_group', '0', '', 'groupselect', 'advanced')
				";

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates Database to 4.4.0
	 *
	 * @throws  Exception - if queries fail
	 * @return  void
	 */
	public function update440()
	{
		$db = JFactory::getDbo();

		// Add new preallocation columns for booking fields
		$query = "ALTER TABLE `#__matukio_booking_fields`
					ADD `datasource` TINYINT(5) NOT NULL DEFAULT '0'  AFTER `type`,
					ADD `datasource_map` VARCHAR(255) NOT NULL AFTER `datasource`;";

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Updates Database to 4.5.0
	 *
	 * @throws  Exception - if queries fail
	 * @return  void
	 */
	public function update450()
	{
		$db = JFactory::getDbo();

		// Add the new settings
		$query = "INSERT INTO `#__matukio_settings` (`title`, `value`, `values`, `type`, `catdisp`) VALUES
				('event_default_minpupil', '0', '', 'text', 'defaults'),
				('checkin_only_organizer', '1', '', 'bool', 'advanced'),
				('participant_grading_system', '0', '', 'bool', 'basic')
				";

		$db->setQuery($query);
		$db->execute();

		$query = "ALTER TABLE `#__matukio_bookings` ADD `mark` TINYINT(2) NOT NULL DEFAULT '0' AFTER `grade`;";

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * method to run before an install/update/discover method
	 *
	 * @param   string  $type    - The type
	 * @param   string  $parent  - The parent
	 *
	 * @return  boolean
	 */
	public function preflight($type, $parent)
	{
		$jversion = new JVersion;

		// Extract the version number from the manifest file
		$this->release = $parent->get("manifest")->version;

		// Find mimimum required joomla version from the manifest file
		$this->minimum_joomla_release = $parent->get("manifest")->attributes()->version;

		if (version_compare($jversion->getShortVersion(), $this->minimum_joomla_release, 'lt'))
		{
			Jerror::raiseWarning(
				null, 'Cannot install com_matukio in a Joomla release prior to '
				. $this->minimum_joomla_release
			);

			return false;
		}

		// Abort if the component being installed is not newer than the currently installed version
		if ($type == 'update')
		{
			$oldRelease = $this->getParam('version');
			$rel = $oldRelease . ' to ' . $this->release;

			if (version_compare($this->release, $oldRelease, 'lt'))
			{
				Jerror::raiseWarning(null, 'Incorrect version sequence. Cannot upgrade ' . $rel);

				return false;
			}
		}
	}

	/**
	 * method to run after an install/update/discover method
	 *
	 * @param   string  $type    - The type
	 * @param   string  $parent  - The parent
	 *
	 * @return void
	 */

	public function postflight($type, $parent)
	{
		$jlang = JFactory::getLanguage();
		$path = $parent->getParent()->getPath('source') . '/administrator';
		$jlang->load('com_matukio.sys', $path, 'en-GB', true);
		$jlang->load('com_matukio.sys', $path, $jlang->getDefault(), true);
		$jlang->load('com_matukio.sys', $path, null, true);

		if ($type == 'install')
		{
			if ($this->newInstall())
			{
				$this->dummyContent();
			}
		}

		// Install the modules
		$this->installModules();
		$this->status->plugins = $this->installPlugins($this->installationQueue['plugins']);
		$this->status->libraries = $this->installLibraries($this->installationQueue['libraries']);

		echo $this->displayInfoInstallation();
	}

	/**
	 * Uninstalls the modules
	 *
	 * @return  void
	 */
	private function uninstallModules()
	{
		if (count($this->installationQueue['modules']))
		{
			$db = JFactory::getDbo();

			foreach ($this->installationQueue['modules'] as $folder => $modules)
			{
				if (count($modules))
				{
					foreach ($modules as $module => $modulePreferences)
					{
						// Find the module ID
						$db->setQuery('SELECT `extension_id` FROM #__extensions WHERE `element` = '
							. $db->Quote($module) . ' AND `type` = "module"');

						$id = $db->loadResult();

						// Uninstall the module
						$installer = new JInstaller;
						$result = $installer->uninstall('module', $id, 1);
						$this->status->modules[] = array('name' => $module, 'client' => $folder, 'result' => $result);
					}
				}
			}
		}
	}

	/**
	 * Uninstalls the plugins
	 *
	 * @param   array  $plugins  - The plugin array
	 *
	 * @return  array
	 */
	public function uninstallPlugins($plugins)
	{
		$db = JFactory::getDbo();
		$status = array();

		foreach ($plugins as $plugin => $published)
		{
			$parts = explode('_', $plugin);
			$pluginType = $parts[1];
			$pluginName = $parts[2];
			$db->setQuery(
				'SELECT `extension_id` FROM #__extensions WHERE `type` = "plugin" AND `element` = '
				. $db->Quote($pluginName) . ' AND `folder` = ' . $db->Quote($pluginType)
			);

			$id = $db->loadResult();

			if ($id)
			{
				$installer = new JInstaller;
				$result = $installer->uninstall('plugin', $id, 1);
				$status[] = array('name' => $plugin, 'group' => $pluginType, 'result' => $result);
				$this->status->plugins[] = array('name' => $plugin, 'group' => $pluginType, 'result' => $result);
			}
		}

		return $status;
	}

	/**
	 * Installs the modules
	 *
	 * @return  void
	 */
	private function installModules()
	{
		$src = $this->parent->getParent()->getPath('source');

		// Modules installation
		if (count($this->installationQueue['modules']))
		{
			foreach ($this->installationQueue['modules'] as $folder => $modules)
			{
				if (count($modules))
				{
					foreach ($modules as $module => $modulePreferences)
					{
						// Install the module
						if (empty($folder))
						{
							$folder = 'site';
						}

						$path = "$src/modules/$module";

						if ($folder == 'admin')
						{
							$path = "$src/administrator/modules/$module";
						}

						if (!is_dir($path))
						{
							continue;
						}

						$db = JFactory::getDbo();

						// Was the module alrady installed?
						$sql = 'SELECT COUNT(*) FROM #__modules WHERE `module`=' . $db->Quote($module);
						$db->setQuery($sql);
						$count = $db->loadResult();
						$installer = new JInstaller;
						$result = $installer->install($path);
						$this->status->modules[] = array('name' => $module, 'client' => $folder, 'result' => $result);

						// Modify where it's published and its published state
						if (!$count)
						{
							list($modulePosition, $modulePublished) = $modulePreferences;
							$sql = "UPDATE #__modules SET position=" . $db->Quote($modulePosition);

							if ($modulePublished)
							{
								$sql .= ', published=1';
							}

							$sql .= ', params = ' . $db->quote($installer->getParams());
							$sql .= ' WHERE `module`=' . $db->Quote($module);
							$db->setQuery($sql);
							$db->execute();

							// Get module id
							$db->setQuery('SELECT id FROM #__modules WHERE module = ' . $db->quote($module));
							$moduleId = $db->loadObject()->id;

							// Insert the module on all pages, otherwise we can't use it
							$query = 'INSERT INTO #__modules_menu(moduleid, menuid) VALUES (' . $db->quote($moduleId) . ' ,0 );';
							$db->setQuery($query);

							$db->execute();
						}
					}
				}
			}
		}
	}

	/**
	 * Installs the plugins
	 *
	 * @param   array  $plugins  - The plugin array
	 *
	 * @return  array
	 */
	public function installPlugins($plugins)
	{
		$src = $this->parent->getParent()->getPath('source');

		$db = JFactory::getDbo();
		$status = array();

		foreach ($plugins as $plugin => $published)
		{
			$parts = explode('_', $plugin);
			$pluginType = $parts[1];
			$pluginName = $parts[2];

			$path = $src . "/plugins/$pluginType/$pluginName";

			$query = "SELECT COUNT(*) FROM  #__extensions WHERE element=" . $db->Quote($pluginName) . " AND folder=" . $db->Quote($pluginType);

			$db->setQuery($query);
			$count = $db->loadResult();

			$installer = new JInstaller;
			$result = $installer->install($path);
			$status[] = array('name' => $plugin, 'group' => $pluginType, 'result' => $result);

			if ($published && !$count)
			{
				$query = "UPDATE #__extensions SET enabled=1 WHERE element=" . $db->Quote($pluginName) . " AND folder=" . $db->Quote($pluginType);
				$db->setQuery($query);
				$db->execute();
			}
		}

		return $status;
	}


	/**
	 * Install libraries
	 *
	 * @param   array  $libraries  - libraries to install
	 *
	 * @return array
	 */
	public function installLibraries($libraries)
	{
		$src = $this->parent->getParent()->getPath('source');

		$db = JFactory::getDbo();
		$status = array();

		foreach ($libraries as $library => $published)
		{
			$path = $src . "/libraries/$library";

			$query = $db->getQuery(true);
			$query->select('*')
				->from('#__extensions')
				->where($db->qn('element') . '=' . $db->q($library))
				->where($db->qn('type') . '=' . $db->q('library'));

			$db->setQuery($query);
			$object = $db->loadObject();

			$installer = new JInstaller;

			// If we don't have an object, let us install the library
			if (!$object)
			{
				$result = $installer->install($path);
				$status[] = array('name' => $library, 'result' => $result);
			}
			else
			{
				$manifest = simplexml_load_file($path . '/' . $library . '.xml');
				$manifestCache = json_decode($object->manifest_cache);

				if (version_compare($manifest->version, $manifestCache->version, '>='))
				{
					// Okay, the library with the extension is newer, we need to install it
					$result = $installer->install($path);
					$status[] = array('name' => $library, 'result' => $result);
				}
				else
				{
					$status[] = array('name' => $library, 'result' => false,
						'message' => 'No need to install the library. You are already running a newer version of the library: ' . $manifestCache->version);
				}
			}
		}

		return $status;
	}

	/**
	 * Insert the templates Data into DB
	 *
	 * @param   bool  $install  - Is the function called during the installation (default true)
	 *
	 * @return  mixed|void
	 */
	public function templatesContent($install = true)
	{
		$db = JFactory::getDbo();

		$query = <<<'EOT'
		INSERT INTO `#__matukio_templates` (`id`, `tmpl_name`, `category`, `subject`, `value`, `value_text`, `default`, `modified_by`, `published`) VALUES
		(1, 'mail_booking', 0, '##COM_MATUKIO_EVENT## MAT_EVENT_SEMNUM: MAT_EVENT_TITLE', '<p>##COM_MATUKIO_EMAIL_GREETING## MAT_BOOKING_NAME,<br /><br />##COM_MATUKIO_THANK_YOU_FOR_YOUR_BOOKING##<br /><br /> ##COM_MATUKIO_BOOKING_DETAILS##:<br />MAT_BOOKING_ALL_DETAILS_HTML<br /><br /> ##COM_MATUKIO_EVENT_DETAILS##:<br />MAT_EVENT_ALL_DETAILS_HTML<br /><br />MAT_BOOKING_DETAILPAGE<br /><br /> MAT_SIGNATURE</p>', '##COM_MATUKIO_EMAIL_GREETING## MAT_BOOKING_NAME,\r\n\r\n##COM_MATUKIO_THANK_YOU_FOR_YOUR_BOOKING##\r\n\r\n##COM_MATUKIO_BOOKING_DETAILS##:\r\n\r\nMAT_BOOKING_ALL_DETAILS_TEXT\r\n\r\n##COM_MATUKIO_EVENT_DETAILS##:\r\n\r\nMAT_EVENT_ALL_DETAILS_TEXT\r\n\r\nMAT_SIGNATURE\r\n ', '', 0, 1),
		(2, 'mail_booking_canceled_admin', 0, '##COM_MATUKIO_BOOKING_CANCELED## MAT_EVENT_SEMNUM: MAT_EVENT_TITLE (MAT_BOOKING_NUMBER)', '<p>##COM_MATUKIO_EMAIL_GREETING## MAT_BOOKING_NAME,<br /><br />##COM_MATUKIO_THE_ADMIN_CANCELED_THE_BOOKING_OF_FOLLOWING##<br /> <br /> MAT_BOOKING_ALL_DETAILS_HTML <br /> MAT_SIGNATURE</p>', '##COM_MATUKIO_EMAIL_GREETING## MAT_BOOKING_NAME,\r\n\r\n##COM_MATUKIO_THE_ADMIN_CANCELED_THE_BOOKING_OF_FOLLOWING##\r\n\r\nMAT_BOOKING_ALL_DETAILS_TEXT\r\n\r\nMAT_SIGNATURE', '', 0, 1),
		(3, 'mail_booking_canceled', 0, '##COM_MATUKIO_BOOKING_CANCELED## MAT_EVENT_SEMNUM: MAT_EVENT_TITLE (MAT_BOOKING_NUMBER)', '<p>##COM_MATUKIO_EMAIL_GREETING## MAT_BOOKING_NAME,<br /><br />##COM_MATUKIO_YOU_HAVE_CANCELLED## ##COM_MATUKIO_BOOKING_FOR_EVENT_CANCELLED##<br /> <br /> MAT_BOOKING_ALL_DETAILS_HTML <br /><br /> MAT_SIGNATURE</p>', '##COM_MATUKIO_EMAIL_GREETING## MAT_BOOKING_NAME,\r\n\r\n##COM_MATUKIO_YOU_HAVE_CANCELLED## ##COM_MATUKIO_BOOKING_FOR_EVENT_CANCELLED##\r\n\r\nMAT_BOOKING_ALL_DETAILS_HTML\r\n\r\nMAT_SIGNATURE', '', 0, 1),
		(4, 'export_csv', 1, 'ID', '''MAT_BOOKING_NUMBER'';''MAT_EVENT_TITLE'';MAT_CSV_BOOKING_DETAILS', '', '', 0, 1),
		(5, 'export_signaturelist', 1, '##COM_MATUKIO_SIGNATURE_LIST##', '<p>MAT_NR MAT_BOOKING_NUMBER MAT_BOOKING_FIRSTNAME MAT_BOOKING_LASTNAME MAT_SIGN</p>', '<table class="mat_table table" style="width: 100%;" border="0">\r\n<tbody>\r\n<tr>\r\n<td class="key" width="150px"><strong>##COM_MATUKIO_NR##:</strong></td>\r\n<td>MAT_EVENT_NUMBER</td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_EVENT##:</strong></td>\r\n<td>MAT_EVENT_TITLE</td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_BEGIN##:</strong></td>\r\n<td>MAT_EVENT_BEGIN</td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_END##:</strong></td>\r\n<td>MAT_EVENT_END</td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_FEES##:</strong></td>\r\n<td>MAT_EVENT_FEES</td>\r\n</tr>\r\n</tbody>\r\n</table>', '', 0, 1),
		(6, 'export_participantslist', 1, '##COM_MATUKIO_PARTICIPANTS_LIST##', '<table class="mat_table table" style="width: 100%;" border="0">\r\n<tbody>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_NAME##:</strong></td>\r\n<td>MAT_BOOKING_NAME</td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_EMAIL##:</strong></td>\r\n<td>MAT_BOOKING_EMAIL </td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_BOOKING_NUMBER##:</strong></td>\r\n<td>MAT_BOOKING_NUMBER </td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_STATUS##:</strong></td>\r\n<td>MAT_BOOKING_STATUS </td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_BOOKEDNR##:</strong></td>\r\n<td>MAT_BOOKING_BOOKEDNR</td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_PAYMENT_FEES##:</strong></td>\r\n<td>MAT_BOOKING_FEES_STATUS</td>\r\n</tr>\r\n<tr>\r\n<td style="text-align: center;" colspan="2">MAT_BOOKING_QRCODE_ID<em><br /></em></td>\r\n</tr>\r\n</tbody>\r\n</table>', '<table class="mat_table table" style="width: 100%;" border="0">\r\n<tbody>\r\n<tr>\r\n<td class="key" width="150px"><strong>##COM_MATUKIO_NR##:</strong></td>\r\n<td>MAT_EVENT_NUMBER</td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_FIELDS_TITLE##:</strong></td>\r\n<td>MAT_EVENT_TITLE</td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_BEGIN##:</strong></td>\r\n<td>MAT_EVENT_BEGIN</td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_END##:</strong></td>\r\n<td>MAT_EVENT_END</td>\r\n</tr>\r\n<tr>\r\n<td width="150px"><strong>##COM_MATUKIO_FEES##:</strong></td>\r\n<td>MAT_EVENT_FEES</td>\r\n</tr>\r\n</tbody>\r\n</table>', '', 0, 1),
		(7, 'export_certificate', 2, 'E', '<div style="position: absolute; top: 0; left: 0; z-index: 0;"><img src="MAT_IMAGEDIRcertificate.png" alt="" border="0" /></div>\r\n<div style="position: absolute; top: 0; left: 0; z-index: 1;">\r\n<table style="width: 734pt;" border="0" cellspacing="0" cellpadding="0">\r\n<tbody>\r\n<tr>\r\n<td rowspan="8" width="180pt" height="1080pt"> </td>\r\n<th width="554pt" height="150pt"><span style="color: #330099; font-size: 48pt; font-family: Verdana;">##COM_MATUKIO_CERTIFICATE##</span></th></tr>\r\n<tr><th width="554pt" height="150pt"><span style="color: #000000; font-size: 28pt; font-family: Verdana;">MAT_BOOKING_NAME</span></th></tr>\r\n<tr>\r\n<td width="554pt" height="100pt"><span style="color: #000000; font-size: 24pt; font-family: Verdana;">##COM_MATUKIO_CERTIFICATE_ATTENDED##</span></td>\r\n</tr>\r\n<tr><th width="554pt" height="250pt"><span style="color: #000000; font-size: 28pt; font-family: Verdana;">MAT_EVENT_TITLE</span></th></tr>\r\n<tr>\r\n<td width="554pt" height="230pt"><span style="color: #000000; font-size: 18pt; font-family: Verdana;">##COM_MATUKIO_BEGIN##: MAT_EVENT_BEGIN</span>\r\n<p style="margin-top: 20pt; margin-bottom: 8pt;"><span style="color: #000000; font-size: 18pt; font-family: Verdana;">##COM_MATUKIO_END##: MAT_EVENT_END</span></p>\r\n<p style="margin-top: 20pt; margin-bottom: 8pt;"><span style="color: #000000; font-size: 18pt; font-family: Verdana;">##COM_MATUKIO_CITY##: MAT_EVENT_LOCATION</span></p>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td width="554pt" height="100pt"><span style="color: #000000; font-size: 18pt; font-family: Verdana;">##COM_MATUKIO_TUTOR##: MAT_EVENT_TEACHER</span></td>\r\n</tr>\r\n<tr>\r\n<td width="554pt" height="100pt"><span style="color: #000000; font-size: 18pt; font-family: Verdana;">##COM_MATUKIO_DATE##: MAT_DATE</span></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>', '', '', 0, 1),
		(8, 'invoice', 3, 'E', '<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td align="left" width="100%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td width="100%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td align="left" valign="top" width="50%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td align="left">Your Company</td>\r\n</tr>\r\n<tr>\r\n<td align="left">Your Company Address</td>\r\n</tr>\r\n<tr>\r\n<td align="left">Your Tax Number</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td align="center" valign="middle" width="50%">Your Logo</td>\r\n</tr>\r\n<tr>\r\n<td colspan="2" align="left" width="100%"><br /><br />\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td align="left" valign="top" width="50%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td style="background-color: #d6d6d6;" align="left">\r\n<h4 style="margin: 0px;">Customer Information</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td align="left">MAT_BOOKING_NAME</td>\r\n</tr>\r\n<tr>\r\n<td align="left">MAT_BOOKING_STREET</td>\r\n</tr>\r\n<tr>\r\n<td align="left">MAT_BOOKING_ZIP MAT_BOOKING_CITY</td>\r\n</tr>\r\n<tr>\r\n<td align="left">MAT_BOOKING_COUNTRY</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td align="left" valign="top" width="50%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td style="background-color: #d6d6d6;" colspan="2" align="left">\r\n<h4 style="margin: 0px;">Invoice Information</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td align="left" width="50%">Invoice Number:</td>\r\n<td align="left">MAT_INVOICE_NUMBER</td>\r\n</tr>\r\n<tr>\r\n<td align="left" width="50%">Invoice Date:</td>\r\n<td align="left">MAT_INVOICE_DATE</td>\r\n</tr>\r\n<tr>\r\n<td align="left" width="50%">Booking Number:</td>\r\n<td align="left">MAT_BOOKING_NUMBER</td>\r\n</tr>\r\n<tr>\r\n<td align="left" width="50%">Payment method:</td>\r\n<td align="left">MAT_BOOKING_PAYMENT_METHOD</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<br /><br /></td>\r\n</tr>\r\n<tr>\r\n<td style="background-color: #d6d6d6;" colspan="2" align="left">\r\n<h4 style="margin: 0px;">Order Items</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td colspan="2" align="left" width="100%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td align="left" valign="top" width="10%">#</td>\r\n<td align="left" valign="top" width="50%">Event</td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n</tr>\r\n<tr>\r\n<td align="left" valign="top" width="10%">MAT_BOOKING_NRBOOKED</td>\r\n<td align="left" valign="top" width="50%">MAT_EVENT_TITLE </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n<td align="left" valign="top" width="10%">MAT_BOOKING_PAYMENT_NETTO</td>\r\n</tr>\r\n<tr>\r\n<td colspan="5" align="right" valign="top" width="90%"> </td>\r\n<td align="left" valign="top" width="10%"> </td>\r\n</tr>\r\n<tr>\r\n<td colspan="5" align="right" valign="top" width="90%">Net total:</td>\r\n<td align="left" valign="top" width="10%">MAT_BOOKING_PAYMENT_NETTO</td>\r\n</tr>\r\n<tr>\r\n<td colspan="5" align="right" valign="top" width="90%">Tax total:</td>\r\n<td align="left" valign="top" width="10%">MAT_BOOKING_PAYMENT_TAX</td>\r\n</tr>\r\n<tr>\r\n<td colspan="5" align="right" valign="top" width="90%">Total:</td>\r\n<td align="left" valign="top" width="10%">MAT_BOOKING_PAYMENT_BRUTTO</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style="background-color: #d6d6d6;" colspan="2" align="left">\r\n<h4 style="margin: 0px;">Invoice Note</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td colspan="2" align="left" width="100%">\r\n<table style="width: 100%;" border="0" cellspacing="0" cellpadding="2">\r\n<tbody>\r\n<tr>\r\n<td> Your notes</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p> </p>', '', '', 0, 1),
		(9, 'invoice_email', 3, '##COM_MATUKIO_INVOICE_SUBJECT## MAT_BOOKING_NUMBER', '<div id="mat-invoice-mail">\r\n<p><span style="line-height: 1.3em;">##COM_MATUKIO_EMAIL_GREETING## MAT_BOOKING_NAME,<br /><br /></span>##COM_MATUKIO_EMAIL_INVOICE_ATTACHED##</p>\r\n<p>MAT_SIGNATURE</p>\r\n</div>', '', '', 0, 1),
		(10, 'ticket', 4, 'E', '<table style="width: 100%;">\r\n<tbody>\r\n<tr>\r\n<td>MAT_EVENT_TITLE - MAT_EVENT_BEGIN</td>\r\n</tr>\r\n<tr>\r\n<td>MAT_BOOKING_NRBOOKED </td>\r\n</tr>\r\n<tr>\r\n<td>MAT_BOOKING_NUMBER</td>\r\n</tr>\r\n<tr>\r\n<td><span style="font-family: Tahoma, Helvetica, Arial, sans-serif; font-size: 12px;"></span></td>\r\n</tr>\r\n<tr>\r\n<td>MAT_BOOKING_PAYMENT_BRUTTO</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p><br /><br /><br /><br /></p>', '', '', 0, 1),
		(11, 'mail_cron_reminder', 0, 'Reminder free places MAT_EVENT_SEMNUM: MAT_EVENT_TITLE', '<p>##COM_MATUKIO_EMAIL_GREETING##,<br /><br />##COM_MATUKIO_FREE_PLACES_LEFT##<br /><br /> ##COM_MATUKIO_EVENT_DETAILS##:<br />MAT_EVENT_ALL_DETAILS_HTML<br /><br /> MAT_SIGNATURE</p>', '##COM_MATUKIO_EMAIL_GREETING##,\r\n\r\n##COM_MATUKIO_FREE_PLACES_LEFT##\r\n\r\n##COM_MATUKIO_EVENT_DETAILS##:\r\nMAT_EVENT_ALL_DETAILS_TEXT\r\n\r\nMAT_SIGNATURE', '', 0, 1),
		(12, 'mail_newevent', 0, 'New event MAT_EVENT_SEMNUM: MAT_EVENT_TITLE', '<p>##COM_MATUKIO_EMAIL_GREETING##,<br /><br />##COM_MATUKIO_NEW_EVENT_CREATED##<br /><br />##COM_MATUKIO_EVENT_DETAILS##:<br />MAT_EVENT_ALL_DETAILS_HTML<br /><br /> MAT_SIGNATURE</p>', '##COM_MATUKIO_EMAIL_GREETING##,\r\n\r\n##COM_MATUKIO_NEW_EVENT_CREATED##\r\n\r\n##COM_MATUKIO_EVENT_DETAILS##:\r\n\r\nMAT_EVENT_ALL_DETAILS_TEXT\r\n\r\nMAT_SIGNATURE', '', 0, 1);
EOT;

		$db->setQuery($query);
		$status = $db->execute();

		if ($install)
		{
			$this->status->sql['#__matukio_templates'] = $status;
		}
		else
		{
			return $status;
		}
	}

	/**
	 * Inserts the bookingfields content
	 *
	 * @param   bool  $install  - Are we in the installer?
	 *
	 * @return  mixed
	 */
	public function bookingfieldsContent($install = true)
	{
		$db = JFactory::getDbo();

		$query = "INSERT INTO `#__matukio_booking_fields` (`id`, `field_name`, `label`, `default`, `values`, `page`, `type`, `datasource`, `datasource_map`, `required`, `ordering`, `style`, `published`) VALUES
			(1, 'introtext', 'COM_MATUKIO_BOOKING_INTRO', NULL, NULL, 1, 'spacertext', 0, '', 0, 0, NULL, 1),
			(2, 'title', 'COM_MATUKIO_FIELDS_TITLE', 'choose', '{=COM_MATUKIO_FIELD_CHOOSE}{Mr=COM_MATUKIO_FIELD_MR}{Ms=COM_MATUKIO_FIELD_MS}', 1, 'select', 0, '', 1, 1, NULL, 1),
			(3, 'company', 'COM_MATUKIO_FIELDS_COMPANY', NULL, NULL, 1, 'text', 0, '', 0, 2, NULL, 1),
			(4, 'firstname', 'COM_MATUKIO_FIELDS_FIRST_NAME', NULL, NULL, 1, 'text', 0, '', 1, 3, NULL, 1),
			(5, 'lastname', 'COM_MATUKIO_FIELDS_SURNAME', NULL, NULL, 1, 'text', 0, '', 1, 4, NULL, 1),
			(6, 'spacer', '', NULL, NULL, 1, 'spacer', 0, '', 0, 5, NULL, 1),
			(7, 'street', 'COM_MATUKIO_FIELDS_STREET', '', '', 1, 'text', 1, 'address1', 1, 6, '', 1),
			(8, 'zip', 'COM_MATUKIO_FIELDS_ZIP', NULL, NULL, 1, 'text', 0, 'postal_code', 1, 7, 'width: 80px;', 1),
			(9, 'city', 'COM_MATUKIO_FIELDS_CITY', '', '', 1, 'text', 0, 'city', 1, 8, '', 1),
			(10, 'country', 'COM_MATUKIO_FIELDS_COUNTRY', '', '', 1, 'text', 1, 'country', 1, 9, '', 1),
			(11, 'spacer', '', NULL, NULL, 1, 'spacer', 0, '', 0, 10, NULL, 1),
			(12, 'email', 'COM_MATUKIO_FIELDS_EMAIL', '', '', 1, 'text', 1, 'email', 1, 11, '', 1),
			(13, 'phone', 'COM_MATUKIO_FIELDS_PHONE', '', '', 1, 'text', 1, 'phone', 0, 12, '', 1),
			(14, 'mobile', 'COM_MATUKIO_FIELDS_MOBILE', NULL, NULL, 1, 'text', 0, '', 0, 13, NULL, 1),
			(15, 'fax', 'COM_MATUKIO_FIELDS_FAX', NULL, NULL, 1, 'text', 0, '', 0, 14, NULL, 1),
			(16, 'comments', 'COM_MATUKIO_FIELDS_COMMENTS', '', '', 2, 'textarea', 0, '', 0, 16, '', 1);";

		$db->setQuery($query);
		$status = $db->execute();

		if ($install)
		{
			$this->status->sql['#__matukio_booking_fields'] = $status;
		}
		else
		{
			return $status;
		}
	}

	/**
	 * Inserts the settings Data into DB
	 *
	 * @param   bool  $install  - Is the function called during the installation (default true)
	 *
	 * @return mixed|null|void
	 */
	public function settingsContent($install = true)
	{
		$db = JFactory::getDbo();

		$query = "INSERT INTO `#__matukio_settings` (`id`, `title`, `value`, `values`, `type`, `catdisp`) VALUES
        (1, 'booking_unregistered', '1', '', 'bool', 'basic'),
        (2, 'booking_ownevents', '1', '', 'bool', 'advanced'),
        (4, 'booking_stornoconfirmation', '1', '', 'bool', 'basic'),
        (5, 'frontend_userprintlists', '1', '', 'bool', 'basic'),
        (6, 'event_template', 'modern', '{default=DEFAULT}{modern=MODERN}', 'select', 'layout'),
        (8, 'frontend_usericsdownload', '1', '', 'bool', 'basic'),
        (9, 'frontend_userviewteilnehmer', '0', '{0=NONE}{1=UNREGISTERED}{2=REGISTERED}', 'select', 'basic'),
        (10, 'frontend_teilnehmerviewteilnehmer', '0', '', 'bool', 'basic'),
        (11, 'frontend_teilnehmernametyp', '1', '{0=USERNAME}{1=REALNAME}', 'select', 'basic'),
        (12, 'frontend_ownereditevent', '1', '', 'bool', 'advanced'),
        (13, 'frontend_ratingsystem', '0', '', 'bool', 'basic'),
        (14, 'frontend_certificatesystem', '0', '', 'bool', 'basic'),
        (15, 'frontend_userprintcertificate', '0', '', 'bool', 'advanced'),
        (17, 'sendmail_teilnehmer', '1', '', 'bool', 'basic'),
        (18, 'sendmail_owner', '1', '', 'bool', 'basic'),
        (19, 'sendmail_contact', '1', '', 'bool', 'basic'),
        (21, 'googlemap_booble', '1', '', 'bool', 'layout'),
        (22, 'event_image', '1', '', 'bool', 'layout'),
        (23, 'image_path', 'matukio', '', 'text', 'advanced'),
        (24, 'event_showstatuspictures', '1', '', 'bool', 'layout'),
        (25, 'file_maxsize', '500', '', 'text', 'security'),
        (26, 'file_endings', 'txt zip pdf jpg png gif', '', 'text', 'security'),
        (27, 'event_showinfoline', '1', '', 'bool', 'layout'),
        (28, 'event_statusgraphic', '1', '{0=NONE}{1=AMPEL}{2=SAEULE}', 'select', 'layout'),
        (29, 'event_buttonposition', '1', '{0=TOP}{1=BOTTOM}{2=BOTH}', 'select', 'layout'),
        (30, 'currency_symbol', '$', '', 'text', 'payment'),
        (31, 'dezimal_stellen', '2', '{0=NONE}{1=ONE}{2=TWO}', 'select', 'layout'),
        (32, 'dezimal_trennzeichen', '.', '', 'text', 'layout'),
        (33, 'frontend_usermehrereplaetze', '1', '', 'bool', 'basic'),
        (34, 'booking_edit', '1', '', 'bool', 'basic'),
        (35, 'booking_stornotage', '1', '', 'text', 'advanced'),
        (36, 'event_stopshowing', '0', '{0=START}{1=END}{2=ANMELDESCHLUSS}{3=NEVER}', 'select', 'advanced'),
        (37, 'event_showanzahl', '20', '', 'text', 'layout'),
        (38, 'agb_text', '', '', 'textarea', 'basic'),
        (39, 'frontend_showfooter', '1', '', 'bool', 'layout'),
        (40, 'rss_feed', '1', '', 'bool', 'advanced'),
        (42, 'csv_export_charset', 'UTF-8', '', 'text', 'advanced'),
        (43, 'frontend_showownerdetails', '1', '', 'bool', 'basic'),
        (44, 'date_format_small', 'd-m-Y, H:i', '', 'text', 'layout'),
        (45, 'date_format_without_time', 'd-m-Y', '', 'text', 'layout'),
        (46, 'time_format', 'H:i', '', 'text', 'layout'),
        (47, 'date_format', 'l, d. F Y - H:i', '', 'text', 'layout'),
        (48, 'db_version', '" . $this->matVersion . "', '', 'text', 'hidden'),
        (49, 'oldbookingform', '0', '', 'bool', 'basic'),
        (56, 'payment_coupon', '1', '', 'bool', 'payment'),
        (57, 'banktransfer_account', '', '', 'text', 'payment'),
        (58, 'banktransfer_blz', '', '', 'text', 'payment'),
        (59, 'banktransfer_bank', '', '', 'text', 'payment'),
        (60, 'banktransfer_accountholder', '', '', 'text', 'payment'),
        (61, 'paypal_currency', 'USD', '', 'text', 'payment'),
        (65, 'captcha', '0', '', 'bool', 'security'),
        (66, 'banktransfer_iban', '', '', 'text', 'payment'),
        (67, 'banktransfer_bic', '', '', 'text', 'payment'),
        (68, 'frontend_unregisteredshowlogin', '1', '', 'bool', 'layout'),
        (69, 'social_media', '1', '', 'bool', 'modernlayout'),
        (70, 'oldbooking_redirect_after', 'bookingpage', '{bookingpage=BOOKINGPAGE}{eventpage=EVENTPAGE}{eventlist=EVENTLIST}', 'select', 'advanced'),
        (71, 'frontend_topnavshowmodules', 'SEM_NUMBER SEM_SEARCH SEM_CATEGORIES SEM_RESET', '', 'text', 'advanced'),
        (72, 'frontend_topnavbookingmodules', 'SEM_NUMBER SEM_SEARCH SEM_TYPES SEM_RESET', '', 'text', 'advanced'),
        (73, 'frontend_topnavoffermodules', 'SEM_NUMBER SEM_SEARCH SEM_TYPES SEM_RESET', '', 'text', 'advanced'),
        (74, 'mat_signature', '<strong>Please do not answer this E-Mail</strong>', '', 'text', 'layout'),
        (75, 'email_html', '1', '', 'bool', 'layout'),
        (76, 'export_csv_separator',  ';',  '',  'text',  'advanced'),
        (77, 'location_image', '1', '', 'bool', 'modernlayout'),
        (78, 'bookingfield_desc', '0', '', 'bool', 'advanced'),
        (79, 'navi_eventlist_number', '1', '', 'bool', 'modernlayout'),
        (80, 'navi_eventlist_search', '1', '', 'bool', 'modernlayout'),
        (81, 'navi_eventlist_categories', '1', '', 'bool', 'modernlayout'),
        (82, 'navi_eventlist_types', '1', '', 'bool', 'modernlayout'),
        (83, 'navi_eventlist_reset', '1', '', 'bool', 'modernlayout'),
        (84, 'contact_organizer', '1', '', 'bool', 'advanced'),
        (85, 'event_default_begin', '14:00:00', '', 'text', 'defaults'),
        (86, 'event_default_end', '17:00:00', '', 'text', 'defaults'),
        (87, 'event_default_booked', '12:00:00', '', 'text', 'defaults'),
        (88, 'event_default_title', '', '', 'text', 'defaults'),
        (89, 'event_default_category', '', '', 'text', 'defaults'),
        (90, 'event_default_short_description', '', '', 'text', 'defaults'),
        (91, 'event_default_place', '', '', 'text', 'defaults'),
        (92, 'event_default_webinar', '0', '', 'bool', 'defaults'),
        (93, 'event_default_maxpupil', '', '', 'text', 'defaults'),
        (94, 'event_default_nrbooked', '1', '', 'text', 'defaults'),
        (95, 'event_default_description', '', '', 'textarea', 'defaults'),
        (96, 'event_default_map_location', '', '', 'text', 'defaults'),
        (97, 'event_default_teacher', '', '', 'text', 'defaults'),
        (98, 'event_default_target', '', '', 'text', 'defaults'),
        (99, 'event_default_fees', '0', '', 'text', 'defaults'),
        (100, 'notify_participants_publish', '1', '', 'bool', 'advanced'),
        (101, 'notify_participants_cancel', '1', '', 'bool', 'advanced'),
        (102, 'notify_participants_delete', '1', '', 'bool', 'advanced'),
        (103, 'frontend_organizer_allevent', '0', '', 'bool', 'advanced'),
        (104, 'modern_eventlist_show_fee', '1', '', 'bool', 'modernlayout'),
        (105, 'sendmail_operator', '', '', 'text', 'advanced'),
        (106, 'event_default_different_fees', '0', '', 'bool', 'defaults'),
        (107, 'show_timezone', '0', '', 'bool', 'layout'),
        (108, 'show_different_fees', '1', '', 'bool', 'layout'),
        (109, 'different_fees_absolute', '0', '', 'bool', 'layout'),
        (110, 'recaptcha', '0', '', 'bool', 'security'),
        (111, 'recaptcha_private_key', '', '', 'text', 'security'),
        (112, 'recaptcha_public_key', '', '', 'text', 'security'),
        (113, 'booking_always_active', '0', '', 'bool', 'advanced'),
        (114, 'event_default_recurring', '0', '', 'bool', 'defaults'),
        (115, 'event_default_recurring_type', 'daily', '', 'text', 'defaults'),
        (116, 'event_default_recurring_until', '0000-00-00', '', 'text', 'defaults'),
        (117, 'event_default_recurring_week_day', '1', '', 'text', 'defaults'),
        (118, 'event_default_recurring_count', '0', '', 'text', 'defaults'),
        (119, 'sendmail_invoice', '1', '', 'bool', 'payment'),
        (120, 'sendmail_ticket', '1', '', 'bool', 'basic'),
        (121, 'download_invoice', '1', '', 'bool', 'payment'),
        (122, 'download_ticket', '1', '', 'bool', 'basic'),
        (123, 'booking_always_inactive', '0', '', 'bool', 'advanced'),
        (124, 'cron_freeplaces_reminder', '0', '', 'bool', 'cronjobs'),
        (125, 'cron_freeplaces_days', '60', '', 'text', 'cronjobs'),
        (126, 'cron_confirmationcheck', '0', '', 'bool', 'cronjobs'),
        (127, 'cron_confirmationcheck_days', '30', '', 'text', 'cronjobs'),
        (128, 'cron_invoice_afterevent', '0', '', 'bool', 'cronjobs'),
        (129, 'cron_invoice_days', '7', '', 'text', 'cronjobs'),
        (130, 'cron_usergroup', '0', '', 'groupselect', 'cronjobs'),
        (131, 'sendmail_newevent', '1', '', 'bool', 'advanced'),
        (132, 'sendmail_newevent_group', '0', '', 'groupselect', 'advanced'),
        (133, 'event_default_minpupil', '0', '', 'text', 'defaults'),
        (134, 'checkin_only_organizer', '1', '', 'bool', 'advanced'),
		(135, 'participant_grading_system', '0', '', 'bool', 'basic')
        ;";

		$db->setQuery($query);
		$status = $db->execute();

		if ($install)
		{
			$this->status->sql['#__matukio_settings'] = $status;
		}
		else
		{
			return $status;
		}
	}

	/**
	 * Inserts the dummy content (booking fields, templates etc.)
	 *
	 * @return  void
	 */
	private function dummyContent()
	{
		$this->settingsContent();
		$this->templatesContent();
		$this->bookingfieldsContent();
	}

	/**
	 * Displays the uninstall informations
	 *
	 * @return string
	 */
	private function displayInfoUninstallation()
	{
		$html[] = JText::_('COM_MATUKIO_COMPLETE_UNINSTALL') . "<br /><br />";

		$html[] = $this->renderModuleInfoUninstall($this->status->modules);

		if ($this->status->plugins)
		{
			$html[] = $this->renderPluginInfoUninstall($this->status->plugins);
		}

		return implode('', $html);
	}

	/**
	 * Display the Installation Info
	 *
	 * @return  string
	 */
	private function displayInfoInstallation()
	{
		if (file_exists(JPATH_ADMINISTRATOR . "/components/com_joomfish/config.joomfish.php"))
		{
			rename(JPATH_ADMINISTRATOR . "/components/com_matukio/joomfish/jf_matukio.xml", JPATH_ADMINISTRATOR . "/components/com_joomfish/contentelements/matukio.xml");
		}

		$update = $this->matVersion;

		$imagedir = "../media/com_matukio/images/";
		$lang = JFactory::getLanguage();

		$sprache = strtolower(substr($lang->getName(), 0, 2));

		$html[] = "<div class=\"row-fluid\">";
		$html[] = "<div class=\"span9\">";
		$html[] = "<div style=\"float: left;\"><table class=\"table\" border=\"0\" style=\"width: 100%\"><tbody>";
		$html[] = "<tr><td width=\"18%\"><b>Extension:</b></td><td width=\"80%\">Matukio " . $update . "</td></tr>";
		$html[] = "<tr><td width=\"18%\"><b>Copyright:</b></td><td width=\"80%\">Compojoom.com - Daniel Dimitrov &amp; Yves Hoppe</td></tr>";
		$html[] = "<tr><td width=\"18%\"><b>Web:</b></td><td width=\"80%\"><a target=\"_blank\" href=\"http://compojoom.com\">http://compojoom.com</a></td></tr>";
		$html[] = "<tr><td width=\"18%\"><b>Version:</b></td><td width=\"80%\">" . $update . "</td></tr>";

		$newinstall = ($this->newInstall()) ? "No" : "Yes";

		$html[] = "<tr><td width=\"18%\"><b>Update:</b></td><td width=\"80%\">" . $newinstall . "</td></tr>";

		switch ($sprache)
		{
			case "ge":
				$html[] = "<tr><td colspan=\"2\">";
				$html[] = "<br /><strong>Vielen Dank f&uuml;r die Installation von Matukio!</strong><br /><br />";

				$html[] .= '<p>F&uuml;r die neusten Nachrichten und Aktionen:<br />
							Facebook: <iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Fcompojoom&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=true&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=119257468194823"
							scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:21px;" allowTransparency="true"></iframe>
							<br />Folgen Sie uns auf Twitter: <a href="https://twitter.com/compojoom" class="twitter-follow-button" data-show-count="false">Follow @compojoom</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";
							fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script><br /><br />';
				$html[] = "<strong>Kurzanleitung:</strong><br /><br />
				Bitte erstellen Sie zu erst eine Veranstaltungskategorie und passen Sie Matukio nach Ihren W&uuml;nschen in der Konfiguration an.
				Matukio ben&ouml;tigt einen Joomla Men&uuml;eintrag (kann versteckt sein) zur Veranstaltungs&uuml;bersicht um mit suchmaschinenfreundlichen Adressen zu funktionieren!
				<br /><br />Weitere Informationen finden Sie auch in der <a href=\"https://compojoom.com/support-2/documentation/matukio\">Dokumentation</a> und
				auf unserem <a href=\"http://youtube.com/user/compojoom\" target=\"_blank\">Youtube-Channel</a>!<br /></p>";
				$html[] = "</td>";
				break;

			default:
				$html[] = "<tr><td colspan=\"2\">";
				$html[] = "<br /><strong>Thank you for installing Matukio!</strong><br /><br />";

				$html[] .= '<p>To get the latest news and promotions:<br />
							Like us on Facebook: <iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ffacebook.com%2Fcompojoom&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=true&amp;font&amp;colorscheme=light&amp;action=like&amp;height=21&amp;appId=119257468194823"
							scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:450px; height:21px;" allowTransparency="true"></iframe>
							<br />Follow us on Twitter: <a href="https://twitter.com/compojoom" class="twitter-follow-button" data-show-count="false">Follow @compojoom</a>
							<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";
							fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script><br /><br />';
				$html[] = "<strong>Quick instructions:</strong><br /><br />
				Please fill in the Matukio settings first and create an event category.<br />
				Matukio needs a Joomla menu link to the eventlist overview (can be hidden)
				in order to work properly with search engine friendly urls!
				<br /><br />See <a href=\"https://compojoom.com/support-2/documentation/matukio\" target=\"_blank\">documentation</a> and our
				<a href=\"http://youtube.com/user/compojoom\" target=\"_blank\">Youtube-Channel</a> for more details.<br /></p>";
				$html[] = "</td>";
				break;
		}

		$html[] = "</tr></tbody></table></div>";
		$html[] = "<div class=\"span3\">";
		$html[] = "<img src=\"" . $imagedir . "logo.png\" valign=\"middle\" />";
		$html[] = "</div>";
		$html[] = "<div class=\"clr clear\"></div></div>";

		if (isset($this->status->sql) && count($this->status->sql))
		{
			$tables = array();

			foreach ($this->status->sql as $key => $value)
			{
				if ($value == true)
				{
					$tables[] = $key;
				}
			}

			if (count($tables))
			{
				$html[] = JText::sprintf('COM_MATUKIO_DEFAULT_SETTINGS_FOR_TABLES', implode(',', $this->status->sql));
			}
		}

		$html[] = $this->renderModuleInfoInstall($this->status->modules);

		$html[] = $this->renderPluginInfoInstall($this->status->plugins);

		$html[] = $this->renderLibraryInfoInstall($this->status->libraries);

		return implode('', $html);
	}

	/**
	 * Render modules
	 *
	 * @param   array  $modules  - The modules
	 *
	 * @return string
	 */
	public function renderModuleInfoInstall($modules)
	{
		$rows = 0;

		$html = array();

		if (count($modules))
		{
			$html[] = '<table class="table" style="width: 100%">';
			$html[] = '<tr>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_MODULE') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_CLIENT') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_STATUS') . '</th>';
			$html[] = '</tr>';

			foreach ($modules as $module)
			{
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $module['name'] . '</td>';
				$html[] = '<td class="key">' . ucfirst($module['client']) . '</td>';
				$html[] = '<td>';
				$html[] = '<span style="color:' . (($module['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($module['result']) ? JText::_(strtoupper($this->extension) . '_MODULE_INSTALLED') : JText::_(strtoupper($this->extension) . '_MODULE_NOT_INSTALLED');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = '</tr>';
			}

			$html[] = '</table>';
		}


		return implode('', $html);
	}

	/**
	 * Renders the uninstallation infos
	 *
	 * @param   array  $modules  - The modules
	 *
	 * @return string
	 */

	public function renderModuleInfoUninstall($modules)
	{
		$rows = 0;
		$html = array();

		if (count($modules))
		{
			$html[] = '<table class="table table-hover" style="width: 100%">';
			$html[] = '<tr>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_MODULE') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_CLIENT') . '</th>';
			$html[] = '<th>' . JText::_(strtoupper($this->extension) . '_STATUS') . '</th>';
			$html[] = '</tr>';

			foreach ($modules as $module)
			{
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $module['name'] . '</td>';
				$html[] = '<td class="key">' . ucfirst($module['client']) . '</td>';
				$html[] = '<td>';
				$html[] = '<span style="color:' . (($module['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($module['result']) ? JText::_(strtoupper($this->extension) . '_MODULE_UNINSTALLED') : JText::_(strtoupper($this->extension) . '_MODULE_COULD_NOT_UNINSTALL');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = '</tr>';
			}

			$html[] = '</table>';
		}

		return implode('', $html);
	}

	/**
	 * Render the install infos
	 *
	 * @param   array  $plugins  - The plugins
	 *
	 * @return string
	 */
	public function renderPluginInfoInstall($plugins)
	{
		$rows = 0;
		$html[] = '<table class="table table-hover" style="width: 100%">';

		if (count($plugins))
		{
			$html[] = '<tr>';
			$html[] = '<th width="60%">' . JText::_(strtoupper($this->extension) . '_PLUGIN') . '</th>';
			$html[] = '<th width="20%">' . JText::_(strtoupper($this->extension) . '_GROUP') . '</th>';
			$html[] = '<th width="20%">' . JText::_(strtoupper($this->extension) . '_STATUS') . '</th>';
			$html[] = '</tr>';

			foreach ($plugins as $plugin)
			{
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key" width="60%">' . $plugin['name'] . '</td>';
				$html[] = '<td class="key" width="20%">' . ucfirst($plugin['group']) . '</td>';
				$html[] = '<td width="20%">';
				$html[] = '<span style="color: ' . (($plugin['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($plugin['result']) ? JText::_(strtoupper($this->extension) . '_PLUGIN_INSTALLED') : JText::_(strtoupper($this->extension) . 'PLUGIN_NOT_INSTALLED');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = '</tr>';
			}
		}

		$html[] = '</table>';

		return implode('', $html);
	}

	/**
	 * Render the install uninstall
	 *
	 * @param   array  $plugins  - The plugins
	 *
	 * @return string
	 */
	public function renderPluginInfoUninstall($plugins)
	{
		$rows = 0;
		$html = array();

		if (count($plugins))
		{
			$html[] = '<table class="table table-hover" style="width: 100%">';
			$html[] = '<tbody>';
			$html[] = '<tr>';
			$html[] = '<th width="60%">Plugin</th>';
			$html[] = '<th width="20%">Group</th>';
			$html[] = '<th width="20%">Status</th>';
			$html[] = '</tr>';

			foreach ($plugins as $plugin)
			{
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key" width="60%">' . $plugin['name'] . '</td>';
				$html[] = '<td class="key" width="20%">' . ucfirst($plugin['group']) . '</td>';
				$html[] = '<td width="20%">';
				$html[] = '	<span style="color:' . (($plugin['result']) ? 'green' : 'red') . '; font-weight: bold;">';
				$html[] = ($plugin['result']) ? JText::_(strtoupper($this->extension) . '_PLUGIN_UNINSTALLED') : JText::_(strtoupper($this->extension) . '_PLUGIN_NOT_UNINSTALLED');
				$html[] = '</span>';
				$html[] = '</td>';
				$html[] = ' </tr> ';
			}

			$html[] = '</tbody > ';
			$html[] = '</table > ';
		}

		return implode('', $html);
	}


	/**
	 * Renders information for the installed libraries
	 *
	 * @param   array  $libraries  - array with libraries
	 *
	 * @return string
	 */
	public function renderLibraryInfoInstall($libraries)
	{
		$rows = 0;
		$html[] = '<table class="table">';

		if (count($libraries))
		{
			$html[] = '<tr>';
			$html[] = '<th>' . JText::_('LIB_COMPOJOOM_LIBRARY') . '</th>';
			$html[] = '<th>' . JText::_('LIB_COMPOJOOM_STATUS') . '</th>';
			$html[] = '</tr>';

			foreach ($libraries as $library)
			{
				$html[] = '<tr class="row' . (++$rows % 2) . '">';
				$html[] = '<td class="key">' . $library['name'] . '</td>';
				$html[] = '<td>';
				$html[] = '<span style="color: ' . (($library['result']) ? 'green' : 'green') . '; font-weight: bold;">';
				$html[] = ($library['result']) ? JText::_(strtoupper($this->extension) . '_PLUGIN_INSTALLED') : JText::_('COM_MATUKIO_LIBRARY_NOT_INSTALLED');
				$html[] = '</span>';

				if (isset($library['message']))
				{
					$html[] = ' (' . $library['message'] . ')';
				}

				$html[] = '</td>';
				$html[] = '</tr>';
			}
		}

		$html[] = '</table>';

		return implode('', $html);
	}


	/**
	 * Get a variable from the manifest file (actually, from the manifest cache).
	 *
	 * @param   string  $name  - The name
	 *
	 * @return  mixed
	 */
	private function getParam($name)
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = ' . $db->quote('com_matukio'));
		$manifest = json_decode($db->loadResult(), true);

		return $manifest[$name];
	}
}
