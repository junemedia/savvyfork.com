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
class JFormFieldModal_Contacts extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Contacts';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		$lang	= JFactory::getLanguage();
		$lang->load('com_contactenhanced',JPATH_ADMINISTRATOR.'/components/com_contactenhanced');
		
		// Load the javascript and css
		JHtml::_('behavior.framework');
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
		$query->from('#__ce_details');
		$query->where('id = '.$db->Quote($this->value));
		$db->setQuery($query);
		$title = $db->loadResult();

		try
		{
			$title = $db->loadResult();
		}
		catch (RuntimeException $e)
		{
			JError::raiseWarning(500, $e->getMessage);
		}

		if (empty($title))
		{
			$title = JText::_('COM_CONTACTENHANCED_SELECT_A_CONTACT');
		}

		$link = 'index.php?option=com_contactenhanced&amp;view=contacts&amp;layout=modal&amp;tmpl=component&amp;function=jSelectChart_'.$this->id;

		if (isset($this->element['language']))
		{
			$link .= '&amp;forcedLanguage='.$this->element['language'];
		}

		$html = "\n".'<div class="input-append"><input type="text" class="input-medium" id="'.$this->id.'_name" value="'.htmlspecialchars($title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /><a class="modal btn" title="'.JText::_('COM_CONTACTENHANCED_CHANGE_CONTACT_BUTTON').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-address hasTooltip" title="'.JText::_('COM_CONTACTENHANCED_CHANGE_CONTACT_BUTTON').'"></i> '.JText::_('JSELECT').'</a></div>'."\n";
		// The active contact id field.
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

		$html .= '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';
		
		return $html;
	}
}
