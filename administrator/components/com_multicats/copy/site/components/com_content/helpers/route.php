<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Content Component Route Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_content
 * @since       1.5
 */
abstract class ContentHelperRoute
{
	protected static $lookup = array();

	/**
	 * @param	int	The route of the content item
	 */
	public static function getArticleRoute($id, $catid = 0, $language = 0)
	{
    // CW multicats override
		$catid = ContentHelperRoute::getCategory($catid);

    $needles = array(
			'article'  => array((int) $id)
		);

		//Create the link
		$link = 'index.php?option=com_content&view=article&id='. $id;
		if ((int) $catid > 1)
		{
			$categories = JCategories::getInstance('Content');
			$category = $categories->get((int) $catid);
			if($category)
			{
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}
		if ($language && $language != "*" && JLanguageMultilang::isEnabled()) {
			$db		= JFactory::getDBO();
			$query	= $db->getQuery(true);
			$query->select('a.sef AS sef');
			$query->select('a.lang_code AS lang_code');
			$query->from('#__languages AS a');

			$db->setQuery($query);
			$langs = $db->loadObjectList();
			foreach ($langs as $lang) {
				if ($language == $lang->lang_code) {
					$link .= '&lang='.$lang->sef;
					$needles['language'] = $language;
				}
			}
		}
		//Mcats - first check current category itemid - sometimes seem that _finditem has trouble with that 
    if ( $item = JRequest::getVar('Itemid') ) {
     $link .= '&Itemid='.$item;
    }
    elseif ($item = self::_findItem($needles)) {
      $link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem()) {
			$link .= '&Itemid='.$item;
		}

		return $link;
	}

	public static function getCategoryRoute($catid, $language = 0)
	{
		if ($catid instanceof JCategoryNode)
		{
			$id = $catid->id;
			$category = $catid;
		}
		else
		{
			$id = (int) $catid;
			$category = JCategories::getInstance('Content')->get($id);
		}

		if($id < 1)
		{
			$link = '';
		}
		else
		{

			$link = 'index.php?option=com_content&view=category&id='.$id;

			$needles = array(
				'category' => array($id)
			);

			if ($language && $language != "*" && JLanguageMultilang::isEnabled()) {
				$db		= JFactory::getDBO();
				$query	= $db->getQuery(true);
				$query->select('a.sef AS sef');
				$query->select('a.lang_code AS lang_code');
				$query->from('#__languages AS a');

				$db->setQuery($query);
				$langs = $db->loadObjectList();
				foreach ($langs as $lang) {
					if ($language == $lang->lang_code) {
						$link .= '&lang='.$lang->sef;
						$needles['language'] = $language;
					}
				}
		}

			if ($item = self::_findItem($needles))
			{
				$link .= '&Itemid='.$item;
			}
			else
			{
				//Create the link
				if($category)
				{
					$catids = array_reverse($category->getPath());
					$needles['category'] = $catids;
					$needles['categories'] = $catids;

					if ($item = self::_findItem($needles)) {
						$link .= '&Itemid='.$item;
					}
					elseif ($item = self::_findItem()) {
						$link .= '&Itemid='.$item;
					}
				}
			}
		}

		return $link;
	}

	public static function getFormRoute($id)
	{
		//Create the link
		if ($id) {
			$link = 'index.php?option=com_content&task=article.edit&a_id='. $id;
		} else {
			$link = 'index.php?option=com_content&task=article.edit&a_id=0';
		}

		return $link;
	}

	protected static function _findItem($needles = null)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		$language	= isset($needles['language']) ? $needles['language'] : '*';

		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();

			$component	= JComponentHelper::getComponent('com_content');

			$attributes = array('component_id');
			$values = array($component->id);

			if ($language != '*') {
				$attributes[] = 'language';
				$values[] = array($needles['language'], '*');
			}

			$items		= $menus->getItems($attributes, $values);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$language][$view])) {
						self::$lookup[$language][$view] = array();
					}
					if (isset($item->query['id'])) {

						// here it will become a bit tricky
						// language != * can override existing entries
						// language == * cannot override existing entries
						if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*') {
							self::$lookup[$language][$view][$item->query['id']] = $item->id;
						}
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$language][$view]))
				{
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$language][$view][(int) $id])) {
							return self::$lookup[$language][$view][(int) $id];
						}
					}
				}
			}
		}
		else
		{
			$active = $menus->getActive();
			if ($active && $active->component == 'com_content' && ($active->language == '*' || !JLanguageMultilang::isEnabled())) {
				return $active->id;
			}
		}

		// if not found, return language specific home link
		$default = $menus->getDefault($language);
		return !empty($default->id) ? $default->id : null;
	}
  
  /** CW multicats
   * finds the category id depending on if it is category or article view 
   */     
  
  static function getCategory($catid) {
    $view = JRequest::getCmd('view');
    if($view == 'category'){
      $catid = JRequest::getCmd('id');  
    }
    elseif($view == 'article') {
      $catid = JRequest::getCmd('catid');  
      //can be multicategories like 84:category,85:category2 ...but this should handle it anyway, just the first part before first ":"
      $catarray = explode(':',$catid);
      $catid = $catarray[0];
    }
    elseif($view == 'featured') {
      $catarray = explode(',',$catid);
      $catid = $catarray[0];  
    }
    return $catid; 
  }
    
  static function getMCat($id, $cid){
    $db = JFactory::getDbo();
    //$catid = JRequest::getVar('catid');
    if(isset($catid)){
      $query = 'SELECT id,alias FROM #__categories WHERE id = '.$catid;
      $db->setQuery($query);
      $cat = $db->loadObject();
      return $cat;      
    }else {
      $cats = explode(',',$cid);
      if(is_numeric($cats[0])){
        $query = 'SELECT id,alias FROM #__categories WHERE id = '.$cats[0];
        $db->setQuery($query);
        $cat = $db->loadObject();
        return $cat;      
      } else {
        return;
      }
    }

  }
}
