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
$user         = JFactory::getUser();
$app = JFactory::getApplication();
$userGroups = $user->getAuthorisedGroups();
$_isAuthorized = false;
if((array_search('11',$userGroups) !== false) || (array_search('17',$userGroups) !== false)){
    // We found he is the partner
    $_isAuthorized = true;
}else{
    $message =  "Sorry, only partner can add recipes! If you are a partner, please <a href='./index.php/login'>click here</a> to login!";
    $app->enqueueMessage($message);    
}


//$app->enqueueMessage("<pre>" . print_r($user,true) . "</pre>");

if($_isAuthorized){
?>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" type="text/css" href="media/mod_yoorecipe/styles/com_yoorecipe_import.css" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script type="text/javascript" src="media/mod_yoorecipe/js/com_yoorecipe_import.js"></script>
        </head>
        <body>
		<div class="backhome">
	<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile');?>" class="back_a"><button type="button" style="margin-bottom:5px;">Back To My Profile</button></a>
</div>
            <div>
                <form id="recipeForm" name="recipeForm" method="post" action="./index.php?option=com_yoorecipe&task=import_preview">
                    <div class="addNote">Please click one of the follow buttons to start adding recipes ...</div>
                    <div class="addButtonDiv">
                        <button type="button" id="addInputButton" onClick="addInput();">Add Recipes By URL</button>
                        <button type="button" id="addFreeFormButton" onClick="addFreeForm();">Add Recipes Manually</button>
                        <!--<button type="submit" class="saveForm" style="display:none;">Upload Recipes</button>-->
                    </div>
                    <div id="listItemInput"><ul></ul></div>
                    <div id="listItemFreeForm"></div>
                </form>
            </div>
            <script type="text/javascript">freshItems();</script>
        </body>
<?php } ?>