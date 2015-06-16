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
 * Registration controller class for Users.
 *
 * @package     Joomla.Site
 * @subpackage  com_users
 * @since       1.6
 */
class UsersControllerPartnerRegistration extends UsersController
{
	/**
	 * Method to activate a user.
	 *
	 * @return  boolean  True on success, false on failure.
	 * @since   1.6
	 */
	public function activate()
	{
		$user  = JFactory::getUser();
		$input = JFactory::getApplication()->input;
		$uParams = JComponentHelper::getParams('com_users');

		// If the user is logged in, return them back to the homepage.
		if ($user->get('id'))
		{
			$this->setRedirect('index.php');
			return true;
		}

		// If user registration or account activation is disabled, throw a 403.
		if ($uParams->get('useractivation') == 0 || $uParams->get('allowUserRegistration') == 0)
		{
			JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
			return false;
		}

		$model = $this->getModel('PartnerRegistration', 'UsersModel');
		$token = $input->getAlnum('token');

		// Check that the token is in a valid format.
		if ($token === null || strlen($token) !== 32)
		{
			JError::raiseError(403, JText::_('JINVALID_TOKEN'));
			return false;
		}

		// Attempt to activate the user.
		$return = $model->activate($token);

		// Check for errors.
		if ($return === false)
		{
			// Redirect back to the homepage.
			$this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect('index.php');
			return false;
		}

		$useractivation = $uParams->get('useractivation');

		// Redirect to the login screen.
		if ($useractivation == 0)
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
		elseif ($useractivation == 1)
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_ACTIVATE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		}
		elseif ($return->getParam('activate'))
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_VERIFY_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=partnerregistration&layout=complete', false));
		}
		else
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_ADMINACTIVATE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=partnerregistration&layout=complete', false));
		}
		return true;
	}

	/**
	 * Method to register a user.
	 *
	 * @return  boolean  True on success, false on failure.
	 * @since   1.6
	 */
	public function register()
	{ 
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// If registration is disabled - Redirect to login page.
		if (JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0)
		{
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			return false;
		}

		$app	= JFactory::getApplication();
		$model	= $this->getModel('PartnerRegistration', 'UsersModel');

		// Get the user data.
		$requestData = $this->input->post->get('jform', array(), 'array');
		$file = JRequest::getVar('jform', null, 'files', 'array');
		$requestData['logoimage']['name'] = $file['name']['logoimage'];
		$requestData['logoimage']['type'] = $file['type']['logoimage'];
		$requestData['logoimage']['tmp_name'] = $file['tmp_name']['logoimage'];
		$requestData['logoimage']['error'] = $file['error']['logoimage'];
		$requestData['logoimage']['size'] = $file['size']['logoimage'];
		
		$requestData['profilepicture']['name'] = $file['name']['profilepicture'];
		$requestData['profilepicture']['type'] = $file['type']['profilepicture'];
		$requestData['profilepicture']['tmp_name'] = $file['tmp_name']['profilepicture'];
		$requestData['profilepicture']['error'] = $file['error']['profilepicture'];
		$requestData['profilepicture']['size'] = $file['size']['profilepicture'];

		// Validate the posted data.
		$form	= $model->getForm();
		
		if (!$form)
		{
			JError::raiseError(500, $model->getError());
			return false;
		}
		$recipe = $requestData['recipe'];
		unset($requestData['recipe']);
		$data	= $model->validate($form, $requestData);
		
		// Check for validation errors.
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
			//retain the value of the input field
			$requestData['recipe'] = $recipe;
			
			// Save the data in the session.
			$app->setUserState('com_users.partnerregistration.data', $requestData);

			// Redirect back to the partnerregistration screen.
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=partnerregistration', false));
			return false;
		}
		$data['recipe'] = $recipe;

		// Attempt to save the data.
		$return	= $model->register($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_users.partnerregistration.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=partnerregistration', false));
			return false;
		}

		// Flush the data from the session.
		$app->setUserState('com_users.partnerregistration.data', null);

		// Redirect to the profile screen.
		if ($return === 'adminactivate'){
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=partnerregistration&layout=complete', false));
		} elseif ($return === 'useractivate')
		{
			//$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=partnerregistration&layout=complete', false));
		}
		else
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		}

		return true;
	}
	
	/**
	 * Method for pop up form to register a user.
	 *
	 * @return  boolean  True on success, false on failure.
	 * @since   1.6
	 */
	public function popupRegister()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// If registration is disabled - Redirect to login page.
		if (JComponentHelper::getParams('com_users')->get('allowUserRegistration') == 0)
		{
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			return false;
		}

		$app	= JFactory::getApplication();
		$model	= $this->getModel('PartnerRegistration', 'UsersModel');

		// Get the user data.
		$requestData = $this->input->post->get('jform', array(), 'array');
		
		$postData = JRequest::get();
		if(isset($postData['where']) && $postData['where']=='popup')
		{
			if(!isset($requestData['email2']))
			{
				$requestData['email2'] = $requestData['email1'];
			}
			if(!isset($requestData['name']))
			{
				$requestData['name'] = $requestData['username'];
			}
		}

		// Validate the posted data.
		$form	= $model->getForm();
		if (!$form)
		{
			JError::raiseError(500, $model->getError());
			return false;
		}
		$data	= $model->validate($form, $requestData);

		// Check for validation errors.
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
			$app->setUserState('com_users.partnerregistration.data', $requestData);

			// Redirect back to the registration screen.
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=partnerregistration', false));
			return false;
		}

		// Attempt to save the data.
		$return	= $model->register($data);

		// Check for errors.
		if ($return === false)
		{
			// Save the data in the session.
			$app->setUserState('com_users.partnerregistration.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=partnerregistration', false));
			return false;
		}

		// Flush the data from the session.
		$app->setUserState('com_users.partnerregistration.data', null);

		// Redirect to the profile screen.
		if ($return === 'adminactivate'){
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=partnerregistration&layout=complete', false));
		} elseif ($return === 'useractivate')
		{
			//$this->setMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));
			$this->setMessage(JText::_('Thank you for registering with SavvyFork! Please look out for an email to activate your account.'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=partnerregistration&layout=complete', false));
		}
		else
		{
			$this->setMessage(JText::_('COM_USERS_REGISTRATION_SAVE_SUCCESS'));
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
		}

		return true;
	}
}
