<?php
/*///////////////////////////////////////////////////////////////////////
Part of the code from the book 
Building Findable Websites: Web Standards, SEO, and Beyond
by Aarron Walter (aarron@buildingfindablewebsites.com)
http://buildingfindablewebsites.com

Distrbuted under Creative Commons license
http://creativecommons.org/licenses/by-sa/3.0/us/
///////////////////////////////////////////////////////////////////////*/


function storeAddress(){
	
	// Validation
	if(!$_GET['email']){ return "Enter email address"; }

	if(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $_GET['email'])) {
		return "Email address is invalid"; 
	}
	
	list($user, $domain) = split('@', $_GET['email']);
	if(!checkdnsrr($domain, 'MX')) {
		return "Email is invalid"; 
	}

	require_once('MCAPI.class.php');
	// grab an API Key from http://admin.mailchimp.com/account/api/
	$api = new MCAPI('b52ab9e3b5a9b3468ef6e0d59b30a45f-us7');
	
	// grab your List's Unique Id by going to http://admin.mailchimp.com/lists/
	// Click the "settings" link for the list - the Unique Id is at the bottom of that page. 
	$list_id = "1548e8ac73";

	if($api->listSubscribe($list_id, $_GET['email'], '') === true) {
		// It worked!
		setcookie("EMAIL_ID", $_GET['email'], time()+642816000, "/", ".savvyfork.com");	
		$temp = "<img src='http://jmtkg.com/plant.php?email=".$_GET['email']."' width=0 height=0'></img>";
		return 'Success! Check your email to confirm sign up.'."$temp<script>function dothework() {setTimeout(function(){parent.createCookie('nlcookie', true, 365);parent.hideItem('newsletter-signup-tray');parent.createCookie('nlcookie', true, 365);},3000);}dothework();</script>";
	}else{
		// An error ocurred, return error message	
		return 'Error: ' . $api->errorMessage;
	}
	
}

// If being called via ajax, autorun the function
if($_GET['ajax']){ echo storeAddress(); }
?>
