<?php

include_once("functions.php");

$message = '';
$error = '';

if ($_POST['submit'] == 'Join') {
	$email = trim($_POST['email']);

	// Basic format check and MX lookup check is done as part of LookupImpressionWise and BullseyeBriteVerifyCheck functions...
	
	if (LookupImpressionWise($email) == false) {
		$error = "Invalid Email address";
	}
	if ($error == '') {
		if (BullseyeBriteVerifyCheck($email) == false) {
			$error = "Invalid Email address";
		}
	}
	
	if ($error != '') {
		$message = "<font style='color:red;font-size:12px;'>$error</font>";
	} else {
		// process sign up request...
		$user_ip = trim($_SERVER['REMOTE_ADDR']);
		$listid = '583,508';
		$subcampid = '3502';
		
		$sPostingUrl = "http://sf.popularliving.com/sf_api.php?email=$email&sublists=$listid&subcampid=$subcampid&ipaddr=$user_ip&keycode=ggjig592fkg785kscm8473&source=SFFooter";
		$response = strtolower(file_get_contents($sPostingUrl));
		
		$temp = "<img src='http://jmtkg.com/plant.php?email=$email' width=0 height=0></img>";
		$js = "<script>function dothework() {setTimeout(function(){parent.createCookie('nlcookie', true, 365);parent.hideItem('newsletter-signup-tray');parent.createCookie('nlcookie', true, 365);},3000);}dothework();</script>";
		
		setcookie("EMAIL_ID", $email, time()+642816000, "/", ".savvyfork.com");
		
		$pixel = "<!-- Google Tag Manager -->
				<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id=GTM-PFRPS8\"
				height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
				<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				})(window,document,'script','dataLayer','GTM-PFRPS8');
				dataLayer.push({'event': 'formsubscribesavvyfork'});</script>
				<!-- End Google Tag Manager -->";
		
		
		$message = "<font style='color:#F99C1C;font-size:12px;'>Success!</font>".$temp.$js.$pixel;
		$email = '';
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="background:#008000;" >
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
		<title>Mailing List Sign Up System</title>
		<link type="text/css" rel="stylesheet" href="css/default.css" />
		<script language="JavaScript">
			function check_fields() {
				document.signup.email.style.backgroundColor="";
				var str = '';
				var response = '';
			
				if (document.signup.email.value == '') {
					str += "Please enter your email address.";
					document.signup.email.style.backgroundColor="yellow";
				}
				
				if (str == '') {
					return true;
				} else {
					alert (str);
					return false;
				}
			}
		</script>
	</head>
	<body>
		 <form name="signup" id="signup" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return check_fields();">
		  <span>Get personalized recipe ideas and Savvy Fork news sent directly to you!</span>
				<span id="response">
					<?php echo $message; ?>
				  </span>
			  </label>
			  <input type="text" name="email" id="email" value="<?php echo $email; ?>" size="25" maxlength="80" />
			  <input type="submit"  name="submit" value="Join" class="btn" alt="Join" />
		</form>
	</body>
</html>
