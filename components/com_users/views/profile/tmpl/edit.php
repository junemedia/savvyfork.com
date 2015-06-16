<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
//load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load('plg_user_profile', JPATH_ADMINISTRATOR);
if (!defined('DS')) {
   define('DS',DIRECTORY_SEPARATOR);
}

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
?>
<div class="profile-edit<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>
<?php $session = JFactory::getSession();
      $lr_settings = array ();
      $db = JFactory::getDBO ();
	  $sql = "SELECT * FROM #__LoginRadius_settings";
      $db->setQuery ($sql);
      $rows = $db->LoadAssocList ();
      if (is_array ($rows)) {
        foreach ($rows AS $key => $data) {
          $lr_settings [$data ['setting']] = $data ['value'];
        }
      }
	  $sql = "SELECT * FROM #__LoginRadius_users WHERE id =".JFactory::getUser()->id;
      $db->setQuery($sql);
      $acmaprows = $db->loadObjectList();
	  
	  ?>
	  <style>
	  .buttondelete {
	     background: -moz-linear-gradient(center top , #FCFCFC 0%, #E0E0E0 100%) repeat scroll 0 0 transparent;
         border: 1px solid #CCCCCC;
         border-radius: 5px 5px 5px 5px;
         color: #666666;
         padding: 1px;
         text-shadow: 0 1px 0 #FFFFFF;
		 cursor:pointer;
		 margin-left:5px;
      }
	  .AccountSetting-addprovider {
         list-style: none outside none !important;
         margin: 0 !important;
         padding: 0 !important;
         text-decoration: none;
		 line-height:normal!important;
      }
	  .AccountSetting-addprovider li {
         background: none repeat scroll 0 0 transparent !important;
		border: medium none !important;
		border-radius: 0 0 0 0 !important;
		float: left !important;
		height: auto !important;
		line-height: normal !important;
		list-style: none outside none !important;
		margin: 0 0 5px !important;
		min-width: 30px !important;
		padding: 0 !important;
		width: auto !important;
		word-wrap: break-word !important;
       }
	  </style>
<fieldset id="users-profile-core" >
	<legend>
		<?php echo JText::_('Link your account with another social account'); ?>
	</legend>
	
	    <div>
	       <div style="float:right;">
	         <?php if (!empty($lr_settings['apikey'])) {
             $http = "http://";
			  if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off')&&(isset($_SERVER['HTTPS']))) {
			  	$http = "http://";
			}

	          $loc = (isset($_SERVER['REQUEST_URI']) ? urlencode($http.$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']) : urlencode($http.$_SERVER["HTTP_HOST"].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']));?><script src="//hub.loginradius.com/include/js/LoginRadius.js" ></script> <script type="text/javascript"> var options={}; options.login=true; LoginRadius_SocialLogin.util.ready(function () { $ui = LoginRadius_SocialLogin.lr_login_settings;$ui.interfacesize = "small";$ui.apikey = "<?php echo $lr_settings['apikey'] ?>";$ui.callback="<?php echo $loc; ?>"; $ui.lrinterfacecontainer ="interfacecontainerdiv"; LoginRadius_SocialLogin.init(options); }); </script>
			   <div id="interfacecontainerdiv" class="interfacecontainerdiv"></div> 
            <?php }?></div>
			<div style="float:left; width:270px;">
			   <div style="float:left; padding:5px;">
			   <?php $user_picture = $session->get('user_picture');?>
			   <img src="<?php echo $headimage;?>" alt="<?php echo JFactory::getUser()->name?>" style="height:80px;width:80px; height:auto;background: none repeat scroll 0 0 #FFFFFF; border: 1px solid #CCCCCC; display: block; margin: 2px 4px 4px 0; padding: 2px;">
			   </div>
			   <div style="float:right;padding:5px;font-size: 20px;margin: 5px;">
			   <b><?php echo JFactory::getUser()->name?></b>
			   </div>
			</div>
	      </div>
		  <div style="clear:both;"></div><br />
	  <?php echo JText::_('By adding another account, you can log in with the new account as well!'); ?><br /><br />
	  
	  <div style="width:350px;">
	  <ul class="AccountSetting-addprovider">
	  <?php $msg = JText::_('You can also use'); ?>
	  <?php foreach ($acmaprows as $row) {?>
	  
	<li>
	<form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_socialloginandsocialshare&task=profile.delmap'); ?>" method="post" class="form-validate" enctype="multipart/form-data">
	<?php if ($row->LoginRadius_id == $session->get('user_lrid')) {
	        $msg = '<span style="color:red;">'.JText::_('Currently connected with').'</span>';   
	      }
		  else {
		    $msg = JText::_('You can also use');
		  }?>
		 
	        <span style="margin-right:5px;"> <img src="<?php echo 'administrator/components/com_socialloginandsocialshare/assets/img/'.$row->provider.'.png'; ?>" /></span>
			
			<?php echo $msg;?>
			<b><?php echo $row->provider; ?></b>
			<button type="submit" class="buttondelete"><span><?php echo JText::_('Remove'); ?></span></button><input type="hidden" name="option" value="com_socialloginandsocialshare" />
			<input type="hidden" name="task" value="profile.delmap" />
			<input type="hidden" name="mapid" value="<?php echo $row->provider; ?>" />
			<input type="hidden" name="lruser_id" value="<?php echo $row->LoginRadius_id; ?>" />
			</form>
			<?php echo JHtml::_('form.token'); ?></li><br />
	<?php }?>
	</ul>
	</div>
	
</fieldset>
<form id="member-profile" action="<?php echo JRoute::_('index.php?option=com_users&task=profile.save'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">
<?php foreach ($this->form->getFieldsets() as $group => $fieldset):// Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($group);?>
	<?php if (count($fields)):?>
	<fieldset>
		<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.?>
		<legend><?php echo JText::_($fieldset->label); ?></legend>
		<?php endif;?>
		<?php foreach ($fields as $field):// Iterate through the fields in the set and display them.?>
		<?php if(!array_search(13,$user->groups)&& ($field->fieldname == "rightbanner" || $field->fieldname =="footerbanner"))
	{ echo "";}
	else{
	?>
			<?php if ($field->hidden):// If the field is hidden, just display the input.?>
				<div class="control-group">
					<div class="controls">
						<?php echo $field->input;?>
					</div>
				</div>
			<?php else:?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $field->label; ?>
						<?php if (!$field->required && $field->type != 'Spacer') : ?>
						<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
						<?php endif; ?>
					</div>
					<div class="controls">
						<?php echo $field->input; ?>
					</div>
				</div>
			<?php endif;?>
			<?php }?>
		<?php endforeach;?>
	</fieldset>
	<?php endif;?>
<?php endforeach;?>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary validate"><span><?php echo JText::_('JSUBMIT'); ?></span></button>
			<a class="btn" style="display:none;" href="<?php echo JRoute::_(''); ?>" title="<?php echo JText::_('JCANCEL'); ?>"><?php echo JText::_('JCANCEL'); ?></a>

			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="profile.save" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>
</div>
