<?php
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_contactslider
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted accessd');

if(JRequest::getVar('option') == 'com_contactenhanced' AND JRequest::getVar('view') == 'edit'){
	return '';
}

$mainframe	= JFactory::getApplication();
$language	= JFactory::getLanguage();
$language->load('com_contactenhanced');
$language->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
$language->load('mod_contactslider',dirname(__FILE__));
	
// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');
require_once (JPATH_ROOT.'/components/com_contactenhanced/helpers/image.php');




// using setting params
$source 		= 	$params->get( 'source', 'contact' );
$xheight 		= 	$params->get( 'content-container-height', 170 );
$xwidth 		= 	$params->get( 'content-container-width', 140 );
$numElem 		= 	$params->get( 'count', 4 ); 

$showtitle	 	= 	$params->get( 'showtitle', 0 ); 
$showreadmore 	= 	$params->get( 'showreadmore', 0 );
$showintrotext 	= 	$params->get( 'showintrotext', 0 );
$link_titles 	= 	$params->get( 'link_titles', 0 );

$auto 			= 	($params->get( 'slidemode', 'auto' ) == 'auto' ? 1 : 0 );
$direction 		=	$params->get( 'direction', 'left' );
$delaytime 		= 	$params->get( 'slidemode-auto-delaytime', 3500 );
$animationtime 	= 	$params->get( 'animationtime', 1000 );
$numberjump 	= 	1;
$useajax 		= 	0;
$mode 			= 	$params->get( 'mode','horizontal' );
$showTab 		= 	($params->get('tab', 1) ? 1 : 0);
$text_heading	=	$params->get( 'tab-category-text_heading', '');
$item_heading	=	$params->get('item_heading',2);

switch ($source) {
	case 'contact':
	default:
		$catid = $params->get('catid', 0);
		break;
}
if(!is_array($catid)) {
	$catid = (array) $catid;
}

$mode = $params->get( 'mode', 'vertical' );

$source = $params->get('source', 'content');

//$contacts = modCEContactSliderHelper::getListItems( $catid, $params,$source );
$contacts = modCEContactSliderHelper::getList( $params 	);

//echo '<pre>'; print_r($contacts); exit;
/**
 * id, title, decscription,link, 
*/
$total = count( $contacts );

if($total < 1){
	return '';
}
//echo '<pre>'; print_r($contacts); exit;
if(!defined('CE_CONTACTSLIDER_ASSETS')) {
	define('CE_CONTACTSLIDER_ASSETS', 1);
	/* load javascript. */ 
	modCEContactSliderHelper::javascript( $params );
	/* load css. */ 
	modCEContactSliderHelper::css( $params );
}
require( JModuleHelper::getLayoutPath('mod_ce_contactslider') );

?>
