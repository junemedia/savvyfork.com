<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');

/**
 * @package		com_contactenhanced
* @since 1.5
 */
class ContactenhancedModelContact extends JModelItem
{
	/**
	 * Model context string.
	 *
	 * @var		string
	 */
	protected $_context = 'com_contactenhanced.contact';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('site');

		// Load state from the request.
		$pk = JRequest::getInt('id');
		$this->setState('contact.id', $pk);

		// Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

		$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_contactenhanced')) &&  (!$user->authorise('core.edit', 'com_contactenhanced'))){
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}

	/**
	 * Gets a list of contacts
	 * @param array
	 * @return mixed Object or null
	 */
	public function &getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('contact.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {
			try
			{
				$db = $this->getDbo();
				$query = $db->getQuery(true);
				//sqlsrv changes
				$case_when = ' CASE WHEN ';
				$case_when .= $query->charLength('a.alias');
				$case_when .= ' THEN ';
				$a_id = $query->castAsChar('a.id');
				$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
				$case_when .= ' ELSE ';
				$case_when .= $a_id.' END as slug';
				
				$case_when1 = ' CASE WHEN ';
				$case_when1 .= $query->charLength('c.alias');
				$case_when1 .= ' THEN ';
				$c_id = $query->castAsChar('c.id');
				$case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
				$case_when1 .= ' ELSE ';
				$case_when1 .= $c_id.' END as catslug';
				$query->select($this->getState('list.select', 'a.*') . ','.$case_when.','.$case_when1);
				//. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, '
				//. ' CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(\':\', c.id, c.alias) ELSE c.id END AS catslug ');
				$query->from('#__ce_details AS a');

				// Join on category table.
				$query->select('c.title AS category_title, c.alias AS category_alias, c.access AS category_access');
				$query->join('LEFT', '#__categories AS c on c.id = a.catid');


				// Join over the categories to get parent category titles
				$query->select('parent.title as parent_title, parent.id as parent_id, parent.path as parent_route, parent.alias as parent_alias');
				$query->join('LEFT', '#__categories as parent ON parent.id = c.parent_id');

				$query->where('a.id = ' . (int) $pk);

				// Filter by start and end dates.
				$nullDate = $db->Quote($db->getNullDate());
				$nowDate = $db->Quote(JFactory::getDate()->toSQL());


				// Filter by published state.
				$published = $this->getState('filter.published');
				$archived = $this->getState('filter.archived');
				if (is_numeric($published)) {
					$query->where('(a.published = ' . (int) $published . ' OR a.published =' . (int) $archived . ')');
					$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
					$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
				}

				$db->setQuery($query);
 
				$data = $db->loadObject();

				if ($error = $db->getErrorMsg()) {
					throw new JException($error);
				}

				if (empty($data)) {
					//throw new JException(JText::_('COM_CONTACTENHANCED_ERROR_CONTACT_NOT_FOUND'), 404);
					JError::raiseError(404, JText::_('COM_CONTACTENHANCED_ERROR_CONTACT_NOT_FOUND'));
				}
 
				// Check for published state if filter set.
				if (((is_numeric($published)) || (is_numeric($archived))) && (($data->published != $published) && ($data->published != $archived)))
				{
					JError::raiseError(404, JText::_('COM_CONTACTENHANCED_ERROR_CONTACT_NOT_FOUND'));
				}

				// Convert parameter fields to objects.
				$registry = new JRegistry;
				$registry->loadString($data->params);
				$data->params = clone $this->getState('params');
				$data->params->merge($registry);

				$registry = new JRegistry;
				$registry->loadString($data->metadata);
				$data->metadata = $registry;

				$jversion = new JVersion();
				if(version_compare( $jversion->getShortVersion(), '3.1') >= 0){
					$data->tags = new JHelperTags;
					$data->tags->getTagIds($data->id, 'com_contactenhanced.contact');
				}
				
				// Compute access permissions.
				if ($access = $this->getState('filter.access')) {
					// If the access filter has been set, we already know this user can view.
					$data->params->set('access-view', true);
				}
				else {
					// If no access filter is set, the layout takes some responsibility for display of limited information.
					$user = JFactory::getUser();
					$groups = $user->getAuthorisedViewLevels();

					if ($data->catid == 0 || $data->category_access === null) {
						$data->params->set('access-view', in_array($data->access, $groups));
					}
					else {
						$data->params->set('access-view', in_array($data->access, $groups) && in_array($data->category_access, $groups));
					}
				}

				$this->_item[$pk] = $data;
			}
			catch (JException $e)
			{
				$this->setError($e);
				$this->_item[$pk] = false;
			}

		}
		if ($this->_item[$pk])
		{
			if ($extendedData = $this->getContactQuery($pk)) {
				$this->_item[$pk]->articles = $extendedData->articles;
				$this->_item[$pk]->profile = $extendedData->profile;
			}
		}
  		return $this->_item[$pk];

	}

	public function getCustomFields($catid){
		return CEHelper::getCustomFields($catid);
	}

	protected function  getContactQuery($pk = null)
	{
		// TODO: Cache on the fingerprint of the arguments
		$db		= $this->getDbo();
		$user	= JFactory::getUser();
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('contact.id');

		$query	= $db->getQuery(true);
		if ($pk)
		{
			//sqlsrv changes
			$case_when = ' CASE WHEN ';
			$case_when .= $query->charLength('a.alias', '!=', '0');
			$case_when .= ' THEN ';
			$a_id = $query->castAsChar('a.id');
			$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
			$case_when .= ' ELSE ';
			$case_when .= $a_id.' END as slug';

			$case_when1 = ' CASE WHEN ';
			$case_when1 .= $query->charLength('cc.alias', '!=', '0');
			$case_when1 .= ' THEN ';
			$c_id = $query->castAsChar('cc.id');
			$case_when1 .= $query->concatenate(array($c_id, 'cc.alias'), ':');
			$case_when1 .= ' ELSE ';
			$case_when1 .= $c_id.' END as catslug';
			$query->select(
				'a.*, cc.access as category_access, cc.title as category_name, '
				. $case_when . ',' . $case_when1
			)

				->from('#__ce_details AS a')

				->join('INNER', '#__categories AS cc on cc.id = a.catid')

				->where('a.id = ' . (int) $pk);
			$published = $this->getState('filter.published');
			$archived = $this->getState('filter.archived');
			if (is_numeric($published))
			{
				$query->where('a.published IN (1,2)')
					->where('cc.published IN (1,2)');
			}
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		

			try
			{
				$db->setQuery($query);
				$result = $db->loadObject();
			
				if (empty($result))
				{
					throw new Exception(JText::_('COM_CONTACTENHANCED_ERROR_CONTACT_NOT_FOUND'), 404);
				}
			
				// If we are showing a contact list, then the contact parameters take priority
				// So merge the contact parameters with the merged parameters
				if ($this->getState('params')->get('show_contact_list'))
				{
					$registry = new JRegistry;
					$registry->loadString($result->params);
					$this->getState('params')->merge($registry);
				}
			}
			catch (Exception $e)
			{
				$this->setError($e);
				return false;
			}
			if ($result)
			{
				$user	= JFactory::getUser();
				$groups	= implode(',', $user->getAuthorisedViewLevels());

				//get the content by the linked user
				$query	= $db->getQuery(true)
					->select('a.id')
					->select('a.title')
					->select('a.state')
					->select('a.access')
					->select('a.created');

				// SQL Server changes
				$case_when = ' CASE WHEN ';
				$case_when .= $query->charLength('a.alias', '!=', '0');
				$case_when .= ' THEN ';
				$a_id = $query->castAsChar('a.id');
				$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
				$case_when .= ' ELSE ';
				$case_when .= $a_id.' END as slug';
				$case_when1 = ' CASE WHEN ';
				$case_when1 .= $query->charLength('c.alias', '!=', '0');
				$case_when1 .= ' THEN ';
				$c_id = $query->castAsChar('c.id');
				$case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
				$case_when1 .= ' ELSE ';
				$case_when1 .= $c_id.' END as catslug';
				$query->select($case_when1 . ',' . $case_when)
					->from('#__content as a')
					->join('LEFT', '#__categories as c on a.catid=c.id')
					->where('a.created_by = ' . (int) $result->user_id)
					->where('a.access IN ('. $groups.')')
					->order('a.state DESC, a.created DESC');
				// filter per language if plugin published
				if (JLanguageMultilang::isEnabled())
				{
					$query->where(('a.created_by = ' . (int) $result->user_id) AND ('a.language=' . $db->quote(JFactory::getLanguage()->getTag()) . ' OR a.language=' . $db->quote('*')));
				}
				if (is_numeric($published))
				{
					$query->where('a.state IN (1,2)');
				}
				$db->setQuery($query, 0, 10);
				$articles = $db->loadObjectList();
				$result->articles = $articles;

				//get the profile information for the linked user
				require_once JPATH_ADMINISTRATOR.'/components/com_users/models/user.php';
				$userModel = JModelLegacy::getInstance('User', 'UsersModel', array('ignore_request' => true));
				$data = $userModel->getItem((int) $result->user_id);

				JPluginHelper::importPlugin('user');
				$form = new JForm('com_users.profile');
				// Get the dispatcher.
				$dispatcher	= JEventDispatcher::getInstance();

				// Trigger the form preparation event.
				$dispatcher->trigger('onContentPrepareForm', array($form, $data));
				// Trigger the data preparation event.
				$dispatcher->trigger('onContentPrepareData', array('com_users.profile', $data));

				// Load the data into the form after the plugins have operated.
				$form->bind($data);
				$result->profile = $form;

				$this->contact = $result;
				return $result;
			}
		}
	}
	/**
	 * Manage the display mode for contact detail groups
	 * @param object $params
	 */
	function displayParamters(&$params, &$item) {
		
		if ($params->get('show_street_address',1) || $params->get('show_suburb') || $params->get('show_state') || $params->get('show_postcode') || $params->get('show_country')) {
			if (!empty ($item->address) || !empty ($item->suburb) || !empty ($item->state) || !empty ($item->country) || !empty ($item->postcode)) {
				$params->set('address_check', 1);
			}
		}
		else {
			$params->set('address_check', 0);
		}

		switch ($params->get('contact_icons'))
			{
				case 1 :
					// text
					$params->set('marker_address',	JText::_('COM_CONTACTENHANCED_ADDRESS').": ");
					$params->set('marker_email',	JText::_('JGLOBAL_EMAIL').": ");
					$params->set('marker_telephone',JText::_('COM_CONTACTENHANCED_TELEPHONE').": ");
					$params->set('marker_fax',		JText::_('COM_CONTACTENHANCED_FAX').": ");
					$params->set('marker_mobile',	JText::_('COM_CONTACTENHANCED_MOBILE').": ");
					$params->set('marker_skype',	JText::_('COM_CONTACTENHANCED_SKYPE').": ");
					$params->set('marker_twitter',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_TWITTER_LABEL').": ");
					$params->set('marker_facebook',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_FACEBOOK_LABEL').": ");
					$params->set('marker_linkedin',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_LINKEDIN_LABEL').": ");
					
					$params->set('marker_website',	JText::_('COM_CONTACTENHANCED_WEBSITE').": ");
					$params->set('marker_birthdate',JText::_('COM_CONTACTENHANCED_BIRTHDATE').": ");
					$params->set('marker_misc',		JText::_('COM_CONTACTENHANCED_OTHER_INFORMATION').": ");
					$params->set('marker_website',	JText::_('COM_CONTACTENHANCED_WEBSITE').": ");
					$params->set('marker_extra_field_1',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_1_LABEL').": ");
					$params->set('marker_extra_field_2',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_2_LABEL').": ");
					$params->set('marker_extra_field_3',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_3_LABEL').": ");
					$params->set('marker_extra_field_4',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_4_LABEL').": ");
					$params->set('marker_extra_field_5',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_5_LABEL').": ");
					$params->set('marker_extra_field_6',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_6_LABEL').": ");
					$params->set('marker_extra_field_7',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_7_LABEL').": ");
					$params->set('marker_extra_field_8',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_8_LABEL').": ");
					$params->set('marker_extra_field_9',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_9_LABEL').": ");
					$params->set('marker_extra_field_10',	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_10_LABEL').": ");
					
					
					$params->set('marker_class',	'jicons-text');
					break;
	
				case 2 :
					// none
					$params->set('marker_address',	'');
					$params->set('marker_email',		'');
					$params->set('marker_telephone',	'');
					$params->set('marker_mobile',	'');
					$params->set('marker_fax',		'');
					$params->set('marker_misc',		'');
					$params->set('marker_skype',	'');
					
					$params->set('marker_twitter',	'');
					$params->set('marker_facebook',	'');
					$params->set('marker_linkedin',	'');
					$params->set('marker_website',	'');
					$params->set('marker_birthdate',	'');
					$params->set('marker_extra_field_1',	'');
					$params->set('marker_extra_field_2',	'');
					$params->set('marker_extra_field_3',	'');
					$params->set('marker_extra_field_4',	'');
					$params->set('marker_extra_field_5',	'');
					$params->set('marker_extra_field_6',	'');
					$params->set('marker_extra_field_7',	'');
					$params->set('marker_extra_field_8',	'');
					$params->set('marker_extra_field_9',	'');
					$params->set('marker_extra_field_10',	'');
					$params->set('marker_class',		'jicons-none');
					break;
	
				default :
					//echo $params->get('icon_address','con_address.png'); exit;
					//using Joomla core contact images
					$imageDefaultPath	= 'media/contacts/images/';
					$imageCEPath		= 'components/com_contactenhanced/assets/images/';
				//	$juri	=	JURI::root(); // Caused problems with SSL, had also to change the image relative parameter to false
					$juri	=	''; 
					// icons
					$image1 = JHTML::_('image',$juri.$params->get('icon_address',	$imageDefaultPath.'con_address.png'), 		JText::_('COM_CONTACTENHANCED_ADDRESS').": ", 	array('title' => JText::_('COM_CONTACTENHANCED_ADDRESS')), false); // had to Set relative to False in order to make it work with SSL
					$image2 = JHTML::_('image',$juri.$params->get('icon_email',		$imageDefaultPath.'emailButton.png'), 		JText::_('JGLOBAL_EMAIL').": ", 				array('title' => JText::_('JGLOBAL_EMAIL')), 	false);
					$image3 = JHTML::_('image',$juri.$params->get('icon_telephone',	$imageDefaultPath.'con_tel.png'), 			JText::_('COM_CONTACTENHANCED_TELEPHONE').": ", array('title' => JText::_('COM_CONTACTENHANCED_TELEPHONE')),	false);
					$image4 = JHTML::_('image',$juri.$params->get('icon_fax',		$imageDefaultPath.'con_fax.png'), 			JText::_('COM_CONTACTENHANCED_FAX').": ", 		array('title' => JText::_('COM_CONTACTENHANCED_FAX')),			false);
					$image5 = JHTML::_('image',$juri.$params->get('icon_misc',		$imageDefaultPath.'con_info.png'), 			JText::_('COM_CONTACTENHANCED_OTHER_INFORMATION').": ",	array('title' => JText::_('COM_CONTACTENHANCED_OTHER_INFORMATION')),	false);
					$image6 = JHTML::_('image',$juri.$params->get('icon_mobile',	$imageDefaultPath.'con_mobile.png'), 		JText::_('COM_CONTACTENHANCED_MOBILE').": ", 	array('title' => JText::_('COM_CONTACTENHANCED_MOBILE')),		false);
					$image7 = JHTML::_('image',$juri.$params->get('icon_skype',		$imageCEPath.'skype.png'), 					JText::_('COM_CONTACTENHANCED_SKYPE').": ", 	array('title' => JText::_('COM_CONTACTENHANCED_SKYPE')),		false);
					$image8 = JHTML::_('image',$juri.$params->get('icon_website',	$imageCEPath.'website.png'), 				JText::_('COM_CONTACTENHANCED_WEBSITE').": ", 	array('title' => JText::_('COM_CONTACTENHANCED_WEBSITE')),		false);
					$imageDOB =JHTML::_('image',$juri.$params->get('icon_birthdate',$imageCEPath.'birthdate.png'), 				JText::_('COM_CONTACTENHANCED_BIRTHDATE').": ", array('title' => JText::_('COM_CONTACTENHANCED_BIRTHDATE')),		false);
						
					$image9 = JHTML::_('image',$juri.$params->get('icon_twitter',	$imageCEPath.'twitter.png'),				JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_TWITTER_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_TWITTER_LABEL')),	false);
					$image10 = JHTML::_('image',$juri.$params->get('icon_facebook',	$imageCEPath.'facebook.png'),				JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_FACEBOOK_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_FACEBOOK_LABEL')),	false);
					$image11 = JHTML::_('image',$juri.$params->get('icon_linkedin',	$imageCEPath.'linkedin.png'),				JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_LINKEDIN_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_LINKEDIN_LABEL')),	false);
					
					$image_ef_1	= JHTML::_('image',$juri.$params->get('icon_extra_field_1',	$imageCEPath.'extra_field.png'),	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_1_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_1_LABEL')),	false);
					$image_ef_2	= JHTML::_('image',$juri.$params->get('icon_extra_field_2',	$imageCEPath.'extra_field.png'),	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_2_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_2_LABEL')),	false);
					$image_ef_3	= JHTML::_('image',$juri.$params->get('icon_extra_field_3',	$imageCEPath.'extra_field.png'),	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_3_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_3_LABEL')),	false);
					$image_ef_4	= JHTML::_('image',$juri.$params->get('icon_extra_field_4',	$imageCEPath.'extra_field.png'),	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_4_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_4_LABEL')),	false);
					$image_ef_5	= JHTML::_('image',$juri.$params->get('icon_extra_field_5',	$imageCEPath.'extra_field.png'),	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_5_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_5_LABEL')),	false);
					$image_ef_6	= JHTML::_('image',$juri.$params->get('icon_extra_field_6',	$imageCEPath.'extra_field.png'),	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_6_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_6_LABEL')),	false);
					$image_ef_7	= JHTML::_('image',$juri.$params->get('icon_extra_field_7',	$imageCEPath.'extra_field.png'),	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_7_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_7_LABEL')),	false);
					$image_ef_8	= JHTML::_('image',$juri.$params->get('icon_extra_field_8',	$imageCEPath.'extra_field.png'),	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_8_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_8_LABEL')),	false);
					$image_ef_9	= JHTML::_('image',$juri.$params->get('icon_extra_field_9',	$imageCEPath.'extra_field.png'),	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_9_LABEL').": ",	array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_9_LABEL')),	false);
					$image_ef_10= JHTML::_('image',$juri.$params->get('icon_extra_field_10',$imageCEPath.'extra_field.png'),	JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_10_LABEL').": ",array('title' => JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_EXTRA_FIELD_10_LABEL')),	false);
					
					
					$params->set('marker_extra_field_1',	$image_ef_1);
					$params->set('marker_extra_field_2',	$image_ef_2);
					$params->set('marker_extra_field_3',	$image_ef_3);
					$params->set('marker_extra_field_4',	$image_ef_4);
					$params->set('marker_extra_field_5',	$image_ef_5);
					$params->set('marker_extra_field_6',	$image_ef_6);
					$params->set('marker_extra_field_7',	$image_ef_7);
					$params->set('marker_extra_field_8',	$image_ef_8);
					$params->set('marker_extra_field_9',	$image_ef_9);
					$params->set('marker_extra_field_10',	$image_ef_10);
					
					
					$params->set('marker_address',	$image1);
					$params->set('marker_email',	$image2);
					$params->set('marker_telephone',$image3);
					$params->set('marker_fax',		$image4);
					$params->set('marker_misc',		$image5);
					$params->set('marker_mobile',	$image6);
					$params->set('marker_skype',	$image7);
					$params->set('marker_twitter',	$image9);
					$params->set('marker_facebook',	$image10);
					$params->set('marker_linkedin',	$image11);
					
					$params->set('marker_website',	$image8);
					$params->set('marker_birthdate',$imageDOB);
					$params->set('marker_class',	'jicons-icons');
					break;
			}
	}
	
	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param	int		$recordId	The primary key id for the item.
	 * @param	string	$urlVar		The name of the URL variable for the id.
	 *
	 * @return	string	The arguments to append to the redirect URL.
	 * @since	1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'a_id')
	{
		// Need to override the parent method completely.
		$tmpl		= JRequest::getCmd('tmpl');
		$template	= JRequest::getCmd('template');
		$append		= '';

		// Setup redirect info.
		if ($tmpl) {
			$append .= '&tmpl='.$tmpl;
		}
		if ($template) {
			$append .= '&template='.$template;
		}

		// TODO This is a bandaid, not a long term solution.
//		if ($layout) {
//			$append .= '&layout='.$layout;
//		}
		$append .= '&layout=edit';

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
	 * @since	1.6
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
	
}

