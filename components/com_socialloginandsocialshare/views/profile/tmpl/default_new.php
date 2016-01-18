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

$user = JFactory::getUser();

if($user->id == $this->data->id)
{
	$user_profile = JUserHelper::getProfile($this->data->id);
	$picture = new ProfilePicture($this->data->id);
	$headimage = $picture->getURL();		
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
$sql = "SELECT * FROM #__LoginRadius_users WHERE id =".$this->data->id;
$db->setQuery($sql);
$user_social = $db->loadObjectList('provider');
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
	<div id="core_pf">
		<div class="top">
			<img style="width:133px;height:132px;" src="<?php echo $headimage; ?>" />
			<div class="right">
				<div class="intro">
					<span style="font-size:25px;color:#0a8e36;font-weight: normal;letter-spacing: 1px;"><?php echo $user->name;?></span><br/>
					<span><?php echo $user_profile->profile['city'].', '.$user_profile->profile['region']?></span>
				</div>
				<div class="link">
				<a href="#">Favorite recipes</a><br/>
				<?php if (JFactory::getUser()->id == $this->data->id) : ?>
				<a href="<?php echo JRoute::_('index.php?option=com_users&task=profile.edit&user_id='.(int) $this->data->id);?>">Edit your profile</a>
				<?php endif; ?>
				</div>				
			</div>
		</div>
		<div class="bottom">
			<div class="follow">
				<span>Following:2345</span>
				<span style="float:right;">Followers:321</span>
			</div>
			<div class="invite"><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/mail_ico.png" /><a href="#">Invite friends</a></div>
		</div>
	</div>
	<div id="socialmedia_pf">
		<span class="title">SOCIAL MEDIA TRACK</span>
		<ul>
			<li><span class="account">www.facebook.com/<?php echo $user_social['facebook']->account; ?></span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/facebook_y.png"/></li>
			<li><span class="account">#<?php echo $user_social['twitter']->account;?></span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/twitter_y.png"/></li>
			<li><span class="account">www.pinterest.com/<?php echo $user_social['pinterest']->account;?></span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/pin_y.png"/></li>
			<li><span class="account"><?php echo $user_social['google']->email;?></span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/gplus_y.png"/></li>
		</ul>
	</div>
	<div id="recent_activity">
		<span class="title">RECENT ACTIVITY</span>
		<ul class="sp_line">
			<li>Shared her <span class="from">Bunny Muffins</span> recipe on <span class="social_media">Facebook.</span></li>
			<li>Tweeted <span class="from">Easter Filled Eggs's</span> recipe on <span class="social_media">Twitter.</span></li>
			<li>Pinned <span class="from">Veggy Pasta's</span> recipe on <span class="social_media">Pinterest.</span></li>
			<li>Shared her <span class="from">Coconut Cake's</span> recipe on <span class="social_media">Facebook.</span></li>
		</ul>
	</div>
</div>

<div id="user_favorite">
	<div class="sp_line2"></div>
	<h2>USER FAVORITES</h2>
	<div class="sp_line2"></div>
</div>



