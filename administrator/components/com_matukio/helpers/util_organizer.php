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

/**
 * Class MatukioHelperOrganizer
 *
 * @since  2.2.0
 */
class MatukioHelperOrganizer
{
	private static $instance;

	/**
	 * Gets the organizers
	 *
	 * @param   int  $userid  - The user id
	 *
	 * @return  mixed
	 */
	public static function getOrganizer($userid)
	{
		$user = JFactory::getUser($userid);

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__matukio_organizers')->where('userId=' . $db->quote($userid));
		$db->setQuery($query, 0, 1);

		$organizer = $db->loadObject();

		if (empty($organizer))
		{
			return null;
		}
		else
		{
			if (empty($organizer->name))
			{
				$organizer->name = $user->name;
			}

			if (empty($organizer->email))
			{
				$organizer->email = $user->email;
			}

			return $organizer;
		}
	}

	/**
	 * Gets the Organizer on the given id
	 *
	 * @param   int  $id  - The id
	 *
	 * @return  mixed
	 */
	public static function getOrganizerId($id)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__matukio_organizers')->where('id=' . $db->quote($id));
		$db->setQuery($query, 0, 1);

		return $db->loadObject();
	}
}
