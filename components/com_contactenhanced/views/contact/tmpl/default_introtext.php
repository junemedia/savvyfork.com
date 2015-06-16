<?php
/**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Joomla
 * @author		Douglas Machado {@link http://idealextensions.com}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die; 

if($this->params->get('show_introtext')): ?>

	<div class="ce-intro-text" id="ce-intro-text">
		<?php
			$introtext	= $this->params->get('show_introtext');
			echo $this->contact->$introtext;
		?>
	</div>
<?php 
endif;
