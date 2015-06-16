<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_socialloginandsocialshare
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}
JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip');
?>
<?php if ($type == 'logout') : ?>
<?php $session = JFactory::getSession();
  if ($lr_settings['showlogout'] == 1) :?>
<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-vertical">
<div>
  <?php $db = JFactory::getDBO();
	   $user_lrid = $session->get('user_lrid');
	   $query = "SELECT * FROM ".$db->quoteName('#__LoginRadius_users')." WHERE id = '".$user->get('id')."' AND LoginRadius_id=".$db->Quote ($user_lrid);
       $db->setQuery($query);
       $find_id = $db->loadResult();
       $query = "SELECT COUNT(*) FROM ".$db->quoteName('#__LoginRadius_users')." WHERE id = ".$user->get('id');
       $db->setQuery($query);
       $count = $db->loadResult();
	   if (empty($find_id)) {
	     $count = $count;
	   }
	   else {
	     $count = ($count == 0 ? $count : $count -1 );
	   }?>
	   <?php $user_picture = $session->get('user_picture');?>
     <div style="float:left;"><a href="<?php echo 'index.php?option=com_socialloginandsocialshare&view=profile';?>" title="My Profile">
<img src="<?php if (!empty($user_picture)) { echo JURI::root().'images'.DS.'sociallogin'.DS. $session->get('user_picture');} else {echo JURI::root().'media' . DS . 'com_socialloginandsocialshare' . DS .'images' . DS . 'noimage.png';}?>" alt="<?php echo $user->get('name');?>" style="width:50px; height:auto;background: none repeat scroll 0 0 #FFFFFF; border: 1px solid #CCCCCC; display: block; margin: 2px 4px 4px 0; padding: 2px;"></a>
     </div>
     <div>
	<div class="login-greeting">
	<div style=" font-weight:bold;">
	<?php if($lr_settings['showname'] == 0) : {
		echo JText::sprintf('MOD_LOGINRADIUS_HINAME', htmlspecialchars($user->get('name')));
	} else : {
		echo JText::sprintf('MOD_LOGINRADIUS_HINAME', htmlspecialchars($user->get('username')));
	} endif; ?></div>
			<?php echo JText::_('MOD_LOGINRADIUS_VALUE_MAP'); ?> <b><?php echo $count;?></b><br /> <?php echo JText::_('MOD_LOGINRADIUS_VALUE_MAPONE'); ?>
       </div><br />
       <a href="<?php echo 'index.php?option=com_socialloginandsocialshare&view=profile';?>"><?php echo JText::_('MOD_LOGINRADIUS_VALUE_ACCOUNT'); ?></a>
	
	<div class="logout-button">
		<input type="submit" name="Submit" class="btn btn-primary" value="<?php echo JText::_('JLOGOUT'); ?>" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	 </div>
	</div>
  </div>
</form>
<?php endif; ?>
<?php else : ?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-inline">
    <?php if ($lr_settings['showicons'] == 0) {?>
	        <div class="pretext">
		         <p><?php echo $params->get('pretext'); ?></p>
		    </div>
	        <?php if (!empty($lr_settings['apikey'])) {
              $http = "http://";
			  if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') && (isset($_SERVER['HTTPS']))) {
			  	$http = "http://";
			}

	          $loc = (isset($_SERVER['REQUEST_URI']) ? urlencode($http.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']) : urlencode($http.$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']));?><script src="//hub.loginradius.com/include/js/LoginRadius.js" ></script> <script type="text/javascript"> var options={}; options.login=true; LoginRadius_SocialLogin.util.ready(function () { $ui = LoginRadius_SocialLogin.lr_login_settings;$ui.interfacesize = "large";$ui.apikey = "<?php echo $lr_settings['apikey'] ?>";$ui.callback="<?php echo $loc; ?>"; $ui.lrinterfacecontainer ="interfacecontainerdiv"; LoginRadius_SocialLogin.init(options); }); </script>
			   <div id="interfacecontainerdiv" class="interfacecontainerdiv" style="float: right;margin: 0 auto;position: relative;text-align: center;top: 108px;width: 87%;"></div> 
      <?php }
	  else{
echo '<div style="background-color: #FFFFE0;border:1px solid #E6DB55;padding:5px;">'.JText::_('MOD_SOCIALLOGIN_AND_SOCIALSHARE_UNPLUSH_ERROR').'</div>';
}
		  }
 if ($lr_settings['showwithicons'] == 1): ?>
	<div class="userdata">
		<div id="form-login-username" class="control-group">
			<div class="controls">
				<div class="input-prepend input-append">
					<span class="add-on"><i class="icon-user tip" title="<?php echo JText::_('MOD_LOGINRADIUS_VALUE_USERNAME') ?>"></i><label for="modlgn-username" class="element-invisible"><?php echo JText::_('MOD_LOGINRADIUS_VALUE_USERNAME'); ?></label></span><input id="modlgn-username" type="text" name="username" class="input-small" tabindex="1" size="18" placeholder="<?php echo JText::_('MOD_LOGINRADIUS_VALUE_USERNAME') ?>" /><a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>" class="btn hasTooltip" title="<?php echo JText::_('MOD_LOGINRADIUS_FORGOT_YOUR_USERNAME'); ?>"><i class="icon-question-sign"></i></a>
				</div>
			</div>
		</div>
		<div id="form-login-password" class="control-group">
			<div class="controls">
				<div class="input-prepend input-append">
					<span class="add-on"><i class="icon-lock tip" title="<?php echo JText::_('JGLOBAL_PASSWORD') ?>"></i><label for="modlgn-passwd" class="element-invisible"><?php echo JText::_('JGLOBAL_PASSWORD'); ?></label></span><input id="modlgn-passwd" type="password" name="password" class="input-small" tabindex="2" size="18" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>" /><a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>" class="btn hasTooltip" title="<?php echo JText::_('MOD_LOGINRADIUS_FORGOT_YOUR_PASSWORD'); ?>"><i class="icon-question-sign"></i></a>
				</div>
			</div>
		</div>
		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
		<div id="form-login-remember" class="control-group checkbox">
			<label for="modlgn-remember" class="control-label"><?php echo JText::_('MOD_LOGINRADIUS_REMEMBER_ME') ?></label> <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
		</div>
		<?php endif; ?>
		<div id="form-login-submit" class="control-group">
			<div class="controls">
				<button type="submit" tabindex="3" name="Submit" class="btn btn-primary btn"><?php echo JText::_('JLOGIN') ?></button>
			</div>
		</div>
		<?php
			$usersConfig = JComponentHelper::getParams('com_users');
			if ($usersConfig->get('allowUserRegistration')) : ?>
			<ul class="unstyled">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
					<?php echo JText::_('MOD_LOGINRADIUS_REGISTER'); ?> <i class="icon-arrow-right"></i></a>
				</li>

			</ul>
		<?php endif; ?>
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
	<?php endif; ?>
	<?php if ($lr_settings['showicons'] == 1) {
	        if ($params->get('pretext')): ?>
		<div class="pretext">
		<p><?php echo $params->get('pretext'); ?></p>
		</div>
	<?php endif; 
	        if (!empty($lr_settings['apikey'])) {
               $http = "http://";
			  if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')&&(isset($_SERVER['HTTPS']))) {
			  	$http = "http://";
			}

	          $loc = (isset($_SERVER['REQUEST_URI']) ? urlencode($http.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']) : urlencode($http.$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']));?><script src="//hub.loginradius.com/include/js/LoginRadius.js" ></script> <script type="text/javascript"> var options={}; options.login=true; LoginRadius_SocialLogin.util.ready(function () { $ui = LoginRadius_SocialLogin.lr_login_settings;$ui.interfacesize = "small";$ui.apikey = "<?php echo $lr_settings['apikey'] ?>";$ui.callback="<?php echo $loc; ?>"; $ui.lrinterfacecontainer ="interfacecontainerdiv"; LoginRadius_SocialLogin.init(options); }); </script> 
			   <div id="interfacecontainerdiv" class="interfacecontainerdiv"></div>
      <?php }
	  else{
echo '<div style="background-color: #FFFFE0;border:1px solid #E6DB55;padding:5px;">'.JText::_('MOD_SOCIALLOGIN_AND_SOCIALSHARE_UNPLUSH_ERROR').'</div>';
}

		 }
	 if ($params->get('posttext')): ?>
		<div class="posttext">
		<p><?php echo $params->get('posttext'); ?></p>
		</div>
	<?php endif; ?>
</form>
<?php  endif;?>