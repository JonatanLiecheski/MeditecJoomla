<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       28.09.13
 *
 * @copyright  Copyright (C) 2008 - 2013 compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die ('Restricted access');

/**
 * Class MatukioHelperInput
 *
 * @since  3.0.0
 */
class MatukioHelperInput
{
	private static $instance;

	/**
	 * Gets a radio button select
	 *
	 * @param   string  $name   - The name of the radio
	 * @param   string  $id     - The id (_yes & _no)
	 * @param   int     $value  - The boolean value (default 1)
	 * @param   string  $class  - The css classes (opt)
	 * @param   string  $add    - Added to the element (opt)
	 *
	 * @return string
	 */
	public static function getRadioButtonBool($name, $id, $value = 1, $class = "", $add = "")
	{
		$nosel = "";
		$ysel = "";

		if ($value == 1)
		{
			$ysel = " checked=\"checked\"";
		}

		if ($value == 0)
		{
			$nosel = " checked=\"checked\"";
		}

		$html = '<fieldset class="radio btn-group">';

		$html .= '<input type="radio" name="' . $name . '" id="' . $id . '_yes" class="btn ' . $class . '" value="1" ' . $ysel . " " . $add . ' />'
			. '<label for="' . $id . '_yes">' . JText::_('COM_MATUKIO_YES') . '</label>';

		$html .= '<input type="radio" name="' . $name . '" id="' . $id . '_no" class="btn ' . $class . '" value="0" ' . $nosel . " " . $add . ' />'
			. '<label for="' . $id . '_no">' . JText::_('COM_MATUKIO_NO') . '</label>';

		$html .= "</fieldset>";

		return $html;
	}


	/**
	 * Gets a radio button group
	 *
	 * @param   string  $name    - The name
	 * @param   string  $id      - The id
	 * @param   array   $values  - The value
	 * @param   string  $sel     - The selected item
	 * @param   string  $class   - The class
	 * @param   string  $add     - Additional css
	 *
	 * @return  string
	 */
	public static function getRadioButton($name, $id, $values, $sel = "", $class = "", $add = "")
	{
		$html = '<fieldset class="radio btn-group">';

		foreach ($values as $option => $val)
		{
			$check = "";

			if ($option == $sel)
			{
				$check = " checked=\"checked\"";
			}

			$html .= '<input type="radio" name="' . $name . '" id="' . $id . '_' . $option . '" class="btn ' . $class . '" value="' . $option . '" ' . $check . " " . $add . ' />'
				. '<label for="' . $id . '_' . $option . '">' . JText::_($val) . '</label>';
		}

		$html .= "</fieldset>";

		return $html;
	}

	/**
	 * Gets a checkbox button group
	 *
	 * @param   string  $name    - The name
	 * @param   string  $id      - The id
	 * @param   array   $values  - The value
	 * @param   string  $sel     - The selected item
	 * @param   string  $class   - The class
	 * @param   string  $add     - Additional css
	 *
	 * @return  string
	 */
	public static function getCheckboxButton($name, $id, $values, $sel = "", $class = "", $add = "")
	{
		$html = '<div class="checkbox btn-group">';

		foreach ($values as $option => $val)
		{
			$check = "";

			if ($option == $sel)
			{
				$check = " checked=\"checked\"";
			}


			$html .= '<input type="checkbox" name="' . $name . '[]" id="' . $id . '_' . $option . '" value="' . $option . '" ' . $check . " " . $add . ' />' .
				'<label for="' . $id . '_' . $option . '" class="checkbox btn">' . JText::_($val) . '</label>';
		}

		$html .= "</div>";

		return $html;
	}

	/**
	 * Gets the tooltip
	 *
	 * @param   string  $text  - The text
	 *
	 * @return string
	 */
	public static function getTooltip($text)
	{
		$html = '<img src="../media/com_matukio/images/info.png" class="hasTooltip" data-original-title="'
			. JText::_($text) . '" />';

		return $html;
	}

	/**
	 * Returns the fitting translated text for 0 1
	 *
	 * @param   int  $bool  - yes or no label
	 *
	 * @return  string
	 */
	public static function getYesNo($bool = 1)
	{
		if ($bool)
		{
			return JText::_("JYES");
		}
		else
		{
			return JText::_("JNO");
		}
	}
}
