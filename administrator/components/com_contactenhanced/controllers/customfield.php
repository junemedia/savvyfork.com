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
class ContactenhancedControllerCustomfield extends JControllerForm
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix	= 'CE_CF'; 
	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

	}
	
/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean	 True if successful, false otherwise and internal error is set.
	 *
	 * @since   2.5
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Customfield', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_contactenhanced&view=customfields' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}
}