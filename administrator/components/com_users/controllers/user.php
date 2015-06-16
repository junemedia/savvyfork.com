<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * User controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class UsersControllerUser extends JControllerForm
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_USERS_USER';

	/**
	 * Overrides JControllerForm::allowEdit
	 *
	 * Checks that non-Super Admins are not editing Super Admins.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean  True if allowed, false otherwise.
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check if this person is a Super Admin
		if (JAccess::check($data[$key], 'core.admin'))
		{
			// If I'm not a Super Admin, then disallow the edit.
			if (!JFactory::getUser()->authorise('core.admin'))
			{
				return false;
			}
		}

		return parent::allowEdit($data, $key);
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @since   2.5
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('User', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_users&view=users' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

	/**
	 * Overrides parent save method to check the submitted passwords match.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.6
	 */
	public function save($key = null, $urlVar = null)
	{
		$data = $this->input->post->get('jform', array(), 'array');
		$file = JRequest::getVar('jform', null, 'files', 'array');
		$userId	= $data['id'];

		// TODO: JForm should really have a validation handler for this.
		if (isset($data['password']) && isset($data['password2']))
		{
			// Check the passwords match.
			if ($data['password'] != $data['password2'])
			{
				$this->setMessage(JText::_('JLIB_USER_ERROR_PASSWORD_NOT_MATCH'), 'warning');
				$this->setRedirect(JRoute::_('index.php?option=com_users&view=user&layout=edit', false));
			}

			unset($data['password2']);
		}
		$oldLogo = '';
		if($file['name']['profile']['logoimage']==''){
        		$user_profile = JUserHelper::getProfile($userId);
				if(isset($user_profile->profile['logoimage'])){
					$oldLogo = $user_profile->profile['logoimage'];
				}
        }
		$return = parent::save();
		if(isset($file)){
			$result = $this->logoUpload($userId,$file,2097152,JPATH_ROOT.DS.'images'.DS.'logos','',$oldLogo);
		}
		return $return;
	}
	
	private function logoUpload($userId,$file,$max, $filedir,$msg,$oldLogo = ''){

        if(isset($file)&& $file['name']['profile']['logoimage']!=''){
                //Clean up filename to get rid of strange characters like spaces etc
                $filename = JFile::makeSafe($file['name']['profile']['logoimage']);

                if($file['size']['profile']['logoimage'] > $max) $msg = JText::_('ONLY_FILES_UNDER').' '.$max;
                //Set up the source and destination of the file
				$uploadedFileNameParts = explode('.',$filename);
				$uploadedFileExtension = array_pop($uploadedFileNameParts);
                $src = $file['tmp_name']['profile']['logoimage'];
                $filename = sha1($userId.uniqid()).'.'.$uploadedFileExtension;
                $dest = $filedir . DS . $filename;
				$validFileExts = explode(',', 'jpeg,jpg,png,gif');

                //First check if the file has the right extension, we need jpg only
                if (in_array($uploadedFileExtension,$validFileExts)) {
                   if ( JFile::upload($src, $dest) ) {

                   		/*$db = JFactory::getDBO ();
						$query	= $db->getQuery(true);
						$query = "INSERT INTO #__user_profiles (user_id,profile_key,profile_value) value($userId,'profile.logoimage','".$filename."') ON DUPLICATE KEY UPDATE profile_value='".$filename."'";
						//print_r($sql);exit;
						$db->setQuery($query);
						$result = $db->query();*/
						$result = $this->updateLogoimage($userId,$filename);

                       //Redirect to a page of your choice
                        $msg = JText::_('FILE_SAVE_AS').' '.$dest;
                   } else {
                          //Redirect and throw an error message
                        $msg = JText::_('ERROR_IN_UPLOAD');
                   }
                } else {
                   //Redirect and notify user file is not right extension
                        $msg = JText::_('FILE_TYPE_INVALID');
                }
        }
		 else{
        	if($oldLogo != '')
        	{
				$result = $this->updateLogoimage($userId,$oldLogo);
        	}
        }
        return $result;
	}
	private function updateLogoimage($userId,$filename)
	{
		$db = JFactory::getDBO ();
		$query	= $db->getQuery(true);
		$query = "INSERT INTO #__user_profiles (user_id,profile_key,profile_value) value($userId,'profile.logoimage','".$filename."') ON DUPLICATE KEY UPDATE profile_value='".$filename."'";
		//print_r($sql);exit;
		$db->setQuery($query);
		$result = $db->query();
		return $result;
	}
}
