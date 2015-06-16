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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');

$user		= JFactory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering', 'cf.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction', 'asc'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$canOrder	= $user->authorise('core.edit.state', 'com_contactenhanced.category');
$saveOrder	= $listOrder == 'cv.ordering';
if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_contactenhanced&task=customvalues.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'articleList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();
?>
<script type="text/javascript">
	Joomla.orderTable = function() {
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
</script>
		
<form	enctype="multipart/form-data" 
		action="<?php echo JRoute::_('index.php?option=com_contactenhanced&view=customvalues'); ?>" 
		method="post" name="adminForm" id="adminForm">
	<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
			<div id="filter-bar" class="btn-toolbar">
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
				<div class="btn-group pull-right hidden-phone">
					<label for="limit" class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC');?></label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<div class="btn-group pull-right hidden-phone">
					<label for="directionTable" class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC');?></label>
					<select name="directionTable" id="directionTable" class="input-small" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JFIELD_ORDERING_DESC');?></option>
						<option value="asc" <?php if ($listDirn == 'asc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING');?></option>
						<option value="desc" <?php if ($listDirn == 'desc') echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING');?></option>
					</select>
				</div>
				<div class="btn-group pull-right">
					<label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY');?></label>
					<select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
						<option value=""><?php echo JText::_('JGLOBAL_SORT_BY');?></option>
						<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder);?>
					</select>
				</div>
			</div>
			
			<div class="clearfix"> </div>
	<table class="table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="1%" class="center hidden-phone" nowrap="nowrap">
					<i class="icon-menu-2 hasTip" title="<?php echo JText::_('JGRID_HEADING_ORDERING'); ?>"></i>
				</th>
				<th width="1%" class="hidden-phone">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="5%" style="min-width:55px" class="center">
					<?php echo JText::_('JSTATUS'); ?>
				</th>
				<th class="hidden-phone">
					<?php echo JText::_('JGLOBAL_TITLE'); ?>
				</th>
				
				<th class="hidden-phone">
					<?php echo JText::_('CE_CV_VALUE'); ?>
				</th>
				<th class="hidden-phone">
					<?php echo JText::_('CE_CV_DESCRIPTION'); ?>
				</th>
				
				<th class="hidden-phone">
					<?php echo JText::_('CE_CV_TYPE'); ?>
				</th>
				
				
				<th width="10%" class="hidden-phone">
					<?php echo JText::_('JGRID_HEADING_ACCESS'); ?>
				</th>
				<th width="5%" class="hidden-phone">
					<?php echo JText::_('JGRID_HEADING_LANGUAGE'); ?>
				</th>
				<th width="1%" class="hidden-phone">
					<?php echo JText::_('JGRID_HEADING_ID'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
				<?php
				$n = count($this->items);
				foreach ($this->items as $i => $item) :
					$ordering	= $listOrder == 'a.ordering';
					$canCreate	= $user->authorise('core.create',		'com_contactenhanced.category.'.$item->catid);
					$canEdit	= $user->authorise('core.edit',			'com_contactenhanced.category.'.$item->catid);
					$canCheckin	= $user->authorise('core.manage',		'com_checkin');
					$canChange	= $user->authorise('core.edit.state',	'com_contactenhanced.category.'.$item->catid) && $canCheckin;

					$item->cat_link = JRoute::_('index.php?option=com_categories&extension=com_contactenhanced&task=edit&type=other&id='.$item->catid);
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
						<td class="order nowrap center hidden-phone">
							<?php if ($canChange) :
								$disableClassName = '';
								$disabledLabel	  = '';
								if (!$saveOrder) :
									$disabledLabel    = JText::_('JORDERINGDISABLED');
									$disableClassName = 'inactive tip-top';
								endif; ?>
								<span class="sortable-handler <?php echo $disableClassName?>" title="<?php echo $disabledLabel?>" rel="tooltip">
									<i class="icon-menu"></i>
								</span>
								<input type="text" style="display:none"  name="order[]" size="5"
								value="<?php echo $item->ordering;?>" class="width-20 text-area-order " />
							<?php else : ?>
								<span class="sortable-handler inactive" >
									<i class="icon-menu"></i>
								</span>
							<?php endif; ?>
						</td>
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'customvalues.', $canChange, 'cb'); ?>
						</td>
						<td class="nowrap has-context">
							<div class="pull-left">
						
								<?php if ($canEdit) : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_contactenhanced&task=customvalue.edit&id='.(int) $item->id); ?>">
									<?php echo $this->escape($item->name); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->name); ?>
								<?php endif; ?>
								<div class="small">
									<?php 
									echo JText::_('JCATEGORY').': ';
									if(!$item->category_title){
										echo JText::_("JALL");
									}else{
										echo $item->category_title;
									} ?>
								</div>
							</div>
							<div class="pull-left">
								<?php
									// Create dropdown items
									JHtml::_('dropdown.edit', $item->id, 'customvalue.');
									JHtml::_('dropdown.divider');
									if ($item->published) :
										JHtml::_('dropdown.unpublish', 'cb' . $i, 'customvalues.');
									else :
										JHtml::_('dropdown.publish', 'cb' . $i, 'customvalues.');
									endif;

								
									JHtml::_('dropdown.divider');

									if ($archived) :
										JHtml::_('dropdown.unarchive', 'cb' . $i, 'customvalues.');
									else :
										JHtml::_('dropdown.archive', 'cb' . $i, 'customvalues.');
									endif;

									if ($trashed) :
										JHtml::_('dropdown.untrash', 'cb' . $i, 'customvalues.');
									else :
										JHtml::_('dropdown.trash', 'cb' . $i, 'customvalues.');
									endif;

									// render dropdown list
									echo JHtml::_('dropdown.render');
								?>
							</div>
						</td>
						<td class="small hidden-phone">
							<?php echo $item->value; ?>
						</td>
						<td class="small hidden-phone">
							<?php echo $item->description; ?>
						</td>
						<td class="small hidden-phone">
							<?php echo ($item->type);?>
						</td>
						
						<td class="small hidden-phone">
							<?php echo $item->access_level; ?>
						</td>
						<td class="small hidden-phone">
							<?php if ($item->language == '*'):?>
								<?php echo JText::alt('JALL', 'language'); ?>
							<?php else:?>
								<?php echo $item->language_title ? $this->escape($item->language_title) : JText::_('JUNDEFINED'); ?>
							<?php endif;?>
						</td>
						<td align="center hidden-phone">
							<?php echo $item->id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="10">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
			</table>
			
			
			<?php //Load the batch processing form. ?>
			<?php //echo $this->loadTemplate('batch'); ?>
			
			<div>
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<?php echo JHtml::_('form.token'); ?>
			</div>
		</div>
		<!-- End Content -->
</form>