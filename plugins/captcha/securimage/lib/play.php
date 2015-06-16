<?php

/**
 * Project:     Securimage: A PHP class for creating and managing form CAPTCHA images<br />
 * File:        securimage_play.php<br />
 *
 * @link http://www.phpcaptcha.org Securimage PHP CAPTCHA
 * @link http://www.phpcaptcha.org/latest.zip Download Latest Version
 * @link http://www.phpcaptcha.org/Securimage_Docs/ Online Documentation
 * @copyright 2011 Drew Phillips
 * @author Drew Phillips <drew@drew-phillips.com>
 * @version 3.0 (October 2011)
 * @package Securimage
 * @license BSD License LICENSE.txt
 *
 */
define( '_JEXEC', 1 );
// no direct access
defined('_JEXEC') or die;

define('JPATH_BASE', '../../../../' );
define( 'DS', DIRECTORY_SEPARATOR );
define( 'SECURIMAGE_PATH', dirname(__FILE__) );

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );


$app	= JFactory::getApplication('site');
// Instantiate the application.
$app = JFactory::getApplication('site');

$config		=JFactory::getConfig();
/**
 * @var int	Will set error report to maximum if global settings is set to maximum, otherwise set error reporting to none this will avoid problems with Joom!Fish and some SEF extensions
 */
$error_reporting_level	= $config->get('error_reporting');
if($error_reporting_level != 'development'){
	$error_reporting_level = 0;
}
error_reporting($error_reporting_level);
//error_reporting(E_ALL);

// Initialise the application.
//$app->initialise();

$session	= JFactory::getSession();
$db			= JFactory::getDBO();
$lang		= $session->get('plg_captcha_securimage.lang','en-GB');

$query = 'SELECT params '
		. ' FROM #__extensions '
		. ' WHERE element 	='.$db->Quote('securimage')
		.' AND type		='.$db->Quote('plugin')
		.' AND folder	='.$db->Quote('captcha')
		;
$db->setQuery($query);

$params 	= $db->loadResult();
$registry 	= new JRegistry();
$registry->loadString($params);
$params		= $registry;
//echo '<pre>'; print_r($params); exit;

include 'securimage.php';

$img = new securimage();

if(is_readable(SECURIMAGE_PATH.'/audio/'.$lang.'/A.wav')){
	$img->audio_path	= SECURIMAGE_PATH.'/audio/'.$lang.'/';
}elseif(is_readable(SECURIMAGE_PATH.'/audio/en-GB/A.wav')){
	$img->audio_path	= SECURIMAGE_PATH.'/audio/en-GB/';
}

if($params->get('play_sound-sound-noise',1)){
	$img->audio_use_noise = true;
}else{
	$img->audio_use_noise = false;
}

/**
 * Multiple catpchas are not working properly. 
 */
//$img->namespace	= JRequest::getVar('securimage_namespace', 'default');

$img->outputAudioFile();
