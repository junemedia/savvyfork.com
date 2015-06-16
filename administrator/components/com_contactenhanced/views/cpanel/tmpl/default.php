<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_cpanel
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$user = JFactory::getUser();
?>
<div class="row-fluid">
	<div class="span2">
		<div class="sidebar-nav">
<?php if(!empty( $this->sidebar)): ?>
	<div id="j-sidebar-container">
		<?php echo $this->sidebar; ?>
	</div>
<?php endif; ?>

		</div>
	</div>
	<div class="span6">
		<?php
		foreach ($this->modules as $module)
		{
			$output = JModuleHelper::renderModule($module, array('style' => 'well'));
			$params = new JRegistry;
			$params->loadString($module->params);
			echo $output;
		}
		?>
	</div>
	<div class="span4">
		<?php
		// Display the submenu position modules
		$this->iconmodules = JModuleHelper::getModules('ce-icon');
		foreach ($this->iconmodules as $iconmodule) {
			$output = JModuleHelper::renderModule($iconmodule, array('style' => 'well'));
			$params = new JRegistry;
			$params->loadString($iconmodule->params);
			echo $output;
		}
		?>
	</div>
</div>
