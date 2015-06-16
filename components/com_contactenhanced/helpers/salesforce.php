<?php
/**
 * @copyright	Copyright (C) 2006 - 2013 Ideal Custom Software Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @author 		Douglas Machado, based on the the work of: Jorge Guberte <shout@jorgeguberte.com>
 */

class SFWebtoLead{
	/**
	 * @var string $_oid
	 */
	private $_oid	= null;
	/**
	 * @var string
	 */
	private $_SFServletURL;
	private	$debug		= false;
	private $debugEmail	= null;

	function __construct($oid,$debug=0,$debugEmail=null){
		$this->_oid			= $oid; // OID do Salesforce
		$this->_SFServletURL= "http://www.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8";
		$this->debug		= $debug;
		$this->debugEmail	= $debugEmail;
	}

	function send($args){
		$outboundArgs = array('oid'=>$this->_oid);
		
		if($this->debug AND $this->debugEmail){
			$outboundArgs['debug']		= $this->debug;
			$outboundArgs['debugEmail']	= $this->debugEmail;
		}
		
		foreach ($args as $cf) {
			if($cf->alias == 'name'){
				$outboundArgs['first_name']=	stripslashes($cf->uservalue);
			}elseif($cf->alias == 'surname'){
				$outboundArgs['last_name']=	stripslashes($cf->uservalue);
			}else{
				$outboundArgs[$cf->alias]=	stripslashes($cf->uservalue);
			}
		}
	//	echo ceHelper::print_r($outboundArgs); exit;
		if(!$this->_curlSend($outboundArgs)){
			return False;
		}else{
			return True;
		}
	}


	private function _curlSend($outboundArgs){
		if(!function_exists('curl_init')){
			return false;
		}
		$ch = curl_init();
		if(curl_error($ch) != ""){
			return false;
		}

		try{
			curl_setopt($ch, CURLOPT_URL, $this->_SFServletURL);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($outboundArgs));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		}catch(Exception $e){
			return false;
		}

		try{
			$res = curl_exec($ch);
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($httpCode == '200'){
				return True;
			}else{
				return False;
			}
		}catch(Exception $e){
			return false;
		}
		return true;

	}
}