<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHTML::_('behavior.framework');
JHTML::_('behavior.tooltip');

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
?>
<?php if (empty($this->items)) : ?>
	<?php if (empty($this->children[$this->category->id]) OR $this->maxLevel == 0) : ?>
		<p> <?php echo JText::_('COM_CONTACTENHANCED_NO_CONTACTS'); ?>	 </p>
	<?php endif; ?>
	
<?php else : ?>

<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
<?php if ($this->params->get('show_pagination_limit')  OR $this->params->get('filter_field')) : ?>
	<fieldset class="filters">
	<?php if ($this->params->get('filter_field')) :?>
			<div class="btn-group">
			<?php if($this->params->get('filter_field') ==2): ?>
				<select name="search-field" id="search_field" class="input-medium">
					<option value=""><?php echo JText::_('JGLOBAL_FILTER_LABEL');?></option>
					<?php echo JHtml::_('select.options', $this->search_fields, 'value', 'text', $listOrder);?>
				</select>
				
			<?php else: ?>
				<label	class="filter-search-lbl element-invisible"
						for="filter-search"><?php  echo JText::_('JGLOBAL_FILTER_LABEL').'&#160;'; ?></label>
			<?php endif; ?>
				<input type="text" 
					name="filter-search" 
					id="filter-search" 
					value="<?php echo $this->escape($this->state->get('list.filter')); ?>" 
					class="inputbox" 
					onchange="document.adminForm.submit();" 
					title="<?php echo JText::_('COM_CONTACTENHANCED_FILTER_SEARCH_DESC'); ?>" 
					placeholder="<?php echo JText::_('COM_CONTACTENHANCED_FILTER_SEARCH_DESC'); ?>" />
			</div>
		<?php endif; ?>

		<?php if ($this->params->get('show_pagination_limit')) : ?>
			<div class="display-limit">
				<?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?>&#160;
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>
		<?php endif; ?>
	</fieldset>
<?php endif; ?>
	<table class="category">
		<?php if ($this->params->get('show_headings')) : ?>
		<thead><tr>
			
			<th class="item-title">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_CONTACT_EMAIL_NAME', 'a.name', $listDirn, $listOrder); ?>
				<?php //echo JText::_('COM_CONTACTENHANCED_CONTACT_EMAIL_NAME'); ?>
			</th>
			<?php if ($this->params->get('show_position_headings')) : ?>
			<th class="item-position">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_POSITION', 'a.con_position', $listDirn, $listOrder); ?>
				<?php //echo JText::_('COM_CONTACTENHANCED_POSITION'); ?>
			</th>
			<?php endif; ?>
			<?php if ($this->params->get('show_email_headings')) : ?>
			<th class="item-email">
				<?php echo JText::_('JGLOBAL_EMAIL'); ?>
			</th>
			<?php endif; ?>
			<?php if ($this->params->get('show_telephone_headings')) : ?>
			<th class="item-phone">
				<?php echo JText::_('COM_CONTACTENHANCED_TELEPHONE'); ?>
			</th>
			<?php endif; ?>

			<?php if ($this->params->get('show_mobile_headings')) : ?>
			<th class="item-phone">
				<?php echo JText::_('COM_CONTACTENHANCED_MOBILE'); ?>
			</th>
			<?php endif; ?>

			<?php if ($this->params->get('show_fax_headings')) : ?>
			<th class="item-phone">
				<?php echo JText::_('COM_CONTACTENHANCED_FAX'); ?>
			</th>
			<?php endif; ?>
			
			<?php if ($this->params->get('show_street_address_headings')) : ?>
			<th class="item-street-address">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_ADDRESS', 'a.address', $listDirn, $listOrder); ?>
				<?php //echo JText::_('COM_CONTACTENHANCED_ADDRESS'); ?>
			</th>
			<?php endif; ?>
					
					
			<?php if ($this->params->get('show_suburb_headings')) : ?>
			<th class="item-suburb">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_SUBURB', 'a.suburb', $listDirn, $listOrder); ?>
				<?php //echo JText::_('COM_CONTACTENHANCED_SUBURB'); ?>
			</th>
			<?php endif; ?>

			<?php if ($this->params->get('show_state_headings')) : ?>
			<th class="item-state">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_STATE', 'a.state', $listDirn, $listOrder); ?>
				<?php //echo JText::_('COM_CONTACTENHANCED_STATE'); ?>
			</th>
			<?php endif; ?>
			
			
			<?php if ($this->params->get('show_postcode_headings')) : ?>
			<th class="item-postcode">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_POSTCODE', 'a.postcode', $listDirn, $listOrder); ?>
				<?php //echo JText::_('COM_CONTACTENHANCED_POSTCODE'); ?>
			</th>
			<?php endif; ?>

			<?php if ($this->params->get('show_country_headings')) : ?>
			<th class="item-country">
				<?php echo JHtml::_('grid.sort', 'COM_CONTACTENHANCED_COUNTRY', 'a.country', $listDirn, $listOrder); ?>
				<?php //echo JText::_('COM_CONTACTENHANCED_COUNTRY'); ?>
			</th>
			<?php endif; ?>
			
			<?php if ($this->params->get('show_webpage_headings')) : ?>
			<th class="item-webpage">
				<?php echo JText::_('COM_CONTACTENHANCED_WEBPAGE'); ?>
			</th>
			<?php endif; ?>
			<?php for ($i = 1; $i <= 10; $i++): ?>
				<?php if ($this->params->get('show_extrafield_'.$i.'_headings')) : ?>
				<th class="item-extra_field_<?php echo $i; ?>">
					<?php echo JText::_('COM_CONTACTENHANCED_EXTRA_FIELD_'.$i); ?>
				</th>
				<?php endif; ?>
			<?php endfor; ?>
			
			</tr>
		</thead>
		<?php endif; ?>

		<tbody>
			<?php foreach($this->items as $i => $item) : ?>
				<?php if ($this->items[$i]->published == 0) : ?>
					<tr class="system-unpublished cat-list-row<?php echo $i % 2; ?>">
				<?php else: ?>
					<tr class="cat-list-row<?php echo $i % 2; ?>" >
				<?php endif; ?>

					<td class="item-title">
						<?php 
						$item->link = JRoute::_(ContactenchancedHelperRoute::getContactRoute($item->slug, $item->catid));
						if($this->params->get( 'show_contact_image' ) == '1' AND $item->image ){
							$image = JHTML::_('image',  JURI::base(). $item->image, JText::sprintf('COM_CONTACTENHANCED_CONTACT_IMAGE_ALT',$item->name ), array('align' => 'middle', 'class'=> 'ce-contact-img-cat'));
							echo JHTML::_('link',$item->link,$image, array('class'=>'category'.$this->params->get( 'pageclass_sfx' )) );
							echo JHTML::_('link',$item->link,$item->name, array('class'=>'category'.$this->params->get( 'pageclass_sfx' )) );
						}elseif ($this->params->get( 'show_contact_image','tooltip' ) == 'tooltip' AND $item->image){
							$image = JHTML::_('image',  JURI::base(). $item->image, JText::sprintf('COM_CONTACTENHANCED_CONTACT_IMAGE_ALT',$item->name ), array('align' => 'middle', 'class'=> 'ce-contact-img-cat'));
							$image	= JHTML::tooltip($image,$item->name,'',$item->name);
							echo JHTML::_('link',$item->link,$image, array('class'=>'category'.$this->params->get( 'pageclass_sfx' )) );
						}else{
							echo JHTML::_('link',$item->link,$item->name, array('class'=>'category'.$this->params->get( 'pageclass_sfx' )) );
						} 
					?>
					</td>

					<?php if ($this->params->get('show_position_headings')) : ?>
						<td class="item-position">
							<?php echo $item->con_position; ?>
						</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_email_headings')) : ?>
						<td class="item-email">
							<?php echo $item->email_to; ?>
						</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_telephone_headings')) : ?>
						<td class="item-phone">
							<?php  
								$tel	= nl2br($item->telephone);
								if($this->browser->isMobile()){
									$telLink= 'tel:'.preg_replace('[(?!\+\b)\D]', '', $tel);
									$tel	= JHtml::_('link'
														,$telLink
														,$tel
														, array('title' => JText::sprintf('COM_CONTACTENHANCED_CALL_USING_YOUR_PHONE',$tel))
														);
								}
								echo ($tel); 
							?>
							
						</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_mobile_headings')) : ?>
						<td class="item-phone">
							<?php  
								$tel	= nl2br($item->mobile);
								if($this->browser->isMobile()){
									$telLink= 'tel:'.preg_replace('[(?!\+\b)\D]', '', $tel);
									$tel	= JHtml::_('link'
														,$telLink
														,$tel
														, array('title' => JText::sprintf('COM_CONTACTENHANCED_CALL_USING_YOUR_PHONE',$tel))
														);
								}
								echo ($tel); 
							?>
						</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_fax_headings')) : ?>
					<td class="item-phone">
						<?php echo $item->fax; ?>
					</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_street_address_headings')) : ?>
					<td class="item-street-address">
						<?php echo $item->address; ?>
					</td>
					<?php endif; ?>
					
					<?php if ($this->params->get('show_suburb_headings')) : ?>
					<td class="item-suburb">
						<?php echo $item->suburb; ?>
					</td>
					<?php endif; ?>

					<?php if ($this->params->get('show_state_headings')) : ?>
					<td class="item-state">
						<?php echo $item->state; ?>
					</td>
					<?php endif; ?>
					
					
					<?php if ($this->params->get('show_postcode_headings')) : ?>
					<td class="item-postcode">
						<?php echo $item->postcode; ?>
					</td>
					<?php endif; ?>
					
					<?php if ($this->params->get('show_country_headings')) : ?>
					<td class="item-country">
						<?php echo $item->country; ?>
					</td>
					<?php endif; ?>
					
					<?php if ($this->params->get('show_webpage_headings')) : ?>
					<td class="item-webpage">
						<?php if ($item->webpage): ?>
							<a href="<?php echo $item->webpage; ?>" title="<?php echo $item->webpage; ?>" target="_blank">
							<?php 
								if($this->params->get('show_webpage_headings') == 'trim'){
									 echo ceHelper::trimURL($item->webpage); 
								}elseif($this->params->get('show_webpage_headings') == 'label'){
									 echo JText::_('COM_CONTACTENHANCED_WEBPAGE_LABEL'); 
								}else{
									echo $item->webpage;
								}
							?></a>
						<?php endif;  ?>
					</td>
					<?php endif; ?>
					
					<?php for ($i = 1; $i <= 10; $i++): ?>
						<?php if ($this->params->get('show_extrafield_'.$i.'_headings')) : ?>
						<td class="item-extra_field_<?php echo $i; ?>">
							<?php
							$field	= "extra_field_".$i;
							 echo $item->$field; ?>
						</td>
						<?php endif; ?>
					<?php endfor; ?>
					
				</tr>
			<?php endforeach; ?>

		</tbody>
	</table>

	<?php if ($this->params->get('show_pagination')) : ?>
	<div class="pagination">
		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
		<p class="counter">
			<?php echo $this->pagination->getPagesCounter(); ?>
		</p>
		<?php endif; ?>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
	<?php endif; ?>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="state"	value="<?php echo $this->filter_state; ?>" />
		<input type="hidden" name="country" value="<?php echo $this->filter_country; ?>" />
		<input type="hidden" name="suburb"	value="<?php echo $this->filter_suburb; ?>" />
	</div>
</form>
<?php endif; ?>