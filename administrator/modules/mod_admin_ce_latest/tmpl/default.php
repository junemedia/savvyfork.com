<?php 
/**
 * @version		3.0.0
 * @package		com_contactenhanced
 * @subpackage	mod_admin_ce_latest
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


JHtml::_('bootstrap.tooltip');
?>
<div class="row-striped">
	<?php if (count($list)) : ?>
		<?php foreach ($list as $i => $item) : ?>
			<div class="row-fluid">
				<div class="span5">
					<?php // echo JHtml::_('jgrid.published', $item->state, $i, '', false); ?>
					<?php if (FALSE AND $item->checked_out) : ?>
							<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time); ?>
					<?php endif; ?>
					
				
					

					<strong class="row-title">
						<?php if ($item->link) :?>
							<a href="<?php echo $item->link; ?>">
								<?php echo htmlspecialchars($item->subject, ENT_QUOTES, 'UTF-8');?></a>
						<?php else :
							echo htmlspecialchars($item->subject, ENT_QUOTES, 'UTF-8');
						endif; ?>
					</strong>

					<?php if ($item->replied_by < 1) : ?>
						<span class="badge badge-warning"><?php echo JText::_('COM_CONTACTENHANCED_MESSAGE_NO_REPLIES');?></span>
					<?php endif; ?>
					
				</div>
				<div class="span4">
					<small class="small hasTooltip" title="<?php echo $item->from_email;?>">
						<?php echo $item->from_name;?>
					</small>
				</div>
				<div class="span3">
					<span class="small hasTooltip" title="<?php echo JHtml::_('date', $item->date, 'Y-m-d');?>">
						<i class="icon-calendar"></i> 
						<?php echo $item->time_ago; ?></span>
				</div>
			</div>
		<?php endforeach; ?>
	<?php else : ?>
		<div class="row-fluid">
			<div class="span12">
				<div class="alert"><?php echo JText::_('COM_CONTACTENHANCED_NO_FORM_SUBMITTED');?></div>
			</div>
		</div>
	<?php endif; ?>
</div>
