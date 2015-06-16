<?php
/**
 * @package		ContactEnhanced
 * @author		Douglas Machado {@link http://ideal.fok.com.br}
 * @author		Created on 24-Feb-2011
 * @copyright	Copyright (C) 2006 - 2011 iDealExtensions.com, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$app = JFactory::getApplication();
$input = $app->input;

if(	$input->get('option') != 'com_contactenhanced'
	OR ($input->get('option') == 'com_contactenhanced' AND $input->get('task') != 'edit')
	){
	$form = modCEFormHelper::loadContact($params);
	require JModuleHelper::getLayoutPath('mod_ce_form', $params->get('layout', 'default'));
}