<?php 
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_birthday
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
$modPath    = 'modules/mod_ce_birthday/assets/images/';
?>

<div id="ce-birthday-<?php echo $module->id;?>" class="ce-birthday clearfix" >
<?php 
if($params->get('introtext')){
	echo '<div class="ce-introtext">'.$params->get('introtext').'</div>';
}
?>
      <ul>
      		<?php 
      		require JModuleHelper::getLayoutPath('mod_ce_birthday', 'default_items');
      		?>
      
      </ul>
<?php 
if($params->get('posttext')){
	echo  '<div class="ce-posttext">'.$params->get('posttext').'</div>';
}
?>
</div>
