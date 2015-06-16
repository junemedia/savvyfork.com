<?php
 /**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$document = JFactory::getDocument();
$document->addStylesheet(JURI::base().'components/com_contactenhanced/assets/css/ce-edit.css');
//echo ceHelper::print_r($this->params); exit;

$cparams = JComponentHelper::getParams ('com_media');

$containerClass	= '';
if(JRequest::getVar('tmpl') == 'component'){
	$containerClass	= 'ce-contact-modal';
	$this->document->setMetaData('robots', 'noindex, nofollow');
}
?>
<div id="ce-edit-contact" class="ce-container contact<?php echo $this->params->get('pageclass_sfx'). ' '.$containerClass; ?>">
<?php if ($this->params->get('show_page_heading', 0)) : ?>
<?php // class="title" was added in order to try add compaibility with Gantry based templates?>
<h1 class="title ">
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

	<form	action="<?php echo JRoute::_('index.php?option=com_contactenhanced&c_id='.(int) $this->item->id); //&catid='.JRequest::getVar('catid')) ?>" 
			method="post"
			name="adminForm"
			id="adminForm"
			enctype="multipart/form-data"
			class="form-validate">	
	<?php 
		/**
		 * FORM
		 */
	?>
		<?php  echo $this->loadTemplate('form');  ?>
	
	<?php 
		/**
		 * MAP: After Form
		 */
	?>
	<?php if ($this->params->get('show_map_edit',0) ) : ?>
		<?php if ($this->params->get('presentation_style_edit','plain')!='plain'):?>
			<?php  echo JHtml::_($this->params->get('presentation_style_edit','plain').'.panel', JText::_('CE_MAP'), 'form-map');  ?>
		<?php else: ?>
			<h3><?php echo JText::_('CE_MAP'); ?></h3>
		<?php endif; ?>
		<?php  echo $this->loadTemplate('map');  ?>
	<?php endif; ?>
	
	<?php
	if ($this->params->get('presentation_style_edit','plain')!='plain'){ 
		echo JHtml::_($this->params->get('presentation_style_edit','plain').'.end');
	} ?>
		<div class="formelm-buttons">
			<button type="button" onclick="Joomla.submitbutton('edit.save')">
				<?php echo JText::_('JSAVE') ?>
			</button>
			<button type="button" onclick="Joomla.submitbutton('edit.cancel')">
				<?php echo JText::_('JCANCEL') ?>
			</button>
		</div>
	</form>
</div>
