<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 Ideal Extensions for Joomla. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Make sure this file is called by Joomla
defined('JPATH_BASE') or die;

// The return variable, after validation set throw an exception and set it with the $exception
$return = true;

// If you want to integrate with another component, you might want to laod the language files
//$lang = JFactory::getLanguage();
//$lang->load('com_user');


// Get current user if needed
//$user	= JFactory::getUser();

/**
 * @var Object
 */
$db		= JFactory::getDBO();

/**
 * Database query object
 * @var Object 
 */
$query	= $db->getQuery(true);

/**
 * Application Object
 * @var Object
 */
$app		= JFactory::getApplication();

/**
 * where post data is saved, you ca also use the $data variable
 * @var Object
 */
$jinput		= $app->input;
/**
 * Contains the Submitted Custom Field's values in the format $data['alias']; 
 * In order to know all  Object properties user var_dump($data);
 * @var Array of Objects
 */
$data;
/**
 * Contact information
 * In order to know all  Object properties user var_dump($contact);
 * @var Object
 */
$contact;


try
{
	if(1 == 0){ // change it with your validation
		throw new Exception(JText::_('Some error message'));
	}
}
catch (Exception $e)
{
	$return = $e;
}
