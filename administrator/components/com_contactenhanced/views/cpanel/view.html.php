<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_cpanel
 * @copyright	Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.module.helper');

/**
 * HTML View class for the Cpanel component
 *
 * @static
 * @package		Joomla.Administrator
 * @subpackage	com_cpanel
 * @since 1.0
 */
class ContactenhancedViewCpanel extends JViewLegacy
{
	protected $modules = null;

	public function display($tpl = null)
	{
		
		$session 		=JFactory::getSession();
		
		// Display the cpanel modules
		$this->modules	= JModuleHelper::getModules('ce-cpanel');
		$this->icons	= JModuleHelper::getModules('ce-icon');
		
		// it is a new install or update
		if(empty($this->modules) AND !$session->get('com_contactenhanced.install') AND !$session->get('com_contactenhanced.modulesUpdated')){
			require_once (JPATH_COMPONENT.'/install/script.php');
			$install = new com_contactenhancedInstallerScript();
			$install->updateModules('mod_admin_ce_latest', 'ce-cpanel');
			$install->updateModules('mod_admin_ce_statistics', 'ce-icon');
			$session->set('com_contactenhanced.modulesUpdated',1);
			
			// Display the cpanel modules
			$this->modules	= JModuleHelper::getModules('ce-cpanel');
			$this->icons	= JModuleHelper::getModules('ce-icon');
		}

		
		
		ContactHelper::addSubmenu(JRequest::getVar('view','cpanel'));
		JHtmlSidebar::setAction('index.php?option=com_contactenhanced');
		$this->sidebar = JHtmlSidebar::render();
		// Set toolbar items for the page
		JToolBarHelper::title(JText::_('COM_CONTACTENHANCED_CPANEL_TITLE'), 'ce-contact');
		
		$canDo	= ContactHelper::getActions(0);
		if ($canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_contactenhanced');
		}
		
		parent::display($tpl);
	}
}
