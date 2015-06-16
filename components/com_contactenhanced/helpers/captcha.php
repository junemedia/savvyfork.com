<?php
/**
 * @copyright   Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

class iDealCaptcha extends JObject
{
	public static function display(&$params,$namespace='com_contactenhanced')
	{
		$user = JFactory::getUser();
		if( ($params->get( 'enable_captcha', 2) > 0  AND !$user->id ) 
				OR $params->get( 'enable_captcha', 2) == 2)
		{
			$plugin    = $params->get('captcha_plugin', JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha','recaptcha')));
			
			if(JPluginHelper::isEnabled('captcha')
				AND $plugin !== 0 AND $plugin !== '0' AND $plugin !== '' AND $plugin !== null
			){
				$class     = 'required';
				if (($captcha = JCaptcha::getInstance($plugin, array('namespace' => $namespace))) == null){
					return '';
				}
				return $captcha->display('idealCaptcha', 'idealCaptcha', $class);

			//FOR iCaptcha plugin for Joomla 1.6 and 1.7
			}elseif (JPluginHelper::isEnabled('system','icaptcha')){
				$dispatcher	= JDispatcher::getInstance();
				// Process the content preparation plugins
				$results = $dispatcher->trigger('onAfterDisplayForm', array('params'=>null,'returnType'=>'html'));
				if(isset($results[0])){
					return $results[0];
				} 
			}
		} 
		return '';
	}
	
	/**
	 * Method to test if the Captcha is correct.
	 *
	 * @param	object		$param		Parameters.
	 * @param	mixed		$values		The values to test for validiaty.
	 *
	 * @return	bool		true if the value is valid, false otherwise.
	 *
	 * @since 2.5
	 */
	public static function test(&$params, $value=null, $namespace='com_contactenhanced')
	{
		
		$user = JFactory::getUser();
		if( ($params->get( 'enable_captcha', 2) > 0  AND !$user->id )
				OR $params->get( 'enable_captcha', 2) == 2)
		{
			
			$plugin    = $params->get('captcha_plugin', JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha','recaptcha')));
			
			if(JPluginHelper::isEnabled('captcha')
				AND $plugin !== 0 AND $plugin !== '0' AND $plugin !== '' AND $plugin !== null
			){
				$captcha = JCaptcha::getInstance($plugin, array('namespace' => $namespace));
				
				// Test the value.
				if (!$captcha->checkAnswer($value)){
					$error = $captcha->getError();
					JFactory::getApplication()->enqueueMessage($error,'error');
					return false;
				}
				
				
			//FOR iCaptcha plugin for Joomla 1.6 and 1.7
			}elseif (JPluginHelper::isEnabled('system','icaptcha')){
				$dispatcher	= JDispatcher::getInstance();
				$results	= $dispatcher->trigger( 'onValidateForm', array( &$params ) );
				foreach ($results as $result){
					if ($result	== false) {
						return false;
					}
				}
			}
		} 
		return true;
	}
}
