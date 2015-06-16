<?php
/*----------------------------------------------------------------------
# YooRock! YooRecipe Module 1.0.0
# ----------------------------------------------------------------------
# Copyright (C) 2011 YooRock. All Rights Reserved.
# Coded by: YooRock!
# License: GNU GPL v2
# Website: http://extensions.yoorock.fr
------------------------------------------------------------------------*/

defined('_JEXEC') or die('Direct Access to this location is not allowed.');
 
class ModYooRecipeHelper
{
    /**
     * Returns a list of yoorecipe items
     */
    public static function getRecipes($params)
    {	
        // get a reference to the database
        $db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		$app 	= JFactory::getApplication();
		$menu = $app->getMenu();
		$user	= JFactory::getUser();
		$partnerId = JRequest::getVar('user_id',0);				
		
		if($partnerId == 0)
		{
			if( $user->id != 0)
			{
				$partnerId = $user->id;
			}
		}	
		
		if ($menu->getActive() == $menu->getDefault()) 
		{
			$partnerId = 0;
		}
		
		if($partnerId != 0)
		{
			$usergroups = JUserHelper::getUserGroups($partnerId);	
		}
		else
		{
			return array();
		}		
		
		$max_nb_recipes = $params->get('max_nb_recipes', 5);
		$layout 		= $params->get('layout', 'random');
		
		$total = (int)$params->total;
		$limit = (int)$params->limit;
		$limitStart = (int)$params->limitStart;
		
		if ($limit > $total)
		{
			$limitStart = 0;
		}
			
		
		//If limitstart is greater than total (i.e. we are asked to display records that don't exist)
		//then set limitstart to display the last natural page of results		
		if ($limitStart > $total - $limit)
		{
			$limitStart = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}
		
		if($limitStart%$limit != 0)
		{
			$limitStart = max(0, (int) floor($limitStart / $limit) * $limit);
		}
		
		// get a list of $max_nb_recipes recipes 
		$query->select( 'r.id, r.access, c.cat_id as cat_id, r.title, r.alias, r.description, r.preparation, r.nb_persons, r.difficulty, r.cost, ' .
						'r.creation_date, r.publish_up, r.publish_down, r.preparation_time, r.cook_time, r.wait_time, r.featured, ' . 
						'r.created_by, r.picture, r.picture2, r.picture3, r.published, r.nb_views, r.note, r.price');
		$query->select('CASE WHEN CHARACTER_LENGTH(alias) THEN CONCAT_WS(\':\', r.id, r.alias) ELSE id END as slug');

		// From the recipe table
		$query->from('#__yoorecipe r');
	
		// Join over cross categories
		$query->join('LEFT', '#__yoorecipe_categories c on c.recipe_id = r.id');
		
		if(isset($usergroups) && !in_array(11,$usergroups) && !in_array(12,$usergroups)) //its a user
		{
			// Join over favourites
			$query->join('LEFT', '#__yoorecipe_favourites fr on fr.recipe_id = r.id');
			$query->where('fr.user_id = ' . $db->quote($partnerId));
		}
		
		// Prepare where clause
		$query->where('r.published = 1');
		$query->where('r.validated = 1');
		
		// Filter by language
		if ($app->getLanguageFilter()) {
			$query->where('r.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}
		
		// Filter by access level.		
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$query->where('r.access IN ('.$groups.')');
		
		//Filter by author id - add by howe
		if(isset($usergroups) && in_array(11,$usergroups) || in_array(12,$usergroups)) //a partner
		{
			if($partnerId != 0)
			{
				$query->where('r.created_by ='.$partnerId);
			}
		}		
		
		// Filter by start and end dates.
		$nullDate 	= $db->Quote($db->getNullDate());
		$nowDate 	= $db->Quote(JFactory::getDate()->toSql());

		$query->where('(r.publish_up = ' . $nullDate . ' OR r.publish_up <= ' . $nowDate . ')');
		$query->where('(r.publish_down = ' . $nullDate . ' OR r.publish_down >= ' . $nowDate . ')');
		
		// Filter by category if needed
		if ($params->get('category') != 0) {
			$query->where('c.cat_id = ' . $db->quote($params->get('category')));
		}
		
		// Filter pictureless recipes if needed
		if ($params->get('show_recipes_without_pictures', 0) != 1) {
			$query->where('r.picture != ' . $db->quote(''));
		}
		
		// adapt request to fit the requested layout
		/*if ($layout == 'random') {
			$query->order('rand() limit ' . $max_nb_recipes);
		}
		else if ($layout == 'featured') {
			$query->where('featured = ' . $db->quote('1'));
			$query->order('rand() limit ' . $max_nb_recipes);
		}
		else if ($layout == 'most_viewed') {
			$query->order('r.nb_views desc limit ' . $max_nb_recipes);
		}
		else if ($layout == 'best_rated') {
			$query->order('r.note desc limit ' . $max_nb_recipes);
		}
		else if ($layout == 'latest') {
			$query->order('r.creation_date desc limit ' . $max_nb_recipes);
		}*/
		if ($layout == 'random') {
			$query->order('rand() limit ' . $limitStart . ',' . $limit);
		}
		else if ($layout == 'featured') {
			$query->where('featured = ' . $db->quote('1'));
			$query->order('rand() limit ' . $limitStart . ',' . $limit);
		}
		else if ($layout == 'most_viewed') {
			$query->order('r.nb_views desc limit ' . $limitStart . ',' . $limit);
		}
		else if ($layout == 'best_rated') {
			$query->order('r.note desc limit ' . $limitStart . ',' . $limit);
		}
		else if ($layout == 'latest') {
			$query->order('r.creation_date desc limit ' . $limitStart . ',' . $limit);
		}
		
		$query->group('r.id');

		$db->setQuery($query);
        $items = ($items = $db->loadObjectList())?$items:array();
 
        return $items;
    }
	
	/**
     * Returns a list of yoorecipe ingredients
	 */
	public static function getIngredientsByRecipeId($id)
	{
		// get a reference to the database
        $db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		
		// from the recipe ingredients table
		$query->select('id, recipe_id, quantity, unit, description');
		$query->from('#__yoorecipe_ingredients');
		$query->where('recipe_id = ' . $db->quote($id));

		$db->setQuery($query);
        $ingredients = ($ingredients = $db->loadObjectList())?$ingredients:array();
 
        return $ingredients;
    }
	
	public static function getTotalRecipes()
	{
		// get a reference to the database
        $db 	= JFactory::getDBO();
		$query 	= $db->getQuery(true);
		$app 	= JFactory::getApplication();
		$menu = $app->getMenu();
		$user	= JFactory::getUser();
		$partnerId = JRequest::getVar('user_id',0);		
		$result = 0;
		
		if($partnerId == 0)
		{
			if( $user->id != 0)
			{
				$partnerId = $user->id;
			}
		}	
		
		if ($menu->getActive() == $menu->getDefault()) 
		{
			$partnerId = 0;
		}
		
		if($partnerId != 0)
		{
			$usergroups = JUserHelper::getUserGroups($partnerId);	
		}
		else
		{
			return $result;
		}		
		
		$query->select('count(r.id)');

		// From the recipe table
		$query->from('#__yoorecipe r');
	
		if(isset($usergroups) && !in_array(11,$usergroups) && !in_array(12,$usergroups)) //its a user
		{
			// Join over favourites
			$query->join('LEFT', '#__yoorecipe_favourites fr on fr.recipe_id = r.id');
			$query->where('fr.user_id = ' . $db->quote($partnerId));
		}
		
		// Prepare where clause
		$query->where('r.published = 1');
		$query->where('r.validated = 1');
		
		// Filter by language
		if ($app->getLanguageFilter()) {
			$query->where('r.language in ('.$db->quote(JFactory::getLanguage()->getTag()).','.$db->quote('*').')');
		}
		
		// Filter by access level.		
		$groups	= implode(',', $user->getAuthorisedViewLevels());
		$query->where('r.access IN ('.$groups.')');
		
		//Filter by author id - add by howe
		if(isset($usergroups) && in_array(11,$usergroups) || in_array(12,$usergroups)) //a partner
		{
			if($partnerId != 0)
			{
				$query->where('r.created_by ='.$partnerId);
			}
		}		
		
		// Filter by start and end dates.
		$nullDate 	= $db->Quote($db->getNullDate());
		$nowDate 	= $db->Quote(JFactory::getDate()->toSql());

		$query->where('(r.publish_up = ' . $nullDate . ' OR r.publish_up <= ' . $nowDate . ')');
		$query->where('(r.publish_down = ' . $nullDate . ' OR r.publish_down >= ' . $nowDate . ')');

		$db->setQuery($query);
        $result = (int)$db->loadResult();

		return $result;
	}
	
	public static function getUserType()
	{
		$app 	= JFactory::getApplication();
		$menu = $app->getMenu();
		$user	= JFactory::getUser();
		$partnerId = JRequest::getVar('user_id',0);		
		$result = 0;
		
		if($partnerId == 0)
		{
			if( $user->id != 0)
			{
				$partnerId = $user->id;
			}
		}	
		
		if ($menu->getActive() == $menu->getDefault()) 
		{
			$partnerId = 0;
		}
		
		if($partnerId != 0)
		{
			$usergroups = JUserHelper::getUserGroups($partnerId);	
		}
		else
		{
			return "guest";
		}

		if(isset($usergroups) && !in_array(11,$usergroups) && !in_array(12,$usergroups)) //its a user
		{
			return "user";
		}
		
		if(isset($usergroups) && in_array(11,$usergroups) || in_array(12,$usergroups)) //a partner
		{
			return "partner";
		}
	}
}