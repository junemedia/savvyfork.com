<?php
/**
 * @package		ContactEnhanced
 * @author		Douglas Machado {@link http://iDealExtensions.com}
 * @author		Created on 24-Jun-2013
 * @copyright	Copyright (C) 2006 - 2013 iDealExtensions.com, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modCEAlphaIndexHelper
{
 	public static function getUsedLetters(){
 		$app	= JFactory::getApplication();
 		$db 	= JFactory::getDBO();
 		$query 	= $db->getQuery(true);
 		$query->select('DISTINCT LEFT(name,1) AS letter')
 				->from('#__ce_details a')
 				->where('published = 1')
 				->order('letter ASC');
 		// Filter by language
 		$query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');

 		$db->setQuery($query);
 		$result	= $db->loadObjectList();
 		$array	= array();
 		foreach ($result as $value) {
 			$array[]	= $value->letter;
 		}
 		return $array;
 	}

 	private static function getURL($letter, $usedLetters, $mitemid){
 		$url 	= JRoute::_('index.php?option=com_contactenhanced&view=search&searchphrase=starts&q='.$letter.'&Itemid='.$mitemid);
 		return $url;
 	}

 	public static function getLink($letter, $usedLetters, $params, $mitemid){
 		$url 	= self::getURL($letter, $usedLetters, $mitemid);
 		if(in_array($letter, $usedLetters)){
 			$html 	= " <a href=\"{$url}\" title=\"{$letter}\">$letter</a> ";
 		}else{
 			$html 	= " <span title=\"{$letter}\">$letter</span> ";
 		}
 		return $html;
 	}
}
