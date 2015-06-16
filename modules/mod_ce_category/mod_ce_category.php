<?php
/**
 * @version		1.7.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_category
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;



$app = JFactory::getApplication();
if(  $app->input->get('view') != 'edit' ){ //$app->input->get('option') != 'com_contactenhanced' AND
	$appParams = $app->getParams();
	$cparams	= JComponentHelper::getParams('com_contactenhanced');
	$appParams->merge($cparams);
	$appParams->merge($params);
	$params	= $appParams;
	//echo '<pre>'; print_r($cparams); exit;
	// Include the helper functions only once
	
	$com_path = JPATH_SITE.'/components/com_contactenhanced/';
	require_once $com_path.'router.php';
	require_once $com_path.'helpers/route.php';
	require_once(JPATH_BASE .'/components/com_contactenhanced/defines.php');
	require_once(JPATH_BASE .'/components/com_contactenhanced/helpers/helper.php');
	
	if(!class_exists('iBrowser')){
		require_once(JPATH_ROOT.'/components/com_contactenhanced/helpers/browser.php');
	}
	$browser = new iBrowser();
	
	
	require_once dirname(__FILE__).'/helper.php';
	
	// Prep for Normal or Dynamic Modes
	$mode = $params->get('mode', 'normal');
	$idbase = null;
	switch($mode)
	{
		case 'dynamic':
			$option = JRequest::getCmd('option');
			$view = JRequest::getCmd('view');
			if ($option === 'com_contactenhanced') {
				switch($view)
				{
					case 'category':
						$idbase = JRequest::getInt('id');
						break;
					case 'categories':
						$idbase = JRequest::getInt('id');
						break;
					case 'contact':
						if ($params->get('show_on_contact_page', 1)) {
							$idbase = JRequest::getInt('catid');
						}
						break;
				}
			}
			break;
		case 'normal':
		default:
			$idbase = $params->get('catid');
			break;
	}
	
	
	
	$cacheid = md5(serialize(array ($idbase,$module->module)));
	
	$cacheparams = new stdClass;
	$cacheparams->cachemode = 'id';
	$cacheparams->class = 'modCECategoryHelper';
	$cacheparams->method = 'getList';
	$cacheparams->methodparams = $params;
	$cacheparams->modeparams = $cacheid;
	
	$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);
	
	
	if (!empty($list)) {
		$grouped = false;
		$contact_grouping = $params->get('contact_grouping', 'none');
		$contact_grouping_direction = $params->get('contact_grouping_direction', 'ksort');
		$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
		$item_heading = $params->get('item_heading');
	
		if ($contact_grouping !== 'none') {
			$grouped = true;
			switch($contact_grouping)
			{
				case 'year':
				case 'month_year':
					$list = modCECategoryHelper::groupByDate($list, $contact_grouping, $contact_grouping_direction, $params->get('month_year_format', 'F Y'));
					break;
				case 'author':
				case 'category_title':
					$list = modCECategoryHelper::groupBy($list, $contact_grouping, $contact_grouping_direction);
					break;
				default:
					break;
			}
		}
		require JModuleHelper::getLayoutPath('mod_ce_category', $params->get('layout', 'default'));
	}
}