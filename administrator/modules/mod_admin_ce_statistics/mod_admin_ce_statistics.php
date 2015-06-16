<?php
/**
 * @version		3.0.0
 * @package		com_contactenhanced
 * @subpackage	mod_admin_ce_statistics
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted accessd');


$mainframe	= JFactory::getApplication();
$app		= &$mainframe;
$language	= JFactory::getLanguage();
$language->load('com_contactenhanced',JPATH_ROOT);
$language->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
$language->load('com_contactenhanced');
$language->load('com_contactenhanced',JPATH_ADMINISTRATOR.'/components/com_contactenhanced');

$language->load('mod_admin_ce_statistics',dirname(__FILE__));
	
// using setting params
$maxitems 		= 	$params->get( 'maxitems', 7 ); 

$doc	= JFactory::getDocument();
$doc->addScript(((empty($_SERVER['HTTPS']) OR strtolower($_SERVER['HTTPS']) != "on" ) ? 'http://' : 'https://')."www.google.com/jsapi");

$db		= JFactory::getDBO();
$query	= $db->getQuery(true);
//$query->select('count(msg.id), SUBSTRING(msg.date, 0, 10) as submittedday');
if((strtolower($db->name) == 'mysql' || strtolower($db->name) == 'mysqli')){
	$query->select("count(msg.id) as total, DATE_FORMAT(msg.date, '%Y-%m-%d') as submittedday");
}elseif (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
	$query->select("count(msg.id) as total, CONVERT(VARCHAR(10), msg.date, 110) as submittedday");
}

$query->from('#__ce_messages msg');
$query->group('submittedday');
$query->order('submittedday DESC');
$query->setLimit($maxitems,0);

$db->setQuery($query);
$list	= $db->loadObjectList();
//echo nl2br(str_replace('#__','jos_',$query)); exit;
//echo '<pre>'; print_r($list); exit;
if(count( $list ) < 1){
	return '';
}
require( JModuleHelper::getLayoutPath('mod_admin_ce_statistics') );