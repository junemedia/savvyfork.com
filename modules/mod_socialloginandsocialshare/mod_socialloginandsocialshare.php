<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_socialloginandsocialshare
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the login functions only once
require_once __DIR__ . '/helper.php';

$params->def('greeting', 1);

$type = modSocialLoginAndSocialShareHelper::getType();
$lr_settings = modSocialLoginAndSocialShareHelper::sociallogin_getSettings();
$return = modSocialLoginAndSocialShareHelper::getReturnURL($params, $type);
$user = JFactory::getUser();
require JModuleHelper::getLayoutPath ('mod_socialloginandsocialshare',$params->get('layout', 'default'));