<?php

include_once("functions.php");

$message = '';
$pixel = '';

if (isset($_POST['submit']) && $_POST['submit'] == 'Enter to Win!') {
	$SOURCE = trim($_POST['SOURCE']);
	$EMAIL = trim($_POST['EMAIL']);
	$FNAME = trim($_POST['FNAME']);
	
	if ($EMAIL == '') {
		$message = 'Email address is invalid';
	}
	
	if (!ctype_alnum($SOURCE)) { $SOURCE = ''; }
	if (strlen($SOURCE) > 50) { $SOURCE = ''; }
	
	// MX Lookup and Basic email check is done at below two functions...
	if (LookupImpressionWise($EMAIL) == false) {
		$message = "Email address is invalid";
	}
	if (BullseyeBriteVerifyCheck($EMAIL) == false) {
		$message = "Email address is invalid";
	}
	
	if ($message == '') {
		$user_ip = trim($_SERVER['REMOTE_ADDR']);
		$listid = '583,508';
		
		switch (strtolower($SOURCE)) {
			case 'adjump':
				$subcampid = '3505';
				break;
			case 'facebookad':
				$subcampid = '3719';	// facebook
				break;
			case 'twitterpost':
				$subcampid = '3508';
				break;
			case 'googlegiveaway':
				$subcampid = '3782';	// google
				break;
			case 'cake1':
				$subcampid = '3606';
				break;
			default:
				$subcampid = '4344';	// SF Default Giveaway 0615
		}
		
		$fire_cake_pixel = "";
		// check for dupes before signing up...
		$dupes_response = strtoupper(file_get_contents("http://r4l.popularliving.com/check_record.php?email=$EMAIL&type=emailpluslistid&listid=$listid"));
		if (strstr($dupes_response, 'TRUE')) {
			$fire_cake_pixel = "<iframe src='http://sinettrk.com/p.ashx?o=13333&t=$EMAIL' height='1' width='1' frameborder='0'></iframe>";
		}
		
		$sPostingUrl = "http://sf.popularliving.com/sf_api_giveaway.php?email=$EMAIL&sublists=$listid&subcampid=$subcampid&ipaddr=$user_ip&keycode=ggjig592fkg785kscm8473&fname=$FNAME";
		$response = strtolower(file_get_contents($sPostingUrl));
		
		setcookie("EMAIL_ID", $EMAIL, time()+642816000, "/", ".savvyfork.com");
		
		
		$gtm_pixel = "<!-- Google Tag Manager -->
				<noscript><iframe src=\"//www.googletagmanager.com/ns.html?id=GTM-PFRPS8\"
				height=\"0\" width=\"0\" style=\"display:none;visibility:hidden\"></iframe></noscript>
				<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
				new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
				j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
				'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
				})(window,document,'script','dataLayer','GTM-PFRPS8');
				dataLayer.push({'event': 'giveawaysavvyfork'});</script>
				<!-- End Google Tag Manager -->";
		
		$message = "Success!"."<img src='http://jmtkg.com/plant.php?email=$EMAIL' width=0 height=0></img>".'<iframe frameborder="0" width="1" height="1" src="http://www.savvyfork.com/mcapi/giveaway-thankyou.html"></iframe>'.$fire_cake_pixel.$gtm_pixel;
		$style = "color:green;font-weight:bold;";
		$EMAIL = '';
		$FNAME = '';
		$LNAME = '';
		$pixel = "<img src='http://sf.popularliving.com/subctr/forms/stats.php?a=s&f=SavvyForkGiveaway$SOURCE' width='0' height='0' border='0' />";
	} else {
		$style = "color:red;font-weight:bold;";
	}
} else {
    $SOURCE = "";
	if(isset($_GET['SOURCE']))$SOURCE = trim($_GET['SOURCE']);
	if (!ctype_alnum($SOURCE)) { $SOURCE = ''; }
	if (strlen($SOURCE) > 50) { $SOURCE = ''; }
	
	$pixel = "<img src='http://sf.popularliving.com/subctr/forms/stats.php?a=d&f=SavvyForkGiveaway$SOURCE' width='0' height='0' border='0' />";
}

if (date('m') == 03 || date('m') == 04) {
	$giveaway_title = "Win A Pressure Oven From Wolfgang Puck!";
	$giveaway_text = "At SavvyFork, we provide you with the most elegant and delicious recipes. Not only do we partner with the best food bloggers around, we like to give away the best kitchen items. Just sign up to join the cool table and get our newsletter, The Feed, and you'll also be entered to win a pressure oven from Wolfgang Puck. Yes, the Wolfgang Puck. Entries will be accepted until April 30, 2014 at 11:59 PM CST. Good luck!";
	$giveaway_top_img = "http://pics.recipe4living.com/giveaway/pressureoven2_main.jpg";
	$giveaway_right_img = "http://pics.recipe4living.com/giveaway/pressureoven2_small_img.png";
	$giveaway_extra_right_img = '<br><br><img src="http://pics.recipe4living.com/giveaway/pressureoven2_logo.jpg" width="50%">';
}

if (date('m') == 05 || date('m') == 06) {
	$giveaway_title = "";
	$giveaway_text = "This giveaway is perfect for any home cook who is looking to make fresh dinners with amazing ingredients, but wishes they had a little more time. Hello Fresh makes home cooking easy, healthy and delicious by delivering meals right to your door, complete with simple recipes and all of the ingredients you need. One lucky winner will win two weeks of Hello Fresh meals. Check out HelloFresh.com to see the options! To thank you for entering the giveaway, Hello Fresh is also offering a special. Get two 2 Free Meals off your first delivery by entering the code GET2 at checkout. You have until June 15th to enter. Good luck!";
	$giveaway_top_img = "http://pics.savvyfork.com/giveaway/HelloFresh-Recipe-Box-VERY-SHARP.jpg";
	$giveaway_right_img = "http://pics.savvyfork.com/giveaway/HelloFresh-Recipe-Box-VERY-SHARP.jpg";
	$giveaway_extra_right_img = '<br><br><img src="http://pics.savvyfork.com/giveaway/HelloFresh-Recipe-Box-VERY-SHARP.jpg" width="50%">';
}

if (date('m') == 07) {
	$giveaway_title = "Win A Campbell's BRAND NEW skillet sauces!";
	$giveaway_text = "This month's SavvyFork giveaway is a whole bunch of Campbell's BRAND NEW skillet sauces! Just sign up for our newsletter, The Feed, and you'll also be entered to win! Entries will be accepted until July 31, 2014 at 11:59 PM Central. Good luck!";
	$giveaway_top_img = "images/Campbells-banner.png";
	$giveaway_right_img = "images/Campbells-product.png";
	$giveaway_extra_right_img = '<br><br><img src="images/Campbells-Logo.png" width="50%">';
}

if (date('m') == 8) {
	$giveaway_title = "Win A Prize Pack From The Folks Of Splenda!";
	$giveaway_text = "This month's SavvyFork giveaway is a prize pack from the folks of Splenda, including a $50 VISA giftcard! Just sign up for our newsletter, The Feed, and you'll also be entered to win! Entries will be accepted until August 30, 2014 at 11:59 PM Central. Good luck!";
	$giveaway_top_img = "http://pics.savvyfork.com/giveaway/Splenda_Banner.jpg";
	$giveaway_right_img = "http://pics.savvyfork.com/giveaway/Splenda_Product.png";
	$giveaway_extra_right_img = '<br><br><img src="http://pics.savvyfork.com/giveaway/Splenda_logo.png" width="50%">';
}

if (date('m') == 9 || date('m') == 10) {
	$giveaway_title = "Win A Blendtec Designer 625 Blender!";
	$giveaway_text = "This month's SavvyFork giveaway is a Blendtec Designer 625 Blender! Just sign up for our newsletter, The Feed, and you'll also be entered to win! Entries will be accepted until October 31, 2014 at 11:59 PM Central. Good luck!";
	$giveaway_top_img = "http://pics.savvyfork.com/giveaway/Blendtec_Banner.png";
	$giveaway_right_img = "http://pics.savvyfork.com/giveaway/Blendtec_product.png";
	$giveaway_extra_right_img = '<br><br><img src="http://pics.savvyfork.com/giveaway/blendtec_logo.png" style="max-width:360px;">';
}

if (date('m') == 11 || date('m') == 12) {
	$giveaway_title = "Win the NEW iCoffee Single Serve!";
	$giveaway_text = "The iCoffee Single Serve uses all your favorite K-Cups (including Keurig 2.0) and iCoffee’s signature SpinBrew technology, which helps eliminate the bitter aftertaste. Enter to win this top-of-the-line coffee maker by signing up for our newsletter below. Entries will be accepted until December 31st 2014 at 11:59 PM Central. Good luck!";
	$giveaway_top_img = "";
	$giveaway_right_img = "http://pics.savvyfork.com/giveaway/icoffee.jpg";
	$giveaway_extra_right_img = '';
}

if (date('m') == "03" || date('m') == "04") {
    $giveaway_title = "Savvyfork Giveaways - Coming Soon!";
    $giveaway_text = "Looking for the SavvyFork food and kitchen appliance giveaway? Well, we're currently working on our next great freebie that only requires you to enter your email address! In the meantime, feel free to message us on social media and let us know what you want to see! A KitchenAid stand mixer? A Le Creuset French oven? Some more Omaha Steaks? Some other fancy kitchen gadget or new gourmet food you'd like to try? We'd love to hear it! Keep checking the SavvyFork giveaway page, we'll have another high-quality giveaway for all you food lovers soon. In the meantime, sign up for The Feed by SavvyFork and keep up to date with all the fantastic quick and easy recipes we're cooking up!";
    $giveaway_top_img = "";
    $giveaway_right_img = "http://pics.savvyfork.com/giveaway/icoffee.jpg";
    $giveaway_extra_right_img = '';
}

?>
<html>
<head>
<title>SavvyFork.com Giveaway | <?php echo $giveaway_title; ?></title>
<script language="JavaScript">
function check_fields() {
	if (document.getElementById('EMAIL').value == '') {
		alert ("* Please enter your email address.\n");
		return false;
	}
	if (document.getElementById('AGREE').checked == false) {
		alert ("* You must agree to terms and conditions.\n");
		return false;
	}
	return true;
}
</script>
<style>
* {
	font: 12px Arial, Helvetica, sans-serif;
	line-height: 1.25em; /* = 20px */
	color: #4e4e4e;
}
</style>
</head>
<body>
<table width="750px">
<tr>
	<td colspan="2">
		<h2 style="font-size:20px;height:27px;text-aling:left;padding-left:10px;padding-top:5px;"><?php echo $giveaway_title; ?></h2>
		<?php if($giveaway_top_img !=''){?>
		<p><img src="<?php echo $giveaway_top_img; ?>" style="max-width:730px;"></p>
		<?php }?>
		<p style="font: 12px Arial, Helvetica, sans-serif;padding-left:10px;max-width:360px;">
			<?php echo $giveaway_text; ?>
		</p>
	</td>
</tr>
<tr>
	<td valign="top" align="left">
	<!-- form starts -->
			<!-- Begin MailChimp Signup Form -->
           
			<link href="//cdn-images.mailchimp.com/embedcode/classic-081711.css" rel="stylesheet" type="text/css">
			<style type="text/css">
				#mc_embed_signup{background:#E8E8E8; clear:left; font:14px Helvetica,Arial,sans-serif;  width:300px;}
				/* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
				   We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
				#mc_embed_signup .asterisk {color:#c60; font-size:125%;}
			</style>
			<div id="mc_embed_signup">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<input type="hidden" name="SOURCE" id="SOURCE" value="<?php echo $SOURCE; ?>">
			<div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
			<div class="mc-field-group">
				<label for="mce-FNAME">First Name  
			</label>
				<input type="text" value="<?php echo $FNAME; ?>" name="FNAME" class="required" id="FNAME" maxlength="25">
			</div>
			
			<div class="mc-field-group">
				<label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>
			</label>
				<input type="email" value="<?php echo $EMAIL; ?>" name="EMAIL" id="EMAIL">
			</div>
			<div class="mc-field-group input-group">
			    <strong></strong>
			    <ul><li><input type="checkbox" value="1" name="AGREE" id="AGREE"><label for="AGREE">
			    I understand that by subscribing, I will also receive special offers from third party partners, and agree to SavvyFork's 
					<a href="/index.php/terms-of-use" target="_blank">Terms of Use</a>, and <a href="/index.php/privacy-policy" target="_blank">Privacy Policy</a>.
			    </label></li>
			</ul>
			</div>
				<div id="mce-responses" class="clear">
					<div style="<?php echo $style; ?>"><?php echo $message; echo $pixel; ?><br><br></div>
				</div>	<div class="clear"><input type="submit" value="Enter to Win!" name="submit" id="mc-embedded-subscribe" class="button" onclick="return check_fields();"></div>
			</form>
			</div>
           
			<!--End mc_embed_signup-->
	<!-- form ends -->
	</td>
    <!--
	<?php if(date('m') == 11 || date('m') == 12){?>
	<td align="center" valign="top"><img src="<?php echo $giveaway_right_img; ?>" style="max-width:209px;"><?php echo $giveaway_extra_right_img; ?></td>
	<?php }else{?>
	<td align="center" valign="top"><img src="<?php echo $giveaway_right_img; ?>" style="max-width:287px;"><?php echo $giveaway_extra_right_img; ?></td>
	<?php }?>
    -->
</tr>
</table>
</body>
</html>
