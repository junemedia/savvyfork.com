<?php
/*------------------------------------------------------------------------
# com_yoorecipe - YooRecipe! Joomla 1.6 recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://extensions.yoorock.fr
# Technical Support:  Forum - http://extensions.yoorock.fr/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the YooRecipe Component
 */
class YooRecipeViewRecipe extends JViewLegacy
{
	protected $user_recipes;
	protected $facebook_share;
	protected $twitter_share;
	protected $pinterest_share;
	protected $google_share;
	protected $relevanceRecipes;	
	protected $socialStats;
	
	// Overwriting JViewLegacy display method
	function display($tpl = null) 
	{
		// Init variables
		$app		= JFactory::getApplication();
		$menu 		= $app->getMenu();
		$params 	= JComponentHelper::getParams('com_yoorecipe');	
		$active 	= $menu->getActive();
		$user 		= JFactory::getUser();
		$cache		= JFactory::getCache();
		
		// Get the yoorecipe model
		$mainModel 			= JModelLegacy::getInstance('yoorecipe','YooRecipeModel');
		$categoriesModel	= JModelLegacy::getInstance('categories','YooRecipeModel');
		$tagsModel			= JModelLegacy::getInstance('tags','YooRecipeModel');
		$recipeModel        = JModelLegacy::getInstance('recipe','YooRecipeModel');		
		
		if ($this->getLayout() == 'recipe') 
		{
			// Get recipe identifier to view
			$input 		= JFactory::getApplication()->input;
			$recipeId 	= $input->get('id');
		
			// Assign data to the view
			//$this->recipe  = $cache->call( array( $mainModel, 'getRecipeById' ), $recipeId );
			$this->recipe  = $mainModel->getRecipeById($recipeId);					
			
			if (isset($this->recipe)) {
				
				$details_url = JURI::base().JRoute::_('index.php?option=com_yoorecipe&task=viewRecipe&id=' . $this->recipe->slug); 

				$this->user_recipes = $mainModel->getRecipesByUserId($this->recipe->created_by);
				/*$this->facebook_share = $mainModel->getRecipeSocialStats("facebook",$details_url);
				$this->twitter_share = $mainModel->getRecipeSocialStats("twitter",$details_url);
				$this->pinterest_share = $mainModel->getRecipeSocialStats("pinterest",$details_url);
				$this->google_share = $mainModel->getRecipeSocialStats("google+",$details_url);*/
				$this->socialStats = $mainModel->getLocalSocialStats($recipeId);				
			
				// Optionally prepare content using Joomla Content Plugins
				if ($params->def('prepare_content', 1) && isset($this->recipe))
				{
					JPluginHelper::importPlugin('content');
					$this->recipe->preparation = JHtml::_('content.prepare', $this->recipe->preparation);
				}
				
				// Increment counter of views
				if (isset($this->recipe->published) && isset($this->recipe->validated)) {
					$mainModel->incrementViewCountOfRecipe($recipeId, $this->recipe->nb_views);
				}
				
				// Calculate user rights on edition
				$this->canEdit = 	$user->authorise('core.admin', 'com_yoorecipe') || 
									$user->authorise('core.edit', 'com_yoorecipe') ||
									($user->authorise('core.edit.own', 'com_yoorecipe') && $this->recipe->created_by == $user->id);
								
				$this->canManageComments = 	$user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit', 'com_yoorecipe');
				if ($params->get('comments_manager', 'admin') == 'admin_and_owner') {
					$this->canManageComments |= ($user->authorise('core.edit.own', 'com_yoorecipe') && $this->recipe->created_by == $user->id);
				}
				
				// Get cross categories
				$this->recipe->categories = $categoriesModel->getRecipeCategories($this->recipe->id);
			
				// Get recipe tags
				$this->recipe->tags	= $tagsModel->getTagsByRecipeId($recipeId);
			
				//Get recipes of "more like" section
				//1.Get the top rating recipes from category first
				//2.If the recipes got from category does not reach 9, then use recipe title as key words to research the recipes.
				$this->relevanceRecipes = $recipeModel->getRelevanceRecipes($this->recipe->title,$this->recipe->categories[0]->id);
				/*echo "<pre>";
				print_r($this->relevanceRecipes);
				echo "</pre>";*/
				
				// Add breadcrumbs
				JHTML::_('behavior.modal');
				$app = JFactory::getApplication();
				$pathway = $app->getPathway();
				$pathway->addItem($this->recipe->title, JUri::current());
			}
		}
		
		// In case recipe not found, get categories instead
		$this->categories 	= $categoriesModel->getAllPublishedCategories();
		$this->user			= JFactory::getUser();
		
		// Set Params defined in menu (if applicable)
		$this->menuParams = (isset($active)) ? $active->params : null;
		
		$this->_prepareDocument();
		
		// Display the view
		parent::display($tpl);
	}
	
	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		// Set document title
		if (isset($this->recipe)) {
		
			//$this->document->setTitle(htmlspecialchars(JText::_('COM_YOORECIPE_RECIPE') . ' ' . $this->recipe->title));
			//$this->document->setTitle($this->recipe->title . ' | Recipes | SavvyFork');
			$categoryName = '';
			if(!empty($this->recipe->categories))
			{
				/*foreach($this->recipe->categories as $catItem)
				{
					$categoryName .=$catItem->title." ";
				}*/
				$categoryName = $this->recipe->categories[0]->title;
			}
			
			$this->document->setTitle($this->recipe->title . ' | '.$categoryName.' Recipe | SavvyFork');
			
			// Set meta description
			/*if ($this->recipe->description != '') {
				$this->document->setDescription(strip_tags($this->recipe->description));
			}*/
			$this->document->setDescription($categoryName.','.$this->recipe->title);
			
			// Set meta robots
			if ($this->recipe->metadata) {
				$this->document->setMetadata('robots',$this->recipe->metadata);
			} else {
			
				$app	= JFactory::getApplication();
				$menus	= $app->getMenu();
				$menu 	= $menus->getActive();
				if ($menu) {
					$menuParams = $menu->params;
					$this->document->setMetadata('keywords', $menuParams->get('menu-meta_keywords'));
				}
			}
			
			// Set meta keywords
			/*if ($this->recipe->metakey) {
				$this->document->setMetadata('keywords', $this->recipe->metakey);
			}*/
			$ingredientList = '';
			if(!empty($this->recipe->ingredients))
			{
				foreach($this->recipe->ingredients as $intItem)
				{
					$ingredientList .=$intItem->description.',';
				}
			}
			$ingredientList = substr($ingredientList,0,strlen($ingredientList)-1);
			$this->document->setMetadata('keywords', $categoryName.','.$this->recipe->title.','.$ingredientList);
			
			// Set canonical url
			$canonicalUrl = 'http://' .  $_SERVER['SERVER_NAME'] . JRoute::_(JHtml::_('YooRecipeHelperRoute.getreciperoute', $this->recipe->slug, $this->recipe->catslug) , false);
			$this->document->addCustomTag( '<link rel="canonical" href="' . $canonicalUrl . '"/>' );
		
		} // End if (isset($this->recipe)) {
		
	}
}