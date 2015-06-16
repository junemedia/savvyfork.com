<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * @package		com_contactenhanced
* @since		1.5
 */
class ContactenhancedViewElement extends JViewLegacy
{
	public $items;
	
	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
		$app	= JFactory::getApplication();
		JHTML::_('behavior.tooltip');
		
		// Check for errors.
		if (!is_array($this->items) AND count($this->items)) {
			return false;
		}


		parent::display($tpl);
	}
}
