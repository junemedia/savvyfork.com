<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * @package	com_contactenhanced
 * @since	1.6
 */
class ContactenhancedControllerMessages extends JControllerAdmin
{
	function __construct($config) {
		//echo ceHelper::print_r($_POST); exit;
		parent::__construct($config);
	}
	/**
	 * Proxy for getModel
	 * @since	1.6
	 */
	function getModel($name = 'Messages', $prefix = 'ContactenhancedModel', $config = array())
	{
		$tasks = array('saveorder','publish','unpublish','archive', 'trash','report', 'orderup', 'orderdown', 'delete');
		if( in_array($this->getTask(), $tasks) ){
			$model = parent::getModel('Message', $prefix, array('ignore_request' => true));
		}else{
			$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		}

		return $model;
	}

	function saveMessage($returnType='redirect')
	{
		$date		=JFactory::getDate();
		$user		=JFactory::getUser();
		$post		= JRequest::get('post');
		$parent		= JRequest::getVar('parent');

		$cid		= JRequest::getVar( 'cid', array(0), 'post', 'array' );
		$post['id'] 	= (int) $cid[0];

		//new message
		if($post['id'] ==0){
			$post['replied_by']	= 0;
			$post['from_id']	= $user->id;
			$post['date']		= $date->toSQL();
		}

		$model = $this->getModel( 'Message' );
		if ( ($model->store($post)) ) {
			$msg = JText::_( 'Item Saved' );

			$row =$model->getTable('message');
			$row->load($parent);

			if(isset($row->parent) AND isset($row->id)){

				$row->reply_date	= $date->toSQL();
				$row->replied_by	= $user->id;
				$model->store($row);

			}


			if($returnType == 'bool'){
				JFactory::getApplication()->enqueueMessage($msg, 'message');
				return true;
			}
		} else {
			$msg = JText::_( 'Error Saving Item' );
			if($returnType == 'bool'){
				JFactory::getApplication()->enqueueMessage($msg, 'error');
				return false;
			}
		}

		$link = 'index.php?option=com_contactenhanced&view=messages';
		$this->setRedirect( $link, $msg );
	}


	function send_email(){
		$this->saveMessage('bool');
		$subject	= JRequest::getVar('subject');
		$from_name	= JRequest::getVar('from_name');
		$from_email	= JRequest::getVar('from_email');
		$email_to	= JRequest::getVar('email_to');
		$email_cc	= JRequest::getVar('email_cc');
		$email_bcc	= JRequest::getVar('email_bcc');
		$message	= JRequest::getVar('message');

		jimport('joomla.mail.helper');
		$mail = JFactory::getMailer();
		$mail->setBody( $message);
		$mail->addRecipient($email_to );
		$mail->setSender( array( $from_email, $from_name ) );
		$mail->setSubject( $subject );
		if($email_cc){
			$mail->addCC($email_cc );
		}
		if($email_cc){
			$mail->addBCC($email_bcc );
		}


		if($mail->Send()){
			$msg=(JText::_( 'Thank you for your e-mail'));
		}else{
			JApplication::enqueueMessage(JText::_( 'Email not sent, please notify administrator'),'error');
			$msg=(JText::_( 'Email not sent, please notify administrator'));
		}

		if(JRequest::getVar('tmpl')){
			JRequest::setVar('tmpl','component');
			//return ;
			echo '<script>alert("'.$msg.'");window.parent.location.reload();</script>'; exit;
		}else{
			$this->setRedirect('index.php?option=com_contactenhanced&controller=messages',$msg);
		}

	}


	public function export(){

		$config		=JFactory::getConfig();
		$error_reporting_level	= $config->get('config.error_reporting');
		if($error_reporting_level != 'development'){
			error_reporting(0);
		}


		$model			= $this->getModel();
		$rows			= $model->getDataToExport();
		$db				= JFactory::getDBO();
		$query			= $db->getQuery(true);
		$catid			= JRequest::getVar('filter_category_id');
		//echo ceHelper::print_r($catid); exit;
		$customFields	= cehelper::getCustomFields($catid);

		//echo ceHelper::print_r($rows); exit;
		require_once(JPATH_COMPONENT.'/helpers/csvhandler.php');
		$csv	= new csvHandler();

		$headerLine		= array(
									JText::_('message_id'),		JText::_('parent'), 	JText::_('from_name')
								,	JText::_('from_email'),		JText::_('from_id'), 	JText::_('email_to')
								,	JText::_('email_cc'),		JText::_('email_bcc'), 	JText::_('subject')
								,	JText::_('contact_id'),		JText::_('category_id'),JText::_('date')
								,	JText::_('reply_date'),		JText::_('replied_by'), JText::_('user_ip') //,	JText::_('message')
								,	JText::_('status'),			JText::_('JGRID_HEADING_LANGUAGE')
								,	JText::_('category_name')
								,	JText::_('contact_name') //	JText::_('user_name'),	JText::_('username')
							//	,	JText::_('replies'),		JText::_('attachments')
							);

		$excludeVars	= array('subject','email','name','multiplefiles','file');
		foreach($customFields as $cf){
			if(!in_array($cf->type,$excludeVars)){
				$headerLine[]	= JText::_($cf->name); //JText::_($cf->name);
			}
		}
		//echo '<pre>'; print_r($customFields); exit;
		$csv->addRow($headerLine);

		foreach($rows AS $row){
			//$line	= ceHelper::objectToArray($row);
			unset($row['message']);
			unset($row['message_html']);
			unset($row['access']);
			unset($row['access_level']);
			unset($row['language_title']);

			$line	= $row;

			$query->clear();
			$query->select('mf.value, cf.name, cf.type,cf.id');
			$query->from('#__ce_message_fields mf');
			$query->join('RIGHT', ' #__ce_cf cf ON cf.id = mf.field_id');
			$query->where('message_id = '.$db->Quote($row['id']));
			$query->order('cf.ordering ASC');

			$db->setQuery($query);
			$recordedField		= $db->loadAssocList('id');

			//echo '<pre>'; print_r($recordedField); exit;
			foreach($customFields as $cf){
				if(!in_array($cf->type,$excludeVars)){
					if(isset($recordedField[$cf->id]['value'])){
						$line[]	= ($recordedField[$cf->id]['value']); //JText::_($cf->name);
					}else{
						$line[]	= '';
					}
				}
			}
			//echo '<pre>'; print_r($recordedField); exit;
			$csv->addRow($line);
		}
		//exit;
		echo $csv->render('contactenhanced__'.date('Y-m-d_Hi').'.csv','UTF-8');
		//echo '<pre>'; print_r($recordedField); exit;
		exit;
	}

	public function exportXML(){

		$model			= $this->getModel();
		$submissions	= $model->getDataToExport();
		$db				= JFactory::getDBO();
		$query			= $db->getQuery(true);
		$catid			= JRequest::getVar('filter_category_id');
		//echo ceHelper::print_r($catid); exit;
		$customFields	= cehelper::getCustomFields($catid);



		$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><submissions></submissions>');
		$xml->addAttribute('version', '1.0');

		$xml->addChild('datetime', date('Y-m-d H:i:s'));

		foreach($submissions AS $line){
			$row = $xml->addChild('row');
			$row->addAttribute('id', $line['id']);

			unset($line['message']);
			unset($line['message_html']);
			unset($line['access']);
			unset($line['access_level']);
			unset($line['language_title']);

			foreach ($line as $key => $value) {
				$row->addChild($key, $value);
			}

			$query->clear();
			$query->select('mf.value, cf.name, cf.type,cf.id');
			$query->from('#__ce_message_fields mf');
			$query->join('RIGHT', ' #__ce_cf cf ON cf.id = mf.field_id');
			$query->where('message_id = '.$db->Quote($row['id']));
			$query->order('cf.ordering ASC');

			$db->setQuery($query);
			$recordedField		= $db->loadAssocList('id');

			$excludeVars	= array('subject','email','name','multiplefiles','file');
			foreach($customFields as $cf){
				if(!in_array($cf->type,$excludeVars)){
					if(isset($recordedField[$cf->id]['value'])){
						$row->addChild($cf->alias, $recordedField[$cf->id]['value']);
					}else{
						$row->addChild($cf->alias, '');
					}
				}
			}
		}
		header("Content-type: text/xml");
		header("Content-disposition:attachment;filename=contactenhanced__".date('Y-m-d_Hi').".xml");
		echo $xml->asXML();
		exit;
	}
}