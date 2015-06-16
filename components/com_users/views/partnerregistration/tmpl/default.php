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

$app	= JFactory::getApplication();
$formData = (array) $app->getUserState('com_users.partnerregistration.data', array());
?>

<div class="registration<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>
	<div class="page-header">
		<h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	</div>
<?php endif; ?>

	<form id="member-registration" action="<?php echo JRoute::_('index.php?option=com_users&task=partnerregistration.register'); ?>" enctype="multipart/form-data" method="post" class="form-validate form-horizontal">
<?php foreach ($this->form->getFieldsets() as $fieldset): // Iterate through the form fieldsets and display each one.?>
	<?php $fields = $this->form->getFieldset($fieldset->name);?>
	<?php if (count($fields)):?>
		<fieldset>
		<?php if (isset($fieldset->label)):// If the fieldset has a label set, display it as the legend.
		?>
			<legend><?php if (JText::_($fieldset->label) == 'User Registration') { echo 'Become A Partner'; } else { echo JText::_($fieldset->label); }?></legend>
		<?php endif;?>
		<div class="partner-registration-left">
		<?php 
		$i=0;
		foreach ($fields as $field) :// Iterate through the fields in the set and display them.?>
		<?php $i++;
		if($field->fieldname == "rightbanner" || $field->fieldname =="footerbanner")
	{ echo "";}
	else{?>
			<?php if ($field->hidden):// If the field is hidden, just display the input.?>
				<?php echo $field->input;?>
			<?php else:?>
				<div class="control-group" style="<?php if($i%2==0) echo 'clear:both;'; if($field->type == 'File') echo 'clear:both;width:100%;margin-top:15px;';?>">
					<div class="control-label">
					<?php echo $field->label; ?>
					<?php if (!$field->required && $field->type != 'Spacer') : ?>
						<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL');?></span>
					<?php endif; ?>
					</div>
					<div class="controls">
						<?php echo $field->input;?>
					</div>
				</div>
			<?php endif;?>
		<?php }?>
		<?php endforeach;?>
		<div class="newsletters" style="clear: both; margin-top: 20px; float: left;">
		 <input type="checkbox" name="recive_newsletters" value="" checked />Receive email updates from SavvyFork.
		</div>
		
		</div>
		<div class="partner-registration-right">
			<div style="width:100%" class="control-group">
				<div class="control-label">
					<span class="spacer">
						<span class="before"></span>
						<span class="text">
							<label class="" id="jform_spacer-lbl">Please submit a recipe to be considered.</label>
						</span>
						<span class="after"></span>
					</span>										
				</div>
				<div class="controls">
				</div>
			</div>
			<?php $i=0; 
				$requiredClass = "required";
				$requiredField = 'aria-required="true" required="required"';
				$requiredStar = '<span class="star">&nbsp;*</span>'; 
			?>
			<div class="recipe-demo" id="recipe_demo<?php echo $i;?>" style="float: left;margin-top: -10px;">
				<div style="clear:both;" class="control-group">
					<div class="control-label">
						<label title="Recipe::Enter your recipe name" class="hasTip <?php echo $requiredClass;?>" for="jform_recipe<?php echo $i;?>name" id="jform_recipe<?php echo $i;?>name-lbl">Recipe Name <?php echo $requiredStar;?></label>
					</div>
					<div class="controls">
						<input <?php echo $requiredField;?> type="text" size="30" class="validate-username <?php echo $requiredClass;?>" value="<?php echo isset($formData['recipe'][$i]['name'])?$formData['recipe'][$i]['name']:''?>" id="jform_recipe<?php echo $i;?>name" name="jform[recipe][<?php echo $i;?>][name]">
					</div>
				</div>
				<div style="" class="control-group">
					<div class="control-label">
						<label title="Recipe URL::Enter your recipe URL" class="hasTip <?php echo $requiredClass;?>" for="jform_recipe<?php echo $i;?>url" id="jform_recipe<?php echo $i;?>url-lbl">Recipe URL <?php echo $requiredStar;?></label>
					</div>
					<div class="controls">
						<input <?php echo $requiredField;?> type="text" size="30" class="validate-username <?php echo $requiredClass;?>" value="<?php echo isset($formData['recipe'][$i]['url'])?$formData['recipe'][$i]['url']:''?>" id="jform_recipe<?php echo $i;?>url" name="jform[recipe][<?php echo $i;?>][url]">					
					</div>
				</div>
				<div style="" class="control-group">
					<div class="control-label">
						<label title="Recipe Image URL::Enter your recipe image url" class="hasTip <?php echo $requiredClass;?>" for="jform_recipe<?php echo $i;?>imageurl" id="jform_recipe<?php echo $i;?>imageurl-lbl">Recipe Image URL <?php echo $requiredStar;?></label>
					</div>
					<div class="controls">
						<input <?php echo $requiredField;?> type="text" size="30" class="validate-username <?php echo $requiredClass;?>" value="<?php echo isset($formData['recipe'][$i]['imageurl'])?$formData['recipe'][$i]['imageurl']:''?>" id="jform_recipe<?php echo $i;?>imageurl" name="jform[recipe][<?php echo $i;?>][imageurl]">					
					</div>
				</div>
				<div style="clear: both; width: 55%; margin-top: -10px;" class="control-group">
					<div class="control-label">
						<label title="Recipe Ingredients::Enter your recipe ingredients" class="hasTip <?php echo $requiredClass;?>" for="jform_recipe<?php echo $i;?>ingredients" id="jform_recipe<?php echo $i;?>ingredients-lbl">Recipe Ingredients <?php echo $requiredStar;?></label>		
					</div>
					<div class="controls">
						<textarea <?php echo $requiredField;?> rows="3" cols="10" id="jform_recipe<?php echo $i;?>ingredients" name="jform[recipe][<?php echo $i;?>][ingredients]" class="validate-username <?php echo $requiredClass;?>"><?php echo isset($formData['recipe'][$i]['ingredients'])?$formData['recipe'][$i]['ingredients']:''?></textarea>		
					</div>
				</div>
			</div>
			<?php /*for($i=0;$i<3;$i++){ $requiredClass = '';$requiredField ='';$requiredStar = '';?>
			<?php if($i==0){$requiredClass = "required";$requiredField = 'aria-required="true" required="required"';$requiredStar = '<span class="star">&nbsp;*</span>'; }?>
			<div class="recipe-demo" id="recipe_demo<?php echo $i;?>" style="float: left;margin-top: -10px;">
				<div style="clear:both;" class="control-group">
					<div class="control-label">
						<label title="Recipe::Enter your recipe name" class="hasTip <?php echo $requiredClass;?>" for="jform_recipe<?php echo $i;?>name" id="jform_recipe<?php echo $i;?>name-lbl">Recipe <?php echo $i+1;?>  Name <?php echo $requiredStar;?></label>
					</div>
					<div class="controls">
						<input <?php echo $requiredField;?> type="text" size="30" class="validate-username <?php echo $requiredClass;?>" value="<?php echo isset($formData['recipe'][$i]['name'])?$formData['recipe'][$i]['name']:''?>" id="jform_recipe<?php echo $i;?>name" name="jform[recipe][<?php echo $i;?>][name]">
					</div>
				</div>
				<div style="" class="control-group">
					<div class="control-label">
						<label title="Recipe URL::Enter your recipe URL" class="hasTip <?php echo $requiredClass;?>" for="jform_recipe<?php echo $i;?>url" id="jform_recipe<?php echo $i;?>url-lbl">Recipe <?php echo $i+1;?> URL <?php echo $requiredStar;?></label>
					</div>
					<div class="controls">
						<input <?php echo $requiredField;?> type="text" size="30" class="validate-username <?php echo $requiredClass;?>" value="<?php echo isset($formData['recipe'][$i]['url'])?$formData['recipe'][$i]['url']:''?>" id="jform_recipe<?php echo $i;?>url" name="jform[recipe][<?php echo $i;?>][url]">					
					</div>
				</div>
				<div style="" class="control-group">
					<div class="control-label">
						<label title="Recipe Image URL::Enter your recipe image url" class="hasTip <?php echo $requiredClass;?>" for="jform_recipe<?php echo $i;?>imageurl" id="jform_recipe<?php echo $i;?>imageurl-lbl">Recipe <?php echo $i+1;?> Image URL <?php echo $requiredStar;?></label>
					</div>
					<div class="controls">
						<input <?php echo $requiredField;?> type="text" size="30" class="validate-username <?php echo $requiredClass;?>" value="<?php echo isset($formData['recipe'][$i]['imageurl'])?$formData['recipe'][$i]['imageurl']:''?>" id="jform_recipe<?php echo $i;?>imageurl" name="jform[recipe][<?php echo $i;?>][imageurl]">					
					</div>
				</div>
				<div style="clear: both; width: 55%; margin-top: -10px;" class="control-group">
					<div class="control-label">
						<label title="Recipe Ingredients::Enter your recipe ingredients" class="hasTip <?php echo $requiredClass;?>" for="jform_recipe<?php echo $i;?>ingredients" id="jform_recipe<?php echo $i;?>ingredients-lbl">Recipe <?php echo $i+1;?> Ingredients <?php echo $requiredStar;?></label>		
					</div>
					<div class="controls">
						<textarea <?php echo $requiredField;?> rows="3" cols="10" id="jform_recipe<?php echo $i;?>ingredients" name="jform[recipe][<?php echo $i;?>][ingredients]" class="validate-username <?php echo $requiredClass;?>"><?php echo isset($formData['recipe'][$i]['ingredients'])?$formData['recipe'][$i]['ingredients']:''?></textarea>		
					</div>
				</div>
			</div>
			<?php if($i<2){?>
			<div style="clear: both; width: 81%; margin-bottom: 10px; height: 1px; border-top: 1px solid #d0d0d0;"></div>
			<?php }?>
			<?php }*/?>
			<div class="newsletters" style="clear: both; margin-top: 20px; float: left;">
			<input type="checkbox" name="recive_agreement" required="required" value="" checked /><span style="color: #B94A48;">* </span>Yes, I agree to the <a href="/SavvyFork_Partner_Agreement.pdf" target="_blank">SavvyFork Partner Agreement</a> and <a href="/terms-of-use" target="_blank">Terms of Use</a>
			</div>
			<div class="form-actions">
			<button type="submit" class="btn btn-primary validate" style="font-size:16px;">Submit<?php //echo JText::_('JREGISTER');?></button>
			<a class="btn" style="display:none;" href="<?php echo JRoute::_('');?>" title="<?php echo JText::_('JCANCEL');?>"><?php echo JText::_('JCANCEL');?></a>
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="partnerregistration.register" />
			<?php echo JHtml::_('form.token');?>
			</div>
		</div>
		</fieldset>
	<?php endif; break;?>
<?php endforeach;?>
		
	</form>
</div>
