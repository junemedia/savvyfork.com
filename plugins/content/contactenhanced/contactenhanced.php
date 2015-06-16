<?php
/**
 * @package		ContactEnhanced
 * @author		Douglas Machado {@link http://ideal.fok.com.br}
 * @author		Created on 24-Feb-2011
 * @copyright	Copyright (C) 2006 - 2011 iDealExtensions.com, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Content Plugin
 *
 * @package		Joomla
 * @subpackage	Content
 * @since		1.5
 */
class plgContentContactenhanced extends JPlugin
{
	
	
	/**
	 * Changes the string {loadcontact id=|ID|} for the form by that ID
	 *
	 * Method is called by the view
	 *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The content object.  Note $article->text is also available
	 * @param	object	The content params
	 * @param	int		The 'page' number
	 * @since	1.6
	 */
	public function onContentPrepare($context, &$row, &$params, $limitstart=0)
	{
		$app = JFactory::getApplication();
				$lang =JFactory::getLanguage();
		$lang->load('com_contactenhanced');
		$lang->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
		$lang->load('plg_content_contactenhanced',JPATH_ROOT.'/plugin/content/contactenhanced');
		
		if($app->isAdmin() OR !strpos($row->text, 'loadcontact ')){
			//--The tag is not found in content - abort..
			return;
		}
		
		
	 	// expression to search for
	 	$regex = '/{loadcontact\s*.*?}/i';
	
	 	$pluginParams = $this->params;
	
		// check whether plugin has been unpublished
		if ( !$pluginParams->get( 'enabled', 1 ) ) {
			$row->text = preg_replace( $regex, '', $row->text );
			return true;
		}
	
	 	// find all instances of plugin and put in $matches
		preg_match_all( $regex, $row->text, $matches );
	
		// Number of plugins
	 	$count = count( $matches[0] );
	
	 	// plugin only processes if there are any instances of the plugin in the text
	 	if ( $count ) {
	 		$this->processContacts( $row, $matches, $count, $regex, $params );
		}
	}//function
	
	
	function processContacts ( &$row, &$matches, $count, $regex,&$params )
	{
	 	require_once(JPATH_ROOT .'/components/com_contactenhanced/helpers/helper.php');
	 	
	 	$lang =JFactory::getLanguage();
		$lang->load('com_contactenhanced',JPATH_SITE);
		$jinput = JFactory::getApplication()->input;
		
	 	$pluginParams = $this->params;
		
		for ( $i=0; $i < $count; $i++ )
		{
	 		$inline_params = str_replace( 'loadcontact', '', $matches[0][$i] );
	 		$inline_params = str_replace( '{', '', $inline_params );
	 		$inline_params = str_replace( '}', '', $inline_params );
	 		$inline_params = trim( $inline_params );
	 		
	 		if(strpos($inline_params, ' ') === false AND is_numeric($inline_params)){
	 			$contactId = trim( $inline_params );
	 		}else{
	 		$fields	= array();
	 			$searchfrase_matches	= 'all';
	 			$search_operator_match	= '=';
	 			$search_matches	= array();
	 			preg_match( "#search=\|(.*?)\|#s", $inline_params, $search_matches );
	 			if (isset($search_matches[1])){
	 				$search_matches[1]	= str_replace( '&amp;', '&', $search_matches[1] );
	 				// Get all Fields
	 				parse_str($search_matches[1], $fields);
	 				 
	 				// Get Search Operator option
	 				$search_operator_match = array();
	 				preg_match( "#search_operator=\|(.*?)\|#s", $inline_params, $search_operator_match );
	 				if (isset($search_operator_match[1])){
	 					$search_operator_match	= $search_operator_match[1];
	 				}else{
	 					$search_operator_match	= '=';
	 				}
	 				 
	 				// Get Search Operator option
	 				$searchfrase_matches = array();
	 				preg_match( "#searchfrase=\|(.*?)\|#s", $inline_params, $searchfrase_matches );
	 				if (isset($searchfrase_matches[1])){
	 					$searchfrase_matches	= $searchfrase_matches[1];
	 				}else{
	 					$searchfrase_matches	= 'all';
	 				}
	 			}

	 			// get ID
	        	$id_matches = array();
	        	preg_match( "#id=\|(.*?)\|#s", $inline_params, $id_matches );
	        	if (isset($id_matches[1]) AND is_string($id_matches[1]) AND $id_matches[1] == 'sequential'){
	        		$contactId = $this->getNextContactID( $fields, $search_operator_match, $searchfrase_matches);
	        	}elseif (isset($id_matches[1])){
	        		$contactId = trim( ($id_matches[1]) );
	        	}else{
	        		$contactId	= explode(" ",$inline_params);
	        		if(!is_numeric($contactId)){
	        			$contactId = '';
	        		}
	        	}
	        	
	        	
	 			$detail_matches = array();
	 			preg_match( "#details=\|(.*?)\|#s", $inline_params, $detail_matches );
	        	if (isset($detail_matches[1]) ){
	        		$pluginParams->set('show_contact_details',$detail_matches[1]);
	        	}
	        	
	 			$recipient_matches = array();
	 			preg_match( "#recipient=\|(.*?)\|#s", $inline_params, $recipient_matches );
	        	if (isset($recipient_matches[1]) AND ceHelper::isEmailAddress($recipient_matches[1])){
	        		$session 	= JFactory::getSession(); // Get the session
	        		$session->set(JApplication::getHash($secret.$recipient_matches[1]), $recipient_matches[1]); // Store the emails in the session using a key
	        		$recipient	= JApplication::getHash($secret.$recipient_matches[1]);
	        		JRequest::setVar('encodedrecipient',$recipient);
	        	}
	        	
		 		$image_matches = array();
	 			preg_match( "#image=\|(.*?)\|#s", $inline_params, $image_matches );
	        	if (isset($image_matches[1])){
	        		$pluginParams->set('show_image',$image_matches[1]);
	        	}
	        	
	 			$contact_name_matches = array();
	 			preg_match( "#show_contact_name=\|(.*?)\|#s", $inline_params, $contact_name_matches );
	        	if (isset($contact_name_matches[1]) AND $contact_name_matches[1] > 0){
	        		$pluginParams->set('show_contact_name',$contact_name_matches[1]);
	        	}
	        	
	 			$contact_position_matches = array();
	 			preg_match( "#show_contact_position=\|(.*?)\|#s", $inline_params, $contact_position_matches );
	 			if (isset($contact_position_matches[1]) AND (strtolower($contact_position_matches[1]) == 'yes' OR intval($contact_position_matches[1]) == 1) ){
	        		$pluginParams->set('show_contact_position',true);
	        	}
	        	
		 		// Get Type option
	 			$type_matches = array();
	        	preg_match( "#type=\|(.*?)\|#s", $inline_params, $type_matches );
	        	if (isset($type_matches[1]) AND $type_matches[1] == 'modal'){
		        	$pluginParams->set('plg_display_type','modal');
	        		// Get Text option
		 			$text_matches = array();
		        	preg_match( "#text=\|(.*?)\|#s", $inline_params, $text_matches );
		        	if (isset($text_matches[1])){
		        		$pluginParams->set('plg_display_text', trim($text_matches[1]));
		        	}
	        		$width_matches = array();
		        	preg_match( "#modal_width=\|(.*?)\|#s", $inline_params, $width_matches );
		        	if (isset($width_matches[1])){
		        		$this->params->set('window-size-width', trim($width_matches[1]));
		        	}
	        		$height_matches = array();
		        	preg_match( "#modal_height=\|(.*?)\|#s", $inline_params, $height_matches );
		        	if (isset($height_matches[1])){
		        		$this->params->set('window-size-height', trim($height_matches[1]));
		        	}
	        		$template_matches = array();
		        	preg_match( "#modal_template=\|(.*?)\|#s", $inline_params, $template_matches );
		        	if (isset($template_matches[1])){
		        		jimport('joomla.filesystem.folder');
		        		if (JFolder::exists(JPATH_ROOT.'/templates/'.$template_matches[1])) {
		        			$this->params->set('template', trim($template_matches[1]));
		        		}
		        	}
	        	}

	        	// Get Map option
	 			$map_matches = array();
	        	preg_match( "#map=\|(.*?)\|#s", $inline_params, $map_matches );
	        	if (isset($map_matches[1])){
	        		$pluginParams->set('show_map',$map_matches[1]);
	        	}
	        	
	 			$form_matches = array();
	        	preg_match( "#form=\|(.*?)\|#s", $inline_params, $form_matches );
	        	if (isset($form_matches[1])){
	        		$pluginParams->set('show_form',$form_matches[1]);
	        	}
	        	
	        	$custom_fields	= '';
	 			$field_matches	= array();
	        	preg_match( "#fields=\|(.*?)\|#s", $inline_params, $field_matches );
	        	if (isset($field_matches[1])){
	        		
	        		$field_matches[1]	= str_replace( '&amp;', '&', $field_matches[1] );
	        		
	        		// Get all Fields
	        		$custom_fields	= array();
	        		parse_str($field_matches[1], $custom_fields);
	        		
	        		//Encode email field
	        		if(isset($custom_fields['recipient']) AND $custom_fields['recipient']){
	        			$session 	= JFactory::getSession(); // Get the session
	        			$session->set(JApplication::getHash($secret.$recipient_matches[1]), $recipient_matches[1]); // Store the emails in the session using a key
	        			$custom_fields['encodedrecipient']	= JApplication::getHash($secret.$recipient_matches[1]);
	        		}
	        		if($pluginParams->get('plg_display_type') == 'modal'){
	        			$pluginParams->set('plg_modal_fields', http_build_query($custom_fields));
	        		}else{
	        			foreach ($custom_fields as $key => $value) {
	        				$jinput->set($key, $value);
	        			}
	        		}
	        	}
	        	
	 			if (isset($search_matches[1]) AND (empty($contactId))){
	        		$contactId	= $this->getContactID( $fields, $search_operator_match, $searchfrase_matches);
	        	}	
	 		}
	 		
	 		//echo '<pre>'; print_r($pluginParams); echo '</pre>'; exit;
	 		$contact= $this->loadContact( $contactId,$pluginParams,$row );
	 		$row->text 	= str_replace($matches[0][$i], $contact, $row->text );
	 	}
	
	  	// removes tags without matchings 
		$row->text = preg_replace( $regex, '', $row->text );
	}
	
	function loadContact( &$contactId,&$pluginParams,&$row)
	{
		
		require_once(JPATH_ROOT .'/components/com_contactenhanced/defines.php');
		require_once(JPATH_ROOT .'/components/com_contactenhanced/models/contact.php');
		require_once(JPATH_ROOT .'/components/com_contactenhanced/customFields.class.php');
		
		$document		= JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_contactenhanced/assets/css/ce.css');
		
		$app = JFactory::getApplication();
		
		
		$ceObj			= new JObject();
		$ceObj->params	= $app->getParams('com_contactenhanced');
		$ceObj->user	= JFactory::getUser();
		
		if($pluginParams->get('contact_postion')){
			$style	= ' style="float:'.$pluginParams->get('contact_postion').'; '.$pluginParams->get('contact_layer_style').'" ';
		}else{
			$style	= '';
		}
	
		$model		= JModelLegacy::getInstance('Contact', 'ContactenhancedModel', array('ignore_request' => true));
		$model->setState('contact.id', $contactId);
		$model->setState('params', $ceObj->params);
		
		// query options
		$options['id']		= $contactId;
		$options['aid']		= $ceObj->user->get('aid', 0);
	
		$ceObj->contact		= $model->getItem( $contactId );
		$ceObj->item		= &$ceObj->contact;
		if(!is_object($ceObj->contact)){
			return JText::sprintf('CE_PLUGIN_THERE_IS_NO_CONTACT_ID',$contactId);
		}
		$ceObj->customfields= $model->getCustomFields( $ceObj->contact->catid);
	//echo 'Test: <pre>'; print_r($ceObj->contact); exit;	 
		// Adds parameter handling
		$registry	= new JRegistry();
		$registry->loadString($ceObj->contact->params);
		
		$ceObj->contact->params = $registry;
	
		$ceObj->params->merge($ceObj->contact->params);
		$ceObj->params->merge($pluginParams);
		$ceObj->params->set('plugin_active',1);
		
		JRequest::setVar('plugin_load_method','embedded');
		
		$this->contact	= &$ceObj->contact;
		$this->params->merge($ceObj->params);
		
		JHtml::_('behavior.framework');
		JHTML::_('behavior.tooltip');
		
		if($pluginParams->get('plg_display_type' ) == 'modal'){
			JHTML::_('behavior.modal', 'a.modal');
			$link	= JURI::root().('index.php?option=com_contactenhanced&amp;view=contact&amp;id='
						.$contactId
						.( isset($row->title) ? '&amp;content_title='.ceHelper::encode($row->title) : '')
						.'&amp;content_url='.ceHelper::encode(ceHelper::getCurrentURL())
						.( JRequest::getVar('encodedrecipient')	? '&amp;encodedrecipient='.JRequest::getVar('encodedrecipient')	: '')
						.($this->params->get('modal',1)		? '&amp;tmpl=component' : '') 
						.($this->params->get('template')	? '&amp;template='.$this->params->get('template')	: '')
						.(is_string($pluginParams->get('plg_modal_fields')) ? '&amp;'.$pluginParams->get('plg_modal_fields') : '')
						.'&amp;plugin_load_method=modal'
					);
			$attributes	= array();
			$attributes['rel']	= "{handler:'iframe', size: {x:".$this->params->get('window-size-width',800).", y:".$this->params->get('window-size-height',480)."}}";
			if(strpos($pluginParams->get('plg_display_text'),'<') == 0){
				$attributes['title']= ($ceObj->contact->name);
			}else{
				$attributes['title']= $pluginParams->get('plg_display_text',$ceObj->contact->name);
			}
			$attributes['class']= 'modal ce-modal'; 
			return JHtml::_('link',$link, ($pluginParams->get('plg_display_text',$ceObj->contact->name) ),$attributes );
		}else{
			if (isset($row->title)) {
				JRequest::setVar('content_title',ceHelper::encode($row->title));
			}else{
				JRequest::setVar('content_title',ceHelper::encode($document->getTitle()));
			}
			JRequest::setVar('content_url',ceHelper::encode(ceHelper::getCurrentURL()));
			
			$html 	= '<div class="ce-contact_email" '.$style.'>';
			
			if($pluginParams->get('show_contact_name')){
				$html	.= '<h'.$pluginParams->get('show_contact_name','3').' class="contact-name">'
							.$ceObj->contact->name
							.'</h'.$pluginParams->get('show_contact_name','3').'>';
			}
			
			if($pluginParams->get('show_contact_position') AND $ceObj->contact->con_position){
				$html	.= '<small class="contact-position">'.$ceObj->contact->con_position.'</small>';
			}
			
			// In case form is sent and plugin setup to show thank you message when submitted
			if(JRequest::getVar( 'msgsent' ) AND $pluginParams->get('after_submit') == 'thankyoumessage'){
		 			$html 	.= '<div class="ce-plugin-message">'.JText::_('PLUGIN_THANK_YOU_MESSAGE').'</div>';
		 			$html 	.= '</div>';
		 			return $html;
			}
			
			if($pluginParams->get('after_submit','global') != 'global'){
				$router	= $app->getRouter();
				$vars	= $router->getVars(); 
				$vars['msgsent']	= 1;
				$ceObj->return = JRoute::_('index.php?'.JURI::buildQuery($vars));
				//echo $ceObj->return; exit;
			}
			
			
			
			if($pluginParams->get('show_map') == 'before_form' OR $pluginParams->get('show_map') == '1'){
				if($pluginParams->get('show_contact_details') == 'before_map'){
					$html	.= ceHelper::loadDetails($ceObj);
				}
				// Load map
				$html	.= ceHelper::loadMap($ceObj);
				
				if($pluginParams->get('show_contact_details') == 'after_map'){
					$html	.= ceHelper::loadDetails($ceObj);
				}
			}
			
			if($pluginParams->get('show_contact_details') == 'before_form' OR $pluginParams->get('show_contact_details') == '1'){
				$html	.= ceHelper::loadDetails($ceObj);
			}
			
			// FORM
			if($pluginParams->get('show_form') != '0' AND $pluginParams->get('show_form') != 'no'){
				$html	.=ceHelper::loadForm($ceObj, 'plugin');
			}
			
			
			if($pluginParams->get('show_contact_details') == 'after_form' OR $pluginParams->get('show_contact_details') == '2'){
				$html	.= ceHelper::loadDetails($ceObj);
			}
			
			if($pluginParams->get('show_map') == 'after_form' OR $pluginParams->get('show_map') == '2'){
				if($pluginParams->get('show_contact_details') == 'before_map'){
					$html	.= ceHelper::loadDetails($ceObj);
				}
				// Load map
				$html	.= ceHelper::loadMap($ceObj);
				
				if($pluginParams->get('show_contact_details') == 'after_map'){
					$html	.= ceHelper::loadDetails($ceObj);
				}
			}
			$html	.= '</div>';
		}
		
		return $html;
	}
	
	/**
	 * Get Contact ID based on search fields
	 * @param array	 $fields
	 * @param string $search_operator =, <>, >, <
	 * @param string $searchphrase, all, any
	 */
	public function getContactID($fields, $search_operator = '=',$searchphrase= 'all'){
		// Create a new query object.
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('a.id');
		$query->from('`#__ce_details` AS a');
		$query->where('a.published = 1');
	
		$wildCard	= (strtoupper($search_operator) == 'LIKE' ? '%' : '');
	
		$wheres	= array();
		foreach ($fields as $key => $value) {
			$wheres[]	= $key.' '.$search_operator.' '.$db->Quote($wildCard.$value.$wildCard);
		}
		if (JLanguageMultilang::isEnabled()) {
			$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}
		
		$query->where( '(' . implode(($searchphrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')' );
		$query->order('a.ordering ASC');
		$db->setQuery($query);
		return $db->loadResult();
	}
	/**
	 * Get Contact ID based on search fields
	 * @param array	 $fields
	 * @param string $search_operator =, <>, >, <
	 * @param string $searchphrase, all, any
	 */
	public function getNextContactID($fields, $search_operator = '=',$searchphrase= 'all'){
		// Create a new query object.
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
	
		$subQuery = $db->getQuery(true)
		->select('contact_id')
		->from('#__ce_messages AS m')
		->order('id DESC');
		$db->setQuery($subQuery);
		// gets the last contact id
		if ($lastContactID = $db->loadResult()) {
			$query->where('a.id > '.$lastContactID);
		}else{
			return $this->getContactID( $fields, $search_operator, $searchphrase);
		}
	
		$query->select('a.id');
		$query->from('`#__ce_details` AS a');
		$query->where('a.published = 1');
	
	
		$wildCard	= (strtoupper($search_operator) == 'LIKE' ? '%' : '');
	
		$wheres	= array();
		foreach ($fields as $key => $value) {
			$wheres[]	= $key.' '.$search_operator.' '.$db->Quote($wildCard.$value.$wildCard);
		}
		$query->where( '(' . implode(($searchphrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')' );
		
		if (JLanguageMultilang::isEnabled()) {
			$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
		}
		
		$query->order('a.ordering ASC');
		$db->setQuery($query);
	
		if ($nextContactID = $db->loadResult()) {
			return $nextContactID;
		}else{
			return $this->getContactID( $fields, $search_operator, $searchphrase);
		}
	}
}