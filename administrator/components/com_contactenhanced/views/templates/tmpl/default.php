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
$listOrder	= $this->escape($this->state->get('list.ordering', 'msg.id'));
$listDirn	= $this->escape($this->state->get('list.direction', 'desc'));
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$canOrder	= $user->authorise('core.edit.state', 'com_contactenhanced.category');
$saveOrder	= false;
$sortFields = $this->getSortFields();

?>

<form	enctype="multipart/form-data" 
		action="<?php echo JRoute::_('index.php?option=com_contactenhanced&view=templates'); ?>" 
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
					<?php echo JText::_('CE_TPL_TEMPLATE_TYPE'); ?>
				</th>
				
				<th width="1%" class="hidden-phone">
					<?php echo JText::_('JGRID_HEADING_ID'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
	
	
		<?php
		jimport('joomla.utilities.date');
		$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
		$n = count($this->items);
		$n = count($this->items);
				foreach ($this->items as $i => $item) :
					$ordering	= false;
					$canCreate	= $user->authorise('core.create',		'com_contactenhanced.category');
					$canEdit	= $user->authorise('core.edit',			'com_contactenhanced.category');
					$canCheckin	= $user->authorise('core.manage',		'com_checkin');
					$canChange	= $user->authorise('core.edit.state',	'com_contactenhanced.category') && $canCheckin;
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->catid?>">
						<td class="center hidden-phone">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'messages.', $canChange, 'cb'); ?>
						</td>
						<td class="nowrap has-context">
							<div class="pull-left">
						
								<?php if ($canEdit) : ?>
									<a href="<?php echo JRoute::_('index.php?option=com_contactenhanced&task=template.edit&id='.(int) $item->id); ?>">
									<?php echo $this->escape($item->name); ?></a>
								<?php else : ?>
									<?php echo $this->escape($item->name); ?>
								<?php endif; ?>
								
							</div>
							<div class="pull-left">
								<?php
									// Create dropdown items
									JHtml::_('dropdown.edit', $item->id, 'customvalue.');
									JHtml::_('dropdown.divider');
									if ($item->published) :
										JHtml::_('dropdown.unpublish', 'cb' . $i, 'messages.');
									else :
										JHtml::_('dropdown.publish', 'cb' . $i, 'messages.');
									endif;

								
									JHtml::_('dropdown.divider');

									if ($archived) :
										JHtml::_('dropdown.unarchive', 'cb' . $i, 'messages.');
									else :
										JHtml::_('dropdown.archive', 'cb' . $i, 'messages.');
									endif;

									if ($trashed) :
										JHtml::_('dropdown.untrash', 'cb' . $i, 'messages.');
									else :
										JHtml::_('dropdown.trash', 'cb' . $i, 'messages.');
									endif;

									// render dropdown list
									echo JHtml::_('dropdown.render');
								?>
							</div>
						</td>
						<td class="small hidden-phone">
							<?php echo $item->type; ?>
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