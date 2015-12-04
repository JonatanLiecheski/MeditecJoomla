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

global $mainframe;
$document = JFactory::getDocument();
$database = JFactory::getDBO();
$my = JFactory::getuser();
JHTML::_('stylesheet', 'media/com_matukio/css/matukio.css');
JHTML::_('stylesheet', 'media/com_matukio/backend/css/matukio.css');

JFilterOutput::objectHTMLSafe($this->event);
JHTML::_('behavior.calendar');
?>
<div class="compojoom-bootstrap">
<form class="form-horizontal" action="index.php" method="post" name="FrontForm" id="adminForm" enctype="multipart/form-data">
	<?php
	$knopfunten = "";

	// Zurück
	$backlink = JRoute::_("index.php?option=com_matukio&view=eventlist&art=2");

	$knopfoben = "<a title=\"" . JTEXT::_('COM_MATUKIO_BACK') . "\" href=\"" . $backlink . "\"><img src=\""
		. MatukioHelperUtilsBasic::getComponentImagePath() . "1032.png\" border=\"0\" align=\"absmiddle\"></a>";

	$knopfunten .= "<a href=\"" . $backlink . "\"> <span class=\"btn\" style=\"cursor:pointer;\" type=\"button\">"
		. JTEXT::_('COM_MATUKIO_BACK') . "</span></a>";

	// Submit
	$knopfunten .= " <input type=\"submit\" id=\"btnSave\" class=\"btn btn-success\" style=\"cursor:pointer;\" value=\"" . JText::_("COM_MATUKIO_SAVE") . "\">";

	if ($this->event->cancelled == 0)
	{
		$knopfunten .= " <a class=\"btn btn-danger\" href=\"index.php?option=com_matukio&view=createevent&task=cancel&cid=" . $this->event->id . "\">" . JText::_("COM_MATUKIO_CANCEL_EVENT") . "</a>";
	}
	else
	{
		$knopfunten .= " <a class=\"btn btn-success\" href=\"index.php?option=com_matukio&view=createevent&task=uncancel&cid=" . $this->event->id . "\">" . JText::_("COM_MATUKIO_UNCANCEL_EVENT") . "</a>";
	}

	if ($this->event->id > 0)
	{
		// Event kopieren
		$duplicatelink = JRoute::_("index.php?option=com_matukio&view=createevent&task=duplicateEvent&cid=" . $this->event->id);

		$knopfunten .= " <a title=\"" . JTEXT::_('COM_MATUKIO_DUPLICATE') . "\" href=\"" . $duplicatelink
			. "\"><button class=\"btn\" style=\"cursor:pointer;\" type=\"button\">" . JTEXT::_('COM_MATUKIO_DUPLICATE') . "</button></a> ";


		// Delete (unpublish in reallity)
		$unpublishlink = JRoute::_("index.php?option=com_matukio&view=createevent&task=unpublishevent&cid=" . $this->event->id);


		$knopfoben .= "<a title=\"" . JTEXT::_('COM_MATUKIO_DELETE') . "\" href=\"" . $unpublishlink . "\">
        <img src=\"" . MatukioHelperUtilsBasic::getComponentImagePath()
			. "1532.png\" border=\"0\" align=\"absmiddle\"></a>";

		$knopfunten .= "<a href=\"" . $unpublishlink . "\"><button class=\"btn btn-danger\" style=\"cursor:pointer;\" type=\"button\">"
			. JTEXT::_('COM_MATUKIO_DELETE') . "</button></a>";
	}

	if (MatukioHelperSettings::getSettings('event_buttonposition', 2) == 0
		OR MatukioHelperSettings::getSettings('event_buttonposition', 2) == 2)
	{
		echo $knopfoben;
	}

	// MatukioHelperUtilsEvents::getEventlistHeaderEnd();
	echo "<table class=\"table\" width=\"100%\" cellspacing=\"0\" cellpadding=\"4\" border=\"0\" style=\"border-top: 1px solid #ccc\">"
		. "<tr><td class=\"sem_anzeige\">";

	// Anzeige Bereichsueberschrift
	if ($this->event->id == "")
	{
		$temp1 = JTEXT::_('COM_MATUKIO_NEW_EVENT');
		$temp2 = JTEXT::_('COM_MATUKIO_SUBMIT_NEW_EVENT');
	}
	else
	{
		$temp1 = JTEXT::_('COM_MATUKIO_EDIT_EVENT');
		$temp2 = JTEXT::_('COM_MATUKIO_CHANGE_INFORMATION');
	}

	MatukioHelperUtilsEvents::printHeading("$temp1", "$temp2");

	// Anzeige Eingabefelder

	echo "</table>";

	$html = MatukioHelperUtilsEvents::getEventEdit($this->event, 1, true);

	// Anzeige Funktionsknoepfe unten

	if (MatukioHelperSettings::getSettings('event_buttonposition', 2) > 0)
	{
		$html .= MatukioHelperUtilsEvents::getTableHeader(4) . "<tr>"
			. MatukioHelperUtilsEvents::getTableCell($knopfunten, 'd', 'c', '100%', 'sem_nav_d')
			. "</tr>" . MatukioHelperUtilsEvents::getTableHeader('e');
	}


	if ($this->event->published == "")
	{
		$html .= "\n<input type=\"hidden\" name=\"published\" value=\"1\" />";
	}
	else
	{
		$html .= "\n<input type=\"hidden\" name=\"published\" value=\"" . $this->event->published . "\" />";
	}


	$html .= "<input type=\"hidden\" name=\"id\" value=\"" . $this->event->id . "\" />";
	$html .= MatukioHelperUtilsEvents::getHiddenFormElements("", $this->catid, $this->search,
		$this->limit, $this->limitstart, 0, $this->dateid, -1
	);
	echo $html;
	?>

	<script type="text/javascript">
		jQuery("#btnSave").click(function(){
			if (jQuery('#adminForm').validationEngine('validate')) {
				jQuery("adminForm").submit();
			}
		});
	</script>

	<input type="hidden" name="option" value="com_matukio"/>
	<input type="hidden" name="view" value="createevent"/>
	<input type="hidden" name="controller" value="createevent"/>
	<input type="hidden" name="task" value="saveevent"/>
</form>
</div>
