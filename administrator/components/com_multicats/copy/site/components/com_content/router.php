<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Build the route for the com_content component
 *
 * @return  array  An array of URL arguments
 * @return  array  The URL arguments to use to assemble the subsequent URL.
 * @since	1.5
 */
function ContentBuildRoute(&$query)
{
	$segments	= array();

	// get a menu item based on Itemid or currently active
	$app		= JFactory::getApplication();
	$menu		= $app->getMenu();
	$params		= JComponentHelper::getParams('com_content');
	$advanced	= $params->get('sef_advanced_link', 0);

	// we need a menu item.  Either the one specified in the query, or the current active one if none specified
	if (empty($query['Itemid']))
	{
		$menuItem = $menu->getActive();
		$menuItemGiven = false;
	}
	else {
		$menuItem = $menu->getItem($query['Itemid']);
		$menuItemGiven = true;
	}

	// check again
	if ($menuItemGiven && isset($menuItem) && $menuItem->component != 'com_content')
	{
		$menuItemGiven = false;
		unset($query['Itemid']);
	}

	if (isset($query['view']))
	{
		$view = $query['view'];
	}
	else
	{
		// we need to have a view in the query or it is an invalid URL
		return $segments;
	}

	// are we dealing with an article or category that is attached to a menu item?
	if (($menuItem instanceof stdClass) && $menuItem->query['view'] == $query['view'] && isset($query['id']) && $menuItem->query['id'] == (int) $query['id'])
	{
		unset($query['view']);

		if (isset($query['catid']))
		{
			unset($query['catid']);
		}

		if (isset($query['layout']))
		{
			unset($query['layout']);
		}

		unset($query['id']);

		return $segments;
	}

	if ($view == 'category' || $view == 'article')
	{
		if (!$menuItemGiven)
		{
			$segments[] = $view;
		}

		unset($query['view']);

		if ($view == 'article')
		{
			if (isset($query['id']) && isset($query['catid']) && $query['catid'])
			{
				$catid = $query['catid'];
				// Make sure we have the id and the alias
				if (strpos($query['id'], ':') === false)
				{
					$db = JFactory::getDbo();
					$aquery = $db->setQuery(
						$db->getQuery(true)
						->select('alias')
						->from('#__content')
						->where('id=' . (int) $query['id'])
					);
					$alias = $db->loadResult();
					$query['id'] = $query['id'].':'.$alias;
				}
			}
			else
			{
				// we should have these two set for this view.  If we don't, it is an error
				return $segments;
			}
		}
		else
		{
			if (isset($query['id']))
			{
				$catid = $query['id'];
			}
			else
			{
				// we should have id set for this view.  If we don't, it is an error
				return $segments;
			}
		}

		if ($menuItemGiven && isset($menuItem->query['id']))
		{
			$mCatid = $menuItem->query['id'];
		}
		else
		{
			$mCatid = 0;
		}

		$categories = JCategories::getInstance('Content');
		$category = $categories->get($catid);

		if (!$category)
		{
			// we couldn't find the category we were given.  Bail.
			return $segments;
		}

		$path = array_reverse($category->getPath());

		$array = array();

		foreach ($path as $id)
		{
			if ((int) $id == (int) $mCatid)
			{
				break;
			}

			list($tmp, $id) = explode(':', $id, 2);

			$array[] = $id;
		}

		$array = array_reverse($array);

		if (!$advanced && count($array))
		{
			$array[0] = (int) $catid . ':' . $array[0];
		}

		$segments = array_merge($segments, $array);

		if ($view == 'article')
		{
			if ($advanced)
			{
				list($tmp, $id) = explode(':', $query['id'], 2);
			}
			else
			{
				$id = $query['id'];
			}
			$segments[] = $id;
		}
		unset($query['id']);
		unset($query['catid']);
	}

	if ($view == 'archive')
	{
		if (!$menuItemGiven)
		{
			$segments[] = $view;
			unset($query['view']);
		}

		if (isset($query['year']))
		{
			if ($menuItemGiven)
			{
				$segments[] = $query['year'];
				unset($query['year']);
			}
		}

		if (isset($query['year']) && isset($query['month']))
		{
			if ($menuItemGiven)
			{
				$segments[] = $query['month'];
				unset($query['month']);
			}
		}
	}

	// if the layout is specified and it is the same as the layout in the menu item, we
	// unset it so it doesn't go into the query string.
	if (isset($query['layout']))
	{
		if ($menuItemGiven && isset($menuItem->query['layout']))
		{
			if ($query['layout'] == $menuItem->query['layout'])
			{
				unset($query['layout']);
			}
		}
		else
		{
			if ($query['layout'] == 'default')
			{
				unset($query['layout']);
			}
		}
	}

	return $segments;
}



/**
 * Parse the segments of a URL.
 *
 * @return  array  The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 * @since	1.5
 */
function ContentParseRoute($segments)
{
	$vars = array(); 
  // Count route segments
	$segments_orig = $segments;
  $count = count($segments);
	/* MULTICATS */
   
    $uri = clone JURI::getInstance();

		$app	= JApplication::getInstance('site');
		$menu	= $app->getMenu(true);
		$route	= $uri->getPath();

    //get right route
    $server = $_SERVER['SCRIPT_NAME'];
    $server = str_replace('index.php','',$server);

    if($server != '/') { $route = str_replace($server,'',$route); }

    if(substr($route,0,1) == '/') { $route = ltrim ($route,'/'); } 
    
    
		$segments	= explode('/', $route);
   
		if (count($segments) > 1 && $segments[0] == 'component')
		{
			$vars['option'] = 'com_'.$segments[1];
			$vars['Itemid'] = null;
			$route = implode('/', array_slice($segments, 2));
		}
		else
		{
			//Need to reverse the array (highest sublevels first)
			$items = array_reverse($menu->getMenu());

			$found 				= false;
			$route_lowercase 	= JString::strtolower($route);
			$lang_tag 			= JFactory::getLanguage()->getTag();

			foreach ($items as $item) {
        //echo $route_lowercase.'/ *'.$item->route.'/ <br />';
				//sqlsrv  change
				if(isset($item->language)){
					$item->language = trim($item->language);
				}
				$length = strlen($item->route); //get the length of the route
        if ($length > 0  && JString::strpos($route_lowercase.'/', $item->route.'/') === 0 && $item->type != 'menulink' && (!$app->getLanguageFilter() || $item->language == '*' || $item->language == $lang_tag)) {
					// We have exact item for this language
          if ($item->language == $lang_tag) {
						$found = $item;
						break;
					}
					// Or let's remember an item for all languages
					elseif (!$found) {
						$found = $item;
					}
				}
			}

			if (!$found) {
				$found = $menu->getDefault($lang_tag);
			}
			else {
				$route = substr($route, strlen($found->route));
				if ($route) {
					$route = substr($route, 1);
				}
			}

			$vars['Itemid'] = $found->id;
			$vars['option'] = $found->component;
		}

    
  $segments = $segments_orig;  
	/* END MULTICATS */
  
  
  //Get the active menu item.
	$app	= JFactory::getApplication();
	$menu	= $app->getMenu();
	$item	= $menu->getActive();
	$params = JComponentHelper::getParams('com_content');
	$advanced = $params->get('sef_advanced_link', 0);
	$db = JFactory::getDBO();
  


	// Standard routing for articles.  If we don't pick up an Itemid then we get the view from the segments
	// the first segment is the view and the last segment is the id of the article or category.
	if (!isset($item)) {
		$vars['view']	= $segments[0];
		$vars['id']		= $segments[$count - 1];

		return $vars;
	}

	// if there is only one segment, then it points to either an article or a category
	// we test it first to see if it is a category.  If the id and alias match a category
	// then we assume it is a category.  If they don't we assume it is an article
	if ($count == 1){
		// we check to see if an alias is given.  If not, we assume it is an article
		if (strpos($segments[0], ':') === false) {
			$vars['view'] = 'article';
			$vars['id'] = (int)$segments[0];
			return $vars;
		}

		list($id, $alias) = explode(':', $segments[0], 2);

		// first we check if it is a category
		$category = JCategories::getInstance('Content')->get($id);

		if ($category && $category->alias == $alias) {
			$vars['view'] = 'category';
			$vars['id'] = $id;

			return $vars;
		} else {
			$query = 'SELECT alias, catid FROM #__content WHERE id = '.(int)$id;
			$db->setQuery($query);
			$article = $db->loadObject();
      
			if ($article) {
				if ($article->alias == $alias) {

          /*  MULTICATS */
          $query = 'SELECT link FROM #__menu WHERE id = '.(int)$vars['Itemid'];
    			$db->setQuery($query);
    			$link = $db->loadObject();
          
          $links = explode('&',$link->link);
          foreach($links as $part){
            $parts = explode('=',$part);
            if($parts[0] == 'id'){
              $catid = $parts[1];
            }	
          }					
          /* END MULTICATS */
          if(!isset($catid) AND !is_numeric($catid)){
            $cats = explode(',',$article->catid);
            $catid = $cats[0];           
          }
 
          $vars['view'] = 'article';
					$vars['catid'] = (int)$catid; // $article->catid   - tady asi explode article->catid na ',' a vzit prvni část - pokud $catid neni definovane
					$vars['id'] = (int)$id;

					return $vars;
				}
			}
		}
	}

	// if there was more than one segment, then we can determine where the URL points to
	// because the first segment will have the target category id prepended to it.  If the
	// last segment has a number prepended, it is an article, otherwise, it is a category.
	if (!$advanced) {
		$cat_id = (int)$segments[0];

		$article_id = (int)$segments[$count - 1];

		if ($article_id > 0) {
			$vars['view'] = 'article';
			$vars['catid'] = $cat_id;
			$vars['id'] = $article_id;
		} else {
			$vars['view'] = 'category';
			$vars['id'] = $cat_id;
		}

		return $vars;
	}

	// we get the category id from the menu item and search from there
	$id = $item->query['id'];
	$category = JCategories::getInstance('Content')->get($id);

	if (!$category) {
		JError::raiseError(404, JText::_('COM_CONTENT_ERROR_PARENT_CATEGORY_NOT_FOUND'));
		return $vars;
	}

	$categories = $category->getChildren();
	$vars['catid'] = $id;
	$vars['id'] = $id;
	$found = 0;

	foreach($segments as $segment)
	{
		$segment = str_replace(':', '-', $segment);

		foreach($categories as $category)
		{
			if ($category->alias == $segment) {
				$vars['id'] = $category->id;
				$vars['catid'] = $category->id;
				$vars['view'] = 'category';
				$categories = $category->getChildren();
				$found = 1;
				break;
			}
		}

		if ($found == 0) {
			if ($advanced) {
				$db = JFactory::getDBO();
				$query = 'SELECT id FROM #__content WHERE FIND_IN_SET('.$vars['catid'].',catid) AND alias = '.$db->Quote($segment);
				$db->setQuery($query);
				$cid = $db->loadResult();
			} else {
				$cid = $segment;
			}

			$vars['id'] = $cid;

			if ($item->query['view'] == 'archive' && $count != 1){
				$vars['year']	= $count >= 2 ? $segments[$count-2] : null;
				$vars['month'] = $segments[$count-1];
				$vars['view']	= 'archive';
			}
			else {
				$vars['view'] = 'article';
			}
		}

		$found = 0;
	}

	return $vars;
}
