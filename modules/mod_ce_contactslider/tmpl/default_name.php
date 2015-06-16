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

?>
<h<?php echo $item_heading; ?> class="ce-contact-name">
	<?php  
		$attributes	= array();
		$attributes['title']	= $contact->title;
		echo ($params->get( 'link_titles' ) ) 
			? modCEContactSliderHelper::createLink($link,$contact->title, $params, $attributes) 
			: $contact->title; 
	?>
</h<?php echo $item_heading; ?>>