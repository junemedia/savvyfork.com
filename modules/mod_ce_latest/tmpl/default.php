<?php 
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_latest
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$document = JFactory::getDocument();
$document->addStylesheet(JURI::base(true).'/components/com_contactenhanced/assets/css/ce.css');

if(!class_exists('iBrowser')){
	require_once(JPATH_ROOT.'/components/com_contactenhanced/helpers/browser.php');
}

$ibrowser	= new iBrowser();
$mainframe	= JFactory::getApplication();

$tmplPath   = 'templates/'.$mainframe->getTemplate().'/';
$tmplimages = $tmplPath.'images/';
$modPath    = 'modules/mod_ce_latest/assets/images/';
?>

<div id="ce-latest-<?php echo $module->id;?>" class="mod-ce-latest clearfix" >
<?php 
if($params->get('introtext')){
	echo '<div class="ce-introtext">'.$params->get('introtext').'</div>';
}

if($params->get('show_total')){
	echo '<div class="mod-ce-latest-total">'.JText::sprintf($params->get('show_total-enabled-label', 'MOD_CE_LATEST_TOTAL_LABEL'), $total).'</div>';
}
?>
      <ul>
      		<?php 
      		require JModuleHelper::getLayoutPath('mod_ce_latest', 'default_items');
      		?>
      
      </ul>
<?php 
if($params->get('posttext')){
	echo  '<div class="ce-posttext">'.$params->get('posttext').'</div>';
}
?>
</div>
