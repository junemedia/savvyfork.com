<?php
/**
 * @package     com_contactenhanced
 * @copyright   Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * @package     com_contactenhanced
 *
 * @since       1.6
 */
class ContactHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	$vName	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		JHtmlSidebar::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_CPANEL'),
			'index.php?option=com_contactenhanced&view=cpanel',
			($vName == 'cpanel' OR $vName =='')
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_MESSAGES'),
			'index.php?option=com_contactenhanced&view=messages',
			$vName == 'messages'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_CUSTOMFIELDS'),
			'index.php?option=com_contactenhanced&view=customfields',
			$vName == 'customfields'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_CONTACTS'),
			'index.php?option=com_contactenhanced&view=contacts',
			($vName == 'contacts')
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_TEMPLATES'),
			'index.php?option=com_contactenhanced&view=templates',
			$vName == 'templates'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_CUSTOMVALUES'),
			'index.php?option=com_contactenhanced&view=customvalues',
			$vName == 'customvalues'
		);
		JHtmlSidebar::addEntry(
			JText::_('COM_CONTACTENHANCED_SUBMENU_CATEGORIES'),
			'index.php?option=com_categories&extension=com_contactenhanced',
			$vName == 'categories'
		);
		
		$canDo	= contactHelper::getActions();
		if ($canDo->get('core.admin')) {
			JHtmlSidebar::addEntry(
				JText::_('CE_TITLE_TOOLS'),
				'index.php?option=com_contactenhanced&view=tools',
				$vName == 'tools'
			);
		}
		
		if ($vName=='categories') {
			JToolbarHelper::title(
				JText::sprintf('COM_CATEGORIES_CATEGORIES_TITLE',JText::_('com_contactenhanced')),
				'contact-categories');
		}

	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 * @param	int		The contact ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($categoryId = 0, $contactId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($contactId) && empty($categoryId)) {
			$assetName = 'com_contactenhanced';
			$level = 'component';
		}
		elseif (empty($contactId)) {
			$assetName = 'com_contactenhanced.category.'.(int) $categoryId;
			$level = 'category';
		}
		else {
			$assetName = 'com_contactenhanced.contact.'.(int) $contactId;
			$level = 'category';
		}
//echo $level; exit;
		$actions = JAccess::getActions('com_contactenhanced', $level);

		foreach ($actions as $action) {
			$result->set($action->name,	$user->authorise($action->name, $assetName));
		}
//echo ceHelper::print_r($result); exit;
		return $result;
	}
	
	/**
	 * Adds a title to the <title> tag
	 * @param string $title
	 */
	public static function addTitle($title){
		$document	= JFactory::getDocument();
		$document->setTitle($title.' - '.$document->getTitle());
	}
}
