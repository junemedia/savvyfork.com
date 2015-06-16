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
class ContactenhancedViewContacts extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	protected $categories;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->categories	= $this->get('categories');
		
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
		
		$params			= JComponentHelper::getParams('com_contactenhanced');
		$this->assignRef('params',	$params);
		
		$this->hasContacts	= count($this->items);
		if(!$this->hasContacts){
			//Check if there are contacts in CE
			$this->hasContacts	= $this->get('ContactCount');
		}
		
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
		ContactHelper::addSubmenu(JRequest::getVar('view'));
		//$canDo	= CEHelper::getActions($this->state->get('filter.category_id'));
		$canDo	= ContactHelper::getActions($this->state->get('filter.category_id'));
		$user	= JFactory::getUser();
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		JToolbarHelper::title(JText::_('COM_CONTACTENHANCED_MANAGER_CONTACTS'), 'contact.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_contactenhanced', 'core.create'))) > 0) {
			JToolbarHelper::addNew('contact.add');
		}

		if (count($this->hasContacts) > 0):
		
			if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
				JToolbarHelper::editList('contact.edit');
			}
	
			if ($canDo->get('core.edit.state')) {
				JToolbarHelper::publish('contacts.publish', 'JTOOLBAR_PUBLISH', true);
				JToolbarHelper::unpublish('contacts.unpublish', 'JTOOLBAR_UNPUBLISH', true);
				JToolbarHelper::archiveList('contacts.archive');
				JToolbarHelper::checkin('contacts.checkin');
			}
	
			if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
				JToolbarHelper::deleteList('', 'contacts.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($canDo->get('core.edit.state')) {
				JToolbarHelper::trash('contacts.trash');
			}
	
			// Add a batch button
			if ($user->authorise('core.edit'))
			{
				JHtml::_('bootstrap.modal', 'collapseModal');
				$title = JText::_('JTOOLBAR_BATCH');
				$dhtml = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small\">
							<i class=\"icon-checkbox-partial\" title=\"$title\"></i>
							$title</button>";
				$bar->appendButton('Custom', $dhtml, 'batch');
			}
		
		endif;
		
		if ($canDo->get('core.admin')) {
			JToolbarHelper::preferences('com_contactenhanced');
		}
		
		JHtmlSidebar::setAction('index.php?option=com_contactenhanced');
		//echo '<pre>'; print_r(JHtml::_('jgrid.publishedOptions')); exit;
		
		$waiting	= new stdClass();
		$waiting->value		= -3;
		$waiting->text		= JText::_('COM_CONTACTENHANCED_OPT_WAITING_APPROVAL');
		$waiting->disable	= false;
		
		$publishOptions		= JHtml::_('jgrid.publishedOptions');
		$publishOptions[]	= $waiting;
		
		JHtmlSidebar::addFilter(
		JText::_('JOPTION_SELECT_PUBLISHED'),
		'filter_published',
		JHtml::_('select.options',$publishOptions , 'value', 'text', $this->state->get('filter.published'), true)
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
			'a.ordering' => JText::_('JGRID_HEADING_ORDERING'),
			'a.state' => JText::_('JSTATUS'),
			'a.name' => JText::_('JGLOBAL_TITLE'),
			'category_title' => JText::_('JCATEGORY'),
			'ul.name' => JText::_('COM_CONTACTENHANCED_FIELD_LINKED_USER_LABEL'),
			'a.featured' => JText::_('JFEATURED'),
			'a.access' => JText::_('JGRID_HEADING_ACCESS'),
			'a.language' => JText::_('JGRID_HEADING_LANGUAGE'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}

		
}
