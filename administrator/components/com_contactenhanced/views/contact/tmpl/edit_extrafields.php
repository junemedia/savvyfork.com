<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.Development
 * @author     	Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

?>
<div class="tab-pane" id="extrafields">
	<p><?php echo JText::_('CE_CONTACT_EF_DESC');?></p>
		<?php foreach ($this->form->getFieldset('extrafields') as $field) : ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo $field->label; ?>
			</div>
			<div class="controls">
				<?php echo $field->input; ?>
			</div>
		</div>
		<?php endforeach; ?>

</div>