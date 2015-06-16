<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 Ideal Extensions for Joomla. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// Make sure this file is called by Joomla
defined('JPATH_BASE') or die;

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

//echo ceHelper::print_r($jinput); exit();
$count = 0;
$codes	= array();
$promocodes	= $jinput->get('promocode', array(), 'ARRAY'); // promocode is this case is the alias for a custom field
foreach ($promocodes as $key => $value) {
	if($key == 'value'){
		foreach ($value as $promocode) {
			if(!empty($promocode)){
				$codes[]	= $promocode;
				$query = $db->getQuery(true);
				$query->update('#__ce_cv cv');
				$query->set('published = 0');
				$query->set('description = '.$db->quote("In use. \n User Email:".$jinput->get('email')
															."\n User Name:"	.$jinput->get('name')));
				$query->where('value = '.$db->quote($promocode));
				$db->setQuery($query);
				$db->query();

				$count++;

			}
		}
	}
}

if($count){
	// Add a message
	$app->enqueueMessage(JText::_('You got a coupon code #'.(implode(',', $codes)).'. Make good use out of it ;-)')
		, 'message'); // message, notice, warning, error
}