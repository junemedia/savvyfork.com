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
class ContactenhancedViewCustomvalues extends JViewLegacy
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
		
		$canDo	= contactHelper::getActions($this->state->get('filter.category_id'));
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
		
		JToolBarHelper::title(JText::_('CE_CV_MANAGER'), 'customvalue.png');

		if ($this->canDo->get('core.create')) {
			JToolBarHelper::addNew('customvalue.add','JTOOLBAR_NEW');
		}
		if ($this->canDo->get('core.edit')) {
			JToolBarHelper::editList('customvalue.edit','JTOOLBAR_EDIT');
		}
		if ($this->canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('customvalues.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('customvalues.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('customvalues.archive','JTOOLBAR_ARCHIVE');
		}
		/*if(JFactory::getUser()->authorise('core.manage','com_checkin')) {
			JToolBarHelper::custom('customvalues.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		}*/
		if ($this->state->get('filter.published') == -2 && $this->canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'customvalues.delete','JTOOLBAR_EMPTY_TRASH');
		} else if ($this->canDo->get('core.edit.state')) {
			JToolBarHelper::trash('customvalues.trash','JTOOLBAR_TRASH');
		}

		JHtmlSidebar::setAction('index.php?option=com_contactenhanced&view=customvalues');
		 
		JHtmlSidebar::addFilter(
		JText::_('JOPTION_SELECT_PUBLISHED'),
		'filter_published',
		JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
		);
		 
		JHtmlSidebar::addFilter(
		JText::_('JOPTION_SELECT_CATEGORY'),
		'filter_category_id',
		JHtml::_('select.options', JHtml::_('category.options', 'com_contactenhanced'), 'value', 'text', $this->state->get('filter.category_id'))
		);
		 
		JHtmlSidebar::addFilter(
		JText::_('JOPTION_SELECT_ACCESS'),
		'filter_access',
		JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);
		 
		JHtmlSidebar::addFilter(
		JText::_('JOPTION_SELECT_LANGUAGE'),
		'filter_language',
		JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'))
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
			'cv.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'cv.published' => JText::_('JSTATUS'),
			'cv.name' => JText::_('JGLOBAL_TITLE'),
			'cv.type' => JText::_('CE_CV_VALUE'),
			'cv.access' => JText::_('JGRID_HEADING_ACCESS'),
			'cv.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'cv.id' => JText::_('JGRID_HEADING_ID'),
			'cv.type' => JText::_('CE_CV_TYPE')
		
		);
	}
}