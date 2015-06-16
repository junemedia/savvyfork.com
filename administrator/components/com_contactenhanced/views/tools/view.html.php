<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.Development
 * @author     	Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * @since		1.5
 */
class ContactenhancedViewTools extends JViewLegacy
{
	/**
	 * Display the view
	 */
	function display($tpl = null)
	{
		$params			= JComponentHelper::getParams('com_contactenhanced');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->assignRef('params',	$params);
		
		$this->addToolbar();
		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		
		// Add submenu
		ContactHelper::addSubmenu(JRequest::getVar('view'));
		
		$canDo	= contactHelper::getActions();

		JToolBarHelper::title( JText::_( 'COM_CONTACTENHANCED_TOOLS_MANAGER' ), 'ce-contact' );

		if ($canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_contactenhanced');
		}
		
	}

}
