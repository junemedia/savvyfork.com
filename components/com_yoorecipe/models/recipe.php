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
 
// import Joomla modelitem library
jimport('joomla.application.component.modellist');

/**
 * YooRecipe Model
 */
class YooRecipeModelRecipe extends JModelList
{

	function __construct()
	{
		parent::__construct();

		$app 	= JFactory::getApplication();
		$menu 	= $app->getMenu();
		$active = $menu->getActive();
		$params = new JRegistry();
		
		if ($active) {
			$params->loadString($active->params);
		}
		
		// List state information
		$input 		= JFactory::getApplication()->input;
		$recipeId 	= $input->get('id');
		$this->setState('recipeId', $recipeId);
	}
	
	public function getRelevanceRecipes($recipeTitle,$catId) 
	{
        /**
        * It takes too much time to run such a single query.
        * We will just use cache to replace this.
        */
        $cacheLife = 86400; // Define the cache life time.
        $cacheDir = JPATH_CACHE . "/leon/relevanceRecipes/";
        $cacheFile = $cacheDir . md5($recipeTitle . $catId) . ".cache";
        
        
        $result = getCache($cacheFile, $cacheLife);
        $result = false;
        if($result === false){
            // Create a new query object.        
            $user    = JFactory::getUser();
            $db     = JFactory::getDBO();
            $query     = $db->getQuery(true);
            
            // List state information
            $input         = JFactory::getApplication()->input;
            $recipeId     = $input->get('id');
            
            // Filter by start and end dates.
            $nullDate = $db->quote($db->getNullDate());
            $nowDate = $db->quote(JFactory::getDate()->toSQL());

            $publishWhere =' AND (r.publish_up = ' . $nullDate . ' OR r.publish_up <= ' . $nowDate . ') AND (r.publish_down = ' . $nullDate . ' OR r.publish_down >= ' . $nowDate . ')';
            
            $sql = 'SELECT DISTINCT r.id, r.access, r.title, r.alias, r.description, r.created_by, r.preparation, r.servings_type, r.nb_persons, r.difficulty, r.cost, r.carbs, r.fat, r.saturated_fat, r.proteins, r.fibers, r.salt, r.kcal, r.kjoule, r.diet, r.veggie, r.gluten_free, r.lactose_free, r.creation_date, r.publish_up, r.publish_down, r.preparation_time, r.cook_time, r.wait_time, r.picture, r.video, r.published, r.validated, r.featured, r.nb_views, r.note, r.price,CASE WHEN CHARACTER_LENGTH( r.alias )THEN CONCAT_WS( \':\', r.id, r.alias ) ELSE r.id    END AS slug
            FROM #__yoorecipe AS r
            INNER JOIN #__yoorecipe_ingredients AS i ON i.recipe_id = r.id 
            LEFT JOIN #__yoorecipe_categories AS yc ON yc.recipe_id = r.id' .  
            //LEFT JOIN sf_recipe_rating as rr ON rr.recipe_id = r.id
            ' WHERE r.published =1 AND r.validated =1 AND yc.cat_id = '.(int)$catId.' AND r.id != '.(int)$recipeId.$publishWhere.' ORDER BY r.creation_date DESC LIMIT 9';

            $db->setQuery($sql);
            $result = $db->loadAssocList();
            $countNum = count($result);
            if($countNum<9)
            {
                $words = explode(' ', $recipeTitle);
                $wheres = array();
                foreach ($words as $word)
                {
                    $word        = $db->Quote('%'.$db->escape($word, true).'%', false);
                    $wheres2    = array();
                    $wheres2[]    = 'r.title LIKE '.$word;
                    $wheres2[]    = 'r.description LIKE '.$word;
                    $wheres[]    = implode(' OR ', $wheres2);
                }
                $where = '(' . implode(') OR (', $wheres) . ')';
                
                $sql = 'SELECT DISTINCT r.id, r.access, r.title, r.alias, r.description, r.created_by, r.preparation, r.servings_type, r.nb_persons, r.difficulty, r.cost, r.carbs, r.fat, r.saturated_fat, r.proteins, r.fibers, r.salt, r.kcal, r.kjoule, r.diet, r.veggie, r.gluten_free, r.lactose_free, r.creation_date, r.publish_up, r.publish_down, r.preparation_time, r.cook_time, r.wait_time, r.picture, r.video, r.published, r.validated, r.featured, r.nb_views, r.note, r.price,CASE WHEN CHARACTER_LENGTH( r.alias )THEN CONCAT_WS( \':\', r.id, r.alias ) ELSE r.id    END AS slug
                FROM #__yoorecipe AS r
                INNER JOIN #__yoorecipe_ingredients AS i ON i.recipe_id = r.id' .  
                //LEFT JOIN sf_recipe_rating as rr ON rr.recipe_id = r.id
                ' WHERE r.published =1 AND r.validated =1
                AND ('.$where.'
                ) AND r.id != '.(int)$recipeId.$publishWhere.' ORDER BY r.creation_date DESC LIMIT '.(9-$countNum);
                
                $db->setQuery($sql);

                $result = array_merge($result,$db->loadAssocList());
            }
            // Save the Cache
            saveCache($cacheFile,$result);           
        }
        return $result;
	}

}