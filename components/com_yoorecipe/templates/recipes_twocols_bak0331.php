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

function cutWord($str,$length)
{
	if($str[0]=='"')
	{
		$str = substr($str,1,strlen($str));
	}
	
	if(strlen($str) <= $length){
		if (strlen($str) <= 27) { return $str; }
		return $str;
	}else{
		$pos = strrpos(substr($str, 0, $length) , ' ', -1);
		return substr($str, 0, $pos) . " ...";
		//return substr($str,0,$length).'...';	// we want to get fixed number of chars even if it breaks the word.
	}
}
?>



<!--

MOVE FOLLOWING CODE TO HEAD TAG BEFORE GOING LIVE....KEEP THIS CODE HERE FOR NOW JUST FOR DEVELOPING PURPOSE....


NOTE:  BELOW CSS FILES NEEDS TO BE CLEANED UP BEFORE GOING LIVE.  SAMIR IS WORKING ON THIS TEMPLATE


-->
<link rel="stylesheet" href="/templates/beez3/css/recipe_cards.css" type="text/css" />
<link rel="stylesheet" href="/media/com_yoorecipe/styles/magnific-popup.css" type="text/css" />
<script type="text/javascript" src="http://jqueryjs.googlecode.com/files/jquery-1.3.1.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="/media/com_yoorecipe/js/popup.js"></script>
<style>
.white-popup {
  position: relative;
  background: #FFF;
  padding: 20px;
  width:auto;
  max-width: 500px;
  margin: 20px auto;
}
</style>
<script type="text/javascript">
function switchLogin(showObject,hideObject)
{
	$('#'+showObject).show();
	$('#'+hideObject).hide();
}
function hideOthers()
{
	$('.caption').each(function(){$(this).hide();});

}
$(document).ready(function(){
	var thumbs = $("#main li img");
	for (var i = 0, ii = thumbs.length; i < ii; i++) {
		if (thumbs[i].title && thumbs[i].title.length > 0) {			
			var imgtitle = thumbs[i].title;
			var social = '<div class="social">'+$(this).find("#social_"+thumbs[i].id.split('_')[1]).html()+'</div>'
			$(thumbs[i]).wrap('<div class="cardblock" />').	//after('<div class=\'caption\'>' + imgtitle + social +'</div>').
			after('<div style="display:none" class=\'caption\'>' + social +'</div>').
			removeAttr('title');
		}
	}

	$('.cardblock').hover(
		function(){
			hideOthers();

			$(this).find('img').animate({opacity: ".9"}, 100);
			$(this).find('.caption').fadeIn(300);
		},
		function(){
				$(this).find('img').animate({opacity: "1.0"}, 300);
				$(this).find('.caption').fadeOut(300);//animate({top:"85px"}, 300);	
		}
	);
	
	$('.open-popup-link').magnificPopup({
		  type:'inline',
		  midClick: true, 
		  closeBtnInside: true
		});
});

</script>
<div id="main">
<ul>
<?php
$user = JFactory::getUser();
	
//Get login user favorite recipe id
$db = JFactory::getDBO();
$sql = "SELECT recipe_id FROM #__yoorecipe_favourites WHERE user_id = ".$db->quote($user->id);
$db->setQuery ($sql);
$fav_recipes = $db->loadColumn();
$firstItem = 0;
$pageNum = JRequest::getVar('start',0);
foreach ($this->items as $item) {
	$item_id = $item->id;
	$details_url = JRoute::_('index.php?option=com_yoorecipe&task=viewRecipe&id=' . $item->slug);
	$fav_url = JRoute::_('index.php?option=com_yoorecipe&task=addToFavourites&recipeId=' . $item_id);
	$fav_class = "";
	if ($user->get('guest') == 1)
	{
		$fav_url = "#login-popup";
		$fav_class="open-popup-link";
	}
	$remove_fav = JRoute::_('index.php?option=com_yoorecipe&task=removeFromFavourites&recipeId=' . $item_id);
	$recipe_title = $item->title;
	$recipe_image = $item->picture;
	$created_by = $item->created_by;
	$author = JFactory::getUser($item->created_by);
	$user_profile = JUserHelper::getProfile($item->created_by);

	$firstItem++; //Only the first sponsored recipe should be in big recipe card
	
?>
<?php if(strtoupper($item->featured) == '1' && $firstItem == 1 && $pageNum == 0){?>
<li style="width:434px;height:554px;">
		<a href="<?php echo $details_url; ?>">
		<div class="sponsored">
		<img style="width:430px;height:450px;" src="<?php if(file_exists($recipe_image)){echo $recipe_image;}else{echo "/images/default.jpg";} ?>" title="<?php echo cutWord($recipe_title,46); ?>" class='recipe_card_img' id="IMG_<?php echo $item_id; ?>" /></div>
		</a>
		
		<table id="card_text" style="width:434px;margin-top:-30px;margin-top:3px;">
			<tr>
				<td width="90%" class="title">
					<!--<a style="font-size:20px;" href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$author->username); ?>" id="publisher_name"><?php echo $author->name;?></a>-->
					<a style="font-size:17px;" href="<?php echo $details_url; ?>" id="publisher_name"><?php echo cutWord($recipe_title,46); ?></a>
			     		<?php if (strtoupper($item->featured) == '1' && array_search(13,$author->groups)) {
			     			$sponsored = "Sponsored by";}
			     			else{
			     				$sponsored = "By";
			     			}?>

						<?php if(isset($user_profile->profile['logoimage'])) {?>
							<br><div class="sponsored"><span style="float: left;margin-right: 8px;margin-top: 4px;"><?php echo $sponsored;?></span> <a style="letter-spacing:0px;" href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$author->username); ?>">
						<img style="width: 135px; height: 35px; margin-top: 3px;" src="images/logos/<?php echo $user_profile->profile['logoimage'];?>" /><?php //echo $item->rating;?>
						<?php }else {?><br><div class="sponsored"><span><?php echo $sponsored;?></span> <a style="letter-spacing:0px;" href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$author->username); ?>">
							<?php echo $author->name; } ?></a><div>
				</td>
				<!--<td valign="baseline" width="10%" id="favorite">
				<?php if(in_array($item_id,$fav_recipes)){?>
					<a class="isfav" href="<?php echo $remove_fav;?>"></a>
				<?php } else {?>
					<a class="fav" title="Save as Favorite" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Favorite');" href="<?php echo $fav_url;?>"></a>
				<?php }?>
				</td>-->
			</tr>
		</table>

		<div id="social_<?php echo $item_id; ?>" style="display:none;">
		
		<img src="<?php echo $this->baseurl; ?>/images/social-media-overlay-sp.png" usemap="#Image-Maps_<?php echo $item_id;?>"/>
		<map id="_Image-Maps_<?php echo $item_id;?>" name="Image-Maps_<?php echo $item_id;?>">
		<area target="_blank" shape="rect" coords="21,1,54,25" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>" alt="" title="" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Facebook')"   />
		<area target="_blank" shape="rect" coords="93,1,126,25" href="https://twitter.com/share?original_referer=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>" alt="" title=""  onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Twitter')"  />
		<area target="_blank" shape="rect" coords="168,1,201,25" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>&media=http://www.savvyfork.com/<?php echo urlencode($recipe_image); ?>&description=<?php echo urlencode($recipe_title); ?>" alt="" title=""  onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Pinterest')"  />
		<area target="_blank" shape="rect" coords="241,1,274,25" href="https://plus.google.com/share?url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>" alt="" title=""  onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Google+')"  />
		</map>
		<div class="cardStar">
					<?php if(in_array($item_id,$fav_recipes)){?>
						<a class="isfav" href="<?php echo $remove_fav;?>"></a>
					<?php } else {?>
						<a class="fav <?php echo $fav_class;?>" title="Save as Favorite" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Favorite');" href="<?php echo $fav_url;?>"></a>
					<?php }?>
			</div>
			<!--<a target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Facebook')" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>"><img src="/images/icons/gray/Facebook.png" /></a>
			<a target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Twitter')" href="https://twitter.com/share?original_referer=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>"><img src="/images/icons/gray/Twitter.png" /></a>
			<a target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Pinterest')" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>&media=http://www.savvyfork.com/<?php echo urlencode($recipe_image); ?>&description=<?php echo urlencode($recipe_title); ?>"><img src="/images/icons/gray/Pinterest.png" /></a>
			<a target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Google+')" href="https://plus.google.com/share?url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>"><img src="/images/icons/gray/Google+.png" /></a>-->
		</div>
</li>
<?php } else {?>
<li>
	<div class="cardDiv">
		<div class="cardImage">
			<a href="<?php echo $details_url; ?>">
			<img src="<?php if(file_exists($recipe_image)){echo $recipe_image;}else{echo "/images/default.jpg";} ?>" title="<?php echo cutWord($recipe_title,46); ?>" class='recipe_card_img' id="IMG_<?php echo $item_id; ?>" />
			</a>		
		</div>	
	
		<div class="cardTitle">
						<!--<a href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$author->username); ?>" id="publisher_name"><?php echo $author->name; ?></a>-->
						<a href="<?php echo $details_url; ?>" id="publisher_name"><?php echo cutWord(trim($recipe_title),46); ?></a>
						<?php if (array_search(13,$author->groups)) {$sponsored = "Sponsored by";$xStyle= "margin-top: 0px;float:right;";}else {
							$sponsored = "By";$xStyle= "margin-top: 0px;float:left;padding-left:2px;";
						} ?>
							<?php if(isset($user_profile->profile['logoimage'])) {?>
								<div class="sponsored"><span style="float: left;margin-top: 4px;"><?php echo $sponsored;?></span> <a style="letter-spacing:0px;" href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$author->username); ?>">
						<img style="width: 135px; height: 35px; margin-top: 3px;<?php echo $xStyle;?>" src="images/logos/<?php echo $user_profile->profile['logoimage'];?>" /><?php //echo $item->rating;?>
						<?php }else{?><div class="sponsored"><span><?php echo $sponsored;?></span> <a style="letter-spacing:0px;" href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$author->username); ?>">
							<?php echo $author->name;} ?></a></div>
	
		</div>
	
			
	</div>




		<div id="social_<?php echo $item_id; ?>" style="display:none;">
		<img src="<?php echo $this->baseurl; ?>/images/social-media-overlay.png" usemap="#Image-Maps_<?php echo $item_id;?>"/>
		<map id="_Image-Maps_<?php echo $item_id;?>" name="Image-Maps_<?php echo $item_id;?>">
		<area target="_blank" shape="rect" coords="4,0,32,25" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>" alt="" title="" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Facebook')"   />
		<area target="_blank" shape="rect" coords="43,0,71,25" href="https://twitter.com/share?original_referer=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>" alt="" title=""  onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Twitter')"  />
		<area target="_blank" shape="rect" coords="83,0,111,25" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>&media=http://www.savvyfork.com/<?php echo urlencode($recipe_image); ?>&description=<?php echo urlencode($recipe_title); ?>" alt="" title=""  onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Pinterest')"  />
		<area target="_blank" shape="rect" coords="122,0,150,25" href="https://plus.google.com/share?url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>" alt="" title=""  onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Google+')"  />
		</map>
		<div class="cardStar">
					<?php if(in_array($item_id,$fav_recipes)){?>
						<a class="isfav" href="<?php echo $remove_fav;?>"></a>
					<?php } else {?>
						<a class="fav <?php echo $fav_class;?>" title="Save as Favorite" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Favorite');" href="<?php echo $fav_url;?>"></a>
					<?php }?>
			</div>
			<!--<a target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Facebook')" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>"><img src="<?php echo $this->baseurl; ?>/images/icons/gray/Facebook.png" /></a>
			<a target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Twitter')" href="https://twitter.com/share?original_referer=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>"><img src="<?php echo $this->baseurl; ?>/images/icons/gray/Twitter.png" /></a>
			<a target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Pinterest')" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>&media=http://www.savvyfork.com/<?php echo urlencode($recipe_image); ?>&description=<?php echo urlencode($recipe_title); ?>"><img src="<?php echo $this->baseurl; ?>/images/icons/gray/Pinterest.png" /></a>
			<a target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $item_id;?>','Google+')" href="https://plus.google.com/share?url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>"><img src="<?php echo $this->baseurl; ?>/images/icons/gray/Google+.png" /></a>-->
		</div>
	</li>
<?php }
 } ?>
</ul>
</div>
<div id="login-popup" class="white-popup mfp-hide" style="width:300px;margin:0 auto;background:#E8E8E8;">
	<form></form>
	<form method="post" action="/register.html?task=registration.popupRegister" id="pop-member-registration">
		<div class="prompt">Already registered? <a href="" onclick="switchLogin('pop-login-form','pop-member-registration');return false;">Log in</a></div>
				<fieldset>
					<legend>Join the cool table...</legend>
					<div class="subtitle">(You can save and share your favorite recipes!)</div>
					<div class="control-group">
						<div class="controls">
							<input type="email" size="30" placeholder="Email Address" value="" id="jform_email1" class="validate-email required" name="jform[email1]" aria-required="true" required="required">
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="text" size="30" placeholder="Username" class="validate-username required" value="" id="jform_username" name="jform[username]" aria-required="true" required="required">
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="password" size="30" placeholder="Password" class="validate-password required" autocomplete="off" value="" id="jform_password1" name="jform[password1]" aria-required="true" required="required">
						</div>
					</div>
					<div class="control-group">
						<div class="controls">
							<input type="password" size="30" placeholder="Password Confirm" class="validate-password required" autocomplete="off" value="" id="jform_password2" name="jform[password2]" aria-required="true" required="required">
						</div>
					</div>
			</fieldset>
			<div class="terms"><input type="checkbox" id="pop-terms" name="pop-terms" checked/><span>Yes, I agree to the SavvyFork's <a href="/index.php/terms-of-use">terms of use</a>!</span></div>
			<div class="form-actions">
			<input class="btn btn-primary validate" type="submit" value="Submit">
			<input type="hidden" value="com_users" name="option">
			<input type="hidden" value="registration.popupRegister" name="task">
			<input type="hidden" value="popup" name="where">
			<?php echo JHtml::_('form.token');?>
			</div>
	</form>
	<form class="form-inline" id="pop-login-form" method="post" action="/login.html" style="display:none;">
    	<div class="prompt">New user? Please <a href="" onclick="switchLogin('pop-member-registration','pop-login-form');return false;">Register</a></div>
	    <fieldset>
	      <legend>Login</legend>
		<div class="control-group" id="form-login-username">
			<div class="controls" style="margin-top:18px;">
					<input type="text" placeholder="Username" size="18" tabindex="1" class="input-small validate-username required" name="username" id="modlgn-username" aria-required="true" required="required">
			</div>
		</div>
		<div class="control-group" id="form-login-password">
			<div class="controls">
				<input type="password" placeholder="Password" size="18" tabindex="2" class="input-small validate-password required" name="password" id="modlgn-passwd" aria-required="true" required="required">
			</div>
		</div>
		<div class="control-group checkbox" id="form-login-remember">
			<label class="control-label" for="modlgn-remember">Remember Me</label>
			<input type="checkbox" value="yes" class="inputbox" name="remember" id="modlgn-remember">
			<input class="btn btn-primary btn" name="Submit" tabindex="3" type="submit" value="Login">
		</div>
		</fieldset>
		<input type="hidden" value="com_users" name="option">
		<input type="hidden" value="user.login" name="task">
		<input type="hidden" value="aW5kZXgucGhwP0l0ZW1pZD0yMDE=" name="return">
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
<div class="clear"></div>