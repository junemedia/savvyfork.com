<?php
/**
 * @package    com_contactenhanced
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 24-Feb-2011
 */

//-- No direct access
defined('_JEXEC') or die('Access Denied - Please do not try to fool me! ;-)');

?>
<form	action="<?php echo JRoute::_('index.php?option=com_contactenhanced&amp;Itemid='.$mitemid); ?>"
		method="post"
		id="mod_ce_search_form">
	<div class="search<?php echo $moduleclass_sfx ?>">
		<?php
			$output = '<label for="mod-ce-search-searchword" class="element-invisible">'.$label.'</label>'
						.' <input name="q" id="mod-ce-search-searchword"
							maxlength="'.$maxlength.'"  class="inputbox'.$moduleclass_sfx.'  search-query"
							type="text" size="'.$width.'"
							value=""
							placeholder="'.$text.'"
							title="'.$label.'"
						/>';
			$separator	= "\n ";
			if($button_pos == 'bottom'){
				$separator = "\n<br /> \n";
			}
			foreach ($filters as $filter) {
				$output .= $separator.$filter;
			}

			if ($button_pos) :
				if ($imagebutton) :
					$button = '<input type="image" value="'.$button_text.'"
							class="button'.$moduleclass_sfx.'" src="'.$img.'"
							onclick="this.form.searchword.focus();"/>';
				else :
					$button = '<input type="submit" value="'.$button_text.'" class="button'.$moduleclass_sfx.'" '
						.' onclick="this.form.searchword.focus();"/>';
				endif;
			endif;

			switch ($button_pos) :
				case 'top' :
					$button = $button.'<br />';
					$output = $button.$output;
					break;

				case 'bottom' :
					$button = '<br />'.$button;
					$output = $output.$button;
					break;

				case 'right' :
					$output = $output.$button;
					break;

				case 'left' :
					$output = $button.$output;
					break;
			endswitch;

			echo $output;
		?>
	<input type="hidden" name="view"	value="search" />
	<input type="hidden" name="option"	value="com_contactenhanced" />
	<input type="hidden" name="Itemid"	value="<?php echo $mitemid; ?>" />
	<input type="hidden" name="layout"	value="<?php echo $layout; ?>" />
	<?php if($catids AND !isset($filters['catids'])): ?>
		<input type="hidden" name="catids"	value="<?php echo $catids; ?>" />
	<?php endif; ?>
	</div>
</form>
