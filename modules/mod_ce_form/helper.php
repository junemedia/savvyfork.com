<?php
/**
 * @package		ContactEnhanced
 * @author		Douglas Machado {@link http://ideal.fok.com.br}
 * @author		Created on 24-Feb-2011
 * @copyright	Copyright (C) 2006 - 2011 iDealExtensions.com, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modCEFormHelper
{

	static function loadContact(&$pluginParams)
	{
		$app 	= JFactory::getApplication();
		$doc		= JFactory::getDocument();
		$lang	= JFactory::getLanguage();
		$lang->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
		$lang->load('com_contactenhanced');
		
		if(JRequest::getVar('msgsent') AND $pluginParams->get('after_submit','global') == 'javascript'){
			$doc->addScriptDeclaration("
window.addEvent('domready', function(){alert('".JText::_('COM_CONTACTENHANCED_EMAIL_THANKS')."')});
");
			
			if(!$pluginParams->get('after_submit-javascript-show_form')){
				return '';
			}
		}
		
		//get database
		require_once(JPATH_BASE .'/components/com_contactenhanced/defines.php');
		require_once(JPATH_BASE .'/components/com_contactenhanced/models/contact.php');
		require_once(JPATH_BASE .'/components/com_contactenhanced/customFields.class.php');
		require_once(JPATH_BASE .'/components/com_contactenhanced/helpers/helper.php');
		require_once(JPATH_BASE .'/components/com_contactenhanced/helpers/route.php');
		
		$doc->addStyleSheet(JURI::base(true).'/components/com_contactenhanced/assets/css/ce.css');
		
		$ceObj			= new JObject();
		$ceObj->params	= $app->getParams('com_contactenhanced');
		$ceObj->user	= JFactory::getUser();
		
		
		$style		= '';
		$contactId	= (int) $pluginParams->get('contactid');
		
		$model		= JModelLegacy::getInstance('Contact', 'ContactenhancedModel', array('ignore_request' => true));
		$model->setState('contact.id', $contactId);
		$model->setState('params', $ceObj->params);
		
		// query options
		$options['id']		= $contactId;
		$options['aid']		= $ceObj->user->get('aid', 0);
	
		$ceObj->contact		= $model->getItem( $contactId );
		
		$ceObj->item		= $ceObj->contact;
		if(!is_object($ceObj->contact)){
			return JText::sprintf('MOD_CE_FORM_THERE_IS_NO_CONTACT_ID',$contactId);
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
		
		JHtml::_('behavior.framework', true);
		JHTML::_('behavior.tooltip');
		
		JRequest::setVar( 'content_title',	ceHelper::encode($doc->getTitle() ) );
		JRequest::setVar( 'content_url',	ceHelper::encode(JURI::current() ) );
		
		$html 	= '<div class="ce-contact_email ce-module" '.$style.' id="ce-module-contact-'.$contactId.'">';

		if($pluginParams->get('link-module-title')){
			$contactLink	= ContactenchancedHelperRoute::getContactRoute($contactId,$ceObj->contact->catid );
			$doc->addScriptDeclaration("
window.addEvent('domready', function(){
	var modFormDiv	= document.id('ce-module-contact-".$contactId."');
	var modFormHeading = modFormDiv.getPrevious();
	modFormHeading.set('html','<a href=\"".$contactLink."\" title=\"".$ceObj->contact->name."\">'+modFormHeading.get('html')+'</a>');
});
");
		}
		
		if($pluginParams->get('introtext')){
				$html 	.= '<div class="ce-introtext">'.$pluginParams->get('introtext').'</div>';
		}
		
		
		if($pluginParams->get('after_submit','global') != 'global'){
			$router	= $app->getRouter();
			$vars	= $router->getVars(); 
			$vars['msgsent']	= 1;
			$ceObj->return = JRoute::_('index.php?'.JURI::buildQuery($vars));
		}
		
		if($pluginParams->get('show_map') == 'before_form' OR $pluginParams->get('show_map') == '1'){
			if($pluginParams->get('show_contact_details') == 'before_map'){
				$html	.= ceHelper::loadDetails($ceObj);
			}
			// Load map
			$html	.= ceHelper::loadMap($ceObj);
			
			if($pluginParams->get('show_contact_details') == 'after_map'){
				$html	.= modCEFormHelper::loadDetails($pluginParams,$ceObj);
			}
		}
		
		if($pluginParams->get('show_contact_details') == 'before_form' OR $pluginParams->get('show_contact_details') == '1'){
			$html	.= modCEFormHelper::loadDetails($pluginParams,$ceObj);
			
		}
		
		if($pluginParams->get('show_misc') == 'before_form'){
			$html	.= $ceObj->contact->misc;
		}
		
		if( $pluginParams->get('show_form',1) ){
			$html	.=ceHelper::loadForm($ceObj, 'plugin');
		}
		
		
		if($pluginParams->get('show_contact_details') == 'after_form' OR $pluginParams->get('show_contact_details') == '2'){
			$html	.= modCEFormHelper::loadDetails($pluginParams,$ceObj);
		}
		
		if($pluginParams->get('show_map') == 'after_form' OR $pluginParams->get('show_map') == '2'){
			if($pluginParams->get('show_contact_details') == 'before_map'){
				$html	.= modCEFormHelper::loadDetails($pluginParams,$ceObj);
			}
			// Load map
			$html	.= ceHelper::loadMap($ceObj);
			
			if($pluginParams->get('show_contact_details') == 'after_map'){
				$html	.= ceHelper::loadDetails($ceObj);
			}
		}
		if($pluginParams->get('show_misc') == 'end'){
			$html	.= $ceObj->contact->misc;
		}
		
		if($pluginParams->get('posttext')){
				$html 	.= '<div class="ce-posttext">'.$pluginParams->get('posttext').'</div>';
		}
		
		$html	.= '</div>';
		return $html;
	}

	static function loadDetails(&$pluginParams, &$ceObj){
		$html	= '';
		$html	.= ceHelper::loadDetails($ceObj);
		if($pluginParams->get('show_misc') == 'after_details'){
			$html	.= $ceObj->contact->misc;
		}
		return $html;
	}
	
}
