<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
	
JHTML::_('stylesheet','administrator/components/com_contactenhanced/assets/css/contact_enhanced.css', array(), false);

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_contactenhanced')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
require_once (JPATH_ROOT.'/components/com_contactenhanced/helpers/helper.php');
require_once JPATH_COMPONENT.'/helpers/contact.php';

$lang =JFactory::getLanguage();
$lang->load('com_contactenhanced',		JPATH_ADMINISTRATOR.'/components/com_contactenhanced', 'en-GB');
$lang->load('com_contactenhanced',		JPATH_ADMINISTRATOR.'/components/com_contactenhanced',	null,	true);
$lang->load('com_contactenhanced.sys',	JPATH_ADMINISTRATOR.'/components/com_contactenhanced', 'en-GB');
$lang->load('com_contactenhanced.sys',	JPATH_ADMINISTRATOR.'/components/com_contactenhanced',	null,	true);
$lang->load('com_contactenhanced.menu',	JPATH_ADMINISTRATOR.'/components/com_contactenhanced', 'en-GB');
$lang->load('com_contactenhanced.menu',	JPATH_ADMINISTRATOR.'/components/com_contactenhanced',	null,	true);


// Include dependancies
jimport('joomla.application.component.controller');

//$jversion = new JVersion();

$app = JFactory::getApplication();
$input = $app->input;

$controller = JControllerLegacy::getInstance('Contactenhanced');
$controller->execute($input->getCmd('task'));

$controller->redirect();
