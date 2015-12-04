<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       05.10.13
 *
 * @copyright  Copyright (C) 2008 - {YEAR} Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

/**
 * Class MatukioHelperChart
 *
 * @since  3.0
 */
class MatukioHelperChart
{
	private  static $instance;

	/**
	 * Returns an barchart
	 *
	 * @param   int  $done  - Is the loading done
	 *
	 * @return  string
	 */
	public static function getProcentBarchart($done)
	{
		$max = 100;

		if ($done < 0)
		{
			$done = 0;
		}

		if ($done > $max)
		{
			$done = $max;
		}

		$displayValue = $done / $max * 100;
		$displayValue = number_format($displayValue, 0, '.', '');

		return "<span style=\"white-space: nowrap;\">
		<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
		. "3000.png\" width=\"" . $displayValue . "\" style=\"height: 10px\" />
		<img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "3001.png\" width=\""
		. (100 - $displayValue) . "\" style=\"height: 10px\" /></span>";
	}
}
