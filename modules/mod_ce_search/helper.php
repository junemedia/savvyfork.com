<?php
/**
 * @package		ContactEnhanced
 * @author		Douglas Machado {@link http://iDealExtensions.com}
 * @author		Created on 24-Jun-2013
 * @copyright	Copyright (C) 2006 - 2013 iDealExtensions.com, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modCESearchHelper
{
	/**
	 * Cached array of the category items.
	 *
	 * @var    array
	 * @since  3.0
	 */
	protected static $categories = array();

 	/** Display the search button as an image.
	 *
	 * @param	string	$button_text	The alt text for the button.
	 *
	 * @return	string	The HTML for the image.
	 * @since	1.5
	 */
	public static function getSearchImage($button_text)
	{
		$img = JHtml::_('image','searchButton.gif', $button_text, NULL, true, true);
		return $img;
	}

	public static function getFilterLists(&$params, &$catids)
	{
		$lists	= array();
		$filters= $params->get('filters');
		$app	= JFactory::getApplication('site');
		$input 	= $app->input;

		if($filters){
			$db		= JFactory::getDbo();
			$query	= $db->getQuery(true);

			foreach ($filters as $filter){
				$firstOption	= new stdClass();
				$firstOption->value = '';
				$firstOption->text = JText::_('MOD_CE_SEARCH_SELECT_'.$filter);
				$attributes = '';

				// get onchange behavior and takes into consideration if state is not displayed
				if($filter == 'country' AND in_array('state', $filters)){
					$attributes	= ' onchange="mod_ce_search.getSelect(\'state\');" ';
				}elseif($filter == 'country' AND in_array('suburb', $filters)){
					$attributes	= ' onchange="mod_ce_search.getSelect(\'suburb\');" ';
				}elseif($filter == 'state' AND in_array('suburb', $filters)){
					$attributes	= ' onchange="mod_ce_search.getSelect(\'suburb\');" ';
				}

				if ($filter == 'country' OR $filter == 'con_position'
						OR ($filter == 'state'	AND !in_array('country', $filters) ) 	// Starts with State
						OR ($filter == 'suburb'	AND !in_array('state', $filters) ) 		// Starts with City/suburb
						OR ($filter == 'state'	AND $input->getString('country') )		// Country Field in $_POST
						OR ($filter == 'suburb'	AND $input->getString('state') )		// State Field in $_POST
				){
					$query->clear();
					$query->select('DISTINCT '.$filter. ' as value, '.$filter.' as text' );
					$query->from('#__ce_details');
					$query->where('published = 1');
					$query->where($filter.' <> '.$db->quote(''));
					if(($catids)){
						$query->where('catid  IN ('.$catids.')');
					}
					if(($input->getString('country')) AND $filter == 'state'){
						$query->where('country = '.$db->quote($input->getString('country')));
					}
					if(($input->getString('state')) AND $filter == 'suburb'){
						$query->where('state = '.$db->quote($input->getString('state')));
					}
					$query->where('language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');

					$query->order('text ASC');
					$db->setQuery($query);
					//echo $query; exit;
					$result		= $db->loadObjectList();
					array_unshift($result,$firstOption);
					//echo '<pre>'; print_r($result); exit;

				}elseif($filter == 'catids'){

					if(in_array('country', $filters)){
						$attributes	= ' onchange="mod_ce_search.getSelect(\'country\');" ';
					}elseif(in_array('state', $filters)){
						$attributes	= ' onchange="mod_ce_search.getSelect(\'state\');" ';
					}elseif(in_array('suburb', $filters)){
						$attributes	= ' onchange="mod_ce_search.getSelect(\'suburb\');" ';
					}


					$config 	= array('filter.published' => 1,
							'filter.language' => array((JFactory::getLanguage()->getTag()), ('*')),
							'filter.catid' => $params->get('filter_categories')
					);
					$result	= self::getCategories('com_contactenhanced',$config);
					$firstOption->value = $catids;
					array_unshift($result,$firstOption);
				}else{

					$result = array($firstOption);
				}
				$lists[$filter]	= JHtml::_('select.genericlist',  $result, $filter, 'class="inputbox" '.$attributes, 'value', 'text', $input->getString($filter));
			}

			$doc	= JFactory::getDocument();

			// there is no need to load script if there is no drill down
			if(in_array('state', $filters) OR in_array('country', $filters)){
				// Add script to update state, city and position based on selection
				// loading in the page source instead of a new file in order to save 1 hit (it is a very small script)
				// using jQuery
				JHtml::_('jquery.framework');

				$script	= "var mod_ce_search = ({
	getSelect: function(el)
	{
		urlScript	= '".JUri::root()."modules/mod_ce_search/ajax.php?task=getSelect&update='+el;
		jQuery.post(urlScript,
					jQuery('#mod_ce_search_form').serialize(),
					function(data,status,xhr){
						jQuery('#'+el).html(data);
						if(el == 'country' && jQuery('#state').length){
							jQuery('#state').children('option:not(:first)').remove();
						}
						if( (el == 'state' || el == 'country') && jQuery('#suburb').length){
							jQuery('#suburb').children('option:not(:first)').remove();
						}
					}
		);
	}
});" ;
				$doc->addScriptDeclaration($script);
			}

			if ($params->get('css_code')) {
				$doc->addStyleDeclaration($params->get('css_code'));
			}
		}


		return $lists;
	}
	/**
	 * Returns an array of categories for the given extension.
	 *
	 * @param   string  $extension  The extension option e.g. com_something.
	 * @param   array   $config     An array of configuration options. By default, only
	 *                              published and unpublished categories are returned.
	 *
	 * @return  array
	 *
	 * @since   3.0
	 */
	public static function getCategories($extension, $config = array('filter.published' => array(1)))
	{
		$hash = md5($extension . '.' . serialize($config));

		if (!isset(self::$categories[$hash]))
		{
			$config = (array) $config;
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query->select('a.id, a.title, a.level');
			$query->from('#__categories AS a');
			$query->where('a.parent_id > 0');

			// Filter on extension.
			$query->where('extension = ' . $db->quote($extension));

			// Filter on the published state
			if (isset($config['filter.published']))
			{
				if (is_numeric($config['filter.published']))
				{
					$query->where('a.published = ' . (int) $config['filter.published']);
				}
				elseif (is_array($config['filter.published']))
				{
					JArrayHelper::toInteger($config['filter.published']);
					$query->where('a.published IN (' . implode(',', $config['filter.published']) . ')');
				}
			}

			if (isset($config['filter.catid']))
			{
				if (is_numeric($config['filter.catid']))
				{
					$query->where('a.id = ' . (int) $config['filter.catid']);
				}
				elseif (is_array($config['filter.catid']))
				{
					JArrayHelper::toInteger($config['filter.catid']);
					$query->where('a.id IN (' . implode(',', $config['filter.catid']) . ')');
				}
			}

			// Filter on the language
			if (isset($config['filter.language']))
			{
				if (is_string($config['filter.language']))
				{
					$query->where('a.language = ' . $db->quote($config['filter.language']));
				}
				elseif (is_array($config['filter.language']))
				{
					foreach ($config['filter.language'] as &$language)
					{
						$language = $db->quote($language);
					}
					$query->where('a.language IN (' . implode(',', $config['filter.language']) . ')');
				}
			}

			$query->order('a.lft');

			$db->setQuery($query);
			$items = $db->loadObjectList();
			//echo $query; exit;
			// Assemble the list options.
			self::$categories[$hash] = array();

			foreach ($items as &$item)
			{
				$repeat = ($item->level - 1 >= 0) ? $item->level - 1 : 0;
				$item->title = str_repeat('- ', $repeat) . $item->title;
				self::$categories[$hash][] = JHtml::_('select.option', $item->id, $item->title);
			}
		}

		return self::$categories[$hash];
	}

}
