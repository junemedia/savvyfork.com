<?php
/**
 * @version		1.6.3
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

	JHTML::_('behavior.framework');
	JHTML::_('behavior.tooltip');


$pageClass = $this->params->get('pageclass_sfx');
?>
<div class="ce-search<?php echo $pageClass;?>">
<?php echo ceHelper::loadModulePosition('ce-before-title');  ?>
<h1>
	<?php
	echo $this->escape(
					$this->params->get('search_heading',
						$this->params->get('page_heading',JText::_('COM_CONTACTENHANCED_SEARCH_HEADING') )
					)
			);
	?>
</h1>
<?php echo ceHelper::loadModulePosition('ce-after-title');  ?>
<?php if($this->params->get('show_category_title', 1)) : ?>

<?php endif; ?>

<?php if (empty($this->items)) : ?>
	<p> <?php echo JText::_('COM_CONTACTENHANCED_SEARCH_RESULT_EMPTY'); ?>	 </p>
<?php else : ?>



	<?php
	foreach ($this->categories as $this->category ){
		echo $this->loadTemplate('category');
	}
	?>
<?php endif; ?>
</div>