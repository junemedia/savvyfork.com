<?php 
/**
 * @version		3.0.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_latest
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


?>
<?php foreach ($items as $item) : ?>
	<li>
		
		<?php if($params->get( 'link_titles' ) AND !empty($item->href)):?>
			<a class="mod-ce-latest-title" 
				href="<?php echo $item->href; ?>" 
				title="<?php echo $item->title; ?>"
				target="<?php echo $item->link_target; ?>"
				>
			<?php echo $item->title; ?>
			</a>
		<?php else: ?>
			<span class="mod-ce-latest-title" >
			<?php echo $item->title; ?>
			</span>
		<?php endif;?>
		<span class="mod-ce-latest-time-ago">
				<?php 	echo $item->time_ago; ?>
			</span>
		
	</li>
		
<?php endforeach;?>
