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

$function = JRequest::getVar('function', 'jSelectChart');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<form action="<?php echo JRoute::_('index.php?option=com_contactenhanced&view=customfields&layout=modal&tmpl=component');?>" method="post" name="adminForm">
	<fieldset class="filter clearfix">
		<div class="left">
			<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_('COM_CONTENT_FILTER_SEARCH_DESC');?></label>
					<input type="text" name="filter_search" id="filter_search" 
					placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" 
					value="<?php echo $this->escape($this->state->get('filter.search')); ?>" 
					title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
				</div>
			<div class="btn-group pull-left">
					<button class="btn" rel="tooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
					<button class="btn" rel="tooltip" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();"><i class="icon-remove"></i></button>
				</div>
			
		</div>

		<div class="right">
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>

			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_CATEGORY');?></option>
				<option value="*"><?php echo JText::_('JALL');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('category.options', 'com_contactenhanced'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>
		</div>
	</fieldset>

	<table  class="table table-striped" id="articleList">
		<thead>
			<tr>
	
				<th class="title" style="text-align:left">
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'cf.title', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JCATEGORY', 'cf.catid', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'category', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" nowrap="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'cf.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">

				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $function;?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->name)); ?>');">
						<?php echo $this->escape($item->name); ?></a>
				</td>
				<td align="center">
					<?php 
					if(!$item->category_title){
						echo JText::_("JALL");
					}else{
						echo $item->category_title;
					} ?>
				</td>
				<td align="center">
					<?php echo ($item->access_level); ?>
				</td>

				<td align="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />

	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
