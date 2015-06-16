<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 Ideal Extensions for Joomla. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

jimport('joomla.plugin.plugin');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/**
 * Plugin class
 *
 * @package		com_contactenhanced
 */
class plgContactenhancedCustomcode extends JPlugin
{
	/**
	 * Object Constructor.
	 *
	 * @access	public
	 * @param	object	The object to observe -- event dispatcher.
	 * @param	object	The configuration object for the plugin.
	 * @return	void
	 * @since	3.0
	 */
	function __construct(&$subject, $config)
	{
		$this->session = JFactory::getSession();
		parent::__construct($subject, $config);
	}

	public function onSubmitContact($contact, $emailInfo) {
		$event	= 'onSubmitContact';
		if ($this->params->get($event)
		AND is_readable(dirname(__FILE__).'/code/'.$event.'/'.$this->params->get($event))) {
			require_once (  dirname(__FILE__).'/code/'.$event.'/'.$this->params->get($event));
			return $return;
		}

	}

	public function onAfterSendForm($contact, $data, $emailInfo) {
		$event	= 'onAfterSendForm';
		if ($this->params->get($event)
		AND is_readable(dirname(__FILE__).'/code/'.$event.'/'.$this->params->get($event))) {
			require_once (  dirname(__FILE__).'/code/'.$event.'/'.$this->params->get($event));
			return $return;
		}
	}

	function onValidateContact($contact, $data, $emailInfo)  {
		if ($this->params->get('onValidateContact')
			AND is_readable(dirname(__FILE__).'/code/onValidate/'.$this->params->get('onValidateContact'))) {
			require_once (  dirname(__FILE__).'/code/onValidate/'.$this->params->get('onValidateContact'));
			return $return;
		}
	}



}