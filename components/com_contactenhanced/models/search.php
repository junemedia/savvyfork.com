<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * @package		com_contactenhanced
*/
class ContactenhancedModelSearch extends JModelList
{
	/**
	 * Content items data
	 *
	 * @var array
	 */
	protected $_items = null;

	/**
	 * Method to get a list of items.
	 *
	 * @return	mixed	An array of objects on success, false on failure.
	 */
	public function &getItems()
	{
		// Invoke the parent getItems method to get the main list
		$items = parent::getItems();

		/**
		 * BEGIN: Change for user mgleixner
		 */
		/**
		 * Search returned a blank result
		 */
		$params	= $this->getState('params');

		if( in_array('extra_field_1', $params->get('search_fields',array('name')))
			AND count( $params->get('search_fields',array('name'))) == 1
		){

			// If searching in extra_field_1 ignore result and search again.
			$items	= null;
			$q	= $this->getState('filter.searchquery');
			//Add a space in order to remove in the first loop
			$q	= '|'.$q.' ';
			while (!$items AND strlen($q) > 2) {
				$q	= substr($q,0,-1);
				$this->setState('filter.searchquery',$q);
				//echo $this->getState('filter.searchquery').'<br />';
				$this->_getListQuery();
				// Get a storage key.
				$store = $this->getStoreId();
				// Clean internal storage.
				$this->cache[$store]	= null;
				$items = parent::getItems();

			}

		}

		/**
		 * END: Change for mgleixner
		 */

		// Convert the params field into an object, saving original in _params
		for ($i = 0, $n = count($items); $i < $n; $i++) {
			$item = &$items[$i];
			if (!isset($this->_params)) {
				$params = new JRegistry();
				$params->loadString($item->params);
				$item->params = $params;
			}
		}
		$this->_items	= $items;
		return $items;
	}
	/**
	 * Method added in order to work with custom code for mgleixner@in-screen.de
	 */
	protected function _getListQuery()
	{
		$this->query = $this->getListQuery();
		return $this->query;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$params	= $this->getState('params');

		//sqlsrv changes
		$case_when = ' CASE WHEN ';
		$case_when .= $query->charLength('a.alias');
		$case_when .= ' THEN ';
		$a_id = $query->castAsChar('a.id');
		$case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
		$case_when .= ' ELSE ';
		$case_when .= $a_id.' END as slug';

		// Select required fields from the categories.
		$query->select($this->getState('list.select', 'a.*') . ','
			.$case_when
			//. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug '
		);
		$query->from('#__ce_details AS a');
		$query->where('a.access IN ('.$groups.')');



		// Filter by state
		$state = $this->getState('filter.published');
		if (is_numeric($state)) {
			$query->where('a.published = '.(int) $state);
		}
		// Filter by start and end dates.
		$nullDate = $db->Quote($db->getNullDate());
		$nowDate = $db->Quote(JFactory::getDate()->toSQL());

		if ($this->getState('filter.publish_date')){
			$query->where('(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ')');
			$query->where('(a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')');
		}

		// Filter by language
		if ($this->getState('filter.language')) {
			$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
		}

		if ($this->getState('filter.country')) {
			$query->where('a.country = '.$db->quote($this->getState('filter.country')));
		}
		if ($this->getState('filter.state')) {
			$query->where('a.state = '.$db->quote($this->getState('filter.state')));
		}
		if ($this->getState('filter.suburb')) {
			$query->where('a.suburb = '.$db->quote($this->getState('filter.suburb')));
		}
		if ($this->getState('filter.con_position')) {
			$query->where('a.con_position = '.$db->quote($this->getState('filter.con_position')));
		}
		if ($this->getState('filter.catids')) {
			$query->where('a.catid IN ('.($this->getState('filter.catids')). ')');
		}

		// Get Search related filter:
		$search				= trim($this->getState('filter.searchquery'));

		if (($search))
		{
			// clean filter variable
			$filter_txt	= JString::strtolower($search);

			$searchphrase		= JRequest::getVar('searchphrase',		$params->get('searchphrase','all'));
			$search_fields		= (array)JRequest::getVar('search_fields',		$params->get('search_fields',array('name')));

			switch ($searchphrase) {
				case 'starts':
					$text		= $db->quote(($filter_txt).'%', true);
					$wheres2	= array();
					foreach ($search_fields as $sField) {
						if(!empty($sField)){
							$wheres2[]	= $sField.' LIKE '.$text;
						}
					}
					$query->where( '(' . implode(') OR (', $wheres2) . ')' );
					break;
				case 'exact':
						$text		= $db->quote('%'.($filter_txt).'%', true);
						$wheres2	= array();
						foreach ($search_fields as $sField) {
							if(!empty($sField)){
								$wheres2[]	= $sField.' LIKE '.$text;
							}
						}
						$query->where( '(' . implode(') OR (', $wheres2) . ')' );
						break;
				case 'all':
				case 'any':
				default:
					$words = explode(' ', $filter_txt);
					$wheres = array();
					foreach ($words as $word) {
						$word		= $db->quote('%'.($word).'%', false);
						$wheres2	= array();
						foreach ($search_fields as $sField) {
							$wheres2[]	= $sField.' LIKE '.$word;
						}
						$wheres[]	= implode(' OR ', $wheres2);

					}

					$query->where( '(' . implode(($searchphrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')' );
					break;

			}

		}

		$exclude_categories	= JRequest::getVar('exclude-categories',$params->get('exclude-categories'));

		if(count($exclude_categories)>0){
			if(is_array($exclude_categories)){
				for ($i=0;$i<count($exclude_categories);$i++){
					if(!$exclude_categories[$i]){
						unset($exclude_categories[$i]);
					}
				}
				$exclude_categories	= implode( ',', $exclude_categories);
			}
			if(trim($exclude_categories)){
				$query->where( ' a.catid NOT IN ('.( $exclude_categories).') ' );
			}
		}
		$ordering 	= array();
		if (trim($this->getState('list.ordering')) == 'name' ) {
			$ordering[] 	= ($db->escape('a.sortname1').' '.$db->escape($this->getState('list.direction', 'ASC')));
			$ordering[] 	= ($db->escape('a.sortname2').' '.$db->escape($this->getState('list.direction', 'ASC')));
			$ordering[] 	= ($db->escape('a.sortname3').' '.$db->escape($this->getState('list.direction', 'ASC')));
			$ordering[] 	= ($db->escape('a.name').' '.$db->escape($this->getState('list.direction', 'ASC')));
		}else{
			$ordering[] 	= ($db->escape($this->getState('list.ordering', 'a.ordering')).' '.$db->escape($this->getState('list.direction', 'ASC')));
		}

		// Add the list ordering clause.
		if(JRequest::getVar('layout', $params->get('search_results_layout')) == 'categories'){
			$query->order(('a.catid, '.implode(', ',$ordering)));
		}else{
			$query->order(implode(', ',$ordering));
		}
		//echo nl2br(str_replace('#__','j30_',$query)); exit;
		return $query;
	}

	public function getCategories(){
		// Sanity check
		if(!is_array($this->_items)){
			return null;
		}
		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$params	= $this->getState('params');

		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		// Create a new query object.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
		$params	= $this->getState('params');

		//sqlsrv changes
		$case_when1 = ' CASE WHEN ';
		$case_when1 .= $query->charLength('c.alias');
		$case_when1 .= ' THEN ';
		$c_id = $query->castAsChar('c.id');
		$case_when1 .= $query->concatenate(array($c_id, 'c.alias'), ':');
		$case_when1 .= ' ELSE ';
		$case_when1 .= $c_id.' END as slug';
		// Select required fields from the categories.
		// right join with c for category
		$query->select('c.id,c.title');
		//$query->select('CASE WHEN CHAR_LENGTH(c.alias) THEN CONCAT_WS(":", c.id, c.alias) ELSE c.id END as slug');
		$query->select($case_when1);
		$query->from('#__categories as c');
		$query->where('(c.extension='.$db->Quote('com_contactenhanced').')'); //  OR c.extension='.$db->Quote('system').'


		// Filter by state
		$state = $this->getState('filter.published');
		if (is_numeric($state)) {
			$query->where('c.published = '.(int) $state);
		}


		$query->where( ' id IN ('.( implode(',', $this->state->get('com_contactenhanced.search.catids',array()))).') ' );

		// Add the list ordering clause.
		$query->order(($this->getState('list.catordering','c.title'). ' '.$this->getState('list.catdirection','ASC')));

		$db->setQuery($query);
		return $db->loadObjectList();

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
		// Initialise variables.
		$app	= JFactory::getApplication();
		$input 	= $app->input;
		$params	= $app->getParams('com_contactenhanced');
		$db		= $this->getDbo();
		// List state information
		$format = JRequest::getWord('format');
		if ($format=='feed') {
			$limit = $app->getCfg('feed_limit');
		}
		else {
			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
		}
		$this->setState('list.limit', $limit);

		$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
		$this->setState('list.start', $limitstart);

		//Get default value from parameters
		$ordering	= explode(' ', $params->get('contact_ordering','ordering ASC'));

		$orderCol	= JRequest::getCmd('filter_order', $ordering[0]);
		$this->setState('list.ordering', $orderCol);

		$listOrder	=  JRequest::getCmd('filter_order_Dir', $ordering[1]);
		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', ''))) {
			$listOrder = 'ASC';
		}
		$this->setState('list.direction', $listOrder);


		$id = JRequest::getVar('category_id', JRequest::getVar('id'), '', 'int');
		$this->setState('category.id', $id);

		$user = JFactory::getUser();
		if ((!$user->authorise('core.edit.state', 'com_contactenhanced')) &&  (!$user->authorise('core.edit', 'com_contactenhanced'))){
			// limit to published for people who can't edit or edit.state.
			$this->setState('filter.published', 1);

			// Filter by start and end dates.
			$this->setState('filter.publish_date', true);
		}
		$this->setState('filter.language',$app->getLanguageFilter());
		$this->setState('filter.searchquery',JRequest::getString('q'));

		// catids is a hidden field in mod_ce_search with multiple ids separated by comas
		$this->setState('filter.catids',	$input->getString('catids', false));

		$this->setState('filter.country',	$input->getString('country'));
		$this->setState('filter.state',		$input->getString('state'));
		$this->setState('filter.suburb',	$input->getString('suburb'));
		$this->setState('filter.con_position',$input->getString('con_position'));

		// Load the parameters.
		$this->setState('params', $params);
	}


}
