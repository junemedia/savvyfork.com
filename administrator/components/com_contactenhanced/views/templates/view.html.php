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
class ContactenhancedViewTemplates extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
		//require_once JPATH_COMPONENT.'/helpers/contact.php';
		
		$this->items		= $this->get('items');
		$this->pagination	= $this->get('pagination');
		$this->state		= $this->get('state');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Preprocess the list of items to find ordering divisions.
		// TODO: Complete the ordering stuff with nested sets
		foreach ($this->items as &$item) {
			$item->order_up = true;
			$item->order_dn = true;
		}
		
		$canDo	= CEHelper::getActions($this->state->get('filter.category_id'));
		$this->assignRef('canDo',	$canDo);
		
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
		
		JToolBarHelper::title(JText::_('CE_TPL_MANAGER'), 'template.png');

		if ($this->canDo->get('core.create')) {
			JToolBarHelper::addNew('template.add','JTOOLBAR_NEW');
		}
		if ($this->canDo->get('core.edit')) {
			JToolBarHelper::editList('template.edit','JTOOLBAR_EDIT');
		}
		if ($this->canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('templates.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('templates.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('templates.archive','JTOOLBAR_ARCHIVE');
		}
	
		if ($this->state->get('filter.published') == -2 && $this->canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'templates.delete','JTOOLBAR_EMPTY_TRASH');
		} else if ($this->canDo->get('core.edit.state')) {
			JToolBarHelper::trash('templates.trash','JTOOLBAR_TRASH');
		}
		if ($this->canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_contactenhanced');
		}
		
		JHtmlSidebar::setAction('index.php?option=com_contactenhanced&view=templates');
		
		JHtmlSidebar::addFilter(
		JText::_('JOPTION_SELECT_PUBLISHED'),
		'filter_published',
		JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);
		
		$tplOptions 	= array();
		$tplOptions[]	= JHtml::_('select.option', 'email', JText::_('CE_TPL_TYPE_EMAIL'));
		$tplOptions[]	= JHtml::_('select.option', 'resultpage', JText::_('CE_TPL_TYPE_RESULTPAGE'));
			
		JHtmlSidebar::addFilter(
		JText::_('CE_TPL_SELECT_TYPE'),
		'filter_category_id',
		JHtml::_('select.options', $tplOptions, 'value', 'text', $this->state->get('filter.category_id'))
		);
      
	}
	
	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 *
	 * @since   3.0
	 */
	protected function getSortFields()
	{
		return array(
			'tpl.published' => JText::_('JSTATUS'),
			'tpl.name' => JText::_('JGLOBAL_TITLE'),
			'tpl.id' => JText::_('JGRID_HEADING_ID')
		
		);
	}
}
