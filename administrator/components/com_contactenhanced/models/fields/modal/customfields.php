<?php
/**

 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * @package		com_contactenhanced
* @since		1.6
 */
class JFormFieldModal_Customfields extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Customfields';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$lang	= JFactory::getLanguage();
		$lang->load('com_contactenhanced',JPATH_ADMINISTRATOR);
		$lang->load('com_contactenhanced',JPATH_ADMINISTRATOR.'/components/com_contactenhanced');
		
		// Load the javascript and css
		JHtml::_('behavior.framework',true);
		JHTML::_('behavior.modal', 'a.modal');
		
		// Build the script.
		$script = array();
		$script[] = '	function jSelectChart_'.$this->id.'(id, name, object) {';
		$script[] = '		document.id("'.$this->id.'_id").value = id;';
		$script[] = '		document.id("'.$this->id.'_name").value = name;';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		
		// Get the title of the linked chart
		$db = JFactory::getDBO();
		$query		= $db->getQuery(true);
		$query->select('name');
		$query->from('#__ce_cf');
		$query->where('id = '.$db->Quote($this->value));
		$db->setQuery($query);
		$title = $db->loadResult();

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
			$title = JText::_('COM_CONTACTENHANCED_SELECT_A_CUSTOMFIELD');
		}

		$link = 'index.php?option=com_contactenhanced&amp;view=customfields&amp;layout=modal&amp;tmpl=component&amp;function=jSelectChart_'.$this->id;

		
		$html = "\n".'<div class="fltlft"><input type="text" id="'.$this->id.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" 
						title="'.JText::_('COM_CONTACTENHANCED_CHANGE_CUSTOMFIELD_BUTTON').'"  
						href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
								.JText::_('COM_CONTACTENHANCED_CHANGE_CUSTOMFIELD_BUTTON').'</a></div></div>'
						."\n";
		// The active contact id field.
		if (0 == (int)$this->value) { 
			$value = '';
		} else {
			$value = (int)$this->value; 
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}
		
		$html .= '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

		return $html;
	}
}
