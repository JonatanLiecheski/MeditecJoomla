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
 * Class MatukioHelperSettings
 *
 * @since  1.0.0
 */
class MatukioHelperSettings
{
	private static $instance;

	/**
	 * Gets a setting with the given title, returns default if not available
	 *
	 * @param   string  $title    - The key / title of the setting
	 * @param   string  $default  - The default value (if setting not found)
	 *
	 * @return  mixed
	 */
	public static function _($title = '', $default = '')
	{
		if (!isset(self::$instance))
		{
			self::$instance = self::_loadSettings();
		}

		return self::$instance->get($title, $default);
	}


	/**
	 * Gets a setting with the given title, returns default if not available
	 *
	 * @param   string  $title    - The key / title of the setting
	 * @param   string  $default  - The default value (if setting not found)
	 *
	 * @return  mixed
	 */
	public static function getSettings($title = '', $default = '')
	{
		if (!isset(self::$instance))
		{
			self::$instance = self::_loadSettings();
		}

		return self::$instance->get($title, $default);
	}

	/**
	 * Returns a singleton with all settings
	 *
	 * @return JObject - loads a singleton object with all settings
	 */
	private static function _loadSettings()
	{
		$db = JFactory::getDBO();

		$settings = new JObject;

		$query = ' SELECT st.title, st.value'
			. ' FROM #__matukio_settings AS st'
			. ' ORDER BY st.id';

		$db->setQuery($query);
		$data = $db->loadObjectList();

		foreach ($data as $value)
		{
			$settings->set($value->title, $value->value);
		}

		// Grab the settings from the menu and merge them in the object
		$app = JFactory::getApplication();
		$menu = $app->getMenu();

		if (is_object($menu))
		{
			if ($item = $menu->getActive())
			{
				$menuParams = $menu->getParams($item->id);

				foreach ($menuParams->toArray() as $key => $value)
				{
					if ($key == 'show_page_heading')
					{
						$key = 'show_page_title';
					}

					$settings->set($key, $value);
				}
			}
		}

		return $settings;
	}

	/**
	 * Returns the field html code
	 *
	 * @param   object  $value  -  The field
	 *
	 * @return  string
	 */
	public static function getSettingField($value)
	{
		switch ($value->type)
		{
			case 'textarea':
				return self::getTextareaSettings($value->id, $value->title, $value->value);
				break;

			case 'select':
				return self::getSelectSettings($value->id, $value->title, $value->value, $value->values);
				break;

			case 'bool':
				return self::getBoolField($value->id, $value->title, $value->value);
				break;

			case 'groupselect':
				return self::getGroupSelect($value->id, $value->title, $value->value);
				break;

			case 'text':
			default:
				return self::getTextSettings($value->id, $value->title, $value->value);
				break;
		}
	}

	/**
	 * Generates a nice bootstrap ready (since 3.0) textarea setting
	 *
	 * @param   int     $id     - The setting id
	 * @param   string  $title  - The title
	 * @param   string  $value  - The value
	 * @param   string  $class  - The additional class (btn = default)
	 * @param   int     $rows   - The number of rows
	 * @param   int     $cols   - The number of cols
	 * @param   string  $style  - The style
	 *
	 * @return  string - The html containing the radio boxes
	 */
	public static function getTextareaSettings($id, $title, $value, $class = 'text_area', $rows = 8, $cols = 50, $style = 'width:300px')
	{
		return '<textarea class="input ' . $class . '" name="matukioset[' . $id . ']" id="matukioset[' . $id
		. ']" rows="' . $rows . '" cols="' . $cols . '" style="' . $style . '" title="' . JText::_('COM_MATUKIO_'
		. $title . '_DESC') . '" />' . $value . '</textarea>';
	}

	/**
	 * Generates a nice bootstrap ready (since 3.0) boolean select
	 *
	 * @param   int     $id     - The setting id
	 * @param   string  $title  - The title
	 * @param   string  $value  - The value
	 * @param   string  $class  - The additional class (btn = default)
	 * @param   string  $style  - The style
	 *
	 * @return  string - The html containing the radio boxes
	 */
	public static function getBoolField($id, $title, $value, $class = 'bool', $style = "width: 30px;")
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

		$html .= '<input type="radio" name="matukioset[' . $id . ']" id="matukioset_' . $id . '_yes" class="btn ' . $class . '" value="1" ' . $ysel . ' />'
				. '<label for="matukioset_' . $id . '_yes">' . JText::_('COM_MATUKIO_YES') . '</label>';

		$html .= '<input type="radio" name="matukioset[' . $id . ']" id="matukioset_' . $id . '_no" class="btn ' . $class . '" value="0" ' . $nosel . ' />'
			. '<label for="matukioset_' . $id . '_no">' . JText::_('COM_MATUKIO_NO') . '</label>';

		$html .= "</fieldset>";

		return $html;
	}

	/**
	 * Generates a nice bootstrap ready (since 3.0) group select
	 *
	 * @param   int     $id     - The setting id
	 * @param   string  $title  - The title
	 * @param   string  $value  - The value
	 * @param   string  $class  - The additional class (btn = default)
	 *
	 * @return  string - The html containing the radio boxes
	 */
	public static function getGroupSelect($id, $title, $value, $class = 'chzn-single inputbox')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select("*")
			->from("#__usergroups");

		$db->setQuery($query);
		$groups = $db->loadObjectList();

		$html = '<select name="matukioset[' . $id . ']" id="matukioset[' . $id . ']" class="' . $class . '">';

		$asel = "";

		if ($value == 0)
		{
			$asel = " selected=\"selected\"";
		}

		$html .= '<option value="0"' . $asel . '>' . JText::_("COM_MATUKIO_ALL") . "</option>";

		foreach ($groups as $g)
		{
			$sel = "";

			if ($g->id == $value)
			{
				$sel = " selected=\"selected\"";
			}

			$html .= '<option value="' . $g->id . '"' . $sel . '>' . $g->title . "</option>";
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * Generates a nice bootstrap ready (since 3.0) select list
	 *
	 * @param   int     $id         - The setting id
	 * @param   string  $title      - The title
	 * @param   string  $value      - The value
	 * @param   array   $values     - The possible values formated {name=LABEL}
	 * @param   string  $class      - The additional class (btn = default)
	 * @param   int     $size       - The size of the box
	 * @param   int     $maxlength  - The maxlength of the textfield (not used)
	 * @param   string  $style      - The style
	 *
	 * @return  string - The html containing the radio boxes
	 */
	public static function getSelectSettings($id, $title, $value, $values, $class = 'inputbox', $size = 50, $maxlength = 255, $style = 'width:300px')
	{
		$valuesArray = self::getSettingsValues($values);

		$select = '<select name="matukioset[' . $id . ']" id="matukioset[' . $id . ']" class="chzn-single ' . $class . '">' . "\n";

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

			$text = strtoupper(str_replace(' ', '_', $valueOption['value']));
			$text = str_replace('(', '', $text);
			$text = str_replace(')', '', $text);
			$text = str_replace(':', '', $text);
			$text = str_replace('.', '', $text);
			$text = str_replace('-', '', $text);
			$text = str_replace('__', '_', $text);
			$select .= '<option value="' . $valueOption['id'] . '" ' . $selected . '>' . JText::_('COM_MATUKIO_' . $text) . '</option>' . "\n";
		}

		$select .= '</select>' . "\n";

		return $select;
	}

	/**
	 * Generates a nice bootstrap ready (since 3.0) text setting
	 *
	 * @param   int     $id         - The setting id
	 * @param   string  $title      - The title
	 * @param   string  $value      - The value
	 * @param   string  $class      - The additional class (btn = default)
	 * @param   int     $size       - The size of the box
	 * @param   int     $maxlength  - The maxlength of the textfield
	 * @param   string  $style      - The style
	 *
	 * @return  string - The html containing the radio boxes
	 */

	public static function getTextSettings($id, $title, $value, $class = 'text_area', $size = 50, $maxlength = 255, $style = 'width:300px')
	{
		return '<input class="inputbox ' . $class . '" type="text" name="matukioset[' . $id . ']"
            id="matukioset[' . $id . ']" value="' . $value . '" size="' . $size . '"
            maxlength="' . $maxlength . '" style="' . $style . '" title="' .
			JText::_('COM_MATUKIO_' . strtoupper($title) . '_DESC') . '" />';
	}

	/**
	 * Gets the values as array
	 *
	 * @param   string  $params  -  The values formated {}..
	 *
	 * @return  mixed
	 */

	public static function getSettingsValues($params)
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
	 * Returns the generated html for displaying in Matukio settings
	 *
	 * @param   array  $sets  - The settings
	 *
	 * @return  string - The generated html code
	 */
	public static function getSettingsBlock($sets)
	{
		$i = 0;
		$split = (int) (count($sets) / 2) + 1;
		$html = "";

		foreach ($sets as $value)
		{
			$html .= '<div class="control-group">';
			$html .= '<label for="' . $value->title . '" title="' . JText::_('COM_MATUKIO_'
				. strtoupper($value->title) . '_DESC') . '" class="hasTooltip control-label"
								data-original-title="' . JText::_('COM_MATUKIO_' . strtoupper($value->title) . '_DESC')
				. '" />';
			$html .= JText::_('COM_MATUKIO_' . strtoupper($value->title));
			$html .= '</label>';

			$html .= '<div class="controls">';
			$html .= self::getSettingField($value);
			$html .= '</div>';
			$html .= '</div>';

			if ($i == $split)
			{
				$html .= "</div>";
				$html .= "<div class=\"col-lg-6 col-md-12 col-sm-12\">";
			}

			$i++;
		}

		return $html;
	}
}
