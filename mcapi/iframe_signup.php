<?php

include_once("functions.php");

$message = '';
$pixel = '';

if ($_POST['submit'] == 'Y') {
	$SOURCE = trim($_POST['SOURCE']);
	$email = trim($_POST['email']);
	$site = trim($_POST['site']);
	
	if (!ctype_alnum($SOURCE)) { $SOURCE = ''; }
	
	// MX Lookup and Basic check is done at below two functions...
	if (LookupImpressionWise($email) == false) {
		$message = 'Email address is invalid';
	}
	if ($message == '') {
		if (BullseyeBriteVerifyCheck($email) == false) {
			$message = 'Email address is invalid';
		}
	}
	
	if ($message == '') {
		// process sign up request...
		$user_ip = trim($_SERVER['REMOTE_ADDR']);
		$listid = '583,508';
		
		$subcampid = '3676';	// use default subcampid
		if (in_array(strtolower($SOURCE),array('main','drinks','dessertdis','appetizerdisplay','cakedisplay','display','cake','desserts','quinoa','pie','fish','meatball','pesto','brownie','chili','seafood','appetizers','home'))) {
			$subcampid = '3758';	// this is for Google, for now, it is same as default.
		}
		
		if (in_array(strtolower($SOURCE),array('appetizersbing','cakebing','homebing','dessertsbing'))) {
			$subcampid = '3570';	// this is for Bing.
		}
		
		$sPostingUrl = "http://sf.popularliving.com/sf_api.php?email=$email&sublists=$listid&subcampid=$subcampid&ipaddr=$user_ip&keycode=ggjig592fkg785kscm8473&source=SFDhtml".$SOURCE;
		$response = strtolower(file_get_contents($sPostingUrl));

		$message = 'Success! Check your email to confirm sign up.';
		setcookie("EMAIL_ID", $email, time()+642816000, "/", ".savvyfork.com");
		$plant_cookie = "<img src='http://jmtkg.com/plant.php?email=$email' width='0' height='0'></img>";
		$email = '';
		$pixel = "<img src='http://sf.popularliving.com/subctr/forms/stats.php?a=s&f=SavvyForkDhtml$SOURCE' width='0' height='0' border='0' />";
	}
} else {
	$SOURCE = trim($_GET['SOURCE']);
	$email = trim($_GET['email']);
	$site = trim($_GET['site']);
	
	if ($email == '') { $email = trim($_COOKIE['EMAIL_ID']); }
	
	if (!ctype_alnum($SOURCE)) { $SOURCE = ''; }
	if (!eregi("^[A-Za-z0-9\._-]+[@]{1,1}[A-Za-z0-9-]+[\.]{1}[A-Za-z0-9\.-]+[A-Za-z]$", $email)) { $email = ''; }
	list($prefix, $domain) = split("@",$email);
	if (!getmxrr($domain, $mxhosts)) { $email = ''; }
	
	$pixel = "<img src='http://sf.popularliving.com/subctr/forms/stats.php?a=d&f=SavvyForkDhtml$SOURCE' width='0' height='0' border='0' />";
}


if (!ctype_alnum($site)) { $site = ''; }

switch (strtoupper($site)) {
	case 'FF':
		$text = 'FitandFabLiving members, subscribe with one click!';
		break;
	case 'R4L':
		$text = 'Recipe4Living members, subscribe with one click!';
		break;
	case 'WIM':
		$text = 'Work It, Mom! members, subscribe with one click!';
		break;
	default:
		$text = '';
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
		#response {color:yellow; font-style:italic; font-size:12px;width:300px;border:none;}
		</style>
	</head>
	<body style="background-image:url('/images/SavvyFork-Subscribe-Overlay-0.png');background-repeat:no-repeat;padding-top:130px;">
		 <form id="signup" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" style="padding-left:25px;">
	 		<input type="hidden" name="SOURCE" id="SOURCE" value="<?php echo $SOURCE; ?>">
	 		<input type="hidden" name="site" id="site" value="<?php echo $site; ?>">
	 		<input type="hidden" name="submit" id="submit" value="Y">
			<span id="response">
			<?php
				echo $message;
				echo $pixel;
				if (strstr($message,'Success')) {
					if (in_array(strtolower($SOURCE),array('main','drinks','dessertdis','display','cakedisplay','appetizerdisplay','cake','desserts','quinoa','pie','fish','meatball','pesto','brownie','chili','seafood','appetizers','home'))) { ?>
						<!-- Google Code for Sign-Ups Conversion Page -->
						<script type="text/javascript">
						/* <![CDATA[ */
						var google_conversion_id = 980083667;
						var google_conversion_language = "en";
						var google_conversion_format = "2";
						var google_conversion_color = "ffffff";
						var google_conversion_label = "gdV-CP2dvwcQ08er0wM";
						var google_conversion_value = 0;
						var google_remarketing_only = false;
						/* ]]> */
						</script>
						<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
						</script>
						<noscript>
						<div style="display:inline;">
						<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/980083667/?value=0&amp;label=gdV-CP2dvwcQ08er0wM&amp;guid=ON&amp;script=0"/>
						</div>
						</noscript>
					<?php }
					echo $plant_cookie;
					echo '<iframe src="http://www.savvyfork.com/mcapi/dhtml-thankyou.html" width="1" height="1" frameborder="0" scrolling="No"></iframe>';
					echo "<script>setTimeout(function(){parent.closethis();parent.createCookie('nlcookie', true, 365);parent.hideItem('newsletter-signup-tray');parent.createCookie('nlcookie', true, 365);},1000);</script>";
				}
			?>
			</span><br>
			  <table>
			  	<tr>
			  		<td colspan="2" style="color:white; font-weight:bold;font-size:10px;width:300px;border:none;"><?php echo $text; ?></td>
			  	</tr>
			  	<tr>
			  		<td><input type="text" name="email" id="email" size="25" maxlength="80" value="<?php echo $email; ?>" onfocus="if(this.value=='Your Email')this.value=''" onblur="if(this.value=='')this.value='Your Email'" /></td>
			  		<td><INPUT style="align:left;width:95px;height:20px;border:none;" TYPE="image" SRC="/images/subscribe_btn.png" BORDER="0" ALT="Submit Form" /></td>
			  	</tr>
			  </table>
		</form>
		<script>
			if (document.getElementById('email').value == '') { document.getElementById('email').value='Your Email'; }
		</script>
	</body>
</html>
