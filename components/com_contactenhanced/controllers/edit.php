<?php
/**
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * @package		Joomla.Site
 * @subpackage	com_contactenhanced
 */
class ContactenhancedControllerEdit extends JControllerForm
{
	/**
	 * @since	2.5
	 */
	protected $view_item = 'edit';

	/**
	 * @since	2.5
	 */
	protected $view_list = 'categories';

	/**
	 * Method to add a new record.
	 *
	 * @return	boolean	True if the contact can be added, false if not.
	 * @since	2.5
	 */
	
	public function __construct($config = array())
	{
		$lang		= JFactory::getLanguage();
		$lang->load('com_contactenhanced', JPATH_ADMINISTRATOR.'/components/com_contactenhanced/');
		$lang->load('com_contactenhanced', JPATH_ADMINISTRATOR);
		$lang->load('com_contactenhanced', JPATH_ROOT.'/components/com_contactenhanced/');
		$lang->load('com_contactenhanced', JPATH_ROOT, null, true);
		// Loads all Administrator Global strings and override front-end strings
		$lang->load(null, JPATH_ADMINISTRATOR); 
		// Load front-end strings again
		$lang->load(null, JPATH_BASE);
		
		parent::__construct($config);
	}
	public function add()
	{
		if (!parent::add()) {
			// Redirect to the return page.
			$this->setRedirect($this->getReturnPage());
		}
	}

	/**
	 * Method override to check if you can add a new record.
	 *
	 * @param	array	An array of input data.
	 *
	 * @return	boolean
	 * @since	2.5
	 */
	protected function allowAdd($data = array())
	{
		
		// Initialise variables.
		$user		= JFactory::getUser();
		$categoryId	= JArrayHelper::getValue($data, 'catid', JRequest::getInt('catid'), 'int');
		$allow		= null;
//echo ceHelper::print_r($data); exit;
		if ($categoryId) {
			// If the category has been passed in the data or URL check it.
			$allow	= $user->authorise('core.create', 'com_contactenhanced.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd();
		}
		else {
			return $allow;
		}
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	2.5
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$canDo	= CEHelper::getActions();
		
		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		
		// Check general edit permission first.
		if ($canDo->get('core.edit')) {
			return true;
		}

		// Fallback on edit.own.
		// First test if the permission is available.
		if ($canDo->get('core.edit.own')) {
			// Now test the owner is the user.
			$ownerId	= (int) isset($data['user_id']) ? $data['user_id'] : 0;
			if (empty($ownerId) && $recordId) {
				// Need to do a lookup from the model.
				$record		= $this->getModel()->getItem($recordId);

				if (empty($record)) {
					return false;
				}

				$ownerId = $record->user_id;
			}

			// If the owner matches 'me' then do the test.
			if ($ownerId == $userId) {
				return true;
			}
		}

		// Since there is no asset tracking, revert to the component permissions.
		return parent::allowEdit($data, $key);
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 *
	 * @return	Boolean	True if access level checks pass, false otherwise.
	 * @since	2.5
	 */
	public function cancel($key = 'c_id')
	{
		parent::cancel($key);

		// Redirect to the return page.
		$this->setRedirect($this->getReturnPage());
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	Boolean	True if access level check and checkout passes, false otherwise.
	 * @since	2.5
	 */
	public function edit($key = null, $urlVar = 'c_id')
	{
		$result = parent::edit($key, $urlVar);

		return $result;
	}

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param	string	$name	The model name. Optional.
	 * @param	string	$prefix	The class prefix. Optional.
	 * @param	array	$config	Configuration array for model. Optional.
	 *
	 * @return	object	The model.
	 * @since	2.5
	 */
	public function &getModel($name = 'edit', $prefix = '', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 * @since	2.5
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'a_id')
	{
		// Need to override the parent method completely.
		$tmpl		= JRequest::getCmd('tmpl');
		$layout		= JRequest::getCmd('layout', 'edit');
		$append		= '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}

		// TODO This is a bandaid, not a long term solution.
//		if ($layout) {
//			$append .= '&layout='.$layout;
//		}
		//$append .= '&layout=edit';

		if ($recordId) {
			$append .= '&'.$urlVar.'='.$recordId;
		}

		$itemId	= JRequest::getInt('Itemid');
		$return	= $this->getReturnPage();

		if ($itemId) {
			$append .= '&Itemid='.$itemId;
		}

		if ($return) {
			$append .= '&return='.base64_encode($return);
		}

		return $append;
	}

	/**
	 * Get the return URL.
	 *
	 * If a "return" variable has been passed in the request
	 *
	 * @return	string	The return URL.
	 * @since	2.5
	 */
	protected function getReturnPage()
	{
		$return = JRequest::getVar('return', null, 'default', 'base64');

		if (empty($return) || !JUri::isInternal(base64_decode($return))) {
			return JURI::base();
		}
		else {
			return base64_decode($return);
		}
	}

	/**
	 * Function that allows child controller access to model data after the data has been saved.
	 *
	 * @param	JModel	$model		The data model object.
	 * @param	array	$validData	The validated data.
	 *
	 * @return	void
	 * @since	2.5
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		$task = $this->getTask();

		if ($task == 'save') {
			$this->setRedirect(JRoute::_('index.php?option=com_contactenhanced&view=category&id='.$validData['catid'], false));
		}
	}

	/**
	 * Method to save a record.
	 *
	 * @param	string	$key	The name of the primary key of the URL variable.
	 * @param	string	$urlVar	The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return	Boolean	True if successful, false otherwise.
	 * @since	2.5
	 */
	public function save($key = 'id', $urlVar = 'c_id')
	{
		$user		= JFactory::getUser();
		$contactId	= JRequest::getInt($urlVar);
		$data 		= JRequest::getVar('jform', array(), 'post', 'array');
		$canCreate	= $user->authorise('core.create', 'com_contactenhanced');
		if (!$canCreate)
		{
			$data['published']	= -3;
		}
		if(($image	= $this->upload($contactId))){
			$data['image']	= $image;
			JRequest::setVar('jform', $data, 'post');
			$oldImage	= (JRequest::getVar('old_image'));
			// was old image uploaded by user? If so, delete it;
			// when user adds image the contact id and an underscore 
			// will be added before the file name
			if(substr(JFile::getName($oldImage), 0, strlen($contactId)) == $contactId){
				if(JFile::exists(JPATH_ROOT.'/'.$oldImage)){
					JFile::delete(JPATH_ROOT.'/'.$oldImage);
				}
			}
		}
		
		ceHelper::setSession('jform', $data, false);
		$result = parent::save($key, $urlVar);

		// If ok, redirect to the return page.
		if ($result) {
			ceHelper::setSession('jform', array(), false);
			if (!$canCreate AND $data['id'] == 0)
			{
				$this->sendEmail($data);
			}
			$this->setRedirect($this->getReturnPage());
		}

		return $result;
	}
	

	public function sendEmail(&$data) {
		$mail		= JFactory::getMailer();
		$user		= JFactory::getUser();
		$uri 		= JURI::getInstance();
		$config		= JFactory::getConfig();
		$db			= JFactory::getDbo();
		// Compile the admin notification mail values.
		$data['siteurl']	= JUri::base();
		$data['fromname'] = $config->get('fromname');
		$data['mailfrom'] = $config->get('mailfrom');
		$data['sitename'] = $config->get('sitename');
		$emailSubject	= JText::sprintf(
				'COM_CONTACTENHANCED_NEW_CONTACT_EMAIL_LOGIN_TO_APPROVE_SUBJECT',
				$data['name'],
				$data['sitename']
		);
	
		$emailBody = JText::sprintf(
				'COM_CONTACTENHANCED_NEW_CONTACT_EMAIL_LOGIN_TO_APPROVE_BODY',
				$data['sitename'],
				$data['name'],
				$user->email,
				$user->username."({$user->id})",
				$data['siteurl'].'/administrator/index.php?option=com_contactenhanced&filter_published=-3',
				$data['address'],
				$data['suburb'],
				$data['state'],
				$data['postcode'],
				$data['country'],
				$data['telephone']
		);
		$query	= $db->getQuery(true);
		$query->select('name, email, sendEmail, id');
		$query->from('#__users');
		$query->where('sendEmail=1');
		
		$db->setQuery( $query );
		$rows = $db->loadObjectList();
		$return	= false;
		// Send mail to all users with users creating permissions and receiving system emails
		foreach( $rows as $row )
		{
			$usercreator = JFactory::getUser($id = $row->id);
			if ($usercreator->authorise('core.create', 'com_contactenhanced'))
			{
				$return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBody);
	
			}
		}
		// Check for an error the last sent email only, we believe it is enough.
		if ($return !== true) {
			$this->setError(JText::_('COM_CONTACTENHANCED_NEW_CONTACT_NOTIFY_SEND_MAIL_FAILED'));
			return false;
		}else{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_CONTACTENHANCED_NEW_CONTACT_NOTIFY_SENT'), 'message');
		}
	}
	
	
	function upload($contactId)
	{
		// Get the user
		$user		= JFactory::getUser();
		$this->setupLog();
		
		$lang		= JFactory::getLanguage();
		$lang->load('com_media');
		
		// use com_media helper
		require_once (JPATH_ADMINISTRATOR.'/components/com_media/helpers/media.php');
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		
		$imagepath	= 'images/com_contactenhanced/';
		$filepath	= JPATH_ROOT.'/'.$imagepath;
		if (!JFolder::exists($filepath)) {
			if (!JFolder::create($filepath)) {
				$filepath	= JPATH_ROOT.'/images';
				$imagepath	= 'images/';
			}
		}
		
		// Get some data from the request
		$file		= JRequest::getVar('image', '', 'files', 'array');
		
		// Make the filename safe
		$file['name']	= JFile::makeSafe($contactId.'_'.$file['name']);

		if (isset($file['name']) AND MediaHelper::isImage($file['name']))
		{
			// The request is valid
			$err = null;

			$filepath = JPath::clean($filepath.'/'. strtolower($file['name']));
			
			if (!MediaHelper::canUpload($file, $err))
			{
				JLog::add(array('comment' => 'Invalid: '.$filepath.': '.$err));
				$this->setMessage($err);
			}

			if (JFile::exists($filepath))
			{
				// File exists
				JLog::add(array('comment' => 'File exists: '.$filepath.' by user_id '.$user->id));
				$this->setMessage(JText::_('COM_MEDIA_ERROR_FILE_EXISTS'));
			}

			if (!JFile::upload($file['tmp_name'], $filepath))
			{
				// Error in upload
				JLog::add(array('comment' => 'Error on upload: '.$filepath));
				$this->setMessage(JText::_('COM_MEDIA_ERROR_UNABLE_TO_UPLOAD_FILE'));
			}else{
				return $imagepath.$file['name'];
			}
		}
		return false;
	}

	private function setupLog()
	{
		$path = JFactory::getConfig()->get('log_path');
	
		$fileName = 'upload.error.php';
		$entry = '';
	
		if('preserve' == JFactory::getApplication()->input->get('logMode')
				&& JFile::exists($path.'/'.$fileName)
		)
		{
			$entry = '----------------------------------------------';
		}
		else if(JFile::exists($path.'/'.$fileName))
		{
			JFile::delete($path.'/'.$fileName);
		}
	
		JLog::addLogger(
		array(
		'text_file' => $fileName
		, 'text_entry_format' => '{DATETIME}	{PRIORITY}	{MESSAGE}'
				, 'text_file_no_php' => true
		)
		, JLog::INFO | JLog::ERROR
		);
	
		if('' != $entry)
			JLog::add($entry);
	}
}
