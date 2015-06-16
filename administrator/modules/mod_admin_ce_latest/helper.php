<?php 
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_admin_ce_latest
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_contactenhanced/helpers/route.php';
require_once JPATH_SITE.'/components/com_contactenhanced/helpers/helper.php';
require_once JPATH_SITE.'/components/com_contactenhanced/helpers/image.php';
jimport('joomla.application.component.model');
$jversion = new JVersion();
if( version_compare( $jversion->getShortVersion(), '2.5.8', 'lt' )) {
	JModel::addIncludePath(JPATH_SITE.'/components/com_contactenhanced/models');
}else{
	JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_contactenhanced/models');
}
JHTML::_('behavior.framework',true);
			

class modCELatestHelper {
	/**
	 * get instance of modCELatestHelper
	 */
	function getInstance(){
		static $__instance = null;
		if( !$__instance ){
			$__instance = new modCELatestHelper();
		}
		return $__instance;
	}
	public static function contactHREF(&$item, &$params) {
		$document	= JFactory::getDocument();
		$item->link_target = '';
		
		$item->link		= 'index.php?option=com_contactenhanced&task=message.edit&id='.$item->id;
		
		return $item->link;	
	}

	public static function getTotal()
	{
		$messages = JModelLegacy::getInstance('Messages', 'ContactenhancedModel', array('ignore_request' => true));
		return $messages->getTotal();
	}
	
	public static function getList(&$params)
	{
		$app	= JFactory::getApplication();
		
		$messages = JModelLegacy::getInstance('Messages', 'ContactenhancedModel', array('ignore_request' => true));
		
		// Set application parameters in model

		$messages->setState('params', $params);

		// Set the filters based on the module params
		$max_items	= abs((int)$params->get('maxitems',10));
		
		$messages->setState('list.start', 0);
		$messages->setState('list.limit', $max_items);
		$messages->setState(
				'list.select',
				'msg.*');
		$messages->setState('filter.published', array(1,0));
		
		
		
		
		// Access filter
		$access = !JComponentHelper::getParams('com_contactenhanced')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$messages->setState('filter.access', $access);
		
		$catids = $params->get('catid');
		$messages->setState('filter.category_id.include', (bool) $params->get('category_filtering_type', 1));
		
		// Category filter
		if ($catids) {
			if(is_array($catids)){
				$catids	= implode(',',$catids);
			}
			$messages->setState('filter.category_id', $catids);
		}

		// New Parameters
		//$messages->setState('filter.featured', $params->get('show_front', 'show'));
		$excluded_contacts	= $params->get('excluded_contacts', '');

		if ($excluded_contacts) {
			$messages->setState('filter.contact_id',$excluded_contacts);
			$messages->setState('filter.contact_id.include', false); // Exclude
		}

		$date_filtering	= $params->get('date_filtering', 'off');
		if ($date_filtering !== 'off') {
			$messages->setState('filter.date_filtering',	$date_filtering);
			$messages->setState('filter.date_field',		$params->get('date_field', 'a.created'));
			$messages->setState('filter.start_date_range',	$params->get('date_filtering-range-start_date',	'1000-01-01 00:00:00'));
			$messages->setState('filter.end_date_range',	$params->get('date_filtering-range-end_date',	'9999-12-31 23:59:59'));
			$messages->setState('filter.relative_date',		$params->get('date_filtering-relative-interval',30));
		}

		// Filter by language
		//$messages->setState('filter.language',$app->getLanguageFilter());
		

		$items = $messages->getItems();

		
		// Prepare data for display using display options
		foreach ($items as &$item){
			$item->link		= modCELatestHelper::contactHREF($item, $params);
			$item->title	= &$item->from_name;
			$item->time_ago	= ceHelper::timeDifference($item->date, 'short');
		}
		
		
		return $items;
	}

	
	/**
	 * load javascript files: processing override js, load js compress or not.
	 */
	public static function javascript( $params  ){

		$document =JFactory::getDocument();
		$document->addScript( JURI::base().'modules/mod_admin_ce_latest/assets/js/ce_latest.js' );
	}
	/**
	 * load css files: processing override css
	 */	 
	public static function css( $params ){

		$app = JFactory::getApplication();
		$document =JFactory::getDocument();

		$cssFile  = 'ce_latest.css';

		if( file_exists(JPATH_SITE.'/templates/'.$app->getTemplate().'/css/'.$cssFile) ) {
			$document->addStyleSheet( JURI::base().'templates/'.$app->getTemplate().'/css/'.$cssFile );
		} else {
			$document->addStyleSheet( JURI::base().'modules/mod_admin_ce_latest/assets/css/'.$cssFile );
		}
	}
	/**
	 * check overrider layout.
	 */
	public static function getLayoutPath($module, $layout = 'default') {

		$mainframe = JFactory::getApplication();

		// Build the template and base path for the layout
		$tPath = JPATH_BASE.'/templates/'.$mainframe->getTemplate().'/html/'.$module.'/'.$layout.'.php';
		$bPath = JPATH_BASE.'/modules/'.$module.'/tmpl/'.$layout.'.php';

		// If the template has a layout override use it
		if (file_exists($tPath)) {
			return $tPath;
		} else {
			return $bPath;
		}
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
		if (strlen($html) < $maxLength){
			return $html;
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
	
	public static function createLink($href,$text, &$params, $attribs = null){
		$attributes	= '';
		
		if($params->get('modal',0)){
			JHTML::_('behavior.modal', 'a.modal');
			$attributes	.= 'rel="{handler: \'iframe\', size: {x:'.$params->get('window-size-width',800).', y:'.$params->get('window-size-height',480).'}}"';
			if (isset($attribs) AND is_array($attribs) AND isset($attribs['class'])) {
				$attribs['class'] .= ' modal';
			}else{
				$attributes	.= ' class="modal"';
			}
		}
		
		if (isset($attribs)) {
			if (is_array($attribs)) {
				$attribs = JArrayHelper::toString($attribs);
			}
			$attributes .= ' '.$attribs;
		}
		
		return JHtml::_('link', $href, $text,$attributes);
	}
	
}
?>
