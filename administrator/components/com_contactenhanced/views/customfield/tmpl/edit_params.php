<?php
/**
 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.Development
 * @author     	Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

$fieldSets = $this->form->getFieldsets('params');
foreach ($fieldSets as $name => $fieldSet) :
	if ($name == 'general' OR $name == 'advanced' OR $name == $this->item->type ):
		?>
		<div class="tab-pane" id="params-<?php echo $name;?>">
		<?php
		if (isset($fieldSet->description) && trim($fieldSet->description)) :
			echo '<p class="alert alert-info">'.$this->escape(JText::_($fieldSet->description)).'</p>';
		endif;
		?>
				<?php foreach ($this->form->getFieldset($name) as $field) : ?>
					<div class="control-group">
						<div class="control-label"><?php echo $field->label; ?></div>
						<div class="controls"><?php echo $field->input; ?></div>
					</div>
				<?php endforeach; ?>
		</div>
<?php 
	endif;
endforeach; ?>