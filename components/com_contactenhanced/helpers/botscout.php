<?php
/**
 * @copyright   Copyright (C) 2006 - 2013 idealextensions.com. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

class ceBotscout extends jObject{
	var $apiKey = '';
	var $conn;
	public function __construct($apiKey)
	{
		$this->apiKey	= $apiKey;
	}
	public function getInfo($email, $ip='') {
		$result = true;
		
		$object					= new stdClass();
		$object->IP				= ($ip ? $ip : ceHelper::getIP() );
		$object->emailAddress	= $email;
		
		if (!empty($object->IP) && $object->IP != '127.0.0.1') {
			$data = 'ip=' . $object->IP;
			$resIP = $this->sendInfo($data);
			$result = $this->checkXML($resIP,$object) && $result;
		}
		if (!empty($object->emailAddress)) {
			$data = 'mail=' . $object->emailAddress;
			$resAddress = $this->sendInfo($data);
			$result = $this->checkXML($resAddress,$object) && $result;
		}
		if (is_resource($this->conn)) fclose($this->conn);
		return $result;
	}
	public function sendInfo($data) {
		if (!empty($this->apiKey)) $data .= '&key=' . $this->apiKey;
		$data .= '&format=xml';
		$cookies		= array();
		$custom_headers	= array();
		$timeout		= 1000;
		$result 	= ceHelper::http_request(
				'GET',
				'http://www.botscout.com',
				80,
				'/test/?'.$data,
				array(),
				array(),
				$cookies,
				$custom_headers,
				$timeout //
				,false
				,false
		);

		return $result;
	}
	public function checkXML($res,$object) {
		if(!preg_match('#<response>.*</response>#Uis',$res,$results)){
			throw new Exception(JText::_('There is an error while trying to get the xml could not find "&lt;response&gt;"'));
			return false;
		}
		$xml = new SimpleXMLElement($results[0]);
		if ($xml->matched == "Y" && $xml->test == 'IP'){
			// Check failed. Result indicates dangerous.
			throw new Exception(JText::sprintf('There is a problem with the IP: %s you used to do the registration ( Spam test positive )',$object->IP));
			return false;
		}
		if ($xml->matched == "Y" && $xml->test == 'MAIL'){
			throw new Exception(JText::sprintf('There is a problem with the email: %s you entered in the form ( Spam test positive )', $object->emailAddress));
			return false;
		}
		return true;
	}

}