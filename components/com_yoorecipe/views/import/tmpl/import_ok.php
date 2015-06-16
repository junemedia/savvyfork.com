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
           <script>
               var i = 5;
               function onload()
               {
                   var time = document.getElementById('time');
                   i--;
                   time.innerHTML = i;
                   setTimeout("onload()",1100);
                   if(i==0)
                   {
                       window.location.href="/index.php?option=com_yoorecipe&task=import";
                   }
               }
           </script>
            <script language='javascript' type='text/javascript'> 
                var secs =5; 
                //倒计时的秒数 
                var URL ;
                function Load(url){ 
                    URL =url; 
                    for(var i=secs;i>=0;i--) { 
                        window.setTimeout('doUpdate(' + i + ')', (secs-i) * 1000); 
                    } 
                } 
                function doUpdate(num) { 
                    document.getElementById('ShowDiv').innerHTML = 'The page will auto jump to the recipe eidt page in <font color=red>'+num+'</font> seconds' ; 
                    if(num == 0) { 
                        window.location=URL; 
                    } 
                }
             </script> 
        </head>
        <body onload="onload()">
            <div>
            <div id="ShowDiv"></div>
            <!--
                <form id="recipeForm" name="recipeForm" method="post" action="./index.php?option=com_yoorecipe&task=import_ok">
                    <div>Please click one of the follow buttons to start adding recipes ...</div>
                    <div class="addButtonDiv">
                        <button type="button" id="addInputButton" onClick="addInput();">Add recipes by URL</button>
                        <button type="button" id="addFreeFormButton" onClick="addFreeForm();">Add recipes Manually</button>
                        <button type="submit" class="saveForm" >Save All Recipes</button>
                    </div>
                    <div id="listItemInput"><ul></ul></div>
                    <div id="listItemFreeForm"></div>
                </form>
            -->
            </div>
            <script type="text/javascript">Load("index.php?option=com_users&task=partner_recipes_list")/*Load("/index.php?option=com_yoorecipe&task=import")*/</script>
        </body>
<?php } ?>