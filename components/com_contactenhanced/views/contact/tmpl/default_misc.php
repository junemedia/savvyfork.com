<?php
 /**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die; ?>

<?php if ($this->contact->misc ) : ?>
	<?php if ($this->params->get('presentation_style','plain')!='plain'){?>
		<?php echo JHtml::_($this->params->get('presentation_style','plain').'.panel', JText::_('COM_CONTACTENHANCED_OTHER_INFORMATION'), 'display-misc');
	} ?>
			<div class="contact-miscinfo" id="contact-miscinfo">
				<?php if ($this->params->get('presentation_style','plain')=='plain'
							AND $this->params->get('show_misc_label',1)):?>
					<?php echo '<h3>'. JText::_('COM_CONTACTENHANCED_OTHER_INFORMATION').'</h3>'; ?>
				<?php endif; ?>
				<div class="<?php echo $this->params->get('marker_class'); ?>">
					<?php echo $this->params->get('marker_misc'); ?>
				</div>
				<div class="contact-misc">
					<?php echo $this->contact->misc; ?>
				</div>
			</div>
<?php endif; ?>
	