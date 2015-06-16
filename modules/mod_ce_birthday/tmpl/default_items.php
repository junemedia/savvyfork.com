<?php 
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_birthday
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


?>
<?php foreach ($contacts as $item) : ?>
	<li class="mod-ce-birthday-<?php echo $item->active; ?>" >
		<?php 
        if($params->get('show_image',1) AND $item->image ){
			echo $item->image;
		}
		?>
		<?php if($params->get( 'link_titles' )):?>
			<a class="mod-ce-birthday-title <?php echo $item->active; ?>" 
				href="<?php echo $item->href; ?>" 
				title="<?php echo $item->title; ?>">
			<?php echo $item->title; ?>
			</a>
		<?php else: ?>
			<span class="mod-ce-birthday-title <?php echo $item->active; ?>" >
			<?php echo $item->title; ?>
			</span>
		<?php endif;?>
		<p>
		<span class="<?php echo $params->get('marker_class'); ?>" >
			<?php echo $params->get('marker_birthdate'); ?>
		</span>
		<span class="contact-birthdate">
			<?php 	echo $item->birthday; ?>
		</span>
	</p>
		
	</li>
		
<?php endforeach;?>
