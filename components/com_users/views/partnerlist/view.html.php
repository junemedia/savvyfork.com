<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Profile view class for Users.
 *
 * @package     Joomla.Site
 * @subpackage  com_users
 * @since       1.6
 */
class UsersViewPartnerList extends JViewLegacy
{
	protected $data;

	protected $form;

	protected $params;

	protected $state;
	
	protected $featuredPartnersList;
	protected $partnersList;

	protected $limitStart;
	protected $limit;
	protected $total;
	
	/**
	 * Method to display the view.
	 *
	 * @param   string	$tpl	The template file to include
	 * @since   1.6
	 */
	public function display($tpl = null)
	{ 
		// Get the view data.
		$this->data		= $this->get('Data');
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');
		$this->params	= $this->state->get('params');		
		
		$model = $this->getModel();
		$this->featuredPartnersList = $model->getPartner(12);
		$this->partnersList = $model->getPartner(11);
		if(!empty($this->featuredPartnersList))
		{
			$this->partnersList = array_diff($this->partnersList,$this->featuredPartnersList);
		}
		$this->total = count($this->partnersList);
		
		//Don't remove below code, it may be used in the future
		//Pagination
		/*$this->limit = 5;
		$this->limitStart = (int) max(JRequest::getVar('start',0),0);
		$this->total = count($this->partnersList);
		
		if ($this->limit > $this->total)
		{
			$this->limitStart = 0;
		}
			
		
		//If limitstart is greater than total (i.e. we are asked to display records that don't exist)
		//then set limitstart to display the last natural page of results		
		if ($this->limitStart > $this->total - $this->limit)
		{
			$this->limitStart = max(0, (int) (ceil($this->total / $this->limit) - 1) * $this->limit);
		}
		
		if($this->limitStart%$this->limit != 0)
		{
			$this->limitStart = max(0, (int) floor($this->limitStart / $this->limit) * $this->limit);
		}
		
		$this->partnersList = array_slice($this->partnersList,$this->limitStart,$this->limit);*/
		
		$this->partnersList = $model->fillPartnerDetail($this->partnersList);
		$this->featuredPartnersList = $model->fillPartnerDetail($this->featuredPartnersList);
		/*echo "<pre>";
		print_r($this->featuredPartnersList);
		echo "</pre>";*/
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		// Check if a user was found.
		if (!$this->data->id)
		{
			//JError::raiseError(404, JText::_('JERROR_USERS_PROFILE_NOT_FOUND'));
			//return false;
		}

		// Check for layout override
		$active = JFactory::getApplication()->getMenu()->getActive();
		if (isset($active->query['layout']))
		{
			$this->setLayout($active->query['layout']);
		}

		//Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));

		$this->prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @since   1.6
	 */
	protected function prepareDocument()
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$user		= JFactory::getUser();
		$login		= $user->get('guest') ? true : false;
		$title 		= null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();
		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $user->name));
		}
		else
		{
			$this->params->def('page_heading', JText::_('COM_USERS_PROFILE'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title))
		{
			$title = $app->getCfg('sitename');
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
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
