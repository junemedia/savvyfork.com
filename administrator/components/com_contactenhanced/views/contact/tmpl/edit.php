<?php
/**
 * @package     com_contactenhanced
 * @copyright   Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$input = $app->input;

$assoc = isset($app->item_associations) ? $app->item_associations : 0;

$jversion = new JVersion();

?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'contact.cancel' || document.formvalidator.isValid(document.id('contact-form'))) {
			<?php echo $this->form->getField('misc')->save(); ?>
			<?php echo $this->form->getField('sidebar')->save(); ?>
			<?php echo $this->form->getField('extra_field_1')->save(); ?>
			<?php echo $this->form->getField('extra_field_2')->save(); ?>
			<?php echo $this->form->getField('extra_field_3')->save(); ?>
			<?php echo $this->form->getField('extra_field_4')->save(); ?>
			<?php // echo $this->form->getField('extra_field_5')->save(); ?>
			Joomla.submitform(task, document.getElementById('contact-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_contactenhanced&layout=edit&id='.(int) $this->item->id); ?>" 
		method="post" 
		name="adminForm" 
		id="contact-form" 
		class="form-validate form-horizontal">
		<fieldset>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#details" data-toggle="tab"><?php echo empty($this->item->id) ? JText::_('COM_CONTACTENHANCED_NEW_CONTACT') : JText::sprintf('COM_CONTACTENHANCED_EDIT_CONTACT', $this->item->id); ?></a></li>
			<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></a></li>
			<li><a href="#basic" data-toggle="tab"><?php echo JText::_('COM_CONTACTENHANCED_CONTACT_DETAILS');?></a></li>
			<li><a href="#extrafields" data-toggle="tab"><?php echo JText::_('CE_CONTACT_EF');?></a></li>
			<li><a href="#maps" data-toggle="tab"><?php echo JText::_('CE_CONFIG_MAP');?></a></li>
			<?php
			$fieldSets = $this->form->getFieldsets('params');
				foreach ($fieldSets as $name => $fieldSet) :
				?>
					<li><a href="#params-<?php echo $name;?>" data-toggle="tab"><?php echo JText::_($fieldSet->label);?></a></li>
				<?php endforeach; ?>
			<?php
			$fieldSets = $this->form->getFieldsets('metadata');
			foreach ($fieldSets as $name => $fieldSet) :
			?>
			<li><a href="#metadata-<?php echo $name;?>" data-toggle="tab"><?php echo JText::_($fieldSet->label);?></a></li>
			<?php endforeach; ?>
			<?php if ($assoc AND version_compare( $jversion->getShortVersion(), '3.1' ) >= 0) : ?>
				<li><a href="#assoc" data-toggle="tab"><?php echo JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true); ?></a></li>
			<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('user_id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('user_id'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('ordering'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('featured'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('featured'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('language'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('language'); ?></div>
				</div>
			<?php if(FALSE AND version_compare( $jversion->getShortVersion(), '3.1' ) >= 0 ): ?>
				<div class="control-group">
					<div class="control-label">
						<?php echo $this->form->getLabel('tags'); ?>
					</div>
					<div class="controls">
						<?php echo $this->form->getInput('tags'); ?>
					</div>
				</div>
			<?php endif; ?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('misc'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('misc'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('sidebar'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('sidebar'); ?></div>
				</div>
			</div>
			<div class="tab-pane" id="publishing">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('created_by_alias'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('created_by_alias'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('created'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('created'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('publish_up'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('publish_up'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('publish_down'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('publish_down'); ?></div>
				</div>
					<?php if ($this->item->modified_by) : ?>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('modified_by'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('modified_by'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('modified'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('modified'); ?></div>
						</div>
					<?php endif; ?>
			</div>
			<div class="tab-pane" id="basic">
				<p><?php echo empty($this->item->id) ? JText::_('COM_CONTACTENHANCED_DETAILS') : JText::sprintf('COM_CONTACTENHANCED_EDIT_DETAILS', $this->item->id); ?></p>

				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('image'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('con_position'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('con_position'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('email_to'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('email_to'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('address'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('address'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('suburb'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('suburb'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('postcode'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('postcode'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('country'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('country'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('telephone'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('telephone'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('mobile'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('mobile'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('fax'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('fax'); ?></div>
				</div>
				
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('skype'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('skype'); ?></div>
				</div>
				
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('twitter'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('twitter'); ?></div>
				</div>
				
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('facebook'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('facebook'); ?></div>
				</div>
				
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('linkedin'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('linkedin'); ?></div>
				</div>
			
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('webpage'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('webpage'); ?></div>
				</div>
					<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('birthdate'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('birthdate'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('sortname1'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('sortname1'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('sortname2'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('sortname2'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('sortname3'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('sortname3'); ?></div>
				</div>
			</div>
			<?php echo $this->loadTemplate('map'); ?>
			<?php echo $this->loadTemplate('extrafields'); ?>
			<?php echo $this->loadTemplate('params'); ?>
			<?php if ($assoc AND version_compare( $jversion->getShortVersion(), '3.1' ) >= 0) : ?>
				<?php // echo JHtml::_('bootstrap.addTab', 'myTab', 'associations', JText::_('JGLOBAL_FIELDSET_ASSOCIATIONS', true)); ?>
					<div class="tab-pane" id="assoc">
					<?php echo $this->loadTemplate('associations'); ?>
					</div>
				<?php // echo JHtml::_('bootstrap.endTab'); ?>
			<?php endif; ?>

			<?php echo $this->loadTemplate('metadata'); ?>

			</div>
			</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
