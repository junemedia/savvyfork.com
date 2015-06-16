<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * @package		com_contactenhanced
*/
class ContactenhancedModelContacts extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'user_id', 'a.user_id',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
				'ul.name', 'linked_user'
				
			);
			
			$jversion = new JVersion();
			$app	= JFactory::getApplication();
			$assoc	= isset($app->item_associations) ? $app->item_associations : 0;
			if ($assoc AND version_compare( $jversion->getShortVersion(), '3.1') >= 0)
			{
				$config['filter_fields'][] = 'association';
			}
		}

		parent::__construct($config);
	}
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = 'a.name', $direction = 'asc')
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout', 'default')) {
			$this->context .= '.'.$layout;
		}

		$search = $app->getUserStateFromRequest($this->context.'.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$published = $app->getUserStateFromRequest($this->context.'.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$categoryId = $app->getUserStateFromRequest($this->context.'.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id.= ':' . $this->getState('filter.search');
		$id.= ':' . $this->getState('filter.access');
		$id.= ':' . $this->getState('filter.published');
		$id.= ':' . $this->getState('filter.category_id');
		$id.= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}
	
	/**
	 * @return	array
	 */
	public function getCategories()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'c.*')
		);
		$query->from('#__categories AS c');
		
		$query->where('c.extension = ' . $db->Quote('com_contactenhanced'));
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('c.access = ' . (int) $access);
		}
		
		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('c.published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(c.published = 0 OR c.published = 1)');
		}
		
		$db->setQuery($query);
		
		return $db->loadObjectlist();
	}

	/**
	 * @param	boolean	True to join selected foreign information
	 *
	 * @return	string
	 */
	protected function getListQuery($resolveFKs = true)
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$user	= JFactory::getUser();
		$app	= JFactory::getApplication();
		$jversion = new JVersion();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.name, a.alias, a.checked_out, a.checked_out_time, a.catid, a.user_id' .
				', a.published, a.access, a.created, a.created_by, a.ordering, a.featured, a.language'.
				', a.publish_up, a.publish_down'
			)
		);
		$query->from('#__ce_details AS a');

		// Join over the users for the linked user.
		$query->select('ul.name AS linked_user');
		$query->join('LEFT', '#__users AS ul ON ul.id=a.user_id');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', $db->quoteName('#__languages').' AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Join over the associations.
		$assoc = isset($app->item_associations) ? $app->item_associations : 0;
		if ($assoc AND version_compare( $jversion->getShortVersion(), '3.1') >= 0)
		{
			$query->select('COUNT(asso2.id)>1 as association')
				->join('LEFT', '#__associations AS asso ON asso.id = a.id AND asso.context=' . $db->quote('com_contactenhanced.item'))
				->join('LEFT', '#__associations AS asso2 ON asso2.key = asso.key')
				->group('a.id');
		}
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = ' . (int) $access);
		}

		// Implement View Level Access
		if (!$user->authorise('core.admin'))
		{
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$groups.')');
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '') {
			$query->where('(a.published = 0 OR a.published = 1 OR a.published = -3)');
		}

		// Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('a.catid = '.(int) $categoryId);
		}
		elseif (is_array($categoryId)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('a.catid IN ('.$categoryId.')');
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0) {
				$search = $db->Quote('%'.$db->escape(substr($search, 7), true).'%');
				$query->where('(ua.name LIKE '.$search.' OR ua.username LIKE '.$search.')');
			}
			else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(a.name LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = '.$db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.name');
		$orderDirn	= $this->state->get('list.direction', 'asc');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title' || $orderCol == 'c.title') {
			$orderCol = 'c.title '.$orderDirn.', a.ordering';
		}
		$query->order($db->escape($orderCol.' '.$orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}

	public function getContactCount()
	{
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		
		// Count the number of contacts
		$query->select('count(id)');
		$query->from('#__ce_details');
		$db->setQuery($query);
		return $db->loadResult();
	}
}
