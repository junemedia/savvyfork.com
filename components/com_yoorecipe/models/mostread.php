<?php
/*------------------------------------------------------------------------
# com_yoorecipe - YooRecipe! Joomla 1.6 recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://extensions.yoorock.fr
# Technical Support:  Forum - http://extensions.yoorock.fr/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * YooRecipe Model
 */
class YooRecipeModelMostRead extends JModelList
{
	/**
	* Items total
	* @var integer
	*/
	var $_total = null;

	/**
	* Pagination object
	* @var object
	*/
	var $_pagination = null;
  
	function __construct()
	{
		parent::__construct();
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
		// List state information
		$app 				= JFactory::getApplication();
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$limit				= $app->getUserStateFromRequest('global.list.limit', 'limit', $yooRecipeparams->get('list_length', 10), 'int');

		$input 	= JFactory::getApplication()->input;
		$this->setState('list.limit', $limit);
		$this->setState('list.start', $input->get('limitstart', '0', 'INT'));
		
		// List state information
		$this->setState('orderCol', 'nb_views');
		$this->setState('drn', 'desc');
		$this->setState('filter.access', true);
		$this->setState('filter.language', $app->getLanguageFilter());
	}
 
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'YooRecipe', $prefix = 'YooRecipeTable', $config = array()) 
	{
		return JTable::getInstance($type, $prefix, $config);
	}
	
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Create a new query object.		
		$user	= JFactory::getUser();
		$db		= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		//$catId = JRequest::getVar('catid','0');		
		
		// Join over the users for the author.
		$yooRecipeparams 	= JComponentHelper::getParams('com_yoorecipe');
		$showAuthorName 	= $yooRecipeparams->get('show_author_name', 'username');
		
		if ($showAuthorName == 'username') {
			$ex_field = ",ua.username AS author_name";
		} else if ($showAuthorName == 'name') {
			$ex_field = ",ua.name AS author_name";
		}
	
		// Prepare where clause
		$where = " WHERE r.published = 1 AND r.validated = 1";
		
		/*if((int)$catId != 0)
		{
			$where .= " AND cc.cat_id=".(int)$catId;
		}*/
		
		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			
			$groups	= implode(',', $user->getAuthorisedViewLevels());
			$where .= " AND r.access IN (".$groups.')';
		}
		
		// Filter by language
		if ($this->getState('filter.language')) {
			$where .= ' AND r.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')';
		}
		
		// Filter by start and end dates.
		$nullDate = $db->quote($db->getNullDate());
		$nowDate = $db->quote(JFactory::getDate()->toSQL());		
		
        //$nowDate = substr($nowDate,0,-5) . "00:00";
        
		$where .= ' AND (r.publish_up = ' . $nullDate . ' OR r.publish_up <= ' . $nowDate . ')';
		$where .= ' AND (r.publish_down = ' . $nullDate . ' OR r.publish_down >= ' . $nowDate . ')';		
		$sql="select SQL_CALC_FOUND_ROWS count(uc.user_id) as sharecount, r.id, r.title,r.created_by,r.picture, r.featured,CASE WHEN CHARACTER_LENGTH(r.alias) THEN CONCAT_WS(':', r.id, r.alias) ELSE r.id END as slug FROM #__yoorecipe as r left join #__user_activity as uc on uc.recipe_id = r.id ".$where." AND uc.type_id in (1,2,3,4) group by r.id order by sharecount desc";

		return $sql;
	}
}