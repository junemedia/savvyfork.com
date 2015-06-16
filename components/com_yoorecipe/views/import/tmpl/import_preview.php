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
$baseurl = JURI::base();
$user         = JFactory::getUser();
$app = JFactory::getApplication();
$userGroups = $user->getAuthorisedGroups();
$_isAuthorized = false;
if((array_search('11',$userGroups) !== false) || (array_search('14',$userGroups) !== false)){
    // We found he is the partner
    $_isAuthorized = true;
}else{
    $message =  "Sorry, only partner can add recipes! If you are a partner, please <a href='./index.php/login'>click here</a> to login!";
    $app->enqueueMessage($message);    
}

 global $previewRecipes;
 
 //print_r($previewRecipes);

//$app->enqueueMessage("<pre>" . print_r($user,true) . "</pre>");

$session = JFactory::getSession();
$previewRecipes = $session->get( 'previewRecipes' );


if($_isAuthorized){
?>

        <link rel="stylesheet" type="text/css" href="media/mod_yoorecipe/styles/com_yoorecipe_import.css" />
		<script src="<?php echo $baseurl;?>cropimage/js/jquery.min.js" type="text/javascript"></script>
		<link href="<?php echo $baseurl;?>cropimage/css/facybox.css" media="screen" rel="stylesheet" type="text/css" />
		<link href="<?php echo $baseurl;?>cropimage/css/facybox_urls.css" media="screen" rel="stylesheet" type="text/css" />
		<script src="<?php echo $baseurl;?>cropimage/js/facybox.js" type="text/javascript"></script>
		<script type="text/javascript" src="<?php echo $baseurl;?>cropimage/js/jquery.jWindowCrop.js"></script>
		<script src="<?php echo $baseurl;?>cropimage/js/ajaxfileupload.js" type="text/javascript"></script>
		<script type="text/javascript" src="media/mod_yoorecipe/js/com_yoorecipe_import.js"></script>
		
            <div>
			<script type="text/javascript">
				$(function() {
					$('a#crop_btn').facybox();
				});
				function ajaxFileUpload(image,imagepath)
				{
					$.ajaxFileUpload
					(
						{
							url:'/cropimage/fileupload.php',
							secureuri:false,
							fileElementId:'imageupload'+image,
							dataType: 'json',
							data:{recipe:imagepath,imageN:image},
							success: function (data, status)
							{
								if(typeof(data.error) != 'undefined')
								{
								}
								var timestamp = (new Date()).valueOf();
								$('#image'+image).attr('src',"/"+imagepath + "?t=" + timestamp);
								return false;
							},
							error: function (data, status, e)
							{
								return false;
							}
						}
					)
					
					return false;

				}
			</script>
                <form id="recipeForm" enctype="multipart/form-data" name="recipeForm" method="post" action="./index.php?option=com_yoorecipe&task=import_save_postvars">
                    <div>Edit or Change your Photo(s)...</div>
                    <?php 
					$imageN = 0;
					foreach($previewRecipes  as $recipe){
						$imagePath = "images/com_yoorecipe/".$recipe["images"][0];
						$extend =explode("." ,$recipe["images"][0]); 
						$fileName=substr($imagePath, 0, -1);
						$fileExt = substr($imagePath,-1);
						?>
                        <div class="recipeCard">
                            <div class="recipeTitle"><?php echo $recipe["title"]?></div>
                            <div class="recipeImage"><img id="image<?php echo $imageN;?>" src="<?php echo '/'.$imagePath;?>" alt="" height="200" width="200" ></div>
							<div align="left">
								<span>Upload New Image?</span><br>
								<input type="file" name="imageupload<?php echo $imageN;?>" id="imageupload<?php echo $imageN;?>" style="width:200px;">
							</div>
							<div align="left">
								<button type="submit" style="margin-right: 10px;" onclick="ajaxFileUpload(<?php echo $imageN;?>,'<?php echo $imagePath;?>');return false;">Upload Image</button><a href="<?php echo $baseurl;?>cropimage/crop.php?from=preview&imagen=<?php echo $imageN;?>&image=<?php echo $fileName;?>&ext=<?php echo $fileExt;?>" id="crop_btn"><button type="button">Crop Current Image</button></a>
							</div>
                            <div class="recipeIngredient">
                                <?php 
                                    foreach($recipe["ingredients"] as $ingredient) { 
                                        echo "<li>" . $ingredient . "</li>";
                                    }
                                ?>
                            </div>
                            
                            <input type="hidden" name="title[]" value="<?php echo $recipe["title"]?>" />
                            <input type="hidden" name="ingredients[]" value="<?php echo $recipe["ingredients"]?>" />
                        </div>
                    <hr>
                    <?php 
					$imageN++;
					} ?>
                    <div><button type="submit">Save all</button></div>
                </form>
            </div>
<?php } ?>