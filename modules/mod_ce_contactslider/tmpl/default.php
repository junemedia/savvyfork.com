<?php 
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_contactslider
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$document = JFactory::getDocument();
$document->addStylesheet(JURI::base(true).'/components/com_contactenhanced/assets/css/ce.css');

$mainframe = JFactory::getApplication();
$tmplPath   = 'templates/'.$mainframe->getTemplate().'/';
$tmplimages = $tmplPath.'images'.'/';
$modPath    = 'modules/mod_ce_contactslider/assets/images/';
//Images
$image_path = $modPath;
if ( file_exists (JPATH_SITE.'/'.$tmplimages.'arrows/re-left.gif') ) {
	$image_path = $tmplimages;
}
$image_path = str_replace( '\\', '/', $image_path );
		

$cateArr = array();
foreach ($contacts as $contact) {
	if (isset($contact->cateName) && !isset($cateArr[$contact->catid])) {
		$cateArr[$contact->catid] = $contact->cateName;
	}
}
if (!$showTab || count($cateArr) <= 1) {
	//if not display tabs
	//we must show all items of All Categories on one tab
	$firstCid = 0;
}
else {
	$firstCid = array_keys($cateArr);
	$firstCid = $firstCid[0];
}
?>
<script type="text/javascript">
	//<!--[CDATA[
	function contentSliderInit_<?php echo $module->id;?> (cid) {
		cid = parseInt(cid);
		var containerID = 'ce-contactslider-<?php echo $module->id;?>';
		var container =  $(containerID);
		
		container.getElements('.jsslide').each(function(el){
			el.dispose();
		});
		
		if(cid == 0) {
			var elems = $('ce-contactslider-center-<?php echo $module->id;?>').getElements('div[class*=content_element]');
		}else{
			var elems = $('ce-contactslider-center-<?php echo $module->id;?>').getElements('div[class*=ceslide2_'+cid+']');
		}
		var total = elems.length;

		var options={
			w: <?php echo $xwidth; ?>,
			h: <?php echo $xheight; ?>,
			num_elem: <?php echo  $numElem; ?>,
			mode: '<?php  echo  $mode; ?>', //horizontal or vertical
			direction: '<?php echo $direction; ?>', //horizontal: left or right; vertical: up or down
			total: total,
			url: '<?php echo JURI::base(); ?>modules/mod_ce_contactslider/mod_ce_contactslider.php',
			wrapper:  container.getElement("div.ce-contactslider-center"),
			duration: <?php echo $animationtime; ?>,
			interval: <?php echo $delaytime; ?>,
			modid: <?php echo $module->id;?>,
			running: false,
			auto: <?php echo $auto;?>
		};		
		
		var jscontentslider = new JS_ContentSlider( options );
		
		for(i=0;i<elems.length;i++){
			jscontentslider.update (elems[i].innerHTML, i);
		}
		jscontentslider.setPos(null);
		if(jscontentslider.options.auto){
			jscontentslider.nextRun();
		}
		
		<?php if( $params->get( 'controls',1 ) ): ?>
		  <?php if($params->get( 'controls-display-scroll_when', 'click' ) == 'click'): ?>
			<?php if ($mode == 'vertical'): ?>
			container.getElement(".ce-contactslide-up-img").onclick = function(){setDirection2<?php echo $module->id;?>('down', jscontentslider);};
			container.getElement(".ce-contactslide-down-img").onclick = function(){setDirection2<?php echo $module->id;?>('up', jscontentslider);};
			<?php else: ?>
			container.getElement(".ce-contactslide-left-img").onclick = function(){setDirection2<?php echo $module->id;?>('right', jscontentslider);};
			container.getElement(".ce-contactslide-right-img").onclick = function(){setDirection2<?php echo $module->id;?>('left', jscontentslider);};
			<?php endif; //vertical? ?>
		  <?php else: ?>
			<?php if ($mode == 'vertical'): ?>
			container.getElement(".ce-contactslide-up-img").onmouseover = function(){setDirection<?php echo $module->id;?>('down',0, jscontentslider);};
			container.getElement(".ce-contactslide-up-img").onmouseout = function(){setDirection<?php echo $module->id;?>('down',1, jscontentslider);};
			container.getElement(".ce-contactslide-down-img").onmouseover = function(){setDirection<?php echo $module->id;?>('up',0, jscontentslider);};
			container.getElement(".ce-contactslide-down-img").onmouseout = function(){setDirection<?php echo $module->id;?>('up',1, jscontentslider);};
			<?php else: ?>
			container.getElement(".ce-contactslide-left-img").onmouseover = function(){setDirection<?php echo $module->id;?>('right',0, jscontentslider);};
			container.getElement(".ce-contactslide-left-img").onmouseout = function(){setDirection<?php echo $module->id;?>('right',1, jscontentslider);};
			container.getElement(".ce-contactslide-right-img").onmouseover = function(){setDirection<?php echo $module->id;?>('left',0, jscontentslider);};
			container.getElement(".ce-contactslide-right-img").onmouseout = function(){setDirection<?php echo $module->id;?>('left',1, jscontentslider);};
			<?php endif; //vertical? ?>
		  <?php endif; //scroll event ?>
		<?php endif; //show control? ?>

	<?php if( $params->get( 'controls','topbar' ) == 'topbar' OR !empty($text_heading) OR $showTab): ?>
		/**active tab**/
		container.getElement('.ce-button-control').getElements('a').each(function(el){
			var css = (el.getProperty('rel') == cid) ? 'active' : '';
			el.className = css;
		});
	<?php  endif; ?>
	}
	window.addEvent( 'domready', function(){ contentSliderInit_<?php echo $module->id;?>(<?php echo $firstCid; ?>); } );

	function setDirection<?php echo $module->id;?>(direction,ret, jscontentslider) {
		jscontentslider.options.direction = direction;
		if(ret){
			jscontentslider.options.interval = <?php echo $delaytime; ?>;
			jscontentslider.options.duration = <?php echo $animationtime; ?>;
			jscontentslider.options.auto = <?php echo $auto; ?>;
			jscontentslider.nextRun();
		}
		else{
			jscontentslider.options.interval = 500;
			jscontentslider.options.duration = 500;
			jscontentslider.options.auto = 1;
			jscontentslider.nextRun();
		}
	}
	function setDirection2<?php echo $module->id;?>(direction, jscontentslider) {
		jscontentslider.options.direction = direction;
		jscontentslider.options.interval = 500;
		jscontentslider.options.duration = 200;
		jscontentslider.options.auto = 1;
		jscontentslider.nextRun();
		jscontentslider.options.auto = 0;
	}
	//]]-->
</script>

<div id="ce-contactslider-<?php echo $module->id;?>" class="ce-contactslider clearfix" >
<?php 
if($params->get('introtext')){
	echo '<div class="ce-introtext">'.$params->get('introtext').'</div>';
}
?>
  <!--toolbar-->
  <?php if( $params->get( 'controls','topbar' ) == 'topbar' OR !empty($text_heading) OR $showTab) { ?>
	  <div class="ce-button-control"> 
	    <?php if(!empty($text_heading)): ?>
	    <span class="ce-text-heading"><?php echo $text_heading; ?></span>
	    <?php endif; ?>
	    <?php if(count($cateArr) > 1) : ?>
		    <?php if ($showTab == 1): ?>
			    <a href="javascript:contentSliderInit_<?php echo $module->id;?>(0)" rel="0"><?php echo JText::_('MOD_CE_CONTACTSLIDER_ALL'); ?></a>
			    <?php foreach ($cateArr as $key=>$value): ?>
				    <?php if(!empty($value)): ?>
				  		<a href="javascript:contentSliderInit_<?php echo $module->id;?>('<?php echo $key;?>')" rel="<?php echo $key;?>"><?php echo $value; ?></a>
				    <?php endif; ?>
			    <?php endforeach; ?>
		    <?php endif; //show tab? ?>
	    <?php endif; //if more than one category ?>
	     <?php if( $params->get( 'controls','topbar' ) == 'topbar') { ?>
			<?php if ($mode == 'vertical'){ ?>
				<div class="ce-contactslider-right ce-contactslide-up-img" title="<?php echo JText::_('MOD_CE_CONTACTSLIDER_NEXT'); ?>">&nbsp;</div>
				<div class="ce-contactslider-left ce-contactslide-down-img" title="<?php echo JText::_('MOD_CE_CONTACTSLIDER_PREVIOUS'); ?>">&nbsp;</div>
			<?php } else {?>
				<div class="ce-contactslider-right ce-contactslide-right-img" title="<?php echo JText::_('MOD_CE_CONTACTSLIDER_NEXT'); ?>">&nbsp;</div>
				<div class="ce-contactslider-left ce-contactslide-left-img" title="<?php echo JText::_('MOD_CE_CONTACTSLIDER_PREVIOUS'); ?>">&nbsp;</div>
			<?php } ?>
	    <?php } ?>
	  </div>     <?php // END: ce-button-control ?>
<?php } /* END IF */ ?>

<?php if( $params->get( 'controls','side' ) == 'side' ): ?>  	
	<div class="ce-button-control-side-<?php echo $mode; ?>-previous">
		<?php if ($mode == 'vertical'){ ?>
			<div class="ce-contactslider-side ce-contactslide-down-img" title="<?php echo JText::_('MOD_CE_CONTACTSLIDER_PREVIOUS'); ?>">&nbsp;</div>
		<?php } else {?>
			<div class="ce-contactslider-side ce-contactslide-left-img" title="<?php echo JText::_('MOD_CE_CONTACTSLIDER_PREVIOUS'); ?>">&nbsp;</div>
		<?php } ?>
	</div>
<?php endif; ?>
  
  <!--items-->
  <div class="ce-contactslider-center-wrap clearfix">
    <div id="ce-contactslider-center-<?php echo $module->id;?>" class="ce-contactslider-center">
      <?php 
	foreach( $contacts  as $contact ) { 
		$link	= $contact->href;
		$item	= $contact;
	?>
      <div class="content_element ceslide2_<?php echo $contact->catid; ?>" style="display:none;">
        <?php 
        if( $params->get( 'show_name', 'before_image' ) == 'before_image' ) { 
        	require JModuleHelper::getLayoutPath('mod_ce_contactslider', 'default_name');
        }
        ?>
       
        <?php if(  $params->get( 'images' ) ) { ?>
        <div class="ce_slideimages tooltips clearfix">
          <div class="ce_slideimages_inner">
            <div class="content">
              <?php echo $contact->image; ?>
            </div>
          </div>
        </div>
        <?php } ?>
        
        <div class="ce-contact-details">
        <?php 
        if( $params->get( 'show_name' ) == 'after_image' ) { 
        	require JModuleHelper::getLayoutPath('mod_ce_contactslider', 'default_name');
        }
        ?>

		<?php 
			require JModuleHelper::getLayoutPath('mod_ce_contactslider', 'default_contact_details');
		?>

        <?php if( $params->get('show_readmore') ){ ?>
        <div class="ce-slider-readmore">
        <?php 
			$attributes	= array();
			$attributes['title']	= JText::sprintf('MOD_CE_CONTACTSLIDER_READ_MORE', $contact->name);
			$attributes['class']	= "readon";
        	echo modCEContactSliderHelper::createLink($link
        		, $contact->name
        		, $params
        		, $attributes); ?>
        </div>
        <?php } // endif;?>
		</div> <?php  /* END: Contact details */ ?>
      </div> <?php  /* END: Content Element*/ ?>
      <?php } //endforeach; ?>
    </div>
  </div>
  
  <?php if( $params->get( 'controls','side' ) == 'side' ): ?>  	
	<div class="ce-button-control-side-<?php echo $mode; ?>-next">
		<?php if ($mode == 'vertical'){ ?>
			<div class="ce-contactslider-side ce-contactslide-up-img" title="<?php echo JText::_('MOD_CE_CONTACTSLIDER_NEXT'); ?>">&nbsp;</div>
		<?php } else {?>
			<div class="ce-contactslider-side ce-contactslide-right-img" title="<?php echo JText::_('MOD_CE_CONTACTSLIDER_NEXT'); ?>">&nbsp;</div>
		<?php } ?>
	</div>
<?php endif; ?>
<?php 
if($params->get('posttext')){
	echo  '<div class="ce-posttext">'.$params->get('posttext').'</div>';
}
?>
</div>
