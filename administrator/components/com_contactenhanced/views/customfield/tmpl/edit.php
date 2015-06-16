<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');

$app	= JFactory::getApplication();
$input	= $app->input;

$document = JFactory::getDocument();
?>
<script type="text/javascript">
<!--
Joomla.submitbutton = function(task)
	{
		if (task == 'customfield.cancel' || document.formvalidator.isValid(document.id('contact-form'))) {
			<?php 
			/*
				if($this->code_editor AND 
					($this->item->type == 'css' OR $this->item->type == 'js' OR $this->item->type == 'php' OR $this->item->type == 'wysiwyg')
				){
					echo $this->form->getField('value')->save(); 
				}
				if ($this->code_editor) {
					echo $this->form->getField('attributes')->save();
				}
			**/
				?>
			Joomla.submitform(task, document.getElementById('contact-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}

function updateInputs(ftype) {
	var f = document.adminForm;
	if (ftype=='selectlist' || ftype=='recipient'		|| ftype=='selectmultiple' || ftype=='checkbox' 
			|| ftype=='radiobutton' || ftype=='number'	|| ftype=='numberrange'		
			|| ftype=='freetext' 	|| ftype=='hidden'	|| ftype=='subject' || ftype=='sql' 
			|| ftype=='multitext'	|| ftype=='text'	|| ftype=='email'	|| ftype=='email_verify'
			|| ftype=='surname'		|| ftype=='password' || ftype=='username' 
			|| ftype=='css'			|| ftype=='php'		|| ftype=='js'		|| ftype=='autocomplete'			   	
		) {
		f.getElementById('jform_value_container').setStyle('display','block');
	} else {
		f.getElementById('jform_value_container').setStyle('display','none');
	}

	if (	ftype=='subject'	|| ftype=='name'	 || ftype=='email'		|| ftype=='email_verify'
		||	ftype=='surname'	|| ftype=='password' || ftype=='username'	|| ftype=='password_verify'	
		||	ftype=='recipient'		   	
	) {
		f.getElementById('jform_alias').disabled=true;
		f.getElementById('jform_alias').style.backgroundColor='#F5F5F5'; 
	} else {
		f.getElementById('jform_alias').style.backgroundColor='#FFF'; 
		f.getElementById('jform_alias').disabled=false;
	}
}
window.addEvent('domready', function(){
	updateInputs('<?php echo $this->item->type; ?>');
});
// -->
</script>


<form action="<?php echo JRoute::_('index.php?option=com_contactenhanced&view=customfield&layout=edit&id='.(int) $this->item->id); ?>" 
		method="post" 
		name="adminForm" 
		id="contact-form" 
		class="form-validate form-horizontal">
		<fieldset>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#details" data-toggle="tab"><?php 
				echo JText::_('CE_CF_DETAILS'); ?></a></li>
				
<?php if ($this->item->id): ?>
			<li><a href="#tooltip" data-toggle="tab"><?php 
				echo JText::_('CE_CF_TOOLTIP');?></a></li>
			<li><a href="#attributes" data-toggle="tab"><?php 
				echo JText::_('CE_CF_ATTRIBUTES');?></a></li>
<?php endif; ?>
			<li><a href="#publishing" data-toggle="tab"><?php 
				echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></a></li>
<?php if ($this->item->id): ?>		
			<?php
			$fieldSets = $this->form->getFieldsets('params');
			foreach ($fieldSets as $name => $fieldSet) :
				if ($name == 'general' OR $name == 'advanced' OR $name == $this->item->type ): ?>
				<li><a href="#params-<?php echo $name;?>" data-toggle="tab"><?php echo JText::_($fieldSet->label);?></a></li>
			<?php endif; 
			endforeach; ?>
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
					<div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('type'); ?></div>
				</div>
		
				<?php if($this->item->id): ?>
				<div class="control-group" id="jform_value_container">
					<label  class="hasTip" for="jform_value" id="jform_value-lbl" 
							title="<?php echo JText::_('CE_CF_VALUE_DESC');?>">
					<?php echo JText::_('CE_CF_VALUE_LABEL'); ?></label>
					
					<?php
					
						if($this->item->type == 'freetext'){
							$editor = JFactory::getEditor();
							echo '<div style="clear:both">'
								. $editor->display( 'jform[value]',  $this->item->value , '80%', '200', '75', '20' ).'</div>' ;
						}elseif($this->code_editor){
							$editor = JFactory::getEditor($this->code_editor);
							echo '<div style="clear:both">'
								. $editor->display( 'jform[value]',  $this->item->value , '80%', '200', '75', '20',false ).'</div>' ;
						
						}else{
							echo '<textarea class="inputbox" name="jform[value]" id="jform_value" style="width:70%;height:150px">'.$this->item->value.'</textarea>';
						}
					?>
				</div>
				<div style="clear:both">
				<?php echo JText::_('COM_CONTACTENHANCED_CF_TIP_'.strtoupper($this->item->type)); ?>
				</div>
				<?php 
				else:
						echo '<br /><h2>'.JText::_('COM_CONTACTENHANCED_CF_PLEASE_SAVE_BEFORE_CONTINUING').'</h2>';
				endif;
				?>
			</div>
<?php if ($this->item->id): ?>		
			<div class="tab-pane " id="tooltip">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('tooltip'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('tooltip'); ?></div>
				</div>
			</div>
			
			
			<div class="tab-pane " id="attributes">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('attributes'); ?></div>
					<div class="controls"><?php 
						echo $this->form->getInput('attributes');
					?></div>
				</div>
				<br style="clear: both" />
				
				<?php $params = array();
		$params['title']  = JText::_('COM_CONTACTENHANED_CF_HTML_ATTRIBUTES');
		$params['url']    = 'http://idealextensions.com/index.php?option=com_moofaq&view=article&id=102:html-field-attributes&catid=20:customizing-your-forms&tmpl=component';
		$params['height'] = 480;
		$params['width']  = 600;
		$params['remote']  = true;
		$selector				= 'modal-attributes';
		echo JHtml::_('bootstrap.renderModal', $selector, $params);
		
			echo JHtml::_('link'
					, '#'.$selector
					, $params['title']
					, array(
							'data-toggle'	=> 'modal'
					)
			);
			
			?>
				
				<br style="clear: both" />
			</div>
<?php endif; ?>
			<div class="tab-pane " id="publishing">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('required'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('required'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('ordering'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('ordering'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('language'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('language'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('published'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
				</div>
			</div>
					

			<?php echo $this->loadTemplate('params'); ?>

			</div>
			</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
