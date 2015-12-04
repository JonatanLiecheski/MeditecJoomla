<?php
/**
 * Matukio
 * @package  Joomla!
 * @Copyright (C) 2012 - Yves Hoppe - compojoom.com
 * @All      rights reserved
 * @Joomla   ! is Free Software
 * @Released under GNU/GPL License : http://www.gnu.org/copyleft/gpl.html
 * @version  $Revision: 1.0.0 $
 **/

defined('_JEXEC') or die ('Restricted access');

/**
 * Class MatukioHelperUtilsBooking
 *
 * @since  1.0.0
 */
class MatukioHelperUtilsBooking
{
	private static $instance;

	public static $PENDING = 0;

	public static $ACTIVE = 1;

	public static $WAITLIST = 2;

	public static $ARCHIVED = 3;

	public static $DELETED = 4;

	public static $PAID_NOT = 0;

	public static $PAID = 1;

	/**
	 * Gets the booking id
	 *
	 * @param   int $id - The id
	 *
	 * @return  string
	 */
	public static function getBookingId($id)
	{
		return strtoupper(substr(sha1($id), 0, 10));
	}

	/**
	 * Gets the booking id picture
	 *
	 * @param   int $id - The id
	 *
	 * @return  string
	 */
	public static function getBookingIdCodePicture($id)
	{
		// Gets the frontend user lists $config->get('sem_p029',1);
		$temp = MatukioHelperSettings::getSettings('frontend_userlistscode', 1);

		if ($temp == 1)
		{
			return "<img src=\"http://chart.apis.google.com/chart?cht=qr&amp;chs=100x100&amp;choe=UTF-8&amp;chld=H|4&amp;chl="
			. urlencode(self::getBookingId($id)) . "\" width=\"100\" height=\"100\" /><br /><code><b>"
			. self::getBookingId($id) . "</b></code>";
		}
		elseif ($temp == 2)
		{
			$url = JURI::root() . "index.php?option=com_matukio&format=raw&view=matukio&task=getBarcode&code=" . urlencode(self::getBookingId($id));

			return "<img src=\"" . $url . "\" />";
		}
	}

	/**
	 * Gets the booking qr code
	 *
	 * @param   int  $id  - The booking id
	 *
	 * @return  string    - The img code
	 */
	public static function getBookingIdQRCode($id)
	{
		return "<img src=\"http://chart.apis.google.com/chart?cht=qr&amp;chs=100x100&amp;choe=UTF-8&amp;chld=H|4&amp;chl="
		. urlencode(self::getBookingId($id)) . "\" width=\"100\" height=\"100\" /><br /><code><b>"
		. self::getBookingId($id) . "</b></code>";
	}

	/**
	 * @param $url
	 * @param $id
	 *
	 * @return string
	 */
	public static function getBookingCheckinQRCode($url, $id)
	{
		return "<img src=\"http://chart.apis.google.com/chart?cht=qr&amp;chs=150x150&amp;choe=UTF-8&amp;chld=H|4&amp;chl="
		. urlencode($url) . "\" width=\"150\" height=\"150\" /><br /><code><b>"
		. self::getBookingId($id) . "</b></code>";
	}

	/**
	 * Gets the booking id barcode
	 *
	 * @param   int $id - The id
	 *
	 * @return  string
	 */
	public static function getBookingIdBarcode($id)
	{
		// Gets the frontend user lists $config->get('sem_p029',1);

		$url = JURI::root() . "index.php?option=com_matukio&format=raw&view=matukio&task=getBarcode&code=" . urlencode(self::getBookingId($id));

		return "<img src=\"" . $url .  "\" />";
	}

	/**
	 * Gets the code 99 for the given char
	 *
	 * @param $Asc
	 *
	 * @return string
	 */
	public static function getCode99($Asc)
	{
		switch ($Asc)
		{
			case ' ':
				return "011000100";
			case '$':
				return "010101000";
			case '%':
				return "000101010";
			case '*':
				return "010010100";
			case '+':
				return "010001010";
			case '|':
				return "010000101";
			case '.':
				return "110000100";
			case '/':
				return "010100010";
			case '-':
				return "010000101";
			case '0':
				return "000110100";
			case '1':
				return "100100001";
			case '2':
				return "001100001";
			case '3':
				return "101100000";
			case '4':
				return "000110001";
			case '5':
				return "100110000";
			case '6':
				return "001110000";
			case '7':
				return "000100101";
			case '8':
				return "100100100";
			case '9':
				return "001100100";
			case 'A':
				return "100001001";
			case 'B':
				return "001001001";
			case 'C':
				return "101001000";
			case 'D':
				return "000011001";
			case 'E':
				return "100011000";
			case 'F':
				return "001011000";
			case 'G':
				return "000001101";
			case 'H':
				return "100001100";
			case 'I':
				return "001001100";
			case 'J':
				return "000011100";
			case 'K':
				return "100000011";
			case 'L':
				return "001000011";
			case 'M':
				return "101000010";
			case 'N':
				return "000010011";
			case 'O':
				return "100010010";
			case 'P':
				return "001010010";
			case 'Q':
				return "000000111";
			case 'R':
				return "100000110";
			case 'S':
				return "001000110";
			case 'T':
				return "000010110";
			case 'U':
				return "110000001";
			case 'V':
				return "011000001";
			case 'W':
				return "111000000";
			case 'X':
				return "010010001";
			case 'Y':
				return "110010000";
			case 'Z':
				return "011010000";
			default:
				return "011000100";
		}
	}

	/**
	 * Gets the bookingfields
	 *
	 * @param   int    $page      - The page
	 * @param   int    $published - Published
	 * @param   string $orderby   - Order by (default 'ordering')
	 *
	 * @return  mixed
	 */

	public static function getBookingFields($page = null, $published = 1, $orderby = 'ordering')
	{
		$database = JFactory::getDBO();

		if (empty($page))
		{
			$database->setQuery("SELECT * FROM #__matukio_booking_fields WHERE published = " . $published . " ORDER BY " . $orderby);
		}
		else
		{
			$database->setQuery("SELECT * FROM #__matukio_booking_fields WHERE page = " . $page
				. " AND published = " . $published . " ORDER BY "
				. $orderby
			);
		}

		$fields = $database->loadObjectList();

		return ($fields);
	}

	/**
	 * Gets the booking header
	 *
	 * @param   int  $steps  - The number of steps
	 *
	 * @return  string
	 */

	public static function getBookingHeader($steps)
	{
		$html = "";

		if ($steps == 2)
		{
			$html = '<div id="mat_h1">&nbsp;';
			$html .= '</div>';
			$html .= '<div id="mat_h2">&nbsp;';
			$html .= '</div>';
		}
		else
		{
			$html = '<div id="mat_hp1">&nbsp;';
			$html .= '</div>';
			$html .= '<div id="mat_hp2">&nbsp;';
			$html .= '</div>';
			$html .= '<div id="mat_hp3">&nbsp;';
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Gets the text area field
	 *
	 * @param   string  $name      -  The name
	 * @param   string  $title     -  The title
	 * @param   string  $value     -  The value
	 * @param   string  $style     -  The style
	 * @param   bool    $required  -  Is the fields required
	 * @param   string  $class     -  The class
	 * @param   int     $rows      -  The number of rows
	 * @param   int     $cols      -  The number of cols
	 *
	 * @return  string
	 */
	public static function getTextAreaField($name, $title, $value, $style = 'width:300px', $required = false, $class = 'text_area',
		$rows = 8, $cols = 50)
	{
		$req = "";

		if ($required)
		{
			$req = " validate[required]";
		}

		$bookingfield_desc = "";

		if (MatukioHelperSettings::getSettings('bookingfield_desc', 0))
		{
			$bookingfield_desc = JText::_(strtoupper($title) . '_DESC');
		}

		return '<textarea class="' . $class . $req . '" name="' . $name . '" id="' . $name . '" rows="'
		. $rows . '" cols="' . $cols . '" style="' . $style . '" title="' . $bookingfield_desc
		. '" />' . $value . '</textarea>';
	}

	/**
	 * Gets the textfield
	 *
	 * @param   string  $name       - The name
	 * @param   string  $title      - The title
	 * @param   string  $value      - The value
	 * @param   string  $values     - The values
	 * @param   string  $style      - The style
	 * @param   bool    $required   - Is the fields required
	 * @param   string  $class      - The class
	 * @param   int     $size       - The size
	 * @param   int     $maxlength  - The max length
	 *
	 * @return  string
	 */
	public static function getSelectField($name, $title, $value, $values, $style = 'width:300px',
		$required = false, $class = 'inputbox', $size = 50,
		$maxlength = 255)
	{
		$req = "";

		if ($required)
		{
			$req = " validate[required]";
		}

		$valuesArray = self::getSelectValues($values);

		$bookingfield_desc = "";

		if (MatukioHelperSettings::getSettings('bookingfield_desc', 0))
		{
			$bookingfield_desc = JText::_(strtoupper($title) . '_DESC');
		}

		$select = '<select name="' . $name . '" id="' . $name . '" class="' . $class . $req . '" title="' .
			$bookingfield_desc . '">' . "\n";


		foreach ($valuesArray as $valueOption)
		{
			if ($value == $valueOption['id'])
			{
				$selected = 'selected="selected"';
			}
			else
			{
				$selected = '';
			}

			// $text = strtoupper(str_replace(' ', '_', $valueOption['value']));
			$text = str_replace('(', '', $valueOption['value']);
			$text = str_replace(')', '', $text);
			$text = str_replace(':', '', $text);
			$text = str_replace('.', '', $text);
			$text = str_replace('-', '', $text);
			$text = str_replace('__', '_', $text);
			$select .= '<option value="' . $valueOption['id'] . '" ' . $selected . '>' . JText::_($text) . '</option>' . "\n";
		}

		$select .= '</select>' . "\n";

		return $select;
	}

	/**
	 * Gets the textfield
	 *
	 * @param   string $name     - The name
	 * @param   string $title    - The title
	 * @param   string $value    - The value
	 * @param   string $values   - The values
	 * @param   string $style    - The style
	 * @param   bool   $required - Is the field required
	 * @param   string $class    - The class
	 *
	 * @return  string
	 */
	public static function getRadioField($name, $title, $value, $values, $style = "", $required = false, $class = "inputbox")
	{
		$req = "";

		if ($required)
		{
			$req = " validate[required]";
		}

		$valuesArray = self::getSelectValues($values);
		$radio = "";

		foreach ($valuesArray as $valueOption)
		{
			if ($value == $valueOption['id'])
			{
				$selected = ' checked="checked"';
			}
			else
			{
				$selected = '';
			}

			$text = strtoupper(str_replace(' ', '_', $valueOption['value']));
			$text = str_replace('(', '', $text);
			$text = str_replace(')', '', $text);
			$text = str_replace(':', '', $text);
			$text = str_replace('.', '', $text);
			$text = str_replace('-', '', $text);
			$text = str_replace('__', '_', $text);

			$radio .= '<input type="radio" name="' . $name . '" value="' . $valueOption['id'] . '" ' . $selected . ' /> ' . JText::_($text);
		}

		return $radio;
	}

	/**
	 * Gets the Checkbox
	 *
	 * @param   string $name     - The name
	 * @param   string $title    - The title
	 * @param   string $value    - The value
	 * @param   string $values   - The values
	 * @param   string $style    - The style
	 * @param   bool   $required - Is the fields required
	 * @param   string $class    - The class
	 *
	 * @return  string
	 */
	public static function getCheckboxField($name, $title, $value, $values, $style = "", $required = false, $class = "inputbox")
	{
		$req = "";

		if ($required)
		{
			$req = " validate[required]";
		}

		$valuesArray = self::getSelectValues($values);
		$check = "";

		foreach ($valuesArray as $valueOption)
		{
			if ($value == $valueOption['id'])
			{
				$selected = ' checked="checked"';
			}
			else
			{
				$selected = '';
			}

			$text = strtoupper(str_replace(' ', '_', $valueOption['value']));
			$text = str_replace('(', '', $text);
			$text = str_replace(')', '', $text);
			$text = str_replace(':', '', $text);
			$text = str_replace('.', '', $text);
			$text = str_replace('-', '', $text);
			$text = str_replace('__', '_', $text);

			$check .= '<input type="checkbox" name="' . $name . '" value="' . $valueOption['id'] . '" ' . $selected . ' /> ' . JText::_($text);
		}

		return $check;
	}


	/**
	 * Gets the select options
	 *
	 * @param   string $params - The params
	 *
	 * @return  mixed
	 */
	public static function getSelectValues($params)
	{
		$regex_one = '/({\s*)(.*?)(})/si';
		$regex_all = '/{\s*.*?}/si';
		$matches = array();
		$count_matches = preg_match_all($regex_all, $params, $matches, PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);

		$values = array();

		for ($i = 0; $i < $count_matches; $i++)
		{
			$matukio = $matches[0][$i][0];
			preg_match($regex_one, $matukio, $matukioParts);
			$values_replace = array("/^'/", "/'$/", "/^&#39;/", "/&#39;$/", "/<br \/>/");
			$values = explode("=", $matukioParts[2], 2);

			foreach ($values_replace as $key2 => $values2)
			{
				$values = preg_replace($values2, '', $values);
			}

			$returnValues[$i]['id'] = $values[0];
			$returnValues[$i]['value'] = $values[1];
		}

		return $returnValues;
	}

	/**
	 * Gets the textfield
	 *
	 * @param   string  $name       - The name
	 * @param   string  $title      - The title
	 * @param   string  $value      - The value
	 * @param   string  $style      - The style
	 * @param   bool    $required   - Is the fields required
	 * @param   string  $class      - The class
	 * @param   int     $size       - The size
	 * @param   int     $maxlength  - The max length
	 *
	 * @return  string
	 */

	public static function getTextField(
		$name, $title, $value, $style = 'width: 250px', $required = false, $class = 'input',
		$size = 50, $maxlength = 255)
	{
		$req = "";

		if ($required)
		{
			$req = " validate[required";

			if ($name == "email")
			{
				$req .= ",custom[email]";
			}

			$req .= "]";
		}

		$bookingfield_desc = JText::_(strtoupper($title));

		if (MatukioHelperSettings::getSettings('bookingfield_desc', 0))
		{
			$bookingfield_desc = JText::_(strtoupper($title) . '_DESC');
		}

		return '<input class="' . $class . $req . '" type="text" name="' . $name . '"
            id="' . $name . '" value="' . $value . '" size="' . $size . '"
            maxlength="' . $maxlength . '" style="' . $style . '" title="'
		. $bookingfield_desc . '" />';
	}

	/**
	 * Gets the spacer field
	 *
	 * @param   string  $style  - The style
	 * @param   string  $class  - The class
	 *
	 * @return  string
	 */
	public static function getSpacerField($style = "", $class = "mat_spacer")
	{
		return '<hr class="' . $class . '" style="' . $style . '" />';
	}

	/**
	 * Gets the spacer text field
	 *
	 * @param   string  $name   - The name
	 * @param   string  $label  - The label
	 * @param   string  $style  - The style
	 * @param   string  $class  - The class
	 *
	 * @return  string
	 */
	public static function getSpacerTextField($name, $label, $style = "", $class = "mat_spacertext")
	{
		return '<span name="' . $name . '" class="' . $class . '" style="' . $style . '">' . JText::_($label) . '</span>';
	}

	/**
	 * Gets the checkbox
	 *
	 * @param   string  $name     - The name
	 * @param   string  $title    - The title
	 * @param   string  $link     - The link
	 * @param   bool    $checked  - Is the checkbox checked
	 * @param   string  $style    - The style
	 * @param   string  $class    - The class
	 *
	 * @return  string
	 */
	public static function getCheckbox($name, $title, $link, $checked = false, $style = "width: 20px;", $class = 'checkbox')
	{
		return '<input type="checkbox" name="' . $name . '" id="' . $name . '" value="' . $name
		. '" style="' . $style . '" class="' . $class . '" /> ' . JText::_($title);
	}

	/**
	 * Gets the confirmation fields
	 *
	 * @param   string  $name  - The name
	 *
	 * @return  string
	 */
	public static function getConfirmationfields($name)
	{
		return "<div id=\"conf_" . $name . "\">&nbsp;</div>";
	}

	/**
	 * Prints the field
	 *
	 * @param   object $field       - The field obj
	 * @param   bool   $pageone     - The page
	 * @param   int    $value       - The value
	 * @param   string $field_style - The style
	 *
	 * @static
	 * @return  void
	 */
	public static function printFieldElement($field, $pageone = false, $value = -1, $field_style = "default")
	{
		if ($field->type == 'spacer')
		{
			echo "<tr>";
			echo "<td colspan=\"2\">";
			echo self::getSpacerField($field->style);
			echo "</td>";
			echo "</tr>\n";
		}
		elseif ($field->type == 'spacertext')
		{
			echo "<tr>";
			echo "<td colspan=\"2\">";
			echo self::getSpacerTextField($field->field_name, $field->label, $field->style);
			echo "</td>";
			echo "</tr>\n";
		}
		else
		{
			echo '<tr>';

			if ($field_style == "small")
			{
				$size = "100px";
			}
			else
			{
				$size = "150px";
			}

			echo '<td class="key" width="' . $size . '">';
			echo '<label for="' . $field->field_name . '" width="100" title="' . JText::_($field->label) . '">';
			echo JText::_($field->label);

			if ($field->required == 1)
			{
				echo " <span class=\"mat_req\">*</span>";
			}

			echo '</label>';
			echo '</td>';

			echo '<td>';

			if ($field_style == "small")
			{
				$style = "width: 100px";
			}
			else
			{
				// Default
				$style = "width: 250px";
			}

			if (!empty($field->style))
			{
				$style = $field->style;
			}

			// Checking required only on page one, should be changed sometime
			if (!$pageone)
			{
				$field->required = false;
			}

			// Get the value out of mapping
			if ($field->datasource && JFactory::getUser()->id)
			{
				// Joomla Data mapping
				if ($field->datasource_map != "email" && $field->datasource_map != "username" && $field->datasource_map != "name")
				{
					// TODO optimize
					$field->default = JUserHelper::getProfile()->profile[$field->datasource_map];
				}
				else
				{
					$field->default = JFactory::getUser()->get($field->datasource_map);
				}
			}

			if ($value != -1)
			{
				$field->default = $value;
			}

			switch ($field->type)
			{
				case 'textarea':
					echo self::getTextAreaField(
						$field->field_name, $field->label,
						$field->default, $style, $field->required
					);
					break;

				case 'select':
					echo self::getSelectField(
						$field->field_name, $field->label,
						$field->default, $field->values, $style, $field->required
					);
					break;

				case 'radio':
					echo self::getRadioField(
						$field->field_name, $field->label, $field->default,
						$field->values, $style, $field->required
					);
					break;

				case 'checkbox':
					echo self::getCheckboxField(
						$field->field_name, $field->label, $field->default,
						$field->values, $style, $field->required
					);
					break;

				case 'text':
				default:
					echo self::getTextField(
						$field->field_name,
						$field->label, $field->default, $style, $field->required
					);
					break;
			}

			echo '</td>';
			echo '</tr>';
		}
	}


	/**
	 * Gets the booking out of the database
	 *
	 * @param   int  $booking_id  - The booking id
	 * @param   int  $event_id    - The event id
	 *
	 * @return  mixed
	 */
	public static function getBooking($booking_id, $event_id = 0)
	{
		if (!empty($booking_id))
		{
			$database = JFactory::getDbo();
			$database->setQuery("SELECT * FROM #__matukio_bookings WHERE id='" . $booking_id . "'");

			return $database->loadObject();
		}
		elseif (!empty($event_id))
		{
			$user = JFactory::getUser();

			if (empty($user->id))
			{
				return null;
			}

			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$where[] = "semid = " . $db->quote($event_id);
			$where[] = "userid = " . $db->quote($user->id);

			$query->select("*")->from("#__matukio_bookings")->where(implode(" AND ", $where));
			$db->setQuery($query);

			return $db->loadObject();
		}

		return null;
	}


	/**
	 * Gets the booking status name
	 *
	 * @param   int  $s  - The booking status
	 *
	 * @return  string
	 */
	public static function getBookingStatusName($s = 0)
	{
		switch ($s)
		{
			default:
			case self::$PENDING:
				return JText::_("COM_MATUKIO_PENDING");

			case self::$ACTIVE:
				return JText::_("COM_MATUKIO_PARTICIPANT_ASSURED");

			case self::$ARCHIVED:
				return JText::_("COM_MATUKIO_ARCHIVED");

			case self::$DELETED:
				return JText::_("COM_MATUKIO_DELETED");

			case self::$WAITLIST:
				return JText::_("COM_MATUKIO_BOOKING_ON_WAITLIST");
		}

	}

	/**
	 * Gets the booking paid text
	 *
	 * @param   int  $s  - The booking int (0 = not paid, 1  = paid)
	 *
	 * @return  string
	 */
	public static function getBookingPaidName($s = 0)
	{
		switch ($s)
		{
			default:
			case self::$PAID_NOT:
				return JText::_("COM_MATUKIO_NOT_PAID");

			case self::$PAID:
				return JText::_("COM_MATUKIO_PAID");
		}
	}

	/**
	 * Deletes (Set status) the given bookings
	 *
	 * @param   array  $cid  - The array of ids
	 *
	 * @throws  Exception  - DB Error
	 * @return  bool
	 */
	public static function deleteBookings($cid)
	{
		self::changeStatusBooking($cid, self::$DELETED, MatukioHelperSettings::_("notify_participants_delete", 1));

		return true;
	}

	/**
	 * Change the booking status
	 *
	 * @param   array    $cid                - The cid
	 * @param   int      $status             - The status to which the booking should be changed
	 * @param   boolean  $notifyParticipant  - Should the participant be notified (by email)
	 *
	 * @throws  Exception - DB Error
	 *
	 * @return  bool
	 */
	public static function changeStatusBooking($cid, $status = 1, $notifyParticipant = true)
	{
		if (count($cid))
		{
			$db = JFactory::getDBO();

			$cids = implode(',', $cid);

			$db->setQuery(
				"UPDATE #__matukio_bookings SET status = " . $db->quote($status)
				. " WHERE id IN (" . $cids . ")"
			);

			if (!$db->execute())
			{
				throw new Exception($db->getErrorMsg(), 42);
			}

			if ($notifyParticipant)
			{
				$db->setQuery("SELECT * FROM #__matukio_bookings WHERE id IN (" . $cids . ")");
				$bookings = $db->loadObjectList();

				if ($db->getErrorNum())
				{
					throw new Exception($db->getErrorMsg(), 42);
				}

				foreach ($bookings AS $b)
				{
					$event = MatukioHelperUtilsEvents::getEventRecurring($b->semid);

					if ($status == self::$ACTIVE)
					{
						// Notify users of the activation
						MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $b->id, 1, false, $b);
					}
					elseif ($status == self::$DELETED)
					{
						// Notify users of the organizer delete
						MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $b->id, 3, false, $b);
					}
					elseif ($status == self::$PENDING)
					{
						// Notify users of the organizer delete
						MatukioHelperUtilsEvents::sendBookingConfirmationMail($event, $b->id, 1, false, $b);
					}
					else
					{
						throw new Exception("Uknown Status: " . $status, "42");
					}
				}
			}
		}

		return true;
	}
}
