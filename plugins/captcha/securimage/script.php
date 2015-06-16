<?php
/**
 * @package		plg_captcha_securimage
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

if(!class_exists('plgCaptchaSecurimagedInstallerScript')){
	class plgCaptchaSecurimagedInstallerScript
	{ 
	
	 function update($parent) { 
	     $this->install($parent);
	 }
	 
	  function install($parent) { 
	     // I activate the plugin
	   	$db = JFactory::getDbo();
	     $tableExtensions = $db->quoteName("#__extensions");
	     $columnElement   = $db->quoteName("element");
	     $columnType      = $db->quoteName("type");
	     $columnEnabled   = $db->quoteName("enabled");
	     
	     // Enable plugin
	     $db->setQuery("UPDATE $tableExtensions SET $columnEnabled=1 WHERE $columnElement='securimage' AND $columnType='plugin'");
	     $db->query();
	     
	     echo '<br /><p>'. JText::_('Plugin Enabled') .'</p><br />';    
	  } 
	}
}
?>
