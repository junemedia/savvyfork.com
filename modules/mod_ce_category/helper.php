<?php
/**
 * @version		$Id: helper.php 21518 2011-06-10 21:38:12Z chdemko $
 * @package		Joomla.Site
 * @subpackage	mod_ce_category
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

$com_path = JPATH_SITE.'/components/com_contactenhanced/';


JModelLegacy::addIncludePath($com_path . '/models', 'ContactenhancedModel');



abstract class modCECategoryHelper
{
	public static function getList(&$params)
	{
		$lang	= JFactory::getLanguage();
		$lang->load('com_contactenhanced');
		
		// Get an instance of the generic contacts model
		$contacts = JModelLegacy::getInstance('Contacts', 'ContactenhancedModel', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams	= $app->getParams();
		$comParams	= JComponentHelper::getParams('com_contactenhanced');
	//	$appParams->merge($comParams);
		$appParams->merge($params);
		
		$contacts->setState('params', $appParams);

		// Set the filters based on the module params
		$contacts->setState('list.start', 0);
		$contacts->setState('list.limit', (int) $params->get('count', 0));
		$contacts->setState('filter.published', 1);

		// Access filter
		$access = !JComponentHelper::getParams('com_contactenhanced')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$contacts->setState('filter.access', $access);

		// Prep for Normal or Dynamic Modes
		$mode = $params->get('mode', 'normal');
		switch ($mode)
		{
			case 'dynamic':
				$option = JRequest::getCmd('option');
				$view = JRequest::getCmd('view');
				if ($option === 'com_contactenhanced') {
					switch($view)
					{
						case 'category':
							$catids = array(JRequest::getInt('id'));
							break;
						case 'categories':
							$catids = array(JRequest::getInt('id'));
							break;
						case 'contact':
							if ($params->get('show_on_contact_page', 1)) {
								$contact_id = JRequest::getInt('id');
								$catid = JRequest::getInt('catid');

								if (!$catid) {
									// Get an instance of the generic contact model
									$contact = JModelLegacy::getInstance('Contact', 'ContactenhancedModel', array('ignore_request' => true));

									$contact->setState('params', $appParams);
									$contact->setState('filter.published', 1);
									$contact->setState('contact.id', (int) $contact_id);
									$contact	= $contact->getItem();
									$catids = array($contact->catid);
								}
								else {
									$catids = array($catid);
								}
							}
							else {
								// Return right away if show_on_contact_page option is off
								return;
							}
							break;

						case 'featured':
						default:
							// Return right away if not on the category or contact views
							return;
					}
				}
				else {
					// Return right away if not on a com_contactenhanced page
					return;
				}

				break;

			case 'normal':
			default:
				$catids = $params->get('catid');
				$contacts->setState('filter.category_id.include', (bool) $params->get('category_filtering_type', 1));
				break;
		}

		// Category filter
		if ($catids) {
			if ($params->get('show_child_category_contacts', 0) && (int) $params->get('levels', 0) > 0) {
				// Get an instance of the generic categories model
				$categories = JModelLegacy::getInstance('Categories', 'ContactenhancedModel', array('ignore_request' => true));
				$categories->setState('params', $appParams);
				$levels = $params->get('levels', 1) ? $params->get('levels', 1) : 9999;
				$categories->setState('filter.get_children', $levels);
				$categories->setState('filter.published', 1);
				$categories->setState('filter.access', $access);
				$additional_catids = array();

				foreach($catids as $catid)
				{
					$categories->setState('filter.parentId', $catid);
					$recursive = true;
					$items = $categories->getItems($recursive);

					if ($items)
					{
						foreach($items as $category)
						{
							$condition = (($category->level - $categories->getParent()->level) <= $levels);
							if ($condition) {
								$additional_catids[] = $category->id;
							}

						}
					}
				}

				$catids = array_unique(array_merge($catids, $additional_catids));
			}
//echo ceHelper::print_r($catids); exit;
			$contacts->setState('filter.category_id', $catids);
		}

		// Ordering
		$contacts->setState('list.ordering', $params->get('contact_ordering', 'a.ordering'));
		$contacts->setState('list.direction', $params->get('contact_ordering_direction', 'ASC'));

		// New Parameters
		//$contacts->setState('filter.featured', $params->get('show_front', 'show'));
		$excluded_contacts = $params->get('excluded_contacts', '');

		if ($excluded_contacts) {
			$excluded_contacts = explode("\r\n", $excluded_contacts);
			$contacts->setState('filter.contact_id', $excluded_contacts);
			$contacts->setState('filter.contact_id.include', false); // Exclude
		}

		$date_filtering = $params->get('date_filtering', 'off');
		if ($date_filtering !== 'off') {
			$contacts->setState('filter.date_filtering', $date_filtering);
			$contacts->setState('filter.date_field', $params->get('date_field', 'a.created'));
			$contacts->setState('filter.start_date_range', $params->get('start_date_range', '1000-01-01 00:00:00'));
			$contacts->setState('filter.end_date_range', $params->get('end_date_range', '9999-12-31 23:59:59'));
			$contacts->setState('filter.relative_date', $params->get('relative_date', 30));
		}

		// Filter by language
		$contacts->setState('filter.language',$app->getLanguageFilter());

		$items = $contacts->getItems();

		// Display options
		$show_date = $params->get('show_date', 0);
		$show_date_field = $params->get('show_date_field', 'created');
		$show_date_format = $params->get('show_date_format', 'Y-m-d H:i:s');
		$show_category = $params->get('show_category', 0);
		$show_hits = $params->get('show_hits', 0);
		$show_author = $params->get('show_author', 0);
		$show_misc = $params->get('show_misc', 0);
		$misc_limit = $params->get('misc_limit', 100);

		// Find current Contact ID if on an contact page
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');

		if ($option === 'com_contactenhanced' && $view === 'contact') {
			$active_contact_id = JRequest::getInt('id');
		}
		else {
			$active_contact_id = 0;
		}
		// Get an instance of the generic contact model
		$contact = JModelLegacy::getInstance('Contact', 'ContactenhancedModel', array('ignore_request' => true));
		
		$params->set('icon_address',	$comParams->get('icon_address'));
		$params->set('icon_email',		$comParams->get('icon_email'));
		$params->set('icon_telephone',	$comParams->get('icon_telephone'));
		$params->set('icon_mobile',		$comParams->get('icon_mobile'));
		$params->set('icon_fax',		$comParams->get('icon_fax'));
		$params->set('icon_misc',		$comParams->get('icon_misc'));
		$params->set('icon_skype',		$comParams->get('icon_skype'));
			
		$params->set('icon_twitter',	$comParams->get('icon_twitter'));
		$params->set('icon_facebook',	$comParams->get('icon_facebook'));
		$params->set('icon_linkedin',	$comParams->get('icon_linkedin'));
		$params->set('icon_website',	$comParams->get('icon_website'));
		$params->set('icon_birthdate',	$comParams->get('icon_birthdate'));
		$params->set('icon_extra_field_1',	$comParams->get('icon_extra_field_1'));
		$params->set('icon_extra_field_2',	$comParams->get('icon_extra_field_2'));
		$params->set('icon_extra_field_3',	$comParams->get('icon_extra_field_3'));
		$params->set('icon_extra_field_4',	$comParams->get('icon_extra_field_4'));
		$params->set('icon_extra_field_5',	$comParams->get('icon_extra_field_5'));
		$params->set('icon_extra_field_6',	$comParams->get('icon_extra_field_6'));
		$params->set('icon_extra_field_7',	$comParams->get('icon_extra_field_7'));
		$params->set('icon_extra_field_8',	$comParams->get('icon_extra_field_8'));
		$params->set('icon_extra_field_9',	$comParams->get('icon_extra_field_9'));
		$params->set('icon_extra_field_10',	$comParams->get('icon_extra_field_10'));
		$params->set('marker_class',		'jicons-none');
		
		// Prepare data for display using display options
		foreach ($items as &$item)
		{
			
			$contact->displayParamters($params,$item);
			
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid ? $item->catid .':'.$item->category_alias : $item->catid;

			if ($access || in_array($item->access, $authorised)) {
				// We know that user has the privilege to view the contact
				$item->link = JRoute::_(ContactenchancedHelperRoute::getContactRoute($item->slug, $item->catslug));
			}
			 else {
				// Angie Fixed Routing
				$app	= JFactory::getApplication();
				$menu	= $app->getMenu();
				$menuitems	= $menu->getItems('link', 'index.php?option=com_users&view=login');
				if(isset($menuitems[0])) {
					$Itemid = $menuitems[0]->id;
				} else if (JRequest::getInt('Itemid') > 0) { //use Itemid from requesting page only if there is no existing menu
					$Itemid = JRequest::getInt('Itemid');
				}

				$item->link = JRoute::_('index.php?option=com_users&view=login&Itemid='.$Itemid);
			}

			// Used for styling the active contact
			$item->active = $item->id == $active_contact_id ? 'active' : '';

			$item->displayDate = '';
			if ($show_date) {
				$item->displayDate = JHTML::_('date', $item->$show_date_field, $show_date_format);
			}

			if ($item->catid) {
				$item->displayCategoryLink = JRoute::_(ContactenchancedHelperRoute::getCategoryRoute($item->catid));
				$item->displayCategoryTitle = $show_category ? '<a href="'.$item->displayCategoryLink.'">'.$item->category_title.'</a>' : '';
			}
			else {
				$item->displayCategoryTitle = $show_category ? $item->category_title : '';
			}

			//$item->displayHits	= $show_hits ? $item->hits : '';
			$item->displayHits	= '';
			
			if ($show_misc) {
				$item->misc = JHtml::_('content.prepare', $item->misc);
				$item->misc = self::_cleanMisc($item->misc);
			}
			$item->displayMisc = $show_misc ? self::truncate($item->misc, $misc_limit) : '';
			
			
			
		}
	
		return $items;
	}

	public static function _cleanMisc($misc)
	{
		$misc = str_replace('<p>', ' ', $misc);
		$misc = str_replace('</p>', ' ', $misc);
		$misc = strip_tags($misc, '<a><em><strong>');

		$misc = trim($misc);

		return $misc;
	}

	/**
	* This is a better truncate implementation than what we
	* currently have available in the library. In particular,
	* on index.php/Banners/Banners/site-map.html JHtml's truncate
	* method would only return "Contact...". This implementation
	* was taken directly from the Stack Overflow thread referenced
	* below. It was then modified to return a string rather than
	* print out the output and made to use the relevant JString
	* methods.
	*
	* @link http://stackoverflow.com/questions/1193500/php-truncate-html-ignoring-tags
	* @param mixed $html
	* @param mixed $maxLength
	*/
	public static function truncate($html, $maxLength = 0)
	{
		$printedLength = 0;
		$position = 0;
		$tags = array();

		$output = '';

		if (empty($html)) {
			return $output;
		}

		while ($printedLength < $maxLength && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $html, $match, PREG_OFFSET_CAPTURE, $position))
		{
			list($tag, $tagPosition) = $match[0];

			// Print text leading up to the tag.
			$str = JString::substr($html, $position, $tagPosition - $position);
			if ($printedLength + JString::strlen($str) > $maxLength) {
				$output .= JString::substr($str, 0, $maxLength - $printedLength);
				$printedLength = $maxLength;
				break;
			}

			$output .= $str;
			$lastCharacterIsOpenBracket = (JString::substr($output, -1, 1) === '<');

			if ($lastCharacterIsOpenBracket) {
				$output = JString::substr($output, 0, JString::strlen($output) - 1);
			}

			$printedLength += JString::strlen($str);

			if ($tag[0] == '&') {
				// Handle the entity.
				$output .= $tag;
				$printedLength++;
			}
			else {
				// Handle the tag.
				$tagName = $match[1][0];

				if ($tag[1] == '/') {
					// This is a closing tag.
					$openingTag = array_pop($tags);

					$output .= $tag;
				}
				else if ($tag[JString::strlen($tag) - 2] == '/') {
					// Self-closing tag.
					$output .= $tag;
				}
				else {
					// Opening tag.
					$output .= $tag;
					$tags[] = $tagName;
				}
			}

			// Continue after the tag.
			if ($lastCharacterIsOpenBracket) {
				$position = ($tagPosition - 1) + JString::strlen($tag);
			}
			else {
				$position = $tagPosition + JString::strlen($tag);
			}

		}

		// Print any remaining text.
		if ($printedLength < $maxLength && $position < JString::strlen($html)) {
			$output .= JString::substr($html, $position, $maxLength - $printedLength);
		}

		// Close any open tags.
		while (!empty($tags))
		{
			$output .= sprintf('</%s>', array_pop($tags));
		}

		$length = JString::strlen($output);
		$lastChar = JString::substr($output, ($length - 1), 1);
		$characterNumber = ord($lastChar);

		if ($characterNumber === 194) {
			$output = JString::substr($output, 0, JString::strlen($output) - 1);
		}

		$output = JString::rtrim($output);

		return $output.'&hellip;';
	}

	public static function groupBy($list, $fieldName, $contact_grouping_direction, $fieldNameToKeep = null)
	{
		$grouped = array();

		if (!is_array($list)) {
			if ($list == '') {
				return $grouped;
			}

			$list = array($list);
		}

		foreach($list as $key => $item)
		{
			if (!isset($grouped[$item->$fieldName])) {
				$grouped[$item->$fieldName] = array();
			}

			if (is_null($fieldNameToKeep)) {
				$grouped[$item->$fieldName][$key] = $item;
			}
			else {
				$grouped[$item->$fieldName][$key] = $item->$fieldNameToKeep;
			}

			unset($list[$key]);
		}

		$contact_grouping_direction($grouped);

		return $grouped;
	}

	public static function groupByDate($list, $type = 'year', $contact_grouping_direction, $month_year_format = 'F Y')
	{
		$grouped = array();

		if (!is_array($list)) {
			if ($list == '') {
				return $grouped;
			}

			$list = array($list);
		}

		foreach($list as $key => $item)
		{
			switch($type)
			{
				case 'month_year':
					$month_year = JString::substr($item->created, 0, 7);

					if (!isset($grouped[$month_year])) {
						$grouped[$month_year] = array();
					}

					$grouped[$month_year][$key] = $item;
					break;

				case 'year':
				default:
					$year = JString::substr($item->created, 0, 4);

					if (!isset($grouped[$year])) {
						$grouped[$year] = array();
					}

					$grouped[$year][$key] = $item;
					break;
			}

			unset($list[$key]);
		}

		$contact_grouping_direction($grouped);

		if ($type === 'month_year') {
			foreach($grouped as $group => $items)
			{
				$date = new JDate($group);
				$formatted_group = $date->format($month_year_format);
				$grouped[$formatted_group] = $items;
				unset($grouped[$group]);
			}
		}

		return $grouped;
	}
}
