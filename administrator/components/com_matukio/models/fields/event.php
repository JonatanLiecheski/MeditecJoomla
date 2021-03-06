<?php
/**
 * @version    $Id: article.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright  Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');
require_once JPATH_ADMINISTRATOR . '/components/com_matukio/tables/matukio.php';

/**
 * Supports a modal article picker.
 *
 * @package           Joomla.Administrator
 * @subpackage        com_content
 * @since             1.6
 */
class JFormFieldModal_Event extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var                string
	 * @since        1.6
	 */
	protected $type = 'Modal_Event';

	/**
	 * Method to get the field input markup.
	 *
	 * @return   string  The field input markup.
	 * @since    1.6
	 */
	protected function getInput()
	{
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		$db        = JFactory::getDBO();
		$db->setQuery(
			'SELECT title' .
			' FROM #__matukio' .
			' WHERE id = ' . (int) $this->value
		);

		$title = $db->loadResult();

		if ($error = $db->getErrorMsg())
		{
			JError::raiseWarning(500, $error);
		}

		if (empty($title))
		{
			$title = JText::_('COM_MATUKIO_SELECT_EVENT');
		}

		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		//                $script = array();
		$script[] = '        function selectEvent(id, title, object) {';
		$script[] = '                document.id("' . $this->id . '_id").value = id;';
		$script[] = '                document.id("' . $this->id . '_name").value = title;';
		$script[] = '                SqueezeBox.close();';
		$script[] = '        }';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		$link = 'index.php?option=com_matukio&amp;view=eventlist&layout=element&amp;tmpl=component&amp;object=' . $this->name;

		$html[] = '<div class="fltlft">';
		$html[] = ' <input type="text" id="' . $this->id . '_name" value="' . $title . '" disabled="disabled" size="35" />';
		$html[] = '</div>';

		// The user select button.
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		$html[] = '        <a class="modal cjmodal" title="' . JText::_('COM_CONTENT_CHANGE_ARTICLE') . '"  href="'
			. $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">' . JText::_('COM_MATUKIO_SELECT_EVENT') . '</a>';
		$html[] = '  </div>';
		$html[] = '</div>';

		// The active article id field.
		if (0 == (int) $this->value)
		{
			$value = '';
		}
		else
		{
			$value = (int) $this->value;
		}

		// class='required' for client side validation
		$class = '';

		if ($this->required)
		{
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="' . $this->id . '_id"' . $class . ' name="' . $this->name . '" value="' . $value . '" />';

		return implode("\n", $html);
	}
}
