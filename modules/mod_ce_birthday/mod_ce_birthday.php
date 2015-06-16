<?php
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_birthday
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
$language->load('com_contactenhanced');
$language->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
$language->load('mod_ce_birthday',dirname(__FILE__));
	
// Include the syndicate functions only once
require_once (dirname(__FILE__).'/helper.php');
require_once (JPATH_ROOT.'/components/com_contactenhanced/helpers/image.php');

$cparams		= $app->getParams('com_contactenhanced');
$params->merge($cparams);

// using setting params
$xheight 		= 	$params->get( 'content-container-height', 170 );
$xwidth 		= 	$params->get( 'content-container-width', 140 );
$maxitems 		= 	$params->get( 'maxitems', 4 ); 

$link_titles 	= 	$params->get( 'link_titles', 0 );


$catid = $params->get('catid', 0);
if(!is_array($catid)) {
	$catid = (array) $catid;
}


$source = $params->get('source', 'content');

$contacts = modCEBirthdayHelper::getList( $params 	);

//echo '<pre>'; print_r($contacts); exit;
/**
 * id, title, decscription,link, 
*/
$total = count( $contacts );
//echo $total; exit;
if($total < 1){
	return '';
}

require( JModuleHelper::getLayoutPath('mod_ce_birthday') );
if(!defined('CE_BIRTHDAY_ASSETS')) {
	define('CE_BIRTHDAY_ASSETS', 1);
	/* load css. */
	modCEBirthdayHelper::css( $params );
}

?>
