<?php
function LookupImpressionWise($email_addr) {
	if (!eregi("^[A-Za-z0-9\._-]+[@]{1,1}[A-Za-z0-9-]+[\.]{1}[A-Za-z0-9\.-]+[A-Za-z]$", $email_addr)) {
		return false;
	}
	list($prefix, $domain) = split("@",$email_addr);
	if (!getmxrr($domain, $mxhosts)) {
		return false;
	}
	
	$isValid = true;
	$isValid_msg = 'Y';
	$sPostingUrl = "http://post.impressionwise.com/fastfeed.aspx?code=560020&pwd=SilCar&email=$email_addr";
	$response = strtolower(file_get_contents($sPostingUrl));
	
	//	code=560020&pwd=SilCar&email=testme@impressionwise.com&result=Key&NPD=NA&TTP=0.16
	$pieces = explode("&", $response);
	foreach ($pieces as $pair) {
		$data = explode("=", $pair);
		$$data[0] = $data[1];
	}
	
	if (in_array($result, array("invalid", "seed", "trap", "mole"))) {
		$isValid = false;
		$isValid_msg = 'N';
	}
	
	if ($result == 'retry') {
		mail('samirp@silvercarrot.com','Impression Wise RETRY',$sPostingUrl."\n\n\n".$response);
	}
	
	return $isValid;
}

/*function BullseyeBriteVerifyCheck ($email) {
	if (!eregi("^[A-Za-z0-9\._-]+[@]{1,1}[A-Za-z0-9-]+[\.]{1}[A-Za-z0-9\.-]+[A-Za-z]$", $email)) {
		return false;
	}
	list($prefix, $domain) = split("@",$email);
	if (!getmxrr($domain, $mxhosts)) {
		return false;
	}
	
	$handle = fopen("http://www3.tendollars.com/BriteVerifyForSubscriptionCenter.aspx?email=$email&source=subcenter", "rb");
	$server_response = stream_get_contents($handle);
	fclose($handle);
	
	if (strstr($server_response,'valid') || strstr($server_response,'unknown')) {
		$return_value = true;
	} else {
		$return_value = false;
	}
	
	if (strstr($server_response,'not valid') || strstr($server_response,'invalid')) {
		$return_value = false;
	}
	
	return $return_value;
}*/

function BullseyeBriteVerifyCheck ($email) {
	if (!eregi("^[A-Za-z0-9\._-]+[@]{1,1}[A-Za-z0-9-]+[\.]{1}[A-Za-z0-9\.-]+[A-Za-z]$", $email)) {
		return false;
	}
	list($prefix, $domain) = split("@",$email);
	if (!getmxrr($domain, $mxhosts)) {
		return false;
	}
	
	$emailInfo = array();
	if(!empty($email))
	{
		mysql_pconnect ("192.168.51.75", "root", "6fsi539T");
		mysql_select_db ("savvyfork_live");
		$result = mysql_query("SELECT * FROM email_validation WHERE email = \"$email\"");
		$emailInfo = mysql_fetch_array($result,MYSQL_ASSOC);
		if (empty($emailInfo)) {
			$url = "https://bpi.briteverify.com/emails.json?address=$email&apikey=ad6d5755-ff3e-4a0b-8d63-c61bcffd57b1";
			$content = file_get_contents($url);
			$emailInfo = json_decode($content, true);
			
			$ipaddress = $_SERVER['REMOTE_ADDR'];
			
			if(!empty($emailInfo))
			{
				//Cache the new email address
				$sql = 'INSERT IGNORE INTO email_validation (email,status,error_code,error,dateAdded,ipaddress) VALUES ("'.$emailInfo["address"].'","'.$emailInfo["status"].'","'.$emailInfo["error_code"].'", "'.$emailInfo["error"].'", NOW(),"'.$ipaddress.'")';
				$result = mysql_query($sql);
			}
		} 
	}
	
	if(!empty($emailInfo) && ($emailInfo["status"]=="valid" || $emailInfo["status"]=="unknown" || $emailInfo["status"]="accept all"))
	{
		return true;
	}
	else
	{
		return false;
	}
}



?>
