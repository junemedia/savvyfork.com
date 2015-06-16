<?php

include_once("functions.php");

$subcampid = '4325';	// SF_R4L CrossPromo_0615
$listid = '583,508';
$message = '';
if ($_POST['submit'] == 'Y') {
	$email = trim($_POST['email']);
	
	// process sign up request...
	$user_ip = trim($_SERVER['REMOTE_ADDR']);
		
		
	$sPostingUrl = "http://sf.popularliving.com/sf_api.php?email=$email&sublists=$listid&subcampid=$subcampid&ipaddr=$user_ip&keycode=ggjig592fkg785kscm8473&source=SFSqueeze";
	$response = strtolower(file_get_contents($sPostingUrl));

	$message = 'success';
	setcookie("EMAIL_ID", $email, time()+642816000, "/", ".savvyfork.com");
	$plant_cookie = "<img src='http://jmtkg.com/plant.php?email=$email' width='0' height='0'></img>";
	$email = '';
	$pixel = "<img src='http://sf.popularliving.com/subctr/forms/stats.php?a=s&f=SFSqueeze$subcampid' width='0' height='0' border='0' />";
} else {
	$email = trim($_GET['email']);
	
	if (!eregi("^[A-Za-z0-9\._-]+[@]{1,1}[A-Za-z0-9-]+[\.]{1}[A-Za-z0-9\.-]+[A-Za-z]$", $email)) { $email = ''; }
	list($prefix, $domain) = split("@",$email);
	if (!getmxrr($domain, $mxhosts)) { $email = ''; }
	
	$pixel = "<img src='http://sf.popularliving.com/subctr/forms/stats.php?a=d&f=SFSqueeze$subcampid' width='0' height='0' border='0' />";
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"  >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<title>Mailing List Sign Up System</title>
		<style>
		body {text-align:left;margin-left:auto;margin-right:auto;}
		* {margin:0; padding:0; font:10px Helvetica,sans-serif; color:#333; border:none;width:300px;}
		input {padding:.1em; width:142px; font-size:1.3em;border:none;}
		/*#response {color:yellow; font-style:italic; font-size:12px;width:300px;border:none;}*/
		</style>
	</head>
	<body style="background-image:url('http://pics.savvyfork.com/squeeze/chocolate.png');background-repeat:no-repeat;padding-top:130px;">
		 <form id="signup" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" style="padding-left:25px;">
	 		<input type="hidden" name="email" id="email" value="<?php echo $email; ?>">
	 		<input type="hidden" name="submit" id="submit" value="Y">
			<INPUT style="position:relative;left:129px;top:190px;width:124px;height:50px;border:none;" TYPE="image" SRC="http://pics.savvyfork.com/squeeze/button.png" BORDER="0" ALT="Submit Form" />
		</form>
		<span id="response">
		<?php
			echo $pixel;
			if (strstr($message,'success')) {
				echo $plant_cookie;
				echo "<script>setTimeout(function(){parent.closethis();parent.createCookie('nlcookie', true, 365);parent.hideItem('newsletter-signup-tray');parent.createCookie('nlcookie', true, 365);},1000);</script>";
			}
		?>
		</span>
	</body>
</html>
