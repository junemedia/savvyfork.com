<?php
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_latest
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted accessd');

if(JRequest::getVar('option') == 'com_contactenhanced' AND JRequest::getVar('view') == 'edit'){
	return '';
}

$mainframe	= JFactory::getApplication();
$app		= &$mainframe;
$language	= JFactory::getLanguage();
$language->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
$language->load('com_contactenhanced');
$language->load('mod_ce_latest',dirname(__FILE__));
	
// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');


$cparams		= $app->getParams('com_contactenhanced');
$params->merge($cparams);

// using setting params
$maxitems 		= 	$params->get( 'maxitems', 5 ); 
$link_titles 	= 	$params->get( 'link_titles', 0 );


$catid = $params->get('catid', 0);
if(!is_array($catid)) {
	$catid = (array) $catid;
}

if($params->get( 'maxitems',10)){
	$items = modCELatestHelper::getList( $params );
}else{
	$items	= array();
}


$total	= 0;
if(count( $items ) < 1 AND !$params->get( 'show_total')){
	return '';
}elseif($params->get( 'show_total')){
	$total =  modCELatestHelper::getTotal($params) ;
}



require( JModuleHelper::getLayoutPath('mod_ce_latest') );
if(!defined('CE_LATEST_ASSETS')) {
	define('CE_LATEST_ASSETS', 1);
	/* load css. */
	modCELatestHelper::css( $params );
}

?>
