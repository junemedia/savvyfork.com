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
jimport('joomla.mail.helper');

/**
 * HTML View class for the Contacts component
 *
 * @package		com_contactenhanced
* @since		1.5
 */
class ContactenhancedViewCategory extends JViewLegacy
{
	protected $state;
	protected $items;
	protected $category;
	protected $categories;
	protected $pagination;

	function display($tpl = null)
	{
		$app		= JFactory::getApplication();
		$input		= $app->input;
		$doc		= JFactory::getDocument();
		$params		= $app->getParams();
		
		
		// Get some data from the models
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$category	= $this->get('Category');
		$children	= $this->get('Children');
		$parent 	= $this->get('Parent');
		$pagination	= $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		if ($category == false) {
			return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}

		if ($parent == false) {
			return JError::raiseWarning(404, JText::_('JGLOBAL_CATEGORY_NOT_FOUND'));
		}

		// Check whether category access level allows access.
		$user	= JFactory::getUser();
		$groups	= $user->getAuthorisedViewLevels();
		if (!in_array($category->access, $groups)) {
			return JError::raiseError(403, JText::_("JERROR_ALERTNOAUTHOR"));
		}

		// Prepare the data.
		// Compute the contact slug.
		for ($i = 0, $n = count($items); $i < $n; $i++)
		{
			$item		= &$items[$i];
			$item->slug	= $item->alias ? ($item->id.':'.$item->alias) : $item->id;
			$temp		= new JRegistry();
			$temp->loadString($item->params);
			$item->params = clone($params);
			$item->params->merge($temp);
			if ($item->params->get('show_email', 0)) {
				$item->email_to = trim($item->email_to);
				if (!empty($item->email_to) && JMailHelper::isEmailAddress($item->email_to)) {
					if($params->get('show_email_headings') == 'label'){
						$item->email_to = JHtml::_('email.cloak', $item->email_to, true, JText::_('JGLOBAL_EMAIL'), false);
					}else{
						$item->email_to = JHtml::_('email.cloak', $item->email_to);
					}
				}
				else {
					$item->email_to = '';
				}
			}
		}

		$search_fields	= array();
		if($params->get('filter_field') == 2 AND $params->get('search_fields')){
			foreach($params->get('search_fields', array()) as $search_field){
				$search_fields[$search_field]	= JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_'.($search_field).'_LABEL');
			}
		}
		
		// Setup the category parameters.
		$cparams = $category->getParams();
		$category->params = clone($params);
		$category->params->merge($cparams);
		$children = array($category->id => $children);
		
		if(!class_exists('iBrowser')){
			require_once(JPATH_COMPONENT.'/helpers/browser.php');
		}
		$browser = new iBrowser();
		
		$maxLevel	= $params->get('maxLevel', -1);
		$filter_suburb		= $input->getCmd('suburb');
		$filter_state		= $input->getCmd('state');
		$filter_country		= $input->getCmd('country');
		
		$this->assignRef('search_fields',	$search_fields);
		$this->assignRef('maxLevel',	$maxLevel);
		$this->assignRef('state',		$state);
		$this->assignRef('items',		$items);
		$this->assignRef('category',	$category);
		$this->assignRef('children',	$children);
		$this->assignRef('params',		$params);
		$this->assignRef('parent',		$parent);
		$this->assignRef('pagination',	$pagination);
		$this->assignRef('doc',			$doc);
		$this->assignRef('browser',		$browser);
		
		$this->assignRef('filter_suburb',	$filter_suburb);
		$this->assignRef('filter_state',	$filter_state);
		$this->assignRef('filter_country',	$filter_country);
		
		/* Active Menu item */
		$active	= $app->getMenu()->getActive();
		// Check for layout override only if this is not the active menu item
		// If it is the active menu item, then the view and category id will match
		if ((!$active) || ((strpos($active->link, 'view=category') === false) || (strpos($active->link, '&id=' . (string) $this->category->id) === false))) {
			if ($layout = $category->params->get('category_layout')) {
				$this->setLayout($layout);
			}
		}
		elseif (isset($active->query['layout'])) {
			// We need to set the layout in case this is an alternative menu item (with an alternative layout)
			$this->setLayout($active->query['layout']);
		}

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

		$id = (int) @$menu->query['id'];

		if ($menu && ($menu->query['option'] != 'com_contactenhanced' || $menu->query['view'] == 'contact' || $id != $this->category->id)) {
			$path = array(array('title' => $this->category->title, 'link' => ''));
			$category = $this->category->getParent();

			while (($menu->query['option'] != 'com_contactenhanced' || $menu->query['view'] == 'contact' || $id != $category->id) && $category->id > 1)
			{
				$path[] = array('title' => $category->title, 'link' => ContactenchancedHelperRoute::getCategoryRoute($category->id));
				$category = $category->getParent();
			}

			$path = array_reverse($path);

			foreach ($path as $item)
			{
				$pathway->addItem($item['title'], $item['link']);
			}
		}

		$title = $this->params->get('page_title', '');

		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		}
		elseif ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}

		$this->document->setTitle($title);

		if ($this->category->metadesc)
		{
			$this->document->setDescription($this->category->metadesc);
		}
		elseif (!$this->category->metadesc && $this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->category->metakey)
		{
			$this->document->setMetadata('keywords', $this->category->metakey);
		}
		elseif (!$this->category->metakey && $this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}

		if ($app->getCfg('MetaAuthor') == '1') {
			$this->document->setMetaData('author', $this->category->getMetadata()->get('author'));
		}

		$mdata = $this->category->getMetadata()->toArray();

		foreach ($mdata as $k => $v)
		{
			if ($v) {
				$this->document->setMetadata($k, $v);
			}
		}

		// Add alternative feed link
		if ($this->params->get('show_feed_link', 1) == 1)
		{
			$link	= '&format=feed&limitstart=';
			$attribs = array('type' => 'application/rss+xml', 'title' => 'RSS 2.0');
			$this->document->addHeadLink(JRoute::_($link.'&type=rss'), 'alternate', 'rel', $attribs);
			$attribs = array('type' => 'application/atom+xml', 'title' => 'Atom 1.0');
			$this->document->addHeadLink(JRoute::_($link.'&type=atom'), 'alternate', 'rel', $attribs);
		}
	}
}
