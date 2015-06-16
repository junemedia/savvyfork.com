<?php
/**
 * @version		1.6.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once (JPATH_COMPONENT.'/helpers/image.php');
?>
<?php if (empty($this->items)) : ?>
	<p> <?php echo JText::_('COM_CONTACTENHANCED_NO_ARTICLES'); ?>	 </p>
<?php else : ?>

<?php echo $this->loadTemplate('contact'); ?>

<ul id="ce-thumbnails">
	<?php foreach($this->category->contacts as $i => $item) : ?>
		<?php 
		if (!$item->image){
			$item->image	= 'components/com_contactenhanced/assets/images/no-contact-image.png';
		}
		$images[]	= JURI::root().$item->image;
		$item->link = JRoute::_(ContactenchancedHelperRoute::getContactRoute($item->slug, $item->catid));
		$image		= ceRenderImage(	$item->name,
										$item->image,
										$this->params, 
										$this->params->get('thumbnail_width',130).'px',
										$this->params->get('thumbnail_height',0).'px'
										);
		$attributes	= array(
							'class'		=> 'ce-contact-id-'.$item->id,
							//"onclick"	=> "ceCatThumb.getInfo({$item->id},".JRequest::getVar("Itemid").",'".JURI::base()."'); return false;"
						);
		$item->params->merge($this->params);
		$ceObj			= new stdClass();
		$ceObj->params	= &$item->params;
		$ceObj->contact	= &$item;
		$ceObj->item	= &$ceObj->contact;
		$details	= ceHelper::getContactDetails($ceObj, 'html');
		?>
		<li class="<?php echo ($details ? 'details' : '');?>"><?php
			
			if($this->params->get('link_thumbnails',1)){
				echo JHtml::_('link', $item->link, $image,$attributes );
			}else{
				echo $image;
			}
			
			if($this->params->get('show_name_heading') == 'show_with_link'){
				echo '<h3>'.JHtml::_('link',$item->link,$item->name,'title="'.$item->name.'"').'</h3>';
			}else if($this->params->get('show_name_heading')){
				echo '<h3>'.$item->name.'</h3>';
			} 
			echo $details;
			
		?></li>

	<?php endforeach; ?>
</ul>
<?php 
$this->doc->addScriptDeclaration("window.addEvent('domready',function(){var img=Asset.images(['".(implode("','",$images))."']);});");
?>
<?php endif; ?>

<br style="clear:both" />