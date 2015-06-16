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

$lang 			= JFactory::getLanguage();

$active			= JRequest::getVar('q',htmlspecialchars($params->get('text', JText::_('MOD_CE_ALPHAINDEX_SEARCHBOX_TEXT'))) );
$set_Itemid		= intval($params->get('set_itemid', 0));
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$layout			= $params->get('search_results_layout', '');

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
$usedLetters= modCEAlphaIndexHelper::getUsedLetters();
$letters 	= strtoupper(str_replace(" ",'', $params->get('letters', 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z') ) );
$letters	= explode(',', $letters);

$mitemid = $set_Itemid > 0 ? $set_Itemid : JRequest::getInt('Itemid');
require JModuleHelper::getLayoutPath('mod_ce_alphaindex', $params->get('layout', 'default'));