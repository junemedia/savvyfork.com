<?php
 define('PHPVER', phpversion());define('CM_PHP_WRAPPER_VERSION', '1.4.9');define( 'SOCKET_TIMEOUT', 1);class CMBase { var $api = '' , $campaign_id = 0 , $client_id = 0 , $list_id = 0;var $method = 'get' , $url = '' , $soapAction = '' , $curl = true , $curlExists = true;var $debug_level = 0 , $debug_request = '' , $debug_response = '' , $debug_url = '' , $debug_info = array() , $show_response_headers = 0;function CMBase( $api = null, $client = null, $campaign = null, $list = null, $method = 'get' ){$this->api = $api; $this->client_id = $client; $this->campaign_id = $campaign; $this->list_id = $list; $this->method = $method; $this->curlExists = function_exists( 'curl_init' ) && function_exists( 'curl_setopt');}function makeCall( $action = '', $options = array() ){$old_method = $this->method; if ( $action == 'Subscriber.AddWithCustomFields' || $action == 'Subscriber.AddAndResubscribeWithCustomFields' || $action == 'Campaign.Create') $this->method = 'soap'; if ( !$action ) return null; $url = $this->url; if ( !isset( $options['header'] ) ) $options['header'] = array();$options['header'][] = 'User-Agent: CMBase URL Handler ' . CM_PHP_WRAPPER_VERSION; $postdata = ''; $method = 'GET'; if ( $this->method == 'soap' ){$options['header'][] = 'Content-Type: text/xml; charset=utf-8'; $options['header'][] = 'SOAPAction: "' . $this->soapAction . $action . '"'; $postdata = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"; $postdata .= "<soap:Envelope xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\""; $postdata .= " xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\""; $postdata .= " xmlns:soap=\"http://schemas.xmlsoap.org/soap/envelope/\">\n"; $postdata .= "<soap:Body>\n"; $postdata .= "	<{$action} xmlns=\"{$this->soapAction}\">\n"; $postdata .= "		<ApiKey>{$this->api}</ApiKey>\n"; if ( isset( $options['params'] ) ) $postdata .= $this->array2xml( $options['params'], "\t\t");$postdata .= "	</{$action}>\n"; $postdata .= "</soap:Body>\n"; $postdata .= "</soap:Envelope>"; $method = 'POST'; } else { $postdata = "ApiKey={$this->api}"; $url .= "/{$action}"; if ( isset( $options['params'] ) ){foreach ( $options['params'] as $k => $v ) $postdata .= '&' . $k . '=' .rawurlencode($this->fixEncoding($v));}if ( $this->method == 'get' ){$url .= '?' . $postdata; $postdata = ''; } else { $options['header'][] = 'Content-Type: application/x-www-form-urlencoded'; $method = 'POST'; } } $res = ''; if ( $this->curl && $this->curlExists ){$ch = curl_init();if ( $this->method != 'get' ){curl_setopt( $ch, CURLOPT_POST, 1);curl_setopt( $ch, CURLOPT_POSTFIELDS, $postdata);}curl_setopt( $ch, CURLOPT_URL, $url);curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);curl_setopt( $ch, CURLOPT_HTTPHEADER, $options['header']);curl_setopt( $ch, CURLOPT_HEADER, $this->show_response_headers);$res = curl_exec( $ch);if ( $this->debug_level ){$this->debug_url = $url; $this->debug_request = $postdata; $this->debug_info = curl_getinfo( $ch);$this->debug_info['headers_sent'] = $options['header']; } $this->debug_response = $res; curl_close( $ch);}else { $postLen = strlen( $postdata);$ctx = array('method' => $method , 'header' => implode( "\n", $options['header'] ) . "\nContent-Length: " . $postLen . "\n\n" . $postdata);if ( $this->debug_level ){$this->debug_url = $url; $this->debug_request = $postdata; $this->debug_info['overview'] = 'Used stream_context_create()/fopen() to make request. Content length=' . $postLen; $this->debug_info['headers_sent'] = $options['header']; } $pv = PHPVER; if ( $pv{0} == '5' ){$context = stream_context_create( array('http' => $ctx ));$fp = fopen( $url, 'r', false, $context);ob_start();fpassthru( $fp);fclose( $fp);$res = ob_get_clean();}else { list( $protocol, $url ) = explode( '//', $url, 2);list( $domain, $path ) = explode( '/', $url, 2);$fp = fsockopen( $domain, 80, $tvar, $tvar2, SOCKET_TIMEOUT);if ( $fp ){$payload = "$method /$path HTTP/1.1\n" . "Host: $domain\n" . $ctx['header'];fwrite( $fp, $payload);ob_start();fpassthru( $fp);list( $headers, $res ) = explode( "\r\n\r\n", ob_get_clean(), 2);if ( $this->debug_level ) $this->debug_info['headers_received'] = $headers; fclose( $fp);}elseif ( $this->debug_level ) $this->debug_info['overview'] .= "\nOpening $domain/$path failed!"; } } if ( $res ){if ( $this->method == 'soap' ){$tmp = $this->xml2array($res, '/soap:Envelope/soap:Body');if ( !is_array($tmp ) ) return $tmp; else return $tmp[$action.'Response'][$action.'Result']; } else return $this->xml2array($res);}else return null; } function fixEncoding($in_str){$cur_encoding = mb_detect_encoding($in_str);if($cur_encoding == "UTF-8" && mb_check_encoding($in_str,"UTF-8")) return $in_str; else return utf8_encode($in_str);}function xml2array($contents, $root = '/', $charset = 'utf-8', $get_attributes = 0, $priority = 'tag'){if(!$contents) return array();if(!function_exists('xml_parser_create')) return array();$parser = xml_parser_create($charset);$extract_on = TRUE; $start_and_end_element_name = ''; $root_elements = explode('/', $root);if ($root_elements != FALSE && !empty($root_elements)){$start_and_end_element_name = trim(end($root_elements));if (!empty($start_and_end_element_name)) $extract_on = FALSE; } xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);xml_parse_into_struct($parser, trim($contents), $xml_values);xml_parser_free($parser);if(!$xml_values) return; $xml_array = array();$parents = array();$opened_tags = array();$arr = array();$current = &$xml_array; $repeated_tag_index = array();foreach($xml_values as $data){unset($attributes,$value);extract($data);if (!empty($start_and_end_element_name) && $tag == $start_and_end_element_name){$extract_on = !$extract_on; continue; } if (!$extract_on) continue; $result = array();$attributes_data = array();if(isset($value)){if($priority == 'tag') $result = $value; else $result['value'] = $value; } if(isset($attributes) and $get_attributes){foreach($attributes as $attr => $val){if($priority == 'tag') $attributes_data[$attr] = $val; else $result['attr'][$attr] = $val; } } if($type == "open"){$parent[$level-1] = &$current; if(!is_array($current) or (!in_array($tag, array_keys($current)))){$current[$tag] = $result; if($attributes_data) $current[$tag. '_attr'] = $attributes_data; $repeated_tag_index[$tag.'_'.$level] = 1; $current = &$current[$tag]; } else { if(isset($current[$tag][0])){$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; $repeated_tag_index[$tag.'_'.$level]++; } else { $current[$tag] = array($current[$tag],$result);$repeated_tag_index[$tag.'_'.$level] = 2; if(isset($current[$tag.'_attr'])){$current[$tag]['0_attr'] = $current[$tag.'_attr']; unset($current[$tag.'_attr']);}} $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; $current = &$current[$tag][$last_item_index]; } } elseif($type == "complete"){if(!isset($current[$tag])){if (!(is_array($result) && empty($result))) $current[$tag] = $result; $repeated_tag_index[$tag.'_'.$level] = 1; if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; } else { if(isset($current[$tag][0]) and is_array($current[$tag])){$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; if($priority == 'tag' and $get_attributes and $attributes_data){$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; } $repeated_tag_index[$tag.'_'.$level]++; } else { $current[$tag] = array($current[$tag],$result);$repeated_tag_index[$tag.'_'.$level] = 1; if($priority == 'tag' and $get_attributes){if(isset($current[$tag.'_attr'])){$current[$tag]['0_attr'] = $current[$tag.'_attr']; unset($current[$tag.'_attr']);}if($attributes_data){$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; } } $repeated_tag_index[$tag.'_'.$level]++; } } } elseif($type == 'close'){$current = &$parent[$level-1]; } } return($xml_array);}function array2xml( $arr, $indent = '', $escape = true ){$buff = ''; foreach ( $arr as $k => $v ){if ( !is_array($v ) ) $buff .= "$indent<$k>" . ($escape ? $this->fixEncoding(htmlspecialchars($v)) : $v ) . "</" . $k . ">\n"; else { if ( isset( $v[0] ) ){foreach ( $v as $_k => $_v ){if ( is_array($_v ) ) $buff .= "$indent<$k>\n" . $this->array2xml( $_v, $indent . "\t", $escape ) . "$indent</" . $k . ">\n"; else $buff .= "$indent<$k>" . ($escape ? $this->fixEncoding(htmlspecialchars($_v)) : $_v ) . "</" . $k . ">\n"; } } else $buff .= "$indent<$k>\n" . $this->array2xml( $v, $indent . "\t", $escape ) . "$indent</" . $k .">\n"; } } return $buff; } } class CampaignMonitor extends CMBase { var $url = 'http://api.createsend.com/api/api.asmx', $soapAction = 'http://api.createsend.com/api/'; function CampaignMonitor( $api = null, $client = null, $campaign = null, $list = null, $method = 'get' ){CMBase::CMBase( $api, $client, $campaign, $list, $method);}function subscribersGetActive( $date = 0, $list_id = null, $action = 'Subscribers.GetActive' ){if ( !$list_id ) $list_id = $this->list_id; if ( is_numeric( $date ) ) $date = date( 'Y-m-d H:i:s', $date);$valid_actions = array('Subscribers.GetActive' => '', 'Subscribers.GetUnsubscribed' => '', 'Subscribers.GetBounced' => '');if ( !isset( $valid_actions[$action] ) ) $action = 'Subscribers.GetActive'; return $this->makeCall( $action , array('params' => array('ListID' => $list_id , 'Date' => $date ) ));}function subscribersGetUnsubscribed( $date = 0, $list_id = null ){return $this->subscribersGetActive( $date, $list_id, 'Subscribers.GetUnsubscribed');}function subscribersGetBounced( $date = 0, $list_id = null ){return $this->subscribersGetActive( $date, $list_id, 'Subscribers.GetBounced');}function subscriberAdd( $email, $name, $list_id = null, $resubscribe = false ){if ( !$list_id ) $list_id = $this->list_id; $action = 'Subscriber.Add'; if ( $resubscribe ) $action = 'Subscriber.AddAndResubscribe'; return $this->makeCall( $action , array('params' => array('ListID' => $list_id , 'Email' => $email , 'Name' => $name ) ));}function subscriberAddRedundant( $email, $name, $list_id = null ){$added = $this->subscriberAdd( $email, $name, $list_id);if ( $added && $added['Result']['Code'] == '204' ){$subscribed = $this->subscribersGetIsSubscribed( $email, $list_id);if ( $subscribed['anyType'] == 'False' ){$added = $this->subscriberAdd( $email, $name, $list_id, true);return $added; } } return $added; } function subscriberAddWithCustomFields( $email, $name, $fields, $list_id = null, $resubscribe = false ){if ( !$list_id ) $list_id = $this->list_id; $action = 'Subscriber.AddWithCustomFields'; if ( $resubscribe ) $action = 'Subscriber.AddAndResubscribeWithCustomFields'; if ( !is_array($fields ) ) $fields = array();$_fields = array('SubscriberCustomField' => array());foreach ( $fields as $k => $v ){if ( is_array($v ) ){foreach ( $v as $nv ) $_fields['SubscriberCustomField'][] = array('Key' => $k, 'Value' => $nv);}else $_fields['SubscriberCustomField'][] = array('Key' => $k, 'Value' => $v);}return $this->makeCall( $action , array('params' => array('ListID' => $list_id , 'Email' => $email , 'Name' => $name , 'CustomFields' => $_fields ) ));}function subscriberAddWithCustomFieldsRedundant( $email, $name, $fields, $list_id = null ){$added = $this->subscriberAddWithCustomFields( $email, $name, $fields, $list_id);if ( $added && $added['Code'] == '0' ){$subscribed = $this->subscribersGetIsSubscribed( $email);if ( $subscribed == 'False' ){$added = $this->subscriberAddWithCustomFields( $email, $name, $fields, $list_id, true);return $added; } } return $added; } function subscriberUnsubscribe( $email, $list_id = null, $check_subscribed = false ){if ( !$list_id ) $list_id = $this->list_id; $action = 'Subscriber.Unsubscribe'; if ( $check_subscribed ) $action = 'Subscribers.GetIsSubscribed'; return $this->makeCall( $action , array('params' => array('ListID' => $list_id , 'Email' => $email ) ));}function subscribersGetIsSubscribed( $email, $list_id = null ){return $this->subscriberUnsubscribe( $email, $list_id, true);}function checkSubscriptions( $email, $lists, $no_assoc = true ){$nlist = array();foreach ( $lists as $lid => $misc ){$val = $this->subscribersGetIsSubscribed( $email, $lid);$val = $val != 'False'; if ( $no_assoc && $val ) $nlist[] = $lid; elseif ( !$no_assoc ) $nlist[$lid] = $val; } return $nlist; } function subscriberAddAndResubscribe( $email, $name, $list_id = null ){return $this->subscriberAdd( $email, $name, $list_id, true);}function subscriberAddAndResubscribeWithCustomFields( $email, $name, $fields, $list_id = null ){return $this->subscriberAddWithCustomFields( $email, $name, $fields, $list_id, true);}function subscriberGetSingleSubscriber($list_id = null, $email){if (!$list_id != null) $list_id = $this->list_id; return $this->makeCall( 'Subscribers.GetSingleSubscriber', array('params' => array('ListID' => $list_id, 'EmailAddress' => $email ) ));}function clientGeneric( $method, $client_id = null ){if ( !$client_id ) $client_id = $this->client_id; return $this->makeCall( 'Client.' . $method , array('params' => array('ClientID' => $client_id ) ));}function clientGetLists( $client_id = null ){return $this->clientGeneric( 'GetLists', $client_id);}function clientGetListsDropdown( $client_id = null ){$lists = $this->clientGetLists( $client_id);if ( !isset( $lists['List'] ) ) return null; else $lists = $lists['List']; $_lists = array();if ( isset( $lists[0] ) ){foreach ( $lists as $list ) $_lists[$list['ListID']] = $list['Name']; } else $_lists[$lists['ListID']] = $lists['Name']; return $_lists; } function clientGetSegmentsDropdown( $client_id = null ){$lists = $this->clientGetSegments( $client_id);if ( !isset( $lists['List'] ) ) return null; else $lists = $lists['List']; $_lists = array();if ( isset( $lists[0] ) ){foreach ( $lists as $list ) $_lists[$list['ListID'].':'.$list['Name']] = '(' . $list['ListID'] . ') ' . $list['Name']; } else $_lists[$lists['ListID'].':'.$lists['Name']] = '(' . $lists['ListID'] . ') ' . $lists['Name']; return $_lists; } function clientGetCampaigns( $client_id = null ){return $this->clientGeneric( 'GetCampaigns', $client_id);}function clientGetSegments( $client_id = null ){return $this->clientGeneric( 'GetSegments', $client_id);}function clientGetSuppressionList( $client_id = null ){return $this->clientGeneric( 'GetSuppressionList', $client_id);}function clientGetTemplates( $client_id = null ){return $this->clientGeneric( 'GetTemplates', $client_id);}function clientGetDetail( $client_id = null ){return $this->clientGeneric( 'GetDetail', $client_id);}function clientCreate( $companyName, $contactName, $emailAddress, $country, $timezone ){return $this->makeCall( 'Client.Create' , array('params' => array('CompanyName' => $companyName , 'ContactName' => $contactName , 'EmailAddress' => $emailAddress , 'Country' => $country , 'Timezone' => $timezone ) ));}function clientUpdateBasics( $client_id, $companyName, $contactName, $emailAddress, $country, $timezone ){return $this->makeCall( 'Client.UpdateBasics' , array('params' => array('ClientID' => $client_id , 'CompanyName' => $companyName , 'ContactName' => $contactName , 'EmailAddress' => $emailAddress , 'Country' => $country , 'Timezone' => $timezone ) ));}function clientUpdateAccessAndBilling( $client_id, $accessLevel, $username, $password, $billingType, $currency, $deliveryFee, $costPerRecipient, $designAndSpamTestFee ){return $this->makeCall( 'Client.UpdateAccessAndBilling' , array('params' => array('ClientID' => $client_id , 'AccessLevel' => $accessLevel , 'Username' => $username , 'Password' => $password , 'BillingType' => $billingType , 'Currency' => $currency , 'DeliveryFee' => $deliveryFee , 'CostPerRecipient' => $costPerRecipient , 'DesignAndSpamTestFee' => $designAndSpamTestFee ) ));}function userGetClients(){return $this->makeCall( 'User.GetClients');}function userGetSystemDate(){return $this->makeCall( 'User.GetSystemDate');}function userGetTimezones(){return $this->makeCall( 'User.GetTimezones');}function userGetCountries(){return $this->makeCall( 'User.GetCountries');}function userGetApiKey($site_url, $username, $password){return $this->makeCall( 'User.GetApiKey', array('params' => array('SiteUrl' => $site_url, 'Username' => $username, 'Password' => $password, ) ));}function campaignGeneric( $method, $campaign_id = null ){if ( !$campaign_id ) $campaign_id = $this->campaign_id; return $this->makeCall( 'Campaign.' . $method , array('params' => array('CampaignID' => $campaign_id ) ));}function campaignGetSummary( $campaign_id = null ){return $this->campaignGeneric( 'GetSummary', $campaign_id);}function campaignGetOpens( $campaign_id = null ){return $this->campaignGeneric( 'GetOpens', $campaign_id);}function campaignGetBounces( $campaign_id = null ){return $this->campaignGeneric( 'GetBounces', $campaign_id);}function campaignGetSubscriberClicks( $campaign_id = null ){return $this->campaignGeneric( 'GetSubscriberClicks', $campaign_id);}function campaignGetUnsubscribes( $campaign_id = null ){return $this->campaignGeneric( 'GetUnsubscribes', $campaign_id);}function campaignGetLists( $campaign_id = null ){return $this->campaignGeneric( 'GetLists', $campaign_id);}function campaignCreate( $client_id, $name, $subject, $fromName, $fromEmail, $replyTo, $htmlUrl, $textUrl, $subscriberListIds, $listSegments ){if ($client_id == null) $client_id = $this->client_id; $_subListIds = ''; if ($subscriberListIds != ""){$_subListIds = array('string' => array());if ( is_array($subscriberListIds ) ){foreach ( $subscriberListIds as $lid ){$_subListIds['string'][] = $lid; } } } $_seg = ''; if ($listSegments != ""){$_seg = array();if (is_array($listSegments)){for($i=0; $i < count($listSegments);$i++){foreach ($listSegments[$i] as $k => $v){$_seg['List'][$i][$k] = $v; } } } } return $this->makeCall( 'Campaign.Create', array('params' => array('ClientID' => $client_id , 'CampaignName' => $name , 'CampaignSubject' => $subject , 'FromName' => $fromName , 'FromEmail' => $fromEmail , 'ReplyTo' => $replyTo , 'HtmlUrl' => $htmlUrl , 'TextUrl' => $textUrl , 'SubscriberListIDs' => $_subListIds , 'ListSegments' => $_seg ) ));}function campaignSend( $campaign_id, $confirmEmail, $sendDate ){if ( $campaign_id == null ) $campaign_id = $this->campaign_id; return $this->makeCall( 'Campaign.Send', array('params' => array('CampaignID' => $campaign_id , 'ConfirmationEmail' => $confirmEmail , 'SendDate' => $sendDate ) ));}function campaignDelete($campaign_id){return $this->campaignGeneric('Delete', $campaign_id);}function listCreate( $client_id, $title, $unsubscribePage, $confirmOptIn, $confirmationSuccessPage ){if ( $confirmOptIn == 'false' ) $confirmationSuccessPage = ''; return $this->makeCall( 'List.Create', array('params' => array('ClientID' => $client_id , 'Title' => $title , 'UnsubscribePage' => $unsubscribePage , 'ConfirmOptIn' => $confirmOptIn , 'ConfirmationSuccessPage' => $confirmationSuccessPage ) ));}function listUpdate( $list_id, $title, $unsubscribePage, $confirmOptIn, $confirmationSuccessPage ){if ( $confirmOptIn == 'false' ) $confirmationSuccessPage = ''; return $this->makeCall( 'List.Update', array('params' => array('ListID' => $list_id , 'Title' => $title , 'UnsubscribePage' => $unsubscribePage , 'ConfirmOptIn' => $confirmOptIn , 'ConfirmationSuccessPage' => $confirmationSuccessPage ) ));}function listDelete( $list_id ){return $this->makeCall( 'List.Delete', array('params' => array('ListID' => $list_id ) ));}function listGetDetail( $list_id ){return $this->makeCall( 'List.GetDetail', array('params' => array('ListID' => $list_id ) ));}function listGetStats($list_id){return $this->makeCall( 'List.GetStats', array('params' => array('ListID' => $list_id ) ));}function listCreateCustomField( $list_id, $fieldName, $dataType, $options ){if ( $dataType == 'Text' || $dataType == 'Number' ) $options = null; return $this->makeCall( 'List.CreateCustomField', array('params' => array('ListID' => $list_id , 'FieldName' => $fieldName , 'DataType' => $dataType , 'Options' => $options ) ));}function listGetCustomFields( $list_id ){return $this->makeCall( 'List.GetCustomFields', array('params' => array('ListID' => $list_id ) ));}function listDeleteCustomField( $list_id, $key ){return $this->makeCall( 'List.DeleteCustomField', array('params' => array('ListID' => $list_id , 'Key' => $key ) ));}function templateCreate($client_id, $template_name, $html_url, $zip_url, $screenshot_url){return $this->makeCall('Template.Create', array('params' => array('ClientID' => $client_id, 'TemplateName' => $template_name, 'HTMLPageURL' => $html_url, 'ZipFileURL' => $zip_url, 'ScreenshotURL' => $screenshot_url )));}function templateGetDetail($template_id){return $this->makeCall('Template.GetDetail', array('params' => array('TemplateID' => $template_id )));}function templateUpdate($template_id, $template_name, $html_url, $zip_url, $screenshot_url){return $this->makeCall('Template.Update', array('params' => array('TemplateID' => $template_id, 'TemplateName' => $template_name, 'HTMLPageURL' => $html_url, 'ZIPFileURL' => $zip_url, 'ScreenshotURL' => $screenshot_url )));}function templateDelete($template_id){return $this->makeCall('Template.Delete', array('params' => array('TemplateID' => $template_id )));}}