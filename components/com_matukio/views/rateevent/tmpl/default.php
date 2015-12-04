<?php
/**
 * @package    Matukio
 * @author     Yves Hoppe <yves@compojoom.com>
 * @date       03.04.13
 *
 * @copyright  Copyright (C) 2008 - 2014 Yves Hoppe - compojoom.com . All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die('Restricted access');

JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');

$htxt = str_replace("SEM_TITLE", $this->event->title, JTEXT::_('COM_MATUKIO_PLEASE_RATE_THIS_EVENT'));

$html = "\n<body onload=\"parent.sbox-window.focus();\">";
$html .= "<form action=\"index.php\" method=\"post\" name=\"FrontForm\">\n";
$html .= "<div class=\"sem_cat_title\">" . JTEXT::_('COM_MATUKIO_YOUR_RATING') . "</div><br />";
$html .= "<div class=\"sem_shortdesc\">" . $htxt . "</div>";
$html .= "<br /><center><table cellpadding=\"2\" cellspacing=\"0\" border=\"0\">";

$tempa = "";
$tempb = "";
for ($i = 6; $i > 0; $i = $i - 1)
{
	$tempa .= "<th><img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath() . "240" . $i . ".png\"></th><td width=\"10px\">&nbsp;</td>";
	$tempb .= "<th><input type=\"radio\" name=\"grade\" value=\"" . $i . "\"";
	if ($i == $this->booking->grade)
	{
		$tempb .= " checked";
	}
	$tempb .= "></th><td width=\"10px\">&nbsp;</td>";
}
$html .= "<tr>" . $tempa . "</tr>";
$html .= "<tr>" . $tempb . "</tr>";
$html .= "</table></center>";
$html .= "<br /><div class=\"sem_shortdesc\">" . JTEXT::_('COM_MATUKIO_COMMENT') . ":</div>";
$html .= "<br /><center><input type=\"text\" name=\"text\" size=\"70\" maxlength=\"200\" value=\""
	. $this->booking->comment . "\"></center><br />";
$html .= "<input type=\"hidden\" name=\"option\" value=\"com_matukio\">
        <input type=\"hidden\" name=\"view\" value=\"rateevent\" />
        <input type=\"hidden\" name=\"controller\" value=\"rateevent\" />
        <input type=\"hidden\" name=\"cid\" value=\"" . $this->event->id . "\">
        <input type=\"hidden\" name=\"task\" value=\"rate\">";
$html .= "<center><button class=\"button\" style=\"cursor:pointer;\" type=\"button\" onclick=\"this.disabled=true;document.FrontForm.submit();\">" . JTEXT::_('COM_MATUKIO_SEND') . "</button></center>";
$html .= "</form>";
$html .= "</body></html>";
echo $html;
