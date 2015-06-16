<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.Development
 * @author     	Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$fields = $this->form->getFieldset('item_associations');
?>

<fieldset>
	<?php foreach ($fields as $field) : ?>
		<div class="control-group">
			<div class="control-label">
				<?php echo $field->label ?>
			</div>
			<div class="controls">
				<?php echo $field->input; ?>
			</div>
		</div>
	<?php endforeach; ?>
</fieldset>
