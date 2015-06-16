<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 *
 * @copyright   Copyright (C) 2006 - 2012 IdealExtensions.com, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

error_reporting(E_ALL);
//error_reporting(E_ALL);
// Set flag that this is a parent file
define( '_JEXEC', 1 );

// no direct access
defined('_JEXEC') or die;

define('JPATH_BASE', '../../../../' );
define( 'DS', DIRECTORY_SEPARATOR );
define( 'SECURIMAGE_PATH', dirname(__FILE__) );

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );


// Instantiate the application.
$app 		= JFactory::getApplication('site');
$config		= JFactory::getConfig();
$lang 		= JFactory::getLanguage();
$lang->load('plg_captcha_securimage',JPATH_ADMINISTRATOR);
$lang->load('plg_captcha_securimage');
$lang->load('plg_captcha_securimage',JPATH_BASE.'/plugins/captcha/securimage');

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
$langCode	= $session->get('plg_captcha_securimage.lang','en-GB');

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


// Change some settings

$img->image_width		= (int) $params->get('width', 	150);
$img->image_height		= (int) $params->get('height', 	70);
$img->perturbation		= (float) $params->get('perturbation',	0.7); // 1.0 = high distortion, higher numbers = more distortion


if($params->get('ttf') ){
	if(is_readable(SECURIMAGE_PATH.'/ttf/'.$params->get('ttf',	'AHGBold.ttf')) ){
		$img->ttf_file 			= SECURIMAGE_PATH.'/ttf/'.$params->get('ttf',	'AHGBold.ttf');
		$img->signature_font	= SECURIMAGE_PATH.'/ttf/'.$params->get('ttf',	'AHGBold.ttf');
	}
}

if($params->get('type') =='word' ){
	$img->use_wordlist	= true;
	if(is_readable(SECURIMAGE_PATH.'/words/'.$langCode.'.txt')){
		$img->wordlist_file	= SECURIMAGE_PATH.'/words/'.$langCode.'.txt';
	}elseif(is_readable(SECURIMAGE_PATH.'/words/en-GB.txt')){
		$img->wordlist_file	= SECURIMAGE_PATH.'/words/en-GB.txt';
	}
}else if($params->get('type') =='math' ){
	$img->captcha_type    = Securimage::SI_CAPTCHA_MATHEMATIC;
}else{ // string :: characters

}
//echo $img->wordlist_file; exit;
if($params->get('type-characters-length') == 'random'){
	$img->code_length 	= rand(3, 5);
}else{
	$img->code_length 	= $params->get('type-characters-length',	4);
}

$img->image_bg_color	= new Securimage_Color("#".$params->get('bg_color',		'FFFFFF'));
$img->text_color		= new Securimage_Color("#".$params->get('text_color', 	'3D3D3D'));
$img->line_color		= new Securimage_Color("#".$params->get('line_color', 	'3D3D3D'));
$img->signature_color	= new Securimage_Color("#".$params->get('signature_color', 	'FFFFFF'));
$img->image_signature	= $params->get('image_signature', 	'');
$img->num_lines			= (int) $params->get('number_lines', 	8);
$img->charset			= $params->get('type-characters-charset', 	'ABCDEFGHKLMNPRSTUVWYZabcdefghkmnprstuvwyz23456789');
$img->noise_level		= (int) $params->get('noise_level', 	0);


$bgimg	= '';
if($params->get('background') != '-1' ){
	if(is_readable(SECURIMAGE_PATH.'/backgrounds/'.$params->get('background',	'letters-x.jpg'))){
		$bgimg 			= SECURIMAGE_PATH.'/backgrounds/'.$params->get('background',	'letters-x.jpg');
	}
}


if(is_readable(SECURIMAGE_PATH.'/audio/'.$langCode.'/A.wav')){
	$img->audio_path	= SECURIMAGE_PATH.'/audio/'.$langCode.'/';
}elseif(is_readable(SECURIMAGE_PATH.'/audio/en-GB/A.wav')){
	$img->audio_path	= SECURIMAGE_PATH.'/audio/en-GB/';
}

/**
 * Multiple catpchas are not working properly.
 */
//$img->namespace	= JRequest::getVar('securimage_namespace', 'default');

if(JRequest::getVar('firstLoad') == 1 AND $params->get('clickMe') ==1){
	$img->showClickMeImage($bgimg, JText::_('PLG_CAPTCHA_SECURIMAGE_CLICK_HERE')); //
}else{
	$img->show($bgimg);
}