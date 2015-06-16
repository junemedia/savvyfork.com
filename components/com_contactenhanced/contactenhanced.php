<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');
JHTML::addIncludePath(JPATH_COMPONENT.'/helpers/html');
require_once JPATH_COMPONENT.'/controller.php';
require_once JPATH_COMPONENT.'/helpers/route.php';
require_once JPATH_COMPONENT.'/helpers/helper.php';


$lang = JFactory::getLanguage();

// Load backend language file
//Load English always, useful if file is partially translated
$lang->load('com_contactenhanced',		JPATH_ADMINISTRATOR.'/components/com_contactenhanced', 'en-GB');
$lang->load('com_contactenhanced',		JPATH_ADMINISTRATOR.'/components/com_contactenhanced', null, true);

//Load site language file
$lang->load('com_contactenhanced',		JPATH_ROOT.'/components/com_contactenhanced', 'en-GB');
$lang->load('com_contactenhanced',		JPATH_ROOT.'/components/com_contactenhanced', null, true);


$jversion = new JVersion();
if( version_compare( $jversion->getShortVersion(), '2.5.5', 'lt' ) ) {
	$controller = JController::getInstance('Contactenhanced');
}else{
	$controller = JControllerLegacy::getInstance('Contactenhanced');
}
$app = JFactory::getApplication();
$input = $app->input;

$controller->execute($input->getCmd('task'));

$controller->redirect();
