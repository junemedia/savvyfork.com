<?php
/**
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
		if (task == 'customvalue.cancel' || document.formvalidator.isValid(document.id('contact-form'))) {
			Joomla.submitform(task, document.getElementById('contact-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
// -->
</script>

<form action="<?php echo JRoute::_('index.php?option=com_contactenhanced&view=customvalue&layout=edit&id='.(int) $this->item->id); ?>" 
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
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="details">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('value'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('value'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('type'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('type'); ?></div>
				</div>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('description'); ?></div>
				</div>
			</div>
			<div class="tab-pane " id="publishing">
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
					
			</div>
			</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>