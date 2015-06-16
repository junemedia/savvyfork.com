<?php
/**
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

$jversion = new JVersion();
if( version_compare( $jversion->getShortVersion(), '2.5', 'gt' ) ):
$published = $this->state->get('filter.published');
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_CONTACTENHANCED_BATCH_OPTIONS');?></legend>
	<p><?php echo JText::_('COM_CONTACTENHANCED_BATCH_TIP'); ?></p>
	<?php echo JHtml::_('batch.access');?>
	<?php echo JHtml::_('batch.language'); ?>
	<?php echo JHtml::_('batch.user'); ?>

	<?php if ($published >= 0) : ?>
		<?php echo JHtml::_('batch.item', 'com_contactenhanced');?>
	<?php endif; ?>

	<button type="submit" onclick="Joomla.submitbutton('contact.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-category-id').value='';document.id('batch-access').value='';document.id('batch-language-id').value='';document.id('batch-user-id').value=''">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>
<?php 
endif;