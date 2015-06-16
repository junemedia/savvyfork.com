<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Editor Article buton
 *
 * @package Editors-xtd
 * @since 1.5
 */
class plgButtonContactenhanced extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
		
		$lang =JFactory::getLanguage();
		$lang->load('com_contactenhanced');
		$lang->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
		$lang->load('plg_editors-xtd_contactenhanced',JPATH_ROOT.'/plugin/editors-xtd/contactenhanced');
	}


	/**
	 * Display the button
	 *
	 * @return array A four element array of (article_id, article_title, category_id, object)
	 */
	function onDisplay($name)
	{
		/*
		 * Javascript to insert the link
		 * View element calls jSelectArticle when an article is clicked
		 * jSelectArticle creates the link tag, sends it to the editor,
		 * and closes the select frame.
		 */
		if( 
			(is_array($this->params->get('avoid_components')) 
				AND in_array(JRequest::getVar('option'),$this->params->get('avoid_components',array())))
			OR (!is_array($this->params->get('avoid_components')) 
				AND JRequest::getVar('option') == 'com_contactenhanced')
		){
			return '';
		}
		
		
		$contentPlugin	= JPluginHelper::getPlugin('content','contactenhanced');
		if(isset($contentPlugin->params)){
			$registry = new JRegistry;
			$registry->loadString($contentPlugin->params);
			$contentPlugin->params	= $registry;
			$modalOptions	= 	'  type=|'			.	$contentPlugin->params->get('plg_display_type',	'modal OR embedded').'| '
								.' text=|'			.	$contentPlugin->params->get('plg_display_text',	'Text when type is modal').'| '
								.' modal_width=|'	.	$contentPlugin->params->get('window-size-width',800).'| '
								.' modal_height=|'	.	$contentPlugin->params->get('window-size-height',480).'| '
								.' modal_template=|'.	$contentPlugin->params->get('template','beez5').' (optional)| '
								;			
		}else{
			$modalOptions	= ' type=|modal OR embedded| text=|Text when type is modal|  modal_width=|800| modal_height=|500| modal_template=|beez5 (optional)| ';
		}
		
		$js = "
		function jSelectContact(id, title, catid, object) {
			var tag = '{loadcontact id=|'+id+'| form=|yes| map=|before_form OR after_form| details=|before_map OR after_map OR before_form OR after_form| show_contact_name=|3| show_contact_position=|0| image=|before_details OR after_details| ".$modalOptions." fields=|key1=value&key2=value2|  recipient=|someone@domain.com (optional)| }';
			jInsertEditorText(tag, '".$name."');
			SqueezeBox.close();
		}";
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);
		$doc->addStyleDeclaration(
			'.icon-contactenhanced:before{content:"\0022";color:#51A351}'		
			);
		JHTML::_('behavior.modal');

		/*
		 * Use the built-in element view to select the article.
		 * Currently uses blank class.
		 */
		$link = 'index.php?option=com_contactenhanced&amp;view=contacts&amp;layout=modal&amp;tmpl=component';

		$button = new JObject();
		$button->set('modal', true);
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_CONTACTENHANCED_BUTTON_CE'));
		$button->set('name', 'contactenhanced');
		$button->set('options', "{handler: 'iframe', size: {x: 770, y: 400}}");

		return $button;
	}
}
