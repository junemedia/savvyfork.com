<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.
 * @author     Douglas Machado {@link http://idealextensions.com}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$canDo	= CEHelper::getActions();
?>



	
		<div class="formelm-buttons">
			<button type="button" onclick="Joomla.submitbutton('edit.save')">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="Joomla.submitbutton('edit.cancel')">
				<?php echo JText::_('JCANCEL') ?>
			</button>
		</div>
		<?php
		if ($this->params->get('presentation_style_edit','plain')!='plain'){
			echo  JHtml::_($this->params->get('presentation_style_edit','plain').'.start', 'contact-slider');
			echo JHtml::_('sliders.panel',JText::_('COM_CONTACTENHANCED_CONTACT_PUBLISHING_INFO'), 'publishing-info'); 
		}else{
			echo '<h3>'.JText::_('COM_CONTACTENHANCED_CONTACT_PUBLISHING_INFO').'</h3>';
		}
		
		?>
		
		<fieldset class="adminform">
			<ul class="adminformlist">
		<?php if ($this->params->get('show_name_edit',1)){
			$style	= '';
		}else{
			$style	= 'style="display:none"';
		}?>
			<li <?php echo $style; ?>><?php echo $this->form->getLabel('name'); ?>
			<?php echo $this->form->getInput('name'); ?></li>
		
		<?php if ($canDo->get('core.edit.state')):?>
			<li><?php echo $this->form->getLabel('access'); ?>
			<?php echo $this->form->getInput('access'); ?></li>
		
			<li><?php echo $this->form->getLabel('published'); ?>
			<?php echo $this->form->getInput('published'); ?></li>
		
		<?php elseif ($this->isNew): ?>	
			<input type="hidden" name="jform[published]" value="-3" />
		<?php endif; ?>
		
		<?php if ($canDo->get('core.admin')):?>
			<!-- li><?php // echo $this->form->getLabel('user_id'); ?>
			<?php // echo $this->form->getInput('user_id'); ?></li -->
			
			<li><?php echo $this->form->getLabel('language'); ?>
			<?php echo $this->form->getInput('language'); ?></li>
			
			<!-- li><?php echo $this->form->getLabel('ordering'); ?>
			<?php echo $this->form->getInput('ordering'); ?></li  -->
			
			<li><?php echo $this->form->getLabel('featured'); ?>
			<?php echo $this->form->getInput('featured'); ?></li>
			
			<li><?php echo $this->form->getLabel('id'); ?>
			<?php echo $this->form->getInput('id'); ?></li>
		<?php else: ?>	
			
		<?php endif; ?>
		
		<?php if ($this->params->get('show_category_edit',1) OR (JRequest::getVar('id',0) == 0 AND $this->params->get('category') == 0)):?>
			<li><?php echo $this->form->getLabel('catid'); ?>
			<?php echo $this->form->getInput('catid'); ?></li>
		<?php else: ?>	 
			<input type="hidden" name="jform[catid]" value="<?php 
				echo (isset($this->contact->catid) ? $this->contact->catid : JRequest::getVar('catid', $this->params->get('category')));?>"
				/>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_image_edit',1)):?>
			<li><?php echo $this->form->getLabel('image'); ?>
			<?php 
				if ($this->contact->image) {
					echo JHtml::_('image', $this->contact->image, '', array('id' => 'ce-contact-image') ).' <br />';
				}
			?>
			<?php /* Let's record old image in order to save a SQL query */ ?>
			<input type="hidden" name="old_image" value="<?php echo $this->contact->image; ?>" />
			<?php echo '<input type="file" name="image" id="upload-file">'; ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_position_edit',1)):?>
			<li><?php echo $this->form->getLabel('con_position'); ?>
			<?php echo $this->form->getInput('con_position'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_email_edit',0)):?>
			<li><?php echo $this->form->getLabel('email_to'); ?>
			<?php echo $this->form->getInput('email_to'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_street_address_edit',1)):?>
			<li><?php echo $this->form->getLabel('address'); ?>
			<?php echo $this->form->getInput('address'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_suburb_edit',1)):?>
			<li><?php echo $this->form->getLabel('suburb'); ?>
			<?php echo $this->form->getInput('suburb'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_state_edit',1)):?>
			<li><?php echo $this->form->getLabel('state'); ?>
			<?php echo $this->form->getInput('state'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_postcode_edit',1)):?>
			<li><?php echo $this->form->getLabel('postcode'); ?>
			<?php echo $this->form->getInput('postcode'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_country_edit',1)):?>
			<li><?php echo $this->form->getLabel('country'); ?>
			<?php echo $this->form->getInput('country'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_telephone_edit',1)):?>
			<li><?php echo $this->form->getLabel('telephone'); ?>
			<?php echo $this->form->getInput('telephone'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_mobile_edit',1)):?>
			<li><?php echo $this->form->getLabel('mobile'); ?>
			<?php echo $this->form->getInput('mobile'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_fax_edit',1)):?>
			<li><?php echo $this->form->getLabel('fax'); ?>
			<?php echo $this->form->getInput('fax'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_skype_edit',1)):?>
			<li><?php echo $this->form->getLabel('skype'); ?>
			<?php echo $this->form->getInput('skype'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_twitter_edit',1)):?>
			<li><?php echo $this->form->getLabel('twitter'); ?>
			<?php echo $this->form->getInput('twitter'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_facebook_edit',1)):?>
			<li><?php echo $this->form->getLabel('facebook'); ?>
			<?php echo $this->form->getInput('facebook'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_linkedin_edit',1)):?>
			<li><?php echo $this->form->getLabel('linkedin'); ?>
			<?php echo $this->form->getInput('linkedin'); ?></li>
		<?php endif; ?>
		
		<?php if ($this->params->get('show_webpage_edit',1)):?>
			<li><?php echo $this->form->getLabel('webpage'); ?>
			<?php echo $this->form->getInput('webpage'); ?></li>
		<?php endif; ?>
		
		
		<?php /* Add all Extra fields to frontend edit view */ ?>
		<?php if ($this->params->get('show_extrafield_1_edit',0)):?>
			<li><?php echo $this->form->getLabel('extra_field_1'); ?>
			<br style="clear:both" />
			<?php echo $this->form->getInput('extra_field_1'); ?></li>
		<?php endif; ?>

		<?php if ($this->params->get('show_extrafield_2_edit',0)):?>
			<li><?php echo $this->form->getLabel('extra_field_2'); ?>
			<br style="clear:both" />
			<?php echo $this->form->getInput('extra_field_2'); ?></li>
		<?php endif; ?>

		<?php if ($this->params->get('show_extrafield_3_edit',0)):?>
			<li><?php echo $this->form->getLabel('extra_field_3'); ?>
			<br style="clear:both" />
			<?php echo $this->form->getInput('extra_field_3'); ?></li>
		<?php endif; ?>

		<?php if ($this->params->get('show_extrafield_4_edit',0)):?>
			<li><?php echo $this->form->getLabel('extra_field_4'); ?>
			<br style="clear:both" />
			<?php echo $this->form->getInput('extra_field_4'); ?></li>
		<?php endif; ?>

		<?php if ($this->params->get('show_extrafield_5_edit',0)):?>
			<li><?php echo $this->form->getLabel('extra_field_5'); ?>
			<?php echo $this->form->getInput('extra_field_5'); ?></li>
		<?php endif; ?>

		<?php if ($this->params->get('show_extrafield_6_edit',0)):?>
			<li><?php echo $this->form->getLabel('extra_field_6'); ?>
			<br style="clear:both" />
			<?php echo $this->form->getInput('extra_field_6'); ?></li>
		<?php endif; ?>

		<?php if ($this->params->get('show_extrafield_7_edit',0)):?>
			<li><?php echo $this->form->getLabel('extra_field_7'); ?>
			<br style="clear:both" />
			<?php echo $this->form->getInput('extra_field_7'); ?></li>
		<?php endif; ?>

		<?php if ($this->params->get('show_extrafield_8_edit',0)):?>
			<li><?php echo $this->form->getLabel('extra_field_8'); ?>
			<br style="clear:both" />
			<?php echo $this->form->getInput('extra_field_8'); ?></li>
		<?php endif; ?>

		<?php if ($this->params->get('show_extrafield_9_edit',0)):?>
			<li><?php echo $this->form->getLabel('extra_field_9'); ?>
			<br style="clear:both" />
			<?php echo $this->form->getInput('extra_field_9'); ?></li>
		<?php endif; ?>

		<?php if ($this->params->get('show_extrafield_10_edit',0)):?>
			<li><?php echo $this->form->getLabel('extra_field_10'); ?>
			<br style="clear:both" />
			<?php echo $this->form->getInput('extra_field_10'); ?></li>
		<?php endif; ?>
		
		
			</ul>
			
		<?php if ($this->params->get('show_misc_edit',0)):?>
			<?php
				if ($this->params->get('presentation_style_edit','plain')!='plain'){
					echo JHtml::_('sliders.panel',JText::_('COM_CONTACTENHANCED_CONTACT_MISC_INFO'), 'misc-info'); 
				}else{
					echo '<h3 style="clear:both;margin-top:60px">'.JText::_('COM_CONTACTENHANCED_CONTACT_MISC_INFO').'</h3>';
				}
			?>
			<div class="clr" > </div>
			<?php echo $this->form->getInput('misc'); ?>
		
		<?php endif; ?>
		
		<?php if ($this->params->get('show_sidebar_edit',0)):?>
			<?php
				if ($this->params->get('presentation_style_edit','plain')!='plain'){
					echo JHtml::_('sliders.panel',JText::_('COM_CONTACTENHANCED_CONTACT_SIDEBAR'), 'sidebar-slider'); 
				}else{
					echo '<h3 style="clear:both;margin-top:60px">'.JText::_('COM_CONTACTENHANCED_CONTACT_SIDEBAR').'</h3>';
				}
			?>

			<div class="clr"> </div>
			<?php echo $this->form->getInput('sidebar'); ?>
		<?php endif; ?>
		

			<?php //echo $this->loadTemplate('extrafields'); ?>
			
		</fieldset>
		

	
	<?php if (!$canDo->get('core.admin')):?>
		<input type="hidden" name="jform[id]" value="<?php echo $this->contact->id ? $this->contact->id : 0; ?>" />
	<?php endif; ?>
	<input type="hidden" name="jform[user_id]" value="<?php echo $this->contact->user_id ? $this->contact->user_id : $this->user->id; ?>" />

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
	
	<?php echo JHtml::_('form.token'); ?>
