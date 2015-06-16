<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.Development
 * @author    	Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * @package		com_contactenhanced
* @since		1.6
 */
class JFormFieldOrdering extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Ordering';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
		// Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		
		// Get some field values from the form.
		$contactId		= (int) $this->form->getValue('id');
		$categoryId		= (int) $this->form->getValue('catid');
		$categoryName	= $this->form->getValue('category');

		$table	= ($this->element['table'] ? $this->element['table'] : '#__ce_details');
		
		if($this->element['query']){
			$query	= $this->element['query'];
			$query	.= " FROM ".$table;
			if($categoryName){
				$query	.= " WHERE category = '".$categoryName."'";
			}
			$query	.= " ORDER BY ordering";
		}else{
			// Build the query for the ordering list.
			$query = 'SELECT ordering AS value, name AS text' .
					' FROM '.$table .
					' WHERE catid = ' . (int) $categoryId .
					' ORDER BY ordering';
		}

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('list.ordering', '', $query, trim($attr), $this->value, $contactId ? 0 : 1);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('list.ordering', $this->name, $query, trim($attr), $this->value, $contactId ? 0 : 1);
		}

		return implode($html);
	}
}