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
class ContactenhancedViewCustomfields extends JViewLegacy
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
		
		JToolBarHelper::title(JText::_('CE_CF_MANAGER'), 'customfield.png');

		if ($this->canDo->get('core.create')) {
			JToolBarHelper::addNew('customfield.add','JTOOLBAR_NEW');
		}
		if ($this->canDo->get('core.edit')) {
			JToolBarHelper::editList('customfield.edit','JTOOLBAR_EDIT');
		}
		if ($this->canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom('customfields.publish', 'publish.png', 'publish_f2.png','JTOOLBAR_PUBLISH', true);
			JToolBarHelper::custom('customfields.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			JToolBarHelper::divider();
			JToolBarHelper::archiveList('customfields.archive','JTOOLBAR_ARCHIVE');
		}
		/*if(JFactory::getUser()->authorise('core.manage','com_checkin')) {
			JToolBarHelper::custom('customfields.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
		}*/
		if ($this->state->get('filter.published') == -2 && $this->canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'customfields.delete','JTOOLBAR_EMPTY_TRASH');
		} else if ($this->canDo->get('core.edit.state')) {
			JToolBarHelper::trash('customfields.trash','JTOOLBAR_TRASH');
		}
		if (FALSE AND $this->canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::custom( 'customfields.export', 'export', 'restore.png', 'Export' );
			$bar = JToolBar::getInstance('toolbar');
			$bar->addButtonPath(JPATH_COMPONENT.'/buttons');
			$js	= ' onclick="document.ceImportSliderBox.toggle();document.adminForm.task.value=\'customfields.import\';"';
			// Add a back button
			$bar->appendButton( 'Javascript', 'import', JText::_('Import'), '#',$js );

      	}
	
		if ($this->canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_contactenhanced');
      	}
      	
      	JHtmlSidebar::setAction('index.php?option=com_contactenhanced&view=customfields');
      	
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
			'cf.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'cf.published' => JText::_('JSTATUS'),
			'cf.name' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'cf.access' => JText::_('JGRID_HEADING_ACCESS'),
			'cf.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'cf.id' => JText::_('JGRID_HEADING_ID')
		);
	}
}
