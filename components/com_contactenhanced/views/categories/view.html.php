<?php
/**
 * @version		1.6.0
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Content categories view.
 *
 * @package		com_contactenhanced
* @since 1.6
 */
class ContactenhancedViewCategories extends JViewLegacy
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;

	/**
	 * Display the view
	 *
	 * @return	mixed	False on error, null otherwise.
	 */
	function display($tpl = null)
	{
		// Initialise variables
		$app		= JFactory::getApplication();
		$input		= $app->input;

		$state		= $this->get('State');
		if(JRequest::getVar('layout') == 'contacts'
			OR JRequest::getVar('layout') == 'thumbnails'){
			$items		= $this->get('CategoriesContacts');
		}else{
			$items		= $this->get('Items');
		}
		$pagination	= $this->get('Pagination');
		$parent		= $this->get('Parent');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		if ($items === false) {
			return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));

		}

		if ($parent == false) {
			return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}

		$params = &$state->params;

		$items = array($parent->id => $items);

		if(!class_exists('iBrowser')){
			require_once(JPATH_COMPONENT.'/helpers/browser.php');
		}
		$browser = new iBrowser();

		$exclude_categories	= JRequest::getVar('exclude-categories',$params->get('exclude-contact-categories'));
		if(!is_array($exclude_categories)){
			$exclude_categories	= explode( ',', $exclude_categories);
		}

		$this->assignRef('exclude_categories',		$exclude_categories);

		//echo ceHelper::print_r($items); exit;
		$this->assign('maxLevelcat',	$params->get('maxLevelcat', -1));
		$this->assignRef('params',		$params);
		$this->assignRef('parent',		$parent);
		$this->assignRef('items',		$items);
		$this->assignRef('state',		$state);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('browser',		$browser);

		$filter_suburb		= $input->getCmd('suburb');
		$filter_state		= $input->getCmd('state');
		$filter_country		= $input->getCmd('country');

		$this->assignRef('filter_suburb',	$filter_suburb);
		$this->assignRef('filter_state',	$filter_state);
		$this->assignRef('filter_country',	$filter_country);

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu();
		$title	= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if($menu)
		{
			$this->params->def('page_heading', $this->params->def('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('COM_CONTACTENHANCED_DEFAULT_PAGE_TITLE'));
		}
		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}
		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}
}
