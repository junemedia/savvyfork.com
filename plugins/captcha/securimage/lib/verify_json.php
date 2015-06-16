<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 *
 * @copyright   Copyright (C) 2005 - 2012 IdealExtensions.com, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

error_reporting(E_ALL);

// Set flag that this is a parent file
define( '_JEXEC', 1 );

// no direct access
defined('_JEXEC') or die;

define('JPATH_BASE', '../../../../' );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

// Instantiate the application.
$app 	= JFactory::getApplication('site');

$config	= JFactory::getConfig();
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

$session   = JFactory::getSession();
include 'securimage.php';
$img = new securimage();


/**
 * Multiple catpchas are not working properly. 
 */
//$img->namespace	= JRequest::getVar('securimage_namespace', 'default');
$img->namespace= 'default';

if ($img->jsonCheck(strtolower(JRequest::getVar('captcha_code')))) {
	$return	= 'success';
}else{
	$return = 'failed';
}
header('Content-type: application/json'); 
echo json_encode( array('action'=>$return)); exit();