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

$lang = JFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();

$button			= $params->get('button', '');
$imagebutton	= $params->get('imagebutton', '');
$button_pos		= $params->get('button_pos', 'left');
$button_text	= htmlspecialchars($params->get('button_text', JText::_('MOD_CE_SEARCH_SEARCHBUTTON_TEXT')));
$width			= intval($params->get('width', 20));
$maxlength		= $upper_limit;
$text			= JRequest::getVar('q',htmlspecialchars($params->get('text', JText::_('MOD_CE_SEARCH_SEARCHBOX_TEXT'))) );
$label			= htmlspecialchars($params->get('label', JText::_('MOD_CE_SEARCH_LABEL_TEXT')));
$set_Itemid		= intval($params->get('set_itemid', 0));
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$layout			= $params->get('search_results_layout', '');

if ($imagebutton) {
	$img = modCESearchHelper::getSearchImage($button_text);
}


$catids	= $params->get('filter_categories');

for ($i=0;$i<count($catids);$i++){
	if(!$catids[$i]){
		unset($catids[$i]);
	}
}
if(empty($catids) ){
	$catids=false;
}else{
	$catids	= implode(',', $catids);
}
$filters = modCESearchHelper::getFilterLists($params, $catids);

$mitemid = $set_Itemid > 0 ? $set_Itemid : JRequest::getInt('Itemid');
require JModuleHelper::getLayoutPath('mod_ce_search', $params->get('layout', 'default'));