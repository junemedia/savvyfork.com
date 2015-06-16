<?php
/**

 * @package		com_contactenhanced
* @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$function = JRequest::getVar('function', 'jSelectContact');

?>
<form action="index.php?option=com_contact_enhanced&amp;task=element&amp;tmpl=component&amp;object=id&amp;elemType=<?php 
				echo JRequest::getCmd('elemType'); 
				?>&amp;elemVar1=<?php echo JRequest::getCmd('elemVar1');
				?>&amp;elemVar2=<?php echo JRequest::getCmd('elemVar2');
				?>&amp;elemVar3=<?php echo JRequest::getCmd('elemVar3');
				?>&amp;elemVar4=<?php echo JRequest::getCmd('elemVar4');
				?>&amp;elemVar5=<?php echo JRequest::getCmd('elemVar5'); 
				?>"
				method="post" name="adminForm">


	<table class="adminlist">
		<thead>
			<tr>
	
				
				<th width="2%" nowrap="nowrap">
					<?php JText::_('JGRID_HEADING_ID'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('JGLOBAL_TITLE'); ?>
				</th>
				
			</tr>
		</thead>
		
		<tbody>
		<?php 
		
		foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td align="center">
					<?php echo (int) $item->id; ?>
				</td>
				<td>
					<a class="pointer" onclick="if(window.parent) window.parent.<?php 
						echo $function;?>('<?php echo $item->id; ?>', '<?php 
							echo str_replace(array("'", "\""), array("\\'", ""),
									$item->title); ?>', '<?php echo JRequest::getVar('object'); ?>');">
						<?php echo $this->escape($item->title); ?></a>
				</td>
							</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
