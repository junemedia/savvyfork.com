<?php
/**
 * @package    com_contactenhanced
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 24-Feb-2011
 */

//-- No direct access
defined('_JEXEC') or die('Access Denied - Please do not try to fool me! ;-)');
if($params->get('load_css',1)){
	$doc	= JFactory::getDocument();
	$doc->addStyleSheet('modules/mod_ce_alphaindex/assets/css/mod_ce_alphaindex.css');
}
?>
<div class="mod_ce_alphaindex<?php echo $moduleclass_sfx ?>">
	<div class="mod_ce_alphaindex-2">
		<div class="mod_ce_alphaindex-3">
		<?php
			foreach ($letters as $letter) {
				echo modCEAlphaIndexHelper::getLink($letter, $usedLetters, $params, $mitemid);
			}
		?>
		</div>
	</div>
</div>