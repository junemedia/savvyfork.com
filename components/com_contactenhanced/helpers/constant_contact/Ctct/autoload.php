<?php
require_once('SplClassLoader.php');


// Load the Ctct namespace
$loader = new \Ctct\SplClassLoader('Ctct', dirname(__DIR__));
$loader->register();

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Components\Contacts\CustomField;
use Ctct\Components\Contacts\Address;
use Ctct\Exceptions\CtctException;

function ctctGetLists($key, $token, $list =null){
	$ctct = new ConstantContact($key);
	$listDetails	= $ctct->getLists($token);
	return $listDetails;
}

function ctctGetObject($key){
	$obj = new ConstantContact($key);
	return $obj;
}

function ctctGetContactObject(){
	$obj = new Contact();
	return $obj;
}
function ctctGetContactEmailObject($email, $params){
	$props	= array();
	$props['status']			= 'UNCONFIRMED';
	$props['confirm_status']	= 'UNCONFIRMED';
	$props['opt_in_source']		= 'ACTION_BY_VISITOR';
	$props['email_address']		= $email;
	
	$obj = new EmailAddress();
	return $obj->create($props);
}
function ctctCreateCustomField($field){
	$ctct_cm	= new CustomField();
	$ctct_cm->name 	= $field->name;
	if(count($field->arrayFieldElements) > 1){
		$ctct_cm->value 	= implode(', ',$field->uservalue);
	}else{
		$ctct_cm->value		= (string)$field->uservalue;
	}
	$ctct_cm->value = substr(strip_tags($ctct_cm->value), 0,50);
	//echo $ctct_cm->value; exit;
	return $ctct_cm;
}
function ctctCreateAddress($field){
	$address	= new Address();
	return $address->create($field);
}
