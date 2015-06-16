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

if($this->partnerid != null)
{
	$partner = JFactory::getUser($this->partnerid);
	$db = JFactory::getDBO();
	$nullDate 	= $db->Quote($db->getNullDate());
	$nowDate 	= $db->Quote(JFactory::getDate()->toSql());
	
	$sql = "SELECT id FROM #__yoorecipe WHERE published = 1 AND validated = 1 AND (publish_up = " . $nullDate . " OR publish_up <= " . $nowDate . ") AND created_by = ".$db->quote($this->partnerid);
	$db->setQuery ($sql);
	$recipe_count = count($db->loadAssocList());
	$partnerprofile = JUserHelper::getProfile($this->partnerid);
	$picture = new ProfilePicture($this->partnerid);
	$headimage = $picture->getURL('original');
	if(!$headimage)
	{
		$headimage="images/headimg_reserve.jpg";
	}	
	
	$this->document->setTitle($partner->name . ' | SavvyFork');
	
	$get_views = "SELECT SUM(nb_views) AS totalViews FROM #__yoorecipe WHERE created_by = ".$db->quote($this->partnerid);
	$db->setQuery ($get_views);
	$temp = $db->loadAssocList();
	echo "<!-- Views: ".$temp[0]['totalViews']." -->";
}
else
{
	header("Location:./login");
}

function getSocialStats($socialmedia = null,$url = null)
{
	$result = 0;
	if($socialmedia != null && $url != null)
	{
		$source_url = urlencode($url);
		switch($socialmedia)
		{
			case 'facebook':
			{					
				$url = "http://api.facebook.com/restserver.php?method=links.getStats&urls=".$source_url;
				$xml = file_get_contents($url);
				$xml = simplexml_load_string($xml);
				$result = $xml->link_stat->like_count;
				break;
			}
			case 'twitter':
			{
				$url = "http://urls.api.twitter.com/1/urls/count.json?url=".$source_url;
				$json = json_decode(file_get_contents($url)); 
				$result = $json->count;
				break;
			}
			case 'pinterest':
			{
				$url = "http://api.pinterest.com/v1/urls/count.json?callback=&url=".$source_url;
				$json_string = file_get_contents($url);
				$json = json_decode(substr($json_string,1,strlen($json_string)-2));
				$result = $json->count;
				break;
			}
			case 'google+':
			{
				$ch = curl_init();   
				curl_setopt($ch, CURLOPT_URL, "https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ"); 
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.'http://www.savvyfork.com/index.php/component/yoorecipe/recipe/85-15-minute-taco-in-a-pan'. '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));

				$curl_results = curl_exec($ch);
				curl_close ($ch);

				$parsed_results = json_decode($curl_results, true);

				$result = $parsed_results[0]['result']['metadata']['globalCounts']['count'];
				break;
			}
		}
	}
	
	return (int)$result;
}

function cutWord($str,$length)
{
	if($str[0]=='"')
	{
		$str = substr($str,1,strlen($str));
	}
	
	if(strlen($str) <= $length){
		if (strlen($str) < 27) { return $str."<br>"; }
		return $str;
	}else{
		$pos = strrpos(substr($str, 0, $length) , ' ', -1);
		return substr($str, 0, $pos) . " ...";
	}
}
?>
<!--<div class="profile <?php echo $this->pageclass_sfx?>">
<?php if (JFactory::getUser()->id == $this->data->id) : ?>
<ul class="btn-toolbar pull-right">
	<li class="btn-group">
		<a class="btn" href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>">
			<span class="icon-user"></span> <?php echo JText::_('COM_USERS_EDIT_PROFILE'); ?></a>
	</li>
</ul>
<?php endif; ?>
<?php if ($this->params->get('show_page_heading')) : ?>
<div class="page-header">
	<h1>
		<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
</div>
<?php endif; ?>

<?php echo $this->loadTemplate('core'); ?>

<?php echo $this->loadTemplate('params'); ?>

<?php echo $this->loadTemplate('custom'); ?>

</div>-->

<div class="profile shadow">
	<div id="core_pf" style="padding-right:45px;">
		<div class="top">
			<img style="border:1px solid #e7e7e7;width:135px;height:134px;" src="<?php echo $headimage; ?>" />
			<div class="right">
				<div class="intro">
					<span style="font-size:25px;color:#0a8e36;font-weight: normal;letter-spacing: 1px;"><?php echo $partner->name?></span>
				</div>
				<div class="sp_line" style="margin-top:5px;"></div>
				<div class="addiction">
				<span style="color: #818181;font-size: 2em;font-weight: normal;"><?php echo $this->rating;?></span>
				</div>		
				<div class="sp_line"></div>		
				<div style="color: #494646;text-align: center; font-weight: bold;">Avg. Editor Rating</div>
			</div>
		</div>
		<div class="bottom">
			<div class="link" style="padding-top:0px;">
			<?php 
				if($partnerprofile->profile["companyurl"] != "")
				{ $link = $partnerprofile->profile["companyurl"];
				?>
					<a href="http://<?php echo $link;?>" style="font-size:16px;" target="_blank"><?php echo $link;?></a>
				<?php }?>
			</div>
			<div class="p_invite">
				Total Recipes Submitted:<span><?php echo $recipe_count;?></span>
			</div>
		</div>
	</div>
	<div id="socialmedia_pf" style="padding-right:40px;width:260px;">
		<span class="title">SOCIAL MEDIA TRACK</span>
		<ul>
			<!--<li><span class="account"><?php echo getSocialStats('facebook',$link);?> likes</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/facebook_y.png"/></li>
			<li><span class="account"><?php echo getSocialStats('twitter',$link);?> following</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/twitter_y.png"/></li>
			<li><span class="account"><?php echo getSocialStats('pinterest','http://'.$link);?> pins</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/pin_y.png"/></li>
			<li><span class="account"><?php echo getSocialStats('google+','http://'.$link);?> users</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/gplus_y.png"/></li>-->			
			<li><span class="account"><?php echo isset($this->socialStats[1])?$this->socialStats[1]["sharecount"]:0;?> likes</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/facebook_y.png"/></li>
			<li><span class="account"><?php echo isset($this->socialStats[2])?$this->socialStats[2]["sharecount"]:0;?> tweets</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/twitter_y.png"/></li>
			<li><span class="account"><?php echo isset($this->socialStats[3])?$this->socialStats[3]["sharecount"]:0;?> pins</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/pin_y.png"/></li>
			<li><span class="account"><?php echo isset($this->socialStats[4])?$this->socialStats[4]["sharecount"]:0;?> +1s</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/gplus_y.png"/></li>
		</ul>
	</div>
	<div id="recent_activity">
		<span class="title">ABOUT ME</span>
		<div class="sp_line" style="margin-top:3px;"></div>
		<div class="aboutme">
		<span>"<?php echo cutWord($partnerprofile->profile["aboutme"],240);?>"</span> 
		</div>
	</div>
</div>

<div id="user_favorite">

</div>



