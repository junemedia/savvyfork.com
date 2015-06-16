<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

$lang = JFactory::getLanguage();
//Load English always, useful if file is partially translated
$lang->load('com_contactenhanced',		JPATH_ADMINISTRATOR.'/components/com_contactenhanced', 'en-GB');
$lang->load('com_contactenhanced',		JPATH_ADMINISTRATOR.'/components/com_contactenhanced',	null,	true);
$lang->load('com_contactenhanced.sys',	JPATH_ADMINISTRATOR.'/components/com_contactenhanced', 'en-GB');
$lang->load('com_contactenhanced.sys',	JPATH_ADMINISTRATOR.'/components/com_contactenhanced',	null,	true);
$lang->load('com_contactenhanced.menu',	JPATH_ADMINISTRATOR.'/components/com_contactenhanced', 'en-GB');
$lang->load('com_contactenhanced.menu',	JPATH_ADMINISTRATOR.'/components/com_contactenhanced',	null,	true);

/**
 * @package		com_contactenhanced
* @since		1.6
 */
class JFormFieldTitle extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Title';

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
		
		if(!defined('CE_PARAMHELPER_SCRIPT')){
			define('CE_PARAMHELPER_SCRIPT', 1);
			$document = JFactory::getDocument();
			$document->addScript(JURI::root(true).'/administrator/components/com_contactenhanced/assets/js/paramhelper.js');
			$document->addStyleSheet(JURI::root(true).'/administrator/components/com_contactenhanced/assets/css/paramhelper.css');
		}
		
		//Set label to blank
		$this->label = '&nbsp;';
		$html[]	= '<h4 id="'.$this->id.'" class="block-head clearfix">'.JText::_($this->element['label']).'</h4>';
		if($this->element['description']){
			$html[]	= '<div class="block-des">'.JText::_($this->element['description']).'</div>';
		}

		return implode($html);
	}
	
	/**
	 * Method to get the field label markup.
	 *
	 * @return	string	The field label markup.
	 * @since	1.6
	 */
	protected function getLabel()
	{
		return '';
	}
}