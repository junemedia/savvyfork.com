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
			<img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/headerimg_reserve.png" />
			<div class="right">
				<div class="intro">
					<span style="font-size:25px;color:#0a8e36;font-weight: normal;letter-spacing: 1px;">ThatGirl23</span><br/>
					<span>San Franciso, CA</span>
				</div>
				<div class="link">
				<a href="#">Favorite recipes</a><br/>
				<a href="#">Edit your profile</a>
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
			<li><span class="account">www.facebook.com/ThatGirl23</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/facebook_y.png"/></li>
			<li><span class="account">#thatgirl23</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/twitter_y.png"/></li>
			<li><span class="account">www.pinterest.com/ThatGirl23</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/pin_y.png"/></li>
			<li><span class="account">thatgirl23@gmail.com</span><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/gplus_y.png"/></li>
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
	<div id="recipe_cards">
	 Recipe cards will be here! 	
	</div>
</div>



