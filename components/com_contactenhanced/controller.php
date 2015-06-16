<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * Contact Component Controller
 *
 * @package		com_contactenhanced
* @since 1.5
 */
class ContactenhancedController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = true, $urlparams = false)
	{

		// Get the document object.
		$document = JFactory::getDocument();
		$document->addStylesheet(JURI::base(true).'/components/com_contactenhanced/assets/css/ce.css');
		
		// Set the default view name and format from the Request.
		$vName		= JRequest::getWord('view', 'categories');
		JRequest::setVar('view', $vName);
		
		if($vName == 'search' OR $vName == 'contact'){
			$cachable	= false;
		}

		$user = JFactory::getUser();
		
		$safeurlparams = array('catid'=>'INT','id'=>'INT','cid'=>'ARRAY','year'=>'INT','month'=>'INT','limit'=>'INT','limitstart'=>'INT',
			'showall'=>'INT','return'=>'BASE64','filter'=>'STRING','filter_order'=>'CMD','filter_order_Dir'=>'CMD',
			'filter-search'=>'STRING','print'=>'BOOLEAN','lang'=>'CMD','q'=>'STRING');

		parent::display($cachable,$safeurlparams);

		return $this;
	}
	/**
	 * Creates cookies (actually session variable) from $_POST variable, 
	 * this way we can retrieve the information in case the captcha code was wrong
	 * @param	int		Unix time;
	 * @author	Douglas Machado {@link http://ideal.fok.com.br}
	 */
	function createCookiesFromPost(){
		$session 	=JFactory::getSession();
		$ce_session	= $session->get('com_contactenhanced');
		if(!is_array($ce_session)){
			$ce_session	= array();
		}
		if(isset($ce_session['fieldValues'])){
			$ce_session['fieldValues'] = array();
		}
		
		$post	= JRequest::get('post');
		
		foreach($post as $form_field => $field_value){
			if( is_array($field_value) ){
				for($i=0; $i < count($field_value); $i++){
					if( array_key_exists('value',$field_value) ){
						
					}else{
						if(isset($field_value[$i])){
							$ce_session['fieldValues'][$form_field."_".$i] = $field_value[$i];
						}
					}
				}
			}
			$ce_session['fieldValues'][$form_field] = $field_value;
		}
		
		$session->set('com_contactenhanced', $ce_session);
	}

	function detroyPostCookies(){
		$session 	=JFactory::getSession();
		$ce_session	= $session->get('com_contactenhanced');
		if(!is_array($ce_session)){
			// there is not session, 
			return true;
		}
		$ce_session['fieldValues'] = null;
		$session->set('com_contactenhanced', $ce_session);
		return true;
	}
	
	
	function downloadAttachment($download_mode='attachment'){
		$file		= JRequest::getVar('file');
		$file		= ceHelper::decode($file);
		ceHelper::download($file,$download_mode);
	}

	function viewAttachment(){
		$this->downloadAttachment('inline');
	}

	// 	Get the article name
	function checkUsername(){
		$username	= JRequest::getVar('username');
		$action = 'error';
		$row	= true;
		$msg 	= JText::sprintf('CE_CF_USERNAME_IS_NOT_VALID',$username);
		$class	= 'invalid';
		
		$pattern= '/^[a-z\d_.]{3,20}$/i';
		if(preg_match($pattern, $username)){
			$db =JFactory::getDBO();
			$query	= $db->getQuery(true);
			$query->select('id');
			$query->from('#__users');
			$query->where('username = '.$db->Quote($username));
		
			$db->setQuery($query);
			$row = $db->loadResult();
		}
		// There is no registered user by this username 
		if ( !$row ) {
			if(JRequest::getVar('registration')){
				$action = 'success';
				$class	= 'success';
				$msg = JText::sprintf('CE_CF_USERNAME_IS_AVAILABLE',$username);
			}else{
				// user claims to be registered but is not
				$action = 'error';
				$class	= 'invalid';
				$msg = JText::sprintf('CE_CF_USERNAME_IS_NOT_VALID',$username);
			}
		}else{
			if(JRequest::getVar('registration')){
				$action = 'error';
				$class	= 'invalid';
				$msg = JText::sprintf('CE_CF_USERNAME_IS_NOT_AVAILABLE',$username);
			}else{
				$action = 'success';
				$class	= 'success';
				$msg = JText::sprintf('CE_CF_USERNAME_IS_VALID',$username);
			}
		}
		$json	=array('action'=> $action, 'msg' => $msg, 'class' => $class );
		$this->jsonReturn($json);
	}
	/**
	 * Method to check whether the email provided is valid or not 
	 */
	function checkEmail(){
		$json	= ceHelper::checkEmail(JRequest::getVar('email'));
		$this->jsonReturn($json);
	}
	
	function getChainSelect(){
		$config		=JFactory::getConfig();

		JRequest::setVar('tmpl','component');
		//decode incoming JSON string
		//$jsonRequest = json_decode((JRequest::getVar('json')));
		// Decode twice to avoid problem under some servers
		if(	JRequest::getVar('fieldID') ){ //AND JRequest::getVar('selectedOption')
			$db =JFactory::getDBO();
			$query	= $db->getQuery(true);
			$query->select('params,value');
			$query->from('#__ce_cf');
			$query->where('alias = '.$db->Quote(JRequest::getVar('fieldID')));
			
			$db->setQuery($query);
			$obj = $db->loadObject();
				
			$user	= JFactory::getUser();

			$regex = '/{user_id}/i';
			$obj->value  = preg_replace( $regex, $user->id, 		$obj->value );
				
			$regex = '/{user_email}/i';
			$obj->value  = preg_replace( $regex, $user->email, 	$obj->value );
				
			$regex = '/{username}/i';
			$obj->value  = preg_replace( $regex, $user->username,	$obj->value );

			$regex = '/{selectresult}/i';
			$obj->value  = preg_replace( $regex, JRequest::getVar('selectedOption','') ,$obj->value );
			
			$db->setQuery($obj->value);
			$row = $db->loadAssocList();
			
			$registry	= new JRegistry();
			$registry->loadString($obj->params);
			if ( is_array($row)) {
				$obj->params = $registry;
				$option	= array();
				$option[0]['value']	= '';
				$option[0]['text']	= $obj->params->get('chain_select-enabled-first_option', JText::_('Please select one'));
				$row	= array_merge($option,$row);
				$row	= JHtml::_('select.options', $row);
			}
		}
		if ( isset($row) AND count($row) >= 1 ) {
			$json	= array('action'=> 'success', 'value'=> $row);
			$this->jsonReturn($json);
		}else{
			$json	=array('action' =>'error');
			$this->jsonReturn($json);
		}
	}
	
	function jsonReturn(&$json){
		header('Content-type: application/json'); 
		echo json_encode( $json ); exit();
	}
	
	function file_upload_error_message($error_code) {
		switch ($error_code) {
			case UPLOAD_ERR_INI_SIZE:
				return JText::_('The uploaded file exceeds the upload_max_filesize directive'); // in php.ini
			case UPLOAD_ERR_FORM_SIZE:
				return JText::_('The uploaded file exceeds the MAX_FILE_SIZE directive'); // that was specified in the HTML form
			case UPLOAD_ERR_PARTIAL:
				return JText::_('The uploaded file was only partially uploaded');
			case UPLOAD_ERR_NO_FILE:
				return JText::_('No file was uploaded');
			case UPLOAD_ERR_NO_TMP_DIR:
				return JText::_('Missing a temporary folder');
			case UPLOAD_ERR_CANT_WRITE:
				return JText::_('Failed to write file to disk');
			case UPLOAD_ERR_EXTENSION:
				return JText::_('File upload stopped by extension');
			default:
				return JText::_('Unknown upload error');
		}
	}
	
/**
	 * Method to output a vCard
	 *
	 * @static
	 * @since 1.0
	 */
	function vCardJson(&$contact)
	{
		$mainframe	= JFactory::getApplication();
		
		//decode incoming JSON string
		$jsonRequest = json_decode(json_decode(JRequest::getVar('json')));
		$action = 'error';
		
		// Initialize some variables
		$db = JFactory::getDBO();

		$SiteName = $mainframe->getCfg('sitename');
		
		$user =JFactory::getUser();

		// Get the contact detail parameters
		$params	= new JRegistry();
		$params->loadString($contact->params);
		$accessLevels	= $user->getAuthorisedViewLevels();
		
		// Show the Vcard if contact parameter indicates (prevents direct access)
		if (($params->get('qr', 0)) && in_array($contact->access, $accessLevels))
		{
			// Create a new vcard object and populate the fields
			require_once(JPATH_ADMINISTRATOR.'/components/com_contactenhanced/helpers/vcard.php');
			$v = new JvCard();

			$v->setPhoneNumber($contact->telephone, 'PREF;WORK;VOICE');
			$v->setPhoneNumber($contact->fax, 'WORK;FAX');
			$v->setName($contact->surname, $contact->firstname, $contact->middlename, '');
			$v->setAddress('', '', $contact->address, $contact->suburb, $contact->state, $contact->postcode, $contact->country, 'WORK;POSTAL');
			$v->setEmail($contact->email_to);
			$v->setNote($contact->misc);
			$v->setURL( JURI::base(), 'WORK');
			$v->setTitle($contact->con_position);
			$v->setOrg(html_entity_decode($SiteName, ENT_COMPAT, 'UTF-8'));

			$output = $v->getVCard(html_entity_decode($SiteName, ENT_COMPAT, 'UTF-8'));
			$json	=array('action'=> 'success', 'code' => urlencode($output ));
			
		} else {
			$json	=array('action'=> $action, 'code' => 'User does not have permission' );
		}
		$this->jsonReturn($json);
	}
	
	public function getQRCode() {
		
		$contactId		= JRequest::getVar('id',0,'get');
		$app			= JFactory::getApplication();
		$SiteName		= $app->getCfg('sitename');
		$action 		= 'error';
		$code			= '';
		
		require_once(JPATH_BASE .'/components/com_contactenhanced/models/contact.php');
		$model		= JModelLegacy::getInstance('Contact', 'ContactenhancedModel', array('ignore_request' => true));
		$model->setState('contact.id', $contactId);
		$model->setState('params', $app->getParams('com_contactenhanced'));
		

		$contact	= $model->getItem( $contactId );
		
		//Begin Sanity check
		// In case there is no email address, check if it is linked to a user 
		if(strlen($contact->email_to) < 4 AND $contact->user_id > 0){
			$user			=JFactory::getUser($contact->user_id);
			$contact->email_to = $user->email;
		}
		if(strlen($contact->webpage) < 4 ){
			$contact->webpage= JURI::root();
		}
		
		// Parse the contact name field and build the nam information for the vcard.
		$contact->firstname	= null;
		$contact->middlename= null;
		$contact->surname	= null;
	
		// How many parts do we have?
		$parts = explode(' ', $contact->name);
		$count = count($parts);

		switch ($count) {
			case 1 :
				// only a first name
				$contact->firstname = $parts[0];
				break;

			case 2 :
				// first and last name
				$contact->firstname = $parts[0];
				$contact->surname = $parts[1];
				break;

			default :
				// we have full name info
				$contact->firstname = $parts[0];
				$contact->surname = $parts[$count -1];
				for ($i = 1; $i < $count -1; $i ++) {
					$contact->middlename .= $parts[$i].' ';
				}
				break;
		}
		// quick cleanup for the middlename value
		$contact->middlename = trim($contact->middlename);
		

		
		$body		= JText::sprintf('COM_CONTACTENHANCED_QR_CODE_EMAIL_MSG_BODY', $SiteName, JURI::root(), $contact->webpage, $contact->name);
		$subject	= JText::sprintf('COM_CONTACTENHANCED_QR_CODE_EMAIL_MSG_SUBJECT', $SiteName,$contact->webpage, $contact->name);
		
		switch (strtolower(JRequest::getVar('opt','mecard','get'))){
			case 'email':
				if(strlen($contact->email_to) > 4 ){
					$code	= $contact->email_to;
					$action	= 'success';
				}
			break;
			
			case 'emailto':
				if(strlen($contact->email_to) > 4 ){
					$code	= "mailto:".$contact->email_to.'?subject='.$subject.'&body='.$body;
					$action	= 'success';
				} 
			break;
			
			case 'email_matmsg':
				if(strlen($contact->email_to) > 4 ){
					$code	= 'MATMSG:TO:'.$contact->email_to.';SUB:'.$subject.';BODY:'.$body.';;';
					$action	= 'success';
				}
			break;
			
			case 'sms':
				if(strlen($contact->mobile) > 4 ){
					$code	= 'sms:'.JText::sprintf('COM_CONTACTENHANCED_QR_CODE_SMS_CODE',$contact->mobile) ;
					$action	= 'success';
				}elseif(strlen($contact->telephone) > 4 ){
					$code	= 'sms:'.JText::sprintf('COM_CONTACTENHANCED_QR_CODE_SMS_CODE',$contact->telephone) ;
					$action	= 'success';
				}
			break;
			
			
			
			case 'telephone':
				if(strlen($contact->telephone) > 4 ){
					$code	= 'tel:'.$contact->telephone ;
					$action	= 'success';
				}
			break;
			case 'mobile':
				if(strlen($contact->mobile) > 4 ){
					$code	= 'tel:'.$contact->mobile ;
					$action	= 'success';
				}
			break;
			case 'mecard':
					$address = array( $contact->address, $contact->suburb, $contact->state, $contact->postcode, $contact->country);
					for ($i=0;$i<count($address); $i++){
						if(strlen($address[$i]) < 1){
							unset($address[$i]);
						}
					}
					$address= ',,'.implode(",", $address);
					$name	= ($contact->surname ? $contact->surname .', '. $contact->firstname .($contact->middlename ? ' '.$contact->middlename : '') : $contact->firstname);
					$name	= $contact->name;
					$code	= 'MECARD:N:' . $name  
							.';ADR:'.$address
							.';TEL:'.($contact->telephone)
							.';TEL-AV:'.$contact->mobile
							.';EMAIL:'.$contact->email_to
							.';URL:'.$contact->webpage.';;';
					$action	= 'success';
			break;
			
			case 'bizcard':
					$address = array( $contact->address, $contact->suburb, $contact->state, $contact->postcode, $contact->country);
					for ($i=0;$i<count($address); $i++){
						if(strlen($address[$i]) < 1){
							unset($address[$i]);
						}
					}
					$address= implode("\n", $address);
					$code	= 'BIZCARD:N:'. $contact->firstname 
							.';X:'.($contact->surname ? $contact->surname : '') 
							.';T:'.$contact->con_position
							.';C:'.$SiteName
							.';A:'.$address
							.';B:'.($contact->telephone ? $contact->telephone : $contact->mobile)
							.';E:'.$contact->email_to.';;';
					$action	= 'success';
			break;
			
			case 'vcard':
				$this->vCardJson($contact);
			break;
			
			case 'url':
			case 'website':
				$code	= ''.$contact->webpage ;
				$action	= 'success';
			break;
			case 'urlto':
				$code	= 'URLTO:'.$contact->webpage ; 
				$action	= 'success';
			break;
			case 'url_mebkm':
				$code	= 'MEBKM:TITLE:'. JText::sprintf('COM_CONTACTENHANCED_QR_CODE_MEBKM_TITLE',$contact->webpage,$contact->name) .';URL:'.$contact->webpage.';;';
				$action	= 'success';
			break;
			
			// @todo: Fix this
			case 'android_market_lookup':
				// android: search  market://search?q=pub:"Charles Chen"
				$code	= 'market://search?q=pub:"' . $contact->name . '"';
				$action	= 'success';
			break;
			// @todo: Fix this
			case 'android_market_direct':
				// android: direct link market://search?q=pname:com.google.zxing.client.android
				$code	= 'market://search?q=pname:"' . $contact->name . '"';
				$action	= 'success';
			break;
			case 'wifi':
					// WIFI:T:WPA;S:mynetwork;P:mypass;;
				$code	= 'WIFI:T:'.$contact->extra_field_1.';S:'.$contact->extra_field_2.';P:'.$contact->extra_field_3.';;';
				$action	= 'success';
			break;
			
			case 'geo':
				if(strlen($contact->lat) > 1 AND strlen($contact->lng) > 1 ){
					$code	= 'geo:'.$contact->lat.','.$contact->lng;
					$action	= 'success';
				}
			break;
			case 'extra_field_1':
				if(strlen($contact->extra_field_1) > 7 ){
					$code	= $contact->extra_field_1;
					$action	= 'success';
				}
			break;
			case 'extra_field_2':
				if(strlen($contact->extra_field_2) > 7 ){
					$code	= $contact->extra_field_2;
					$action	= 'success';
				}
			break;
			case 'extra_field_3':
				if(strlen($contact->extra_field_3) > 7 ){
					$code	= $contact->extra_field_3;
					$action	= 'success';
				}
			break;
			case 'extra_field_4':
				if(strlen($contact->extra_field_4) > 7 ){
					$code	= $contact->extra_field_4;
					$action	= 'success';
				}
			break;
			case 'extra_field_5':
				if(strlen($contact->extra_field_5) > 7 ){
					$code	= $contact->extra_field_5;
					$action	= 'success';
				}
			break;
		}
		$json	=array('action'=> $action, 'code' => urlencode($code ));
		$this->jsonReturn($json);
	}
	

	function jsonGetContactDeatails(){
		$contactDetails	= ceHelper::getContactDetails();
		$this->jsonReturn($contactDetails);
	}
	
	function jsonExecuteCF(){
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from('#__ce_cf');
		$query->where('id = '.$db->Quote(JRequest::getVar('cf')));
		
		$db->setQuery($query);
		$cf		= $db->loadObject();
		
		jimport('joomla.filesystem.file');
		require_once( JPATH_ROOT.'/components/com_contactenhanced/customFields.class.php' );
		
		$fieldClass		= "ceFieldType_".$cf->type;
		$registry = new JRegistry;
		$registry->loadString($cf->params);
		$cf->params		= $registry;
		$fieldObject	= new $fieldClass($cf, $cf->params);
		
		$this->jsonReturn($fieldObject->jsonExecute());
	}
}
