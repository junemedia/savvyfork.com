<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;


JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id')
?>


<form action="<?php echo JRoute::_('index.php?option=com_contactenhanced'); ?>" 
		method="post" 
		name="adminForm" 
		id="adminForm" 
		enctype="multipart/form-data" 
		class="form-validate form-horizontal">
<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
			
			<div class="clearfix"> </div>
	<fieldset>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#import" data-toggle="tab"><?php 
				echo JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_LABEL'); ?></a></li>
			
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="import">

			<fieldset class="uploadform">
				<legend><?php echo JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_UPLOAD_CSV_FILE_LABEL'); ?></legend>
				<div><?php echo JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_UPLOAD_CSV_FILE_DESC'); ?></div>
				<label for="catid"><?php echo JText::_('COM_CONTACTENHANCED_TOOLS_DEFAULT_CATEGORY_LABEL'); ?></label>
				<select name="catid" id="catid" class="inputbox" >
					<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_contactenhanced'), 'value', 'text', JRequest::getVar('catid'));?>
				</select>
				<label for="csv_file"><?php echo JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_CSV_FILE_LABEL'); ?></label>
				<input type="file" size="57" name="csv_file" id="csv_file" class="">
				<input type="button" onclick="Joomla.submitbutton('tools.importcsv')" 
						value="<?php echo JText::_('JTOOLBAR_UPLOAD'); ?>" 
						class="button" />
			</fieldset>
			<div class="clr"><br /><br /><br /></div>
			<fieldset class="uploadform">
				<legend><?php echo JText::_('COM_CONTACTENHANCED_INSTRUCTIONS_IMPORT_CONTACTS_AND_CATEGORIES'); ?></legend>
			
			<?php echo '<p>'.JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_WARNING').'</p>'; ?>
				<!-- label for=""><?php echo JText::_('COM_CONTACTENHANCED_TOOLS_IMPORT_LABEL'); ?></label  -->
					<button class="button" 
						onclick="Joomla.submitbutton('import')">
						<?php echo JText::_('COM_CONTACTENHANCED_INSTRUCTIONS_IMPORT_CONTACTS_AND_CATEGORIES');?>
					</button>
			</fieldset>
	</div>
	<div class="clr"></div>
	</div>
			</fieldset>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>