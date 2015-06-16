<?php

/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

/* marker_class: Class based on the selection of text, none, or icons
 * jicon-text, jicon-none, jicon-icon
 */
?>
<?php if (	($this->params->get('show_extrafield_1') AND $this->contact->extra_field_1)
			|| ($this->params->get('show_extrafield_2') AND $this->contact->extra_field_2)
			|| ($this->params->get('show_extrafield_3') AND $this->contact->extra_field_3)
			|| ($this->params->get('show_extrafield_4') AND $this->contact->extra_field_4)
			|| ($this->params->get('show_extrafield_5') AND $this->contact->extra_field_5)
			|| ($this->params->get('show_extrafield_6') AND $this->contact->extra_field_6)
			|| ($this->params->get('show_extrafield_7') AND $this->contact->extra_field_7)
			|| ($this->params->get('show_extrafield_8') AND $this->contact->extra_field_8)
			|| ($this->params->get('show_extrafield_9') AND $this->contact->extra_field_9)
			|| ($this->params->get('show_extrafield_10') AND $this->contact->extra_field_10)
			) : ?>

	<div class="contact-extrafields" id="contact-extrafields">
	<?php 
	for($i=1;$i<=10;$i++) {
		$extra_field	= 'extra_field_'.$i;
		if ($this->params->get('show_extrafield_'.$i) AND $this->contact->$extra_field) :
		?>
			<div>
		<?php 
			if ($this->params->get('show_extrafield_'.$i) == '1') : ?>
			<span class="<?php echo $this->params->get('marker_class'); ?>" >
					<?php echo $this->params->get('marker_extra_field_'.$i); ?>
				</span>
			<?php endif; ?>
				<span class="contact-extrafield-<?php echo $i; ?>" >
					<?php echo $this->contact->$extra_field; ?>
				</span>
			</div>
	<?php endif;
	}
	?>
	
	</div>
<?php endif; ?>
