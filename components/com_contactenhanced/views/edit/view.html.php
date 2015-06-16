<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once JPATH_COMPONENT.'/models/category.php';

/**
 * HTML Contact View class for the Contact component
 *
 * @package		com_contactenhanced
* @since 		1.5
 */
class ContactenhancedViewEdit extends JViewLegacy
{
	public 	$item;
	protected $form;
	protected $return_page;
	protected $state;

	function display($tpl = null)
	{
		
		// Initialise variables.
		$app		= JFactory::getApplication();
		$input 		= $app->input;
		$user		= JFactory::getUser();
		
		if($user->get('id') AND !$input->get('c_id')){
			$contactId	= $this->get('ContactId');
			$input->set('c_id',$contactId);
			$input->set('id',$contactId);
		}
		
		$canDo		= CEHelper::getActions();
		
		$lang		= JFactory::getLanguage();
		$lang->load('com_contactenhanced', JPATH_ADMINISTRATOR.'/components/com_contactenhanced/');
		$lang->load('com_contactenhanced', JPATH_ADMINISTRATOR);
		$lang->load('com_contactenhanced', JPATH_ROOT, null, true);
		// Loads all Administrator Global strings and override front-end strings
		$lang->load(null, JPATH_ADMINISTRATOR); 
		// Load front-end strings again
		$lang->load(null, JPATH_BASE);
		
		
		$dispatcher = JDispatcher::getInstance(); 
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$this->form	= $this->get('Form');
		$this->return_page	= $this->get('ReturnPage');
		$model		= $this->getModel();
		 
		$jform	= ceHelper::getSession('jform', array(), false);
		ceHelper::mergeObjects($jform, $item, false);
		
		$isNew	= ($input->get('id') ? false : true);
		
	// Get the parameters of the active menu item
		$menus	= $app->getMenu();
		$menu	= $menus->getActive();
		$params = JComponentHelper::getParams('com_contactenhanced');

		$params->merge($item->params);
		
		if(is_object($menu) AND isset($menu->params) ){
			$params->merge($menu->params);
		}
		
		// Get Category Model data
		if ($item) {
			$categoryModel = JModelLegacy::getInstance('Category', 'ContactenhancedModel', array('ignore_request' => true));
			$categoryModel->setState('category.id', $item->catid);
			$ordering	= explode(' ',$params->get('contact_ordering','a.name ASC'));
			$categoryModel->setState('list.ordering', $ordering[0]);
			$categoryModel->setState('list.direction', $ordering[1]);
			$contacts = $categoryModel->getItems();
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		
		
		//echo ceHelper::print_r($menu->params); exit;
		// check if access is not public
		$groups	= $user->getAuthorisedViewLevels();

		$return = '';

		if ((!in_array($item->access, $groups))  OR (!$canDo->get('core.create') AND $isNew) ) {
			$uri		= JFactory::getURI();
			$return		= (string)$uri;

			JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}

		$options['category_id']	= $item->catid;
		$options['order by']	= 'a.default_con DESC, a.ordering ASC';


	
		// Override the layout only if this is not the active menu item
		// If it is the active menu item, then the view and item id will match
		$active	= $app->getMenu()->getActive();
		if ((!$active) 
			|| ((strpos($active->link, 'view=contact') === false) 
			|| (strpos($active->link, '&id=' . (string) $item->id) === false))) {
			if ($layout = $params->get('contact_layout')) {
				$this->setLayout($layout);
			}
		}
		elseif (isset($active->query['layout'])) {
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}
		
		
		$this->assignRef('contact',		$item);
		$this->assignRef('isNew',		$isNew);
		$this->assignRef('params',		$params);
		$this->assignRef('return',		$return);
		$this->assignRef('state', 		$state);
		$this->assignRef('item', 		$item);
		$this->assignRef('user', 		$user);
		$this->assignRef('contacts', 	$contacts);
		
		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway	= $app->getPathway();
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else {
			$this->params->def('page_heading', JText::_('COM_CONTACTENHANCED_DEFAULT_PAGE_TITLE'));
		}
		
		$title = $this->params->get('page_title', '');
		
		$id = (int) @$menu->query['id'];

		// if the menu item does not concern this contact
		if ($menu && ($menu->query['option'] != 'com_contactenhanced' || $menu->query['view'] != 'contact' || $id != $this->item->id)) 
		{
			
			// If this is not a single contact menu item, set the page title to the contact title
			if ($this->item->name) {
				$title = $this->item->name;
			}
			$path = array(array('title' => $this->contact->name, 'link' => ''));
			$category = JCategories::getInstance('Contactenhanced')->get($this->contact->catid);

			while ($category && ($menu->query['option'] != 'com_contactenhanced' || $menu->query['view'] == 'contact' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => ContactenchancedHelperRoute::getCategoryRoute($this->contact->catid));
				$category = $category->getParent();
			}

			$path = array_reverse($path);

			foreach($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}

		if (empty($title)) {
			$title = $this->item->name;
		}
		$this->document->setTitle($title);		
		
		
	}
}
