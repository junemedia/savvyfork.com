<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
jimport('mosets.profilepicture.profilepicture');

$baseurl = JURI::base();

$user = JFactory::getUser();

if($user->id == $this->data->id)
{
	$user_profile = JUserHelper::getProfile($this->data->id);
	$picture = new ProfilePicture($this->data->id);
	$headimage = $picture->getURL('original');		
}

if(!$headimage)
{
	$headimage="images/headimg_reserve.jpg";
}

//$session = JFactory::getSession();
$lr_settings = array ();
$db = JFactory::getDBO ();
/*$sql = "SELECT * FROM #__LoginRadius_settings";
$db->setQuery ($sql);
$rows = $db->LoadAssocList ();
if (is_array ($rows)) {
foreach ($rows AS $key => $data) {
  $lr_settings [$data ['setting']] = $data ['value'];
}
}*/
/*$sql = "SELECT * FROM #__LoginRadius_users WHERE id =".$this->data->id;
$db->setQuery($sql);
$user_social = $db->loadObjectList('provider');
//Get user recent activities
$sql = "SELECT ua.user_id,y.title,at.activity,ua.sharedate,CASE WHEN CHARACTER_LENGTH(y.alias) THEN CONCAT_WS(':', y.id, y.alias) ELSE y.id END as slug FROM #__user_activity AS ua LEFT JOIN #__yoorecipe AS y ON ua.recipe_id = y.id LEFT JOIN #__activity_type AS at ON at.id=ua.type_id WHERE ua.user_id = ".(int)$user->id." AND at.activity IN ('Facebook','Google+','Twitter','Pinterest') ORDER BY ua.sharedate DESC LIMIT 5";
$db->setQuery($sql);
$shareList = $db->loadObjectList();*/

//Pagination
$limit = 40;
$limitStart = (int) max(JRequest::getVar('start',0),0);

$sql = "SELECT count(y.id) FROM #__yoorecipe AS y WHERE y.created_by = ". $user->id;
$db->setQuery($sql);
$total = (int)($db->loadResult());
		
if ($limit > $total)
{
	$limitStart = 0;
}	

//If limitstart is greater than total (i.e. we are asked to display records that don't exist)
//then set limitstart to display the last natural page of results		
if ($limitStart > $total - $limit)
{
	$limitStart = max(0, (int) (ceil($total / $limit) - 1) * $limit);
}

if($limitStart%$limit != 0)
{
	$limitStart = max(0, (int) floor($limitStart / $limit) * $limit);
}

// Get the recipes of the user
$sql = "SELECT * FROM #__yoorecipe AS y WHERE y.created_by = ". $user->id." ORDER BY y.creation_date DESC LIMIT ".$limitStart.",".$limit;
$db->setQuery($sql);
$recipes = $db->loadObjectList();

if(!function_exists('cutWord'))
{
	function cutWord($str,$length)
	{
		if($str[0]=='"')
		{
			$str = substr($str,1,strlen($str));
		}
		
		if(strlen($str) <= $length){
			return $str;
		}else{
			$pos = strrpos(substr($str, 0, $length) , ' ', -1);
			return substr($str, 0, $pos) . " ...";
		}
	}
}
?>


<!--<div class="profile shadow">
	<div id="core_pf">
		<div class="top">
			<img style="width:133px;height:132px;" src="<?php echo $headimage; ?>" />
			<div class="right">
				<div class="intro">
					<span style="font-size:25px;color:#0a8e36;font-weight: normal;letter-spacing: 1px;"><?php echo $user->name;?></span><br/>
					<?php 
					$userCity = $user_profile->profile['city'];
					$userRegion = $user_profile->profile['region'];
					if($userCity !="" && $userRegion !="") {?>
					<span><?php echo $userCity.', '.$userRegion?></span>
					<?php }?>
				</div>				
			</div>
		</div>
	</div>
</div>-->
<div class="backhome">
	<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile');?>" class="back_a"><button type="button" style="margin-bottom:5px;">Back To My Profile</button></a>
</div>
<div id="user_favorite">
    <div class="sp_line2"></div>
    <h2>USER RECIPES</h2>
    <div class="sp_line2"></div>
</div>
<script src="<?php echo $baseurl;?>cropimage/js/jquery.min.js" type="text/javascript"></script>
	<link href="<?php echo $baseurl;?>cropimage/css/facybox.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="<?php echo $baseurl;?>cropimage/css/facybox_urls.css" media="screen" rel="stylesheet" type="text/css" />
	<script src="<?php echo $baseurl;?>cropimage/js/facybox.js" type="text/javascript"></script>
<?php  
        $user                             = JFactory::getUser();
        $userGroups                  = $user->getAuthorisedGroups();

        $_isAuthorized = false;

        if((array_search('11',$userGroups) !== false) || (array_search('14',$userGroups) !== false)){ 
        
            if(count($recipes) > 0){
            ?>
            
            <ul style="overflow: hidden;list-style:none;">
            <script type="text/javascript">
				$(function() {
					$('a#crop_btn').facybox();
				});

			</script>
            <?php    
                $cTime = time();            
                foreach($recipes as $recipe){
            ?>
			
            <li style="float: left; margin: 10px;  width: 200px;">
			<form method="post" enctype="multipart/form-data" action="" id="recipe-<?php echo $recipe->id;?>">
            <div><img id='image<?php echo $recipe->id;?>' src="<?php echo $recipe->picture.'?t='.$cTime;?>" alt="" width="196" height="196"></div>
            <div style="height: 50px;text-align:left;"><?php echo $recipe->title;?></div>
			<div>
				<a href="<?php echo $baseurl;?>cropimage/crop.php?validate=<?php echo $recipe->id;?>&start=<?php echo $limitStart;?>" id="crop_btn"><button type="button" style="margin-bottom:5px;margin-left:0px;">Crop Current Image</button></a>
			</div>
            <div align="center">
				<input type="file" name="recipeimage" id="recipeimage" style="width:200px;margin-bottom:5px;">
			</div>
            <div>
				<button type="submit" style="margin-left:0px;">Upload New Image</button>
				<input type="hidden" value="com_users" name="option">
				<input type="hidden" value="profile.saveimage" name="task">
				<input type="hidden" value="<?php echo $recipe->id;?>" name="recipe">
				<input type="hidden" value="<?php echo $limitStart;?>" name="limitStart">
			</div>
			</form>
            </li>          
			
            <?php
                }
            ?>
            </ul>
            <?php       
            }
        } 
		else
		{
			$message =  "Sorry, only partner can edit recipes! If you are a partner, please <a href='./index.php/login'>click here</a> to login!";
			$app->enqueueMessage($message);  
		}
?>
<?php if($total > $limit){ ?>
<div style="clear:both;"><table style="width:200px;border:0;align:center;margin-left:auto;margin-right:auto;margin-bottom:0px">
	<tbody>
		<tr>
		<?php if($limitStart == 0){?>
			<td><!--<span class="Prevpagenav">Prev</span>--></td>
		<?php } else {?>
			<td><a class="Prevpagenav" href="/index.php?option=com_users&task=partner_recipes_list&start=<?php echo $limitStart-$limit;?>" title="Next">Prev</a></td>
		<?php }?>
		<?php if($total-$limitStart>$limit){?>
			<td><a class="Nextpagenav" href="/index.php?option=com_users&task=partner_recipes_list&start=<?php echo $limitStart+$limit;?>" title="Next">Next</a></td>
		<?php } else {?>
			<td><!--<span class="Nextpagenav">Next</span>--></td>
		<?php }?>
		</tr>
	</tbody>
</table></div>
<?php }?>





