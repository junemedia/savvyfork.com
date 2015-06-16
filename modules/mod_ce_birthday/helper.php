<?php 
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_birthday
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
			

class modCEBirthdayHelper {
	/**
	 * get instance of modCEBirthdayHelper
	 */
	function getInstance(){
		static $__instance = null;
		if( !$__instance ){
			$__instance = new modCEBirthdayHelper();
		}
		return $__instance;
	}
	public static function contactHREF(&$item, &$params) {
		$document	= JFactory::getDocument();
		
		// Access filter
		$access = !JComponentHelper::getParams('com_contactenhanced')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		
		$item->slug = $item->id.':'.$item->alias;
		$item->catslug = $item->catid.':'.$item->category_alias;

		if ($access || in_array($item->access, $authorised))
		{
			// We know that user has the privilege to view the article
			$item->href = (ContactenchancedHelperRoute::getContactRoute($item->slug, $item->catslug));
		}
		else {
			$app	= JFactory::getApplication();
			$menu	= $app->getMenu();
			$menuitems	= $menu->getItems('link', 'index.php?option=com_users&view=login');
			if(isset($menuitems[0])) {
				$Itemid = $menuitems[0]->id;
			} else if (JRequest::getInt('Itemid') > 0) { //use Itemid from requesting page only if there is no existing menu
				$Itemid = JRequest::getInt('Itemid');
			}

			$item->href = ('index.php?option=com_users&view=login&Itemid='.$Itemid);
		}
		
		if($params->get('modal',0)){
			$item->href	.=	'&amp;content_title='.ceHelper::encode($document->getTitle())
							.'&amp;content_url='.ceHelper::encode(ceHelper::getCurrentURL())
							.'&amp;tmpl=component' 
							.($params->get('template') ? '&amp;template='.$params->get('template') : '')
							;
		}else{
			$item->href	= JRoute::_($item->href);
		}
		return $item->href;	
	}

	public static function getList(&$params)
	{
		$app	= JFactory::getApplication();
		$lang	= JFactory::getLanguage();
		$lang->load('com_contactenhanced');
		
		$jversion = new JVersion();
		// Get an instance of the generic contacts model
		if( version_compare( $jversion->getShortVersion(), '2.5.8', 'lt' )) {
			$contacts = JModel::getInstance('Contacts', 'ContactenhancedModel', array('ignore_request' => true));
		}else{
			$contacts = JModelLegacy::getInstance('Contacts', 'ContactenhancedModel', array('ignore_request' => true));
		}
		
		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$contacts->setState('params', $appParams);

		// Set the filters based on the module params
		$contacts->setState('list.start', 0);
		$contacts->setState('list.limit', (int) $params->get('maxitems', 0));
		$contacts->setState('filter.published', 1);
		
		$contacts->setState(
			'a.id, a.name, a.alias, a.name AS title, a.alias AS title_alias, a.params, ' .
				'a.birthdate, '.
				'a.checked_out, a.checked_out_time, ' .
				'a.misc, a.con_position, a.address, a.suburb, a.state , a.country, a.postcode,'.
				' a.telephone, a.fax, a.mobile, a.skype, a.twitter, a.facebook, a.linkedin, a.webpage, a.image,'.
				' a.extra_field_1, a.extra_field_2, a.extra_field_3, a.extra_field_4, a.extra_field_5,'.
				'a.lat, a.lng, a.email_to,'.
				'a.catid, a.created, a.created_by, a.created_by_alias, ' .
				' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(\':\', a.id, a.alias) ELSE a.id END as slug, ' .
				// use created if modified is 0
				'CASE WHEN a.modified = 0 THEN a.created ELSE a.modified END as modified, ' .
					'a.modified_by, uam.name as modified_by_name,' .
				// use created if publish_up is 0
				'CASE WHEN a.publish_up = 0 THEN a.created ELSE a.publish_up END as publish_up, ' .
					'a.publish_down, a.metadata, a.metakey, a.metadesc, a.access, '.
					'a.xreference, a.featured '
		);
		
		
		// Access filter
		$access = !JComponentHelper::getParams('com_contactenhanced')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$contacts->setState('filter.access', $access);
		


		$catids = $params->get('catid');
		$contacts->setState('filter.category_id.include', (bool) $params->get('category_filtering_type', 1));
		

		// Category filter
		if ($catids) {
			if(is_array($catids)){
				$catids	= implode(',',$catids);
			}
			$contacts->setState('filter.category_id', $catids);
		}

		// Ordering
		$contacts->setState('list.ordering',	'a.name');
		$contacts->setState('list.direction',	'ASC');
		
		$contacts->setState('filter.birthdate',	true);

		// New Parameters
		//$contacts->setState('filter.featured', $params->get('show_front', 'show'));
		$excluded_contacts	= $params->get('excluded_contacts', '');

		if ($excluded_contacts) {
			$contacts->setState('filter.contact_id',$excluded_contacts);
			$contacts->setState('filter.contact_id.include', false); // Exclude
		}

		$date_filtering	= $params->get('date_filtering', 'off');
		if ($date_filtering !== 'off') {
			$contacts->setState('filter.date_filtering',	$date_filtering);
			$contacts->setState('filter.date_field',		$params->get('date_field', 'a.created'));
			$contacts->setState('filter.start_date_range',	$params->get('date_filtering-range-start_date',	'1000-01-01 00:00:00'));
			$contacts->setState('filter.end_date_range',	$params->get('date_filtering-range-end_date',	'9999-12-31 23:59:59'));
			$contacts->setState('filter.relative_date',		$params->get('date_filtering-relative-interval',30));
		}

		// Filter by language
		//$contacts->setState('filter.language',$app->getLanguageFilter());
		
		$contacts->setState('list.limit', 100);
		$items = $contacts->getItems();

		// Display options
		$show_date_format	= $params->get('show_date_format', JText::_('DATE_FORMAT_LC3'));


		// Find current Contact ID if on an contact page
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');

		if ($option === 'com_contactenhanced' && $view === 'contact') {
			$active_contact_id = (int)JRequest::getVar('id');
		}
		else {
			$active_contact_id = 0;
		}
		
		$days_before	= abs((int)$params->get('days_before',1));
		$days_after		= abs((int)$params->get('days_after',0));
		$max_birthdays	= abs((int)$params->get('maxitems',10));
		
		
		if (count($items)) {

			$jversion = new JVersion();
			// Get an instance of the generic contacts model
			if( version_compare( $jversion->getShortVersion(), '2.5.8', 'lt' )) {
				// Get an instance of the generic contacts model
				$contact	 = JModel::getInstance('Contact', 'ContactenhancedModel', array('ignore_request' => true));
				
				$timezone	= $app->getCfg('offset');
				$today		= JFactory::getDate();
				$today->setOffset((double)$timezone);
				$today		= JFactory::getDate($today->toFormat('%Y-%m-%d'));
			}else{
				// Get an instance of the generic contacts model
				$contact = JModelLegacy::getInstance('Contact', 'ContactenhancedModel', array('ignore_request' => true));
				
				// Convert the created and modified dates to local user time for display in the form.
				$tz			= new DateTimeZone($app->getCfg('offset'));
				$today		= new JDate();
				$today->setTimezone($tz);
			}
			$contact->displayParamters($params,$item);
		}
			
		
		
		
		$list = array();
		// Prepare data for display using display options
		foreach ($items as &$item){
			
			$item->title	= $item->name;
			$item->cateName = $item->category_title;
			if($params->get( 'link_titles' )){
				$item->href		= modCEBirthdayHelper::contactHREF($item, $params);
			}else{
				$item->href		= '';
			}
			

			if($item->image){
				$item->image = modCEBirthdayHelper::renderImage( $item->title
						, $item->href
						, $item->image
						, $params
						, $params->get( 'images-display-width' )
						, $params->get( 'images-display-height' ) ) ;
			}
			
			
			// Used for styling the active contact
			$item->active = $item->id == $active_contact_id ? 'active' : '';


			// it might be necessary to add timezone information:
			//$item->age	= DateTime::createFromFormat('Y-m-d', $item->birthdate)->diff(new DateTime('now'))->y;
			$item->birthday	= JHtml::_('date', $item->birthdate, $show_date_format);
		
			$birthday			= new JDate($item->birthdate);
			$item->age			= (int)round(($today->format('U')- $birthday->format('U')) / (365 * 24 * 60 * 60));
			$birthday_details	= getdate($birthday->format('U'));
			$item->new_birthday	= JFactory::getDate(mktime(0, 0, 0, $birthday_details['mon'], $birthday_details['mday'], $birthday_details['year'] + $item->age));
			$days_left = (int)round(($item->new_birthday->toUnix() - $today->toUnix()) / (24 * 60 *60));
			
			//echo "$days_left <= $days_before - $days_left >= -$days_after <br>";
			//echo "item->new_birthday: $item->new_birthday - 	$item->age	";
			if (($days_left <= $days_before) && ($days_left >= -$days_after)) {
				$item->days_left	= $days_left;
				$list[(200 + $days_left).'_'.$item->name] = $item;
			}
			ksort($list);
			
			
		}
		//echo ceHelper::print_r($list); exit;
		// Limit max birthdays count to list
		if ($max_birthdays && (count($list) > $max_birthdays)) {
			$cnt = 0;
			$new_list = array();
			foreach ($list as $key => &$item) {
				if ($cnt >= $max_birthdays) {
					break;
				}
				if ($item->days_left >= 0) {
					$new_list[$key] = $item;
					unset($list[$key]);
					$cnt++;
				}
			}
			$list = array_reverse($list, true);
			foreach ($list as $key => &$item) {
				if ($cnt >= $max_birthdays) {
					break;
				}
				$new_list[$key] = $item;
				$cnt++;
			}
			ksort($new_list);
			$list = $new_list;
		}
		return $list;
	}

	
	public static function renderImage( $title, $link, $image, $params, $width = 0, $height = 0, $attrs='', $returnURL=false ) {
		global $database, $current_charset;
		if ( $image ) {
			$title = strip_tags( $title ); 
			$thumbnailMode	= $params->get( 'source-articles-images-thumbnail_mode', 'crop' );
			$aspect			= $params->get( 'source-articles-images-thumbnail_mode-resize-use_ratio', '1' );
			$crop			= $thumbnailMode == 'crop' ? true:false;
			$ceImageHelper	= ceImageHelper::getInstance();
			$class = ' class="mod-ce-birthday-img" ';
			if( $thumbnailMode != 'none' && $ceImageHelper->sourceExited($image) ) {
				$imageURL = $ceImageHelper->resize( $image, $width, $height, $crop, $aspect );
				if( $returnURL ){
					return $imageURL;
				}
				if ( $imageURL != $image && $imageURL ) {
					$width = $width ? "width=\"$width\"" : "";
					$height = $height ? "height=\"$height\"" : "";
					$image = "<img src=\"$imageURL\" {$class} alt=\"{$title}\" title=\"{$title}\" $width $height $attrs />";
				} else {
					$image = "<img $attrs src=\"$image\" {$class}  $attrs  alt=\"{$title}\" title=\"{$title}\" />";
				}
			} else {
				if( $returnURL ){
					return $image;
				}
				$width = $width ? "width=\"$width\"" : "";
				$height = $height ? "height=\"$height\"" : "";
				$image = "<img $attrs src=\"$image\" {$class} alt=\"{$title}\"   title=\"{$title}\" $width $height />";	
			}	
		} else {
			$image = '';	
		}
		if($link){
			$image = '<a href="'.$link.'" title="'.$title.'" class="contact-image-link ce-image">'.$image.'</a>';
		}
		
		// clean up globals
		return $image;
	}
	

	/**
	 * load javascript files: processing override js, load js compress or not.
	 */
	public static function javascript( $params  ){

		$document =JFactory::getDocument();
		$document->addScript( JURI::base().'modules/mod_ce_birthday/assets/js/ce_birthday.js' );
	}
	/**
	 * load css files: processing override css
	 */	 
	public static function css( $params ){

		$app = JFactory::getApplication();
		$document =JFactory::getDocument();

		$cssFile  = 'ce_birthday.css';

		if( file_exists(JPATH_SITE.'/templates/'.$app->getTemplate().'/css/'.$cssFile) ) {
			$document->addStyleSheet( JURI::base().'templates/'.$app->getTemplate().'/css/'.$cssFile );
		} else {
			$document->addStyleSheet( JURI::base().'modules/mod_ce_birthday/assets/css/'.$cssFile );
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
