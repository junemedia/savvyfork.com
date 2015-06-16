<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */


// no direct access
defined('_JEXEC') or die;

// Component Helper
jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Contact Component Route Helper
 *
 * @static
 * @package		com_contactenhanced
* @since 1.5
 */
abstract class ContactenchancedHelperRoute
{
	protected static $lookup;
	/**
	 * @param	int	The route of the newsfeed
	 */
	public static function getContactRoute($id, $catid, $language = 0)
	{
		$needles = array(
			'contact'  => array((int) $id)
		);
		//Create the link
		$link = 'index.php?option=com_contactenhanced&view=contact&id='. $id;
		if ($catid > 1)
		{
			$categories = JCategories::getInstance('Contactenhanced');
			$category = $categories->get($catid);
			if ($category) {
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}
		if ($language && $language != "*" && JLanguageMultilang::isEnabled())
		{
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true)
			->select('a.sef AS sef')
			->select('a.lang_code AS lang_code')
			->from('#__languages AS a');
		
			$db->setQuery($query);
			$langs = $db->loadObjectList();
			foreach ($langs as $lang)
			{
				if ($language == $lang->lang_code)
				{
					$link .= '&lang='.$lang->sef;
					$needles['language'] = $language;
				}
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}elseif ($item = self::_findItem(array('categories'=>array(0)))) {
			$link .= '&Itemid='.$item;
		}
		elseif ($item = self::_findItem(null, true)) {
			$link .= '&Itemid='.$item;
		}
//echo $link; exit;
		return $link;
	}
	
/**
	 * @param	int	The route of the newsfeed
	 */
	public static function getContactEditRoute($id, $catid = 0, $language = 0)
	{
		$uri = JFactory::getURI();
		$needles = array(
			'contact'  => array((int) $id)
		);
		//Create the link
		$link = 'index.php?option=com_contactenhanced&view=edit&id='. $id.'&c_id='. $id.'&return='.base64_encode($uri); //
		if ($catid > 1)
		{
			$categories = JCategories::getInstance('Contactenhanced');
			$category = $categories->get($catid);
			if ($category) {
				$needles['category'] = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link .= '&catid='.$catid;
			}
		}

		if ($item = self::_findItem($needles)) {
			$link .= '&Itemid='.$item;
		}elseif ($catid > 1 AND $item = self::_findItem(array('catid' =>(int) $catid))) {
			$link .= '&Itemid='.$item;
 		}
//		elseif ($item = self::_findItem(null, true)) {
// 			$link .= '&Itemid='.$item;
// 		}
// 		elseif ($item = self::_findItem()) {
// 			$link .= '&Itemid='.$item;
// 		}
//echo $link; exit;
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
			$category = JCategories::getInstance('Contactenhanced')->get($id);
		}

		if($id < 1)
		{
			$link = '';
		}
		else
		{
			//Create the link
			$link = 'index.php?option=com_contact&view=category&id='.$id;
			$needles = array(
				'category' => array($id)
			);
			
			if ($language && $language != "*" && JLanguageMultilang::isEnabled())
			{
				$db		= JFactory::getDbo();
				$query	= $db->getQuery(true)
				->select('a.sef AS sef')
				->select('a.lang_code AS lang_code')
				->from('#__languages AS a');
			
				$db->setQuery($query);
				$langs = $db->loadObjectList();
				foreach ($langs as $lang)
				{
					if ($language == $lang->lang_code)
					{
						$link .= '&lang='.$lang->sef;
						$needles['language'] = $language;
					}
				}
			}

			if ($item = self::_findItem($needles))
			{
				$link = 'index.php?Itemid='.$item;
			}
			else
			{
				//Create the link
				$link = 'index.php?option=com_contactenhanced&view=category&id='.$id;
				if($category)
				{
					$catids = array_reverse($category->getPath());
					$needles = array(
						'category' => $catids,
						'categories' => $catids
					);
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

	protected static function _findItem($needles = null, $any = false)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		$language	= isset($needles['language']) ? $needles['language'] : '*';
	
		// Prepare the reverse lookup array.
		if (!isset(self::$lookup[$language]))
		{
			self::$lookup[$language] = array();
	
			$component	= JComponentHelper::getComponent('com_contactenhanced');
	
			$attributes = array('component_id');
			$values = array($component->id);
	
			if ($language != '*')
			{
				$attributes[] = 'language';
				$values[] = array($needles['language'], '*');
			}
	
			$items = $menus->getItems($attributes, $values);
		//	echo ceHelper::print_r($items); exit;
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$language][$view]))
					{
						self::$lookup[$language][$view] = array();
					}
					if (isset($item->query['id']))
					{
	
						// here it will become a bit tricky
						// language != * can override existing entries
						// language == * cannot override existing entries
						if (!isset(self::$lookup[$language][$view][$item->query['id']]) || $item->language != '*')
						{
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
					foreach ($ids as $id)
					{
						if (isset(self::$lookup[$language][$view][(int) $id]))
						{
							return self::$lookup[$language][$view][(int) $id];
						}
					}
				}
			}
		}elseif($any)
		{
			$component	= JComponentHelper::getComponent('com_contactenhanced');
			$needles	= array();
			$attributes = array('component_id');
			$values = array($component->id);
	
			if ($language != '*')
			{
				$attributes[] = 'language';
				$values[] = array($needles['language'], '*');
			}
	
			$items = $menus->getItems($attributes, $values);
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']) && $item->query['view'] !='contact')
				{
					return $item->id;
				}
			}
			
			
			$active = $menus->getActive();
			if ($active && ($active->language == '*' || !JLanguageMultilang::isEnabled()))
			{
				return $active->id;
				// removed because was having problems with the contacts without menu items
				//return $active->id;
			}
			
			// if not found, return language specific home link
			$default = $menus->getDefault($language);
			return !empty($default->id) ? $default->id : null;
		}
	
		
	}
	

}
