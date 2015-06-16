<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/controller.php';

/**
 * Profile controller class for Users.
 *
 * @package     Joomla.Site
 * @subpackage  com_users
 * @since       1.6
 */
class UsersControllerProfile extends UsersController
{
	/**
	 * Method to check out a user for editing and redirect to the edit form.
	 *
	 * @since   1.6
	 */
	public function edit()
	{
		$app			= JFactory::getApplication();
		$user			= JFactory::getUser();
		$loginUserId	= (int) $user->get('id');

		// Get the previous user id (if any) and the current user id.
		$previousId = (int) $app->getUserState('com_users.edit.profile.id');
		$userId = $this->input->getInt('user_id', null, 'array');

		// Check if the user is trying to edit another users profile.
		if ($userId != $loginUserId)
		{
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}

		// Set the user id for the user to edit in the session.
		$app->setUserState('com_users.edit.profile.id', $userId);

		// Get the model.
		$model = $this->getModel('Profile', 'UsersModel');

		// Check out the user.
		if ($userId)
		{
			$model->checkout($userId);
		}

		// Check in the previous user.
		if ($previousId)
		{
			$model->checkin($previousId);
		}

		// Redirect to the edit screen.
		$this->setRedirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit', false));
	}

	/**
	 * Method to save a user's profile data.
	 *
	 * @return  void
	 * @since   1.6
	 */
	public function save()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app	= JFactory::getApplication();
		$model	= $this->getModel('Profile', 'UsersModel');
		$user	= JFactory::getUser();
		$userId	= (int) $user->get('id');

		$file = JRequest::getVar('jform', null, 'files', 'array');

		// Force the ID to this user.
		$data['id'] = $userId;
		
		// Get the user data.
		$data = $app->input->post->get('jform', array(), 'array');
		$data['profile']['logoimage'] = $file;

		// Validate the posted data.
		$form = $model->getForm();		
		if (!$form)
		{
			JError::raiseError(500, $model->getError());
			return false;
		}

		// Validate the posted data.
		$data = $model->validate($form, $data);

		// Check for errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				} else {
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_users.edit.profile.data', $data);

			// Redirect back to the edit screen.
			$userId = (int) $app->getUserState('com_users.edit.profile.id');
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit&user_id='.$userId, false));
			return false;
		}

		// Attempt to save the data.
		$return	= $model->save($data); 
		if(isset($file)){
			$result = $this->logoUpload($userId,$file,2097152,JPATH_ROOT.DS.'images'.DS.'logos','');
		}

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_users.edit.profile.data', $data);

			// Redirect back to the edit screen.
			$userId = (int) $app->getUserState('com_users.edit.profile.id');
			$this->setMessage(JText::sprintf('COM_USERS_PROFILE_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=profile&layout=edit&user_id='.$userId, false));
			return false;
		}

		// Redirect the user and adjust session state based on the chosen task.
		switch ($this->getTask())
		{
			case 'apply':
				// Check out the profile.
				$app->setUserState('com_users.edit.profile.id', $return);
				$model->checkout($return);

				// Redirect back to the edit screen.
				$this->setMessage(JText::_('COM_USERS_PROFILE_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_(($redirect = $app->getUserState('com_users.edit.profile.redirect')) ? $redirect : 'index.php?option=com_users&view=profile&layout=edit&hidemainmenu=1', false));
				break;

			default:
				// Check in the profile.
				$userId = (int) $app->getUserState('com_users.edit.profile.id');
				if ($userId)
				{
					$model->checkin($userId);
				}

				// Clear the profile id from the session.
				$app->setUserState('com_users.edit.profile.id', null);

				// Redirect to the list screen.
				$this->setMessage(JText::_('COM_USERS_PROFILE_SAVE_SUCCESS'));
				$this->setRedirect(JRoute::_(($redirect = $app->getUserState('com_users.edit.profile.redirect')) ? $redirect : 'index.php?option=com_users&view=profile&user_id='.$return, false));
				break;
		}

		// Flush the data from the session.
		$app->setUserState('com_users.edit.profile.data', null);
	}
	
	private function logoUpload($userId,$file,$max, $filedir,$msg){

        if(isset($file)){
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

                   		$db = JFactory::getDBO ();
						$query	= $db->getQuery(true);
						$query = "INSERT INTO #__user_profiles (user_id,profile_key,profile_value) value($userId,'profile.logoimage','".$filename."') ON DUPLICATE KEY UPDATE profile_value='".$filename."'";
						//print_r($sql);exit;
						$db->setQuery($query);
						$result = $db->query();

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
        return $result;
	}
	
	public function saveimage()
	{
		$app	= JFactory::getApplication();
		$model	= $this->getModel('Profile', 'UsersModel');
		$user	= JFactory::getUser();
		$userId	= (int) $user->get('id');

		$file = JRequest::getVar('recipeimage', null, 'files', 'array');
		$recipeId = JRequest::getVar('recipe');
		$start = (int) max(JRequest::getVar('limitStart',0),0);

		if($model->updateRecipe($userId,$recipeId,$file))
		{
			$this->setMessage("Recipe image successfully updated.");
			$this->setRedirect(JRoute::_(($redirect = $app->getUserState('com_users.edit.profile.redirect')) ? $redirect : 'index.php?option=com_users&task=partner_recipes_list&start='.$start, false));
		}else
		{
			$app->enqueueMessage("Recipe image update failed.", 'error');
			$this->setRedirect(JRoute::_(($redirect = $app->getUserState('com_users.edit.profile.redirect')) ? $redirect : 'index.php?option=com_users&task=partner_recipes_list&start'.$start, false));
			return false;			
		}
	}
}
