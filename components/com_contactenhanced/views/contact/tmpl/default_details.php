<?php
 /**
 * $Id: default.php 19798 2010-12-08 03:39:51Z dextercowley $
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
		if ($this->params->get('presentation_style','plain')!='plain' AND $this->params->get('show_contact_details') != 'sidebar'){
				
				echo JHtml::_($this->params->get('presentation_style','plain').'.panel',JText::_('COM_CONTACTENHANCED_DETAILS'), 'basic-details'); 
		}elseif ($this->params->get('presentation_style','plain')=='plain'){
			//echo '<h3>'. JText::_('COM_CONTACTENHANCED_DETAILS').'</h3>';  
		}?>
<a name="details"></a>
	<?php if ($this->contact->image && $this->params->get('show_image',1)) : ?>
		<div class="contact-image">
			<?php 
				
				$width		= $this->params->get('show_image-resize-width',200);
				$height		= $this->params->get('show_image-resize-height',200);
				$attributes	= array('align' => 'middle', 'itemprop' => 'image');
				if($this->params->get('show_image') == 'resize'){
					$this->params->set('thumbnail_mode', $this->params->get('show_image-resize-mode', 'crop'));
					$this->params->set('thumbnail_mode-resize-use_ratio', $this->params->get('show_image-resize-use_ratio', 0));
					require_once (JPATH_COMPONENT.'/helpers/image.php');
					echo ceRenderImage( JText::_('COM_CONTACTENHANCED_IMAGE_DETAILS')
							, $this->contact->image
							, $this->params
							, $width
							, $height
							, null
							, $attributes
						);
				}else{
					if($this->params->get('show_image-resize-width')){
						$attributes['style']	= 'width:'.$this->params->get('show_image-resize-width');
					}
					echo JHTML::_('image',$this->contact->image, JText::_('COM_CONTACTENHANCED_IMAGE_DETAILS'), $attributes); 
				}?>
		</div>
	<?php endif; ?> 	

	<?php if ($this->contact->con_position && $this->params->get('show_position',1)) : ?>
		<p itemprop="jobTitle" class="contact-position"><?php echo $this->contact->con_position; ?></p>
	<?php endif; ?>

	<?php echo $this->loadTemplate('address'); ?>
	
	<?php echo $this->loadTemplate('extrafields'); ?>
	
	<?php if ($this->params->get('allow_vcard')) :	?>
		<?php echo JText::_('COM_CONTACTENHANCED_DOWNLOAD_INFORMATION_AS');?>
			<a href="<?php echo JURI::base(); ?>index.php?option=com_contactenhanced&amp;view=contact&amp;id=<?php echo $this->contact->id; ?>&amp;format=vcf">
				<?php echo JText::_('COM_CONTACTENHANCED_VCARD');?></a>
	<?php endif; ?>
	
	<?php 
		if($this->params->get('show_misc') == 'after_details'){
			echo $this->loadTemplate('misc');
		}
	 ?>
	
	<p></p>