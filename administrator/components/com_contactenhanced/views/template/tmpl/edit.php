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
		if (task == 'template.cancel' || document.formvalidator.isValid(document.id('contact-form'))) {
			Joomla.submitform(task, document.getElementById('contact-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
// -->
</script>

<form action="<?php echo JRoute::_('index.php?option=com_contactenhanced&view=template&layout=edit&id='.(int) $this->item->id); ?>" 
		method="post" 
		name="adminForm" 
		id="contact-form" 
		class="form-validate form-horizontal">
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#details" data-toggle="tab"><?php 
				echo JText::_('COM_CONTACTENHANCED_FIELDSET_DETAILS'); ?></a></li>
			<li><a href="#publishing" data-toggle="tab"><?php 
				echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></a></li>
			<?php
			$fieldSets = $this->form->getFieldsets('params');
				foreach ($fieldSets as $name => $fieldSet) :
				?>
					<li><a href="#params-<?php echo $name;?>" 
							data-toggle="tab"><?php echo JText::_($fieldSet->label);?></a></li>
				<?php endforeach; ?>
			<li><a href="#syntax" data-toggle="tab"><?php 
				echo JText::_('CE_TPL_AVAILABLE_SYNTAXES');?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('type'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('html'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('html'); ?></div>
				</div>
			</div>
			<div class="tab-pane " id="publishing">
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
			<?php echo $this->loadTemplate('syntax'); ?>
			</div>
			</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>