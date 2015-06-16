<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * @package		com_contactenhanced
* @since	1.6
 */
class ContactenhancedControllerMessage extends JControllerForm
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix	= 'CE_MSG'; 
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('view',	'edit');

	}
}