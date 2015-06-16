<?php 
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_admin_ce_statistics
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<div class="row-striped">

<script type="text/javascript">
google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(statsusers);
function statsusers(){
	var data = google.visualization.arrayToDataTable([
	  ['Days', 'Emails']<?php
		foreach($list as $oneResult){
			echo ",\n ['".JHtml::_('date', $oneResult->submittedday, 'd/F')."',".intval(@$oneResult->total)."] ";
		}
		?>
	]);
	
	var options = {
	  	width:400,
		height: 300,
		legend:'none',
		hAxis: { format: 'dd MMM' }
	};
	
	var chart = new google.visualization.ColumnChart(document.getElementById('statsSubmittedForms'));
	chart.draw(data, options);
}
</script>
<div id="statsSubmittedForms" style="text-align:center;margin-bottom:20px;overflow:hidden;width:100%"></div>
		 
</div>