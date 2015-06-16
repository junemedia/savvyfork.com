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
// import the Joomla modellist library
jimport('joomla.application.component.modellist');
/**
 * YooRecipeList Model
 */
class YooRecipeModelEditorrating extends JModelList
{
    
    /**
     * insertEditorRating
     */
    public function insertEditorRating($recipe_id, $editor_rating)
    {   
        // Prepare query
        $db = JFactory::getDBO();    
        $query = $db->getQuery(true);
        
        $query->clear();
        $query->insert('#__yoorecipe_editor_rating');
        $query->set('recipe_id = '.$db->quote($recipe_id));
        $query->set('editor_rating = '.$db->quote($editor_rating));
        
        $db->setQuery((string)$query);
        if (!$db->execute()) {
            $this->setError($db->getErrorMsg());
            echo $db->getErrorMsg();
            return false;
        }
    }    
	
	/**
	 * updateIngredient
	 */
	public function updateEditorRating($recipe_id, $editor_rating)
	{
		// Prepare query
		$db = JFactory::getDBO();		
		$query = $db->getQuery(true);
		
		$query->clear();
		$query->update('#__yoorecipe_editor_rating');
		$query->set('editor_rating = ' . $db->quote($editor_rating)); 
		
		$query->where('recipe_id = ' .$db->quote($recipe_id));
		
		$db->setQuery((string)$query);
		$db->execute();
	}
    
    public function setEditorRating($recipe_id, $editor_rating){
        $recipe = $this->getEditorRatingByRecipeId($recipe_id);
        
        if($recipe){
            // Do the update
            $this->updateEditorRating($recipe_id,$editor_rating);
        }else{
            // Do the insert
            $this->insertEditorRating($recipe_id,$editor_rating);
        }
    }
    
    public function getEditorRatingByRecipeId($recipe_id){
                // Prepare query
        $db = JFactory::getDBO();    
        $query = $db->getQuery(true);
        $query->clear();

        $query->select('*');
        $query->from('#__yoorecipe_editor_rating');
        $query->where('recipe_id = ' . $recipe_id);
        $db->setQuery((string)$query);
        
        $groups = $db->loadObjectList();
        if (!$groups) {
            $this->setError($db->getErrorMsg());
            echo $db->getErrorMsg();
            return false;
        }            
        return $groups;
    }
	
}