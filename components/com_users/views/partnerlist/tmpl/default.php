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

<?php


?>

<div id="partnerlist">
<?php if(!empty($this->featuredPartnersList)){ ?>
	<div id="featured_p">
		<h2>FEATURED PARTNERS</h2>
		<div class="sp_line"></div>
		<div class="featured_t">
		<table>
		<tr>
			<th>Publishers</th>
			<th>Published recipes</th>
			<th>Top rated recipes</th>
			<th style="text-align:center;">Avg Recipe Rating</th>
		</tr>
		<?php 
		foreach($this->featuredPartnersList as $partner) {

		?>
		<tr>
			<td width="32%">
				<div class="topspace">
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$partner->username); ?>"><img src="<?php echo $partner->headimage; ?>" style="width:80px;height:80px;" /></a>
					<div class="p_info">
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$partner->username); ?>"><span class="name"><?php echo $partner->name;?></span></a>
					<br/>
					<a href="http://<?php echo $partner->profile->profile["companyurl"];?>" target="_blank" class="site"><?php echo $partner->profile->profile["companyurl"];?> </a>
					</div>
				</div>
			</td>
			<td width="14%">
				<div class="published topspace">
					<!--<img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/search.png"/>-->
					<span class="n_recipe"><?php echo $partner->recipeCount;?> recipes</span>
				</div>		
			</td>
			<td width="38%">
				<div id="rated_recipe" class="topspace">
				<?php if(!empty($partner->recipe)){
					foreach($partner->recipe as $recipe){
						$recipeUrl = JRoute::_('index.php?option=com_yoorecipe&task=viewRecipe&id=' . $recipe["slug"]); 
				?>
					<div class="t_radius"><a href="<?php echo $recipeUrl;?>"><img src="<?php if(file_exists($recipe["picture"])){echo $recipe["picture"];}else{echo "/images/default.jpg";} ?>" /></a></div>
				<?php 
					}
				} else {?>
				<span>No recipes published.</span>
				<?php }?>
				</div>
			</td>
			<td style="text-align:center;">
				<div id="rating" class="topspace">
					<span class="rating"><?php echo $partner->avgrating;?></span>
				</div>
			</td>
		</tr>
		<?php }?>
		</table>
		</div>	
	</div>
	<?php }?>

	<div id="all_p">
		<span class="title">
		All Recipe Publishers (<?php echo $this->total+count($this->featuredPartnersList);?> total)
		</span>
		<div id="all_t" class="featured_t">
			<table>
		<tr>
			<th>Publishers</th>
			<th>Published recipes</th>
			<th>Top rated recipes</th>
			<th style="text-align:center;">Avg Recipe Rating</th>
		</tr>		
		<?php 
		foreach($this->partnersList as $partner) {
		?>
		<tr class="sp_line2"><td colspan="4"></td></tr>
		<tr>
			<td width="32%">
				<div class="topspace">
					<div class="p_radius">
						<a href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$partner->username); ?>"><img src="<?php echo $partner->headimage; ?>" style="width:80px;height:80px;"/></a>
					</div>
					<div class="p_info">
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$partner->username); ?>"><span class="name"><?php echo $partner->name;?></span></a>
					<br/>
					<a href="http://<?php echo $partner->profile->profile["companyurl"];?>" target="_blank" class="site"><?php echo $partner->profile->profile["companyurl"];?> </a>
					</div>
				</div>
			</td>
			<td width="14%">
				<div class="topspace published">
					<!--<img src="<?php echo $this->baseurl; ?>/templates/beez3/images/user/search.png"/>-->
					<span class="n_recipe"><?php echo $partner->recipeCount;?> recipes</span>
				</div>		
			</td>
			<td width="38%">
				<div id="rated_recipe" class="topspace">
				<?php if(!empty($partner->recipe)){
					foreach($partner->recipe as $recipe){
						$recipeUrl = JRoute::_('index.php?option=com_yoorecipe&task=viewRecipe&id=' . $recipe["slug"]); 
				?>
					<div class="t_radius"><a href="<?php echo $recipeUrl;?>"><img src="<?php if(file_exists($recipe["picture"])){echo $recipe["picture"];}else{echo "/images/default.jpg";} ?>" /></a></div>
				<?php 
					}
				} else {?>
				<span>No recipes published.</span>
				<?php }?>
				<!--<div class="t_radius"><a href="#"><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/partnerlist/r2.png" /></a></div>
					<div class="t_radius"><a href="#"><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/partnerlist/r2.png" /></a></div>
					<div class="t_radius"><a href="#"><img src="<?php echo $this->baseurl; ?>/templates/beez3/images/partnerlist/r3.png" /></a></div>-->
				</div>
			</td>
			<td style="text-align:center;">
				<div id="rating" class="topspace">
					<span class="rating"><?php echo $partner->avgrating;?></span>
				</div>
			</td>
		</tr>
		<?php }?>
		</table>
		</div>
		<?php if($this->total > $this->limit){ ?>
		<!--<table style="width:200px;border:0;align:center;margin-left:auto;margin-right:auto;">
			<tbody>
				<tr>
				<?php if($this->limitStart == 0){?>
					<td><span class="Prevpagenav">Prev</span></td>
				<?php } else {?>
					<td><a class="Prevpagenav" href="/index.php/partner-list?start=<?php echo $this->limitStart-$this->limit;?>" title="Next">Prev</a></td>
				<?php }?>
				<?php if($this->total-$this->limitStart>$this->limit){?>
					<td><a class="Nextpagenav" href="/index.php/partner-list?start=<?php echo $this->limitStart+$this->limit;?>" title="Next">Next</a></td>
				<?php } else {?>
					<td><span class="Nextpagenav">Next</span></td>
				<?php }?>
				</tr>
			</tbody>
		</table>-->
		<?php }?>
	</div>
</div>



