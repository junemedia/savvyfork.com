<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;


require_once JPATH_ADMINISTRATOR.'/components/com_contactenhanced/models/contact.php';
/**
 * @package		com_contactenhanced
* @since 1.5
 */
class ContactenhancedModelEdit extends ContactenhancedModelContact
{
	public function getForm($data = array(), $loadData = true)
	{
		jimport('joomla.form.form');
		JForm::addFieldPath(JPATH_ADMINISTRATOR.'/components/com_users/models/fields');
		JForm::addFieldPath(JPATH_ADMINISTRATOR.'/components/com_contactenhanced/models/fields');
		JForm::addFieldPath(JPATH_ROOT.'/libraries/joomla/html/html');

		// Get the form.
		$form = $this->loadForm('com_contactenhanced.contact'
							, JPATH_BASE.'/components/com_contactenhanced/models/form/contact.xml'
							, array('control' => 'jform', 'load_data' => $loadData, 'ce'=>''));
		if (empty($form)) {
			return false;
		}

		return $form;
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();
		$input 		= $app->input;
		
		// Load state from the request.
		$pk = $input->get('c_id',0,'int');
		$this->setState('contact.id', $pk);
		$app->setUserState('com_contactenhanced.edit.edit.id', $pk);
		//$this->setState('contact.catid', JRequest::getInt('catid'));

		$return = JRequest::getVar('return', null, 'default', 'base64');
		$this->setState('return_page', base64_decode($return));
		return parent::populateState($ordering, $direction);
	}
	
	/**
	 * Get the return URL.
	 *
	 * @return	string	The return URL.
	 * @since	1.6
	 */
	public function getReturnPage()
	{
		return base64_encode($this->getState('return_page'));
	}
	
	/**
	 * Get Contact Id based on user id
	 *
	 * @return	int	The Contact id
	 * @since	2.5
	 */
	public function getContactId()
	{
		$user	= JFactory::getUser();
		if($user->get('id')){
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
				
			$query->select('id');
			$query->from('#__ce_details AS a');
			$query->where('a.user_id = ' . (int) $user->get('id'));
				
			$db->setQuery($query,0,1);
			if(($result = $db->loadResult())){
				return $result;
			}
		}
		return 0;
	}
	
	
}

