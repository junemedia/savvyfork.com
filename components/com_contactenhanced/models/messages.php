<?php
/**
 * @version		1.7.0
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * This models supports retrieving lists of contacts.
 *
 * @package		Joomlcon.Site
 * @subpackage	com_contactenhanced
 * @since		1.7
 */
class ContactenhancedModelMessages extends JModelList
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
				'id', 'con.id',
				'name', 'con.name',
				'alias', 'con.alias',
				'checked_out', 'con.checked_out',
				'checked_out_time', 'con.checked_out_time',
				'catid', 'con.catid', 'category_title',
				'state', 'con.published',
				'access', 'con.access', 'access_level',
				'created', 'con.created',
				'created_by', 'con.created_by',
				'ordering', 'con.ordering',
				'featured', 'con.featured',
				'language', 'con.language',
				'hits', 'con.hits',
				'publish_up', 'con.publish_up',
				'publish_down', 'con.publish_down',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = 'ordering', $direction = 'ASC')
	{
		$app = JFactory::getApplication();

		// List state information
		//$value = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		$value = JRequest::getVar('limit', $app->getCfg('list_limit', 10), 'default', 'uint');
		$this->setState('list.limit', $value);

		//$value = $app->getUserStateFromRequest($this->context.'.limitstart', 'limitstart', 0);
		$value = JRequest::getVar('limitstart', 0, 'default', 'uint');
		$this->setState('list.start', $value);

		$orderCol	= JRequest::getCmd('filter_order', 'con.ordering');
		if (!in_array($orderCol, $this->filter_fields)) {
			$orderCol = 'con.ordering';
		}
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', 'ASC');
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'DESC';
		}
		$this->setState('list.direction', $listOrder);

		$params = $app->getParams();
		$this->setState('params', $params);
		$user		= JFactory::getUser();

		if ((!$user->authorise('core.edit.state', 'com_contactenhanced')) &&  (!$user->authorise('core.edit', 'com_contactenhanced'))){
			// filter on published for those who do not have edit or edit.state rights.
			$this->setState('filter.published', 1);
		}

		$this->setState('filter.language',$app->getLanguageFilter());

		// process show_noauth parameter
		if (!$params->get('show_noauth')) {
			$this->setState('filter.access', true);
		}
		else {
			$this->setState('filter.access', false);
		}

		$this->setState('layout', JRequest::getCmd('layout'));
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
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		$published	= $this->getState('filter.published');
		// Compile the store id.
		$id .= ':'.(is_array($published) ? implode('_',$published) : $published);
		$id .= ':'.$this->getState('filter.access');
		$id .= ':'.$this->getState('filter.contact_id');
		$id .= ':'.$this->getState('filter.contact_id.include');
		
		$catid	= $this->getState('filter.category_id');
		if(is_array($catid)){
			$catid	= implode(',',$catid);
		}
		$id .= ':'.$catid;
		$id .= ':'.$this->getState('filter.category_id.include');
		$id .= ':'.$this->getState('filter.author_id');
		$id .= ':'.$this->getState('filter.author_id.include');
		$id .= ':'.$this->getState('filter.author_alias');
		$id .= ':'.$this->getState('filter.author_alias.include');
		$id .= ':'.$this->getState('filter.date_filtering');
		$id .= ':'.$this->getState('filter.date_field');
		$id .= ':'.$this->getState('filter.start_date_range');
		$id .= ':'.$this->getState('filter.end_date_range');
		$id .= ':'.$this->getState('filter.relative_date');

		return parent::getStoreId($id);
	}

	/**
	 * Get the master query for retrieving a list of contacts subject to the model state.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	function getListQuery()
	{
		$app		= JFactory::getApplication();
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		
		
	// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'msg.*')
		);
		$query->from('#__ce_messages AS msg');
		
		// Join over the category
		$query->select('cat.title AS category_title');
		$query->join('LEFT', '#__categories AS cat ON cat.id = msg.catid');
		
		// Join over the Contact
		$query->select('con.name AS contact_name');
		$query->join('LEFT', '#__ce_details AS con ON con.id = msg.contact_id');
		
		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '#__languages AS l ON l.lang_code = msg.language');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = msg.access');

		// Join over the categories.
	//	$query->select('c.title AS category_title');
	//	$query->join('LEFT', '#__categories AS c ON c.id = msg.catid');
		
		$query->where('msg.parent= ' . 0);
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('msg.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('msg.published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(msg.published = 0 OR msg.published = 1)');
		}

		// Filter by category.
		$categoryId = $this->getState('filter.category_id',JRequest::getVar('filter_category_id'));
		
		// Filter by search in  name
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('msg.id = '.(int) substr($search, 3));
			} else if (stripos($search, 'sender:') === 0) {
				$search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
				$query->where('(msg.from_name LIKE '.$search.' OR msg.from_email LIKE '.$search.')');
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(msg.message LIKE '.$search.')');				
			}
		}

		// Filter by published state
		$published = $this->getState('filter.published');

		if (is_numeric($published)) {
			// Use contact state if badcats.id is null, otherwise, force 0 for unpublished
			$query->where('msg.published = ' . (int) $published);
		}
		else if (is_array($published)) {
			JArrayHelper::toInteger($published);
			$published = implode(',', $published);
			$query->where( 'msg.published IN ('.$published.')');
		}


		// Filter by a single or group of contacts.
		$contactId = $this->getState('filter.contact_id');

		if (is_numeric($contactId)) {
			$type = $this->getState('filter.contact_id.include', true) ? '= ' : '<> ';
			$query->where('con.id '.$type.(int) $contactId);
		}
		else if (is_array($contactId)) {
			JArrayHelper::toInteger($contactId);
			$contactId = implode(',', $contactId);
			$type = $this->getState('filter.contact_id.include', true) ? 'IN' : 'NOT IN';
			$query->where('con.id '.$type.' ('.$contactId.')');
		}

		// Filter by a single or group of categories
		$categoryId = $this->getState('filter.category_id');

		if (is_numeric($categoryId)) {
			$type = $this->getState('filter.category_id.include', true) ? '= ' : '<> ';

			// Add subcategory check
			$includeSubcategories = $this->getState('filter.subcategories', false);
			$categoryEquals = 'msg.catid '.$type.(int) $categoryId;

			if ($includeSubcategories) {
				$levels = (int) $this->getState('filter.max_category_levels', '1');
				// Create a subquery for the subcategory list
				$subQuery = $db->getQuery(true);
				$subQuery->select('sub.id');
				$subQuery->from('#__categories as sub');
				$subQuery->join('INNER', '#__categories as this ON sub.lft > this.lft AND sub.rgt < this.rgt');
				$subQuery->where('this.id = '.(int) $categoryId);
				if ($levels >= 0) {
					$subQuery->where('sub.level <= this.level + '.$levels);
				}

				// Add the subquery to the main query
				$query->where('('.$categoryEquals.' OR con.catid IN ('.$subQuery->__toString().'))');
			}
			else {
				$query->where($categoryEquals);
			}
		}
		else if (is_array($categoryId) && (count($categoryId) > 0)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			if (!empty($categoryId)) {
				$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
				$query->where('msg.catid '.$type.' ('.$categoryId.')');
			}
		}else if (strpos($categoryId, ',') > 0) {
			$type = $this->getState('filter.category_id.include', true) ? 'IN' : 'NOT IN';
			$query->where('msg.catid '.$type.' ('.$categoryId.')');
		}

		// Filter by author
		$authorId = $this->getState('filter.author_id');
		$authorWhere = '';

		if (is_numeric($authorId)) {
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<> ';
			$authorWhere = 'con.created_by '.$type.(int) $authorId;
		}
		else if (is_array($authorId)) {
			JArrayHelper::toInteger($authorId);
			$authorId = implode(',', $authorId);

			if ($authorId) {
				$type = $this->getState('filter.author_id.include', true) ? 'IN' : 'NOT IN';
				$authorWhere = 'con.created_by '.$type.' ('.$authorId.')';
			}
		}

		// Filter by author alias
		$authorAlias = $this->getState('filter.author_alias');
		$authorAliasWhere = '';

		if (is_string($authorAlias)) {
			$type = $this->getState('filter.author_alias.include', true) ? '= ' : '<> ';
			$authorAliasWhere = 'con.created_by_alias '.$type.$db->Quote($authorAlias);
		}
		else if (is_array($authorAlias)) {
			$first = current($authorAlias);

			if (!empty($first)) {
				JArrayHelper::toString($authorAlias);

				foreach ($authorAlias as $key => $alias)
				{
					$authorAlias[$key] = $db->Quote($alias);
				}

				$authorAlias = implode(',', $authorAlias);

				if ($authorAlias) {
					$type = $this->getState('filter.author_alias.include', true) ? 'IN' : 'NOT IN';
					$authorAliasWhere = 'con.created_by_alias '.$type.' ('.$authorAlias .
						')';
				}
			}
		}

		if (!empty($authorWhere) && !empty($authorAliasWhere)) {
			$query->where('('.$authorWhere.' OR '.$authorAliasWhere.')');
		}
		else if (empty($authorWhere) && empty($authorAliasWhere)) {
			// If both are empty we don't want to add to the query
		}
		else {
			// One of these is empty, the other is not so we just add both
			$query->where($authorWhere.$authorAliasWhere);
		}

		// Filter by start and end dates.
		$nullDate	= $db->Quote($db->getNullDate());
		$nowDate	= $db->Quote(JFactory::getDate()->toSQL());
		

		$query->where('(con.publish_up = '.$nullDate.' OR con.publish_up <= '.$nowDate.')');
		$query->where('(con.publish_down = '.$nullDate.' OR con.publish_down >= '.$nowDate.')');

		// Filter by Date Range or Relative Date
		$dateFiltering = $this->getState('filter.date_filtering', 'off');
		$dateField = $this->getState('filter.date_field', 'con.created');

		switch ($dateFiltering)
		{
			case 'range':
				$startDateRange = $db->Quote($this->getState('filter.start_date_range', $nullDate));
				$endDateRange = $db->Quote($this->getState('filter.end_date_range', $nullDate));
				$query->where('('.$dateField.' >= '.$startDateRange.' AND '.$dateField .
					' <= '.$endDateRange.')');
				break;

			case 'relative':
				$relativeDate = (int) $this->getState('filter.relative_date', 0);
				$query->where($dateField.' >= DATE_SUB('.$nowDate.', INTERVAL ' .
					$relativeDate.' DAY)');
				break;

			case 'off':
			default:
				break;
		}

		// process the filter for list views with user-entered filters
		$params = $this->getState('params');

		if ((is_object($params)) && ($params->get('filter_field')) && ($filter = $this->getState('list.filter'))) {
			// clean filter variable
			$filter = JString::strtolower($filter);
		
			
			$search_fields		= (array)$app->input->get('search_field',$params->get('search_fields',array('name')));
			$searchphrase		= $app->input->getString('searchphrase',		$params->get('searchphrase','all'));
			
			switch ($searchphrase) {
				case 'exact':
					$text		= $db->Quote('%'.($filter).'%', false);
					$wheres2	= array();
					foreach ($search_fields as $sField) {
						$wheres2[]	= 'con.'.$sField.' LIKE '.$text;
					}
					$query->where( '(' . implode(') OR (', $wheres2) . ')' );
					break;
	
				case 'all':
				case 'any':
				default:
					$words = explode(' ', $filter);
					$wheres = array();
					foreach ($words as $word) {
						$word		= $db->Quote('%'.($word).'%', false);
						$wheres2	= array();
						foreach ($search_fields as $sField) {
							$wheres2[]	= 'con.'.$sField.' LIKE '.$word;
						}
						$wheres[]	= implode(' OR ', $wheres2);
						
					}
					
					$query->where( '(' . implode(($searchphrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')' );
					break;
					
			}
		}

		// Filter by language
		if ($this->getState('filter.language')) {
			$query->where('con.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
			$query->where('(con.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').') OR con.language IS NULL)');
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'msg.id');
		$orderDirn	= $this->state->get('list.direction','DESC');
		// Add the list ordering clause.
		$query->order($orderCol.' '.$orderDirn);
		
		//echo nl2br(str_replace('#__','j30_',$query)); exit;
		return $query;
	}

	/**
	 * Method to get a list of contacts.
	 *
	 * Overriden to inject convert the attribs field into a JParameter object.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 * @since	1.6
	 */
	public function getItems()
	{
		$items	= parent::getItems();
		if(!isset($items) OR !$items){
			return array();
		}
	//	echo '<pre>'; print_r(JFactory::getDbo()->getLog()); exit();
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$guest	= $user->get('guest');
		$groups	= $user->getAuthorisedViewLevels();

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_contactenhanced', true);

		// Convert the parameter fields into objects.
		foreach ($items as &$item)
		{
			
		}

		return $items;
	}
	public function getStart()
	{
		return $this->getState('list.start');
	}
}
