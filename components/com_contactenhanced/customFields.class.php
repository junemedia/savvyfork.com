<?php
/**
 * @package		com_contactenhanced
 * @subpackage	Contact
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 28-Jul-09
 * @license		GNU/GPL, see license.txt
 * Contact Enhanced  is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
defined( '_JEXEC' ) or die( 'Restricted access' );

// Add mootols to ALL contact form pages
JHTML::_('behavior.framework',true);
/**
 *
 * Abstract ceFieldType class.
 *
 */
class ceFieldType {
	var $id 	= null;
	var $name 	= null;
	var $value	= null;
	var $type 	= null;
	var $attributes = null;
	var $uservalue	= null;
	var $arrayFieldElements = null;
	var $allowHTML = false;
	var $params	= null;
	var $errors	= array();


	function ceFieldType( $data,&$params ) {

		if( !is_null($data) ){
			foreach( $data AS $key => $value ) {
				switch($key){
					case 'value':
						$this->arrayFieldElements = explode("|",$data->$key);
						$this->$key = $value;
						break;
					default:
						$this->$key = $value;
						break;
				}
			}
		}
		$this->params	= $params;
		$this->session 	=JFactory::getSession();
		$this->session	= $this->session->get('com_contactenhanced');
	}

	function validateField() {
		if($this->isRequired() AND empty($this->uservalue) AND (int)$this->uservalue !=0){
			return false;
		}
		return true;
	}

	function getFieldClass() {
		$session	=JFactory::getSession();
		$ce_session		= $session->get('com_contactenhanced');
		$errors		= (isset($ce_session['errors']) ? $ce_session['errors'] : array());
		return ($this->isRequired() ? ' required':'')
			. (in_array($this->getInputName(), $errors) ? ' invalid validation-failed': '')
		;
	}

	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		if($format == 'html'){
			// Line breaks added because of an incompatibility with MS Outlook
			$html	= '
			<div class="ce-cf-container">';
			if( is_array($this->uservalue) ){
				$this->uservalue	= implode($delimiter,$this->uservalue);
			}
			$html .= '
			<span class="ce-cf-html-label" style="'.$style['label'].'">'.
						JText::sprintf("COM_CONTACTENHANCED_LABEL_OUTPUT",($this->getInputFieldName())).'</span>' ;
			if($this->type == 'checkbox'
				OR $this->type == 'selectlist'
				OR $this->type == 'radiobutton'
				OR $this->type == 'selectmultiple'
			){
				$html .= '
				<span class="ce-cf-html-field" style="'.$style['value'].'"> '
					.($this->uservalue).'</span>' ;
			}else{
				$html .= '
				<span class="ce-cf-html-field" style="'.$style['value'].'"> '
					.($this->uservalue).'</span>' ;
			}
			$html	.= '
			</div>';
			return $html;
		}else{
			if( is_array($this->uservalue) ){
				if(isset($this->uservalue[0]) AND is_array($this->uservalue[0])){
					$this->uservalue = implode($delimiter,$this->uservalue[0]);
				}else{
					$this->uservalue = implode($delimiter,$this->uservalue);
				}
			}
			return $this->getInputFieldName() .":\t ".($this->uservalue)."\n";
		}
	}
	function getMySQLOutput(){
		if( is_array($this->uservalue) ){
			return implode('|',$this->uservalue);
		}else{
			return $this->uservalue;
		}
	}

	function isRequired() {
		if($this->required){ return true;}else{ return false;}
	}

	function parseValue( $value ) { if ( is_array($value) ) { return ($this->allowHTML) ? implode("|",$value) : strip_tags(implode("|",$value));} else { $value = trim($value); return ($this->allowHTML) ? $value : strip_tags($value);}
	}
	function getFieldType() { return $this->type;}
	function getValue($arg=null) {

		$ce_session	= $this->session;
		if(!is_array($ce_session)){
			$ce_session	= array();
		}
		if(!isset($ce_session['fieldValues'])){
			$ce_session['fieldValues'] = array();
		}

		if(is_null($arg)) {
			if( isset($ce_session['fieldValues'][$this->getInputName('cookie')]) ){
				return $ce_session['fieldValues'][$this->getInputName('cookie')];
			}elseif( isset($ce_session['fieldValues']['cf_'.$this->id]) ){
				return $ce_session['fieldValues']['cf_'.$this->id];
			}elseif(isset($this->field_value)){
				return $this->field_value;
			}
			return JRequest::getVar($this->getInputName(),$this->value,'default', 'none', JREQUEST_ALLOWHTML);
			//return $this->value;
		} elseif(is_numeric($arg)) {
			$values = explode('|',($this->value ? $this->value : (isset($this->field_value) ? $this->field_value : '') ));
			if(array_key_exists(($arg-1),$values)) {
				return trim($values[($arg-1)]);
			} else { return '';}
		} elseif($arg == 'session') {
			$session 		=JFactory::getSession();
			$ce_session		= $session->get('ce_session', array());
			return $ce_session[$this->getInputName()];
		} elseif($arg) {
			$values = explode('|',$this->value);
			if(array_key_exists(($arg-1),$values)) {
				return trim($values[($arg-1)]);
			} else { return '';}
		}

	}
	function getInputHTML()
	{
	    return '<input title="'.$this->name.'" class="inputbox text_area cf-input-text '.($this->getFieldClass()).'"
	    			type="text" name="' . $this->getInputName() . '"
	    			id="' . $this->getInputName() . '"
	    			value="' . htmlspecialchars($this->getValue()) . '" '.$this->attributes.' />'
	    		.'<br />';
	}
	function getName()
	{
		return ($this->name);
	}
	function getInputFieldName($count=1)
	{
	    if ($count == 1) {
	        return $this->getName();
	    } else if ($count <= $this->numOfInputFields ) {
	        return $this->getName() . '_' . $count;
	    }
	}

	function getInputName($count=1) {
		if($this->params->get('advanced-integration-name') AND $this->params->get('advanced',0) ){
			return trim($this->params->get('advanced-integration-name'));
		}
		return (isset($this->alias) ? $this->alias : 'cf_'.$this->id);
	}
	function getLastError(){
		return end($this->errors);
	}

	function getOutput($view=1)
	{
	    return $this->getValue();
	}

	function stripTags($value, $allowedTags='u,b,i,a,ul,li,pre,br,blockquote')
	{
	    if (!empty($allowedTags)) {
	        $tmp = explode(',',$allowedTags);
	        array_walk($tmp,'trim');
	        $allowedTags = '<' . implode('><',$tmp) . '>';
	    } else {
	        $allowedTags = '';
	    }
	    return strip_tags($value, $allowedTags );
	}
	function linkcreator($matches )
	{
	    $url = 'http://';
	    $append = '';
	    if (in_array(substr($matches[1],-1), array('.',')')) ) {
	        $url .= substr($matches[1], 0, -1);
	        $append = substr($matches[1],-1);
	        # Prevent cutting off breaks <br />
	    } else if (substr($matches[1],-3) == '<br' ) {
	        $url .= substr($matches[1], 0, -3);
	        $append = substr($matches[1],-3);
	    } else if (substr($matches[1],-1) == '>' ) {
	        $regex = '/<(.*?)>/i';
	        preg_match($regex, $matches[1], $tags );
	        if (!empty($tags[1]) ) {
	            $append = '<'.$tags[1].'>';
	            $url .= $matches[1];
	            $url = str_replace($append, '', $url );
	        }
	    } else {
	        $url .= $matches[1];
	    }
	    return '<a href="'.$url.'" target="_blank">'.$url.'</a>'.$append.' ';
	}
	function strlen_utf8($str)
	{
	    return strlen(utf8_decode($this->utf8_html_entity_decode($str)));
	}
	function utf8_replaceEntity($result)
	{
	    $value = intval($result[1]);
	    $string = '';
	    $len = round(pow($value,1/8));
	    for ($i=$len; $i>0; $i--) {
	        $part = ($value AND(255>>2)) | pow(2,7);
	        if ($i == 1 ) {
	            $part |= 255<<(8-$len);
	        }
	        $string = chr($part) . $string;
	        $value >>= 6;
	    }
	    return $string;
	}
	function utf8_html_entity_decode($string)
	{
	    return preg_replace_callback('/&#([0-9]+);/u',array($this,'utf8_replaceEntity'),$string);
	}
	function html_cutstr($str, $len)
	{
	    if (!preg_match('/\&#[0-9]*;.*/i', $str)) {
	        return substr($str,0,$len);
	    }
	    $chars = 0;
	    $start = 0;
	    for ($i=0; $i < strlen($str); $i++) {
	        if ($chars >= $len) {
	            break;
	        }
	        $str_tmp = substr($str, $start, $i-$start);
	        if (preg_match('/\&#[0-9]*;.*/i', $str_tmp)) {
	            $chars++;
	            $start = $i;
	        }
	    }
	    $rVal = substr($str, 0, $start);
	    if (strlen($str) > $start) {
	        return $rVal;
	    }
	}
	function html_substr($str, $start, $length = NULL)
	{
	    if ($length === 0) {
	        return '';
	    }
	    if (strpos($str, '&') === false) {
	        if ($length === NULL) {
	            return substr($str, $start);
	        } else {
	            return substr($str, $start, $length);
	        }
	    }
	    $chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);
	    $html_length = count($chars);
	    if (($html_length === 0) or($start >= $html_length) or(isset($length) and($length <= -$html_length)) ) {
	        return '';
	    }
	    if ($start >= 0) {
	        $real_start = $chars[$start][1];
	    } else {
	        $start = max($start,-$html_length);
	        $real_start = $chars[$html_length+$start][1];
	    }
	    if (!isset($length)) {
	        return substr($str, $real_start);
	    } else if ($length > 0) {
	        if ($start+$length >= $html_length) {
	            return substr($str, $real_start);
	        } else {
	            return substr($str, $real_start, $chars[max($start,0)+$length][1] - $real_start);
	        }
	    } else {
	        return substr($str, $real_start, $chars[$html_length+$length][1] - $real_start);
	    }
	}
	function html_strlen($str)
	{
	    $chars = preg_split('/(&[^;\s]+;)|/', $str, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
	    return count($chars);
	}
	function getObjectVars()
	{
	    var_dump(get_object_vars($this));
	}

	function getLabel($output='site'){
		$html	= '';
		if($this->params->get('hide_field_label') == "overtext") {
			// Do nothing
		}elseif(($this->params->get('hide_field_label',0) ==0 )){
			$tooltip_image	= '';

			if($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'image'){
				$tooltip_image = JHtml::_('image'
										, 'components/com_contactenhanced/assets/images/tooltip/'.$this->params->get('tooltip_behavior-image-image','default-blue-help-icon.png')
										, ' ? '
										);
				$tooltip_image = ' <span class="ce-tooltip-image">'
									. JHtml::_('tooltip'
												, $this->tooltip
												, $this->getInputFieldName()
												, ''
												, $tooltip_image)
								. '</span>';
			}

			$label= '<label
							class="cf-label'.($this->isRequired() ? ' requiredField':'').'"
							for="'.$this->getInputId().'"
							id="l'.$this->getInputId().'">'
						.( $this->getInputFieldName() )
						.($this->isRequired() ? ' <span class="requiredsign">'.JText::_('CE_FORM_REQUIRED_SIGN').'</span>' : '')
						.$tooltip_image
					.'</label>';
			if($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'mouseover'){
				$html .= '<span class="editlinktip hasTip" title="'. JText::_( $this->tooltip ). '">'
				. $label
				. '</span>';
			}elseif($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'inline'){
				$html .= $label;
				$html .= '<div class="ce-tooltip" >'. JText::_( $this->tooltip ). '</div>';
			}else{
				$html .= $label;
			}
		}
		return $html;
	}
	function getInputId() {
		return $this->getInputName();
	}
	public function getFieldHTML(){
		$html	= '';
		if($this->published AND $this->type != 'hidden' AND $this->type != 'freetext' OR (!$this->params->get('isAdmin')) ){
			$style	= ($this->params->get('hide_field',0) ? 'display:none;' : ''	);

			$containerClass = 'ce-cf-container cf-type-'.$this->type. ' ';
			if($this->params->get('field_width_type') == 'bootstrap'){
				$containerClass	.= $this->params->get('field_width_type-bootstrap-width');
			}else{
				//ensure compatibility with old versions
				$containerClass	.= 'ce-fltwidth-'.round($this->params->get('field_width_type-percentage-width',$this->params->get('field-width',100)));
			}

			$html .= "\n".'<div class="'.$containerClass.'" id="ce-cf-container-'.$this->id.'" style="'.$style.'">';
			$html .= "\n\t".$this->getLabel();
			$html .= "\n\t".$this->getInputHTML();
			$html .= "\n".'</div>';
		}else{
			$html .= $this->getLabel();
			$html .= $this->getInputHTML();
		}
		if($this->params->get('isAdmin')){
			$html .= $this->getRecordedFieldId();
		}
		return $html;
	}
	function getRecordedFieldId(){
		return '<input type="hidden" name="'.$this->getInputName().'_id" value="'.$this->field_id.'" />';
	}
	function getValidationScript() {
		$script = '';
		if($this->params->get('hide_field_label') == 'overtext'){
			$script	= "\n\t"
					."new OverText(document.id('".$this->getInputId()."'));"
				;
			if (!defined('CE_CF_OVERTEXT')) {
				$script	.= "\n\t var fixOverText = function() {
					OverText.update();
				};
				var pid = fixOverText.periodical(2000);"
				;
				define('CE_CF_OVERTEXT',1);
			}
		}
		return $script;
	}
	public function escapeJSText($string) {
		return addslashes($string);
		//return str_replace("\n", '\n', str_replace('"', '\"', addcslashes(str_replace("\r", '', (string)$string), "\0..\37'\\")));
	}
}

class ceFieldType_gmapsaddress extends ceFieldType {
	function getInputHTML() {
		$html	= '';
		$script	= '';
		require_once (JPATH_SITE.'/components/com_contactenhanced/helpers/gmaps.php');

		$map	= new GMaps($this->params);

		//	echo ceHelper::print_r($this->session['fieldValues']['lat']); exit;
		if(isset($this->session['fieldValues'])){
			$fieldValues	= $this->session['fieldValues'][$this->getInputName()];
			$map->set('lat',(float)$fieldValues['lat']);
			$map->set('lng',(float)$fieldValues['lng']);
			$map->set('zoom',(int)$fieldValues['zoom']);
			$map->set('infoWindowContent',	$fieldValues['address']);
		}else{
			if(trim($this->params->get('gmap_infoWindowContent','')) != 'address'){
				$map->set('infoWindowContent',$this->params->get('gmap_infoWindowContent','')	);
			}
			if($this->params->get('gmaps_lat')){
				$map->set('lat',(float)$this->params->get('gmaps_lat'));
			}
			if($this->params->get('gmaps_lng')){
				$map->set('lng',(float)$this->params->get('gmaps_lng'));
			}
			if($this->params->get('gmaps_zoom')){
				$map->set('zoom',(int)$this->params->get('gmaps_zoom'));
			}
		}

		$map->set('infoWindowDisplay',	$this->params->get('gmap_infoWindowDisplay','alwaysOn'));


		$map->set('scrollwheel',		$this->params->get('gmap_scrollWheel',true));
		$map->set('typeControl',		$this->params->get('gmap_mapTypeControl','true'));
		$map->set('typeId',				$this->params->get('gmaps_MapTypeId','SATELLITE'));
		$map->set('navigationControl',	$this->params->get('gmap_navigationControl','true'));
		$map->set('travelMode',			$this->params->get('gmaps_DirectionsTravelMode','DRIVING'));




		$map->set('input_lat',		$this->getInputName().'lat');
		$map->set('input_lng',		$this->getInputName().'lng');
		$map->set('input_zoom',		$this->getInputName().'zoom');
		$map->set('input_address',	$this->getInputName().'address');
		//	$map->set('input_address',	'googleaddress');

		$map->set('editMode',		true);
		$map->set('useDirections',	false);
		$map->set('reverseGeocode',	true);
		$map->set('showCoordinates',	false);
		//$map->set('showCoordinates',	$this->params->get('gmaps_showCoordinates',true));
		$map->set('companyMarkerDraggable',		true);

		if( trim($this->params->get('gmaps_icon'))){
			$map->set('markerImage',	JURI::root().'components/com_contactenhanced/assets/images/gmaps/marker/'.$this->params->get('gmaps_icon') );
		}
		if ($this->params->get('gmaps_icon_shadow') ) {
			$map->set('markerShadow',JURI::root().'components/com_contactenhanced/assets/images/gmaps/shadow/'.$this->params->get('gmaps_icon_shadow'));
		}

		$html	.= ' <span class="ce-button-container">';
		$html	.= '<input  title="'.$this->name.'"  name="'.$this->getInputName().'[address]" class="'.$this->getFieldClass().' inputbox ce-gmaps-address"
						id="'.$this->getInputId().'"
						value="'. '' .'" />';
		$html	.= ' <span><button class="button ce-gmaps-locate" type="button" onclick="ceMap.codeAddress();">'
		.JText::_('CE_GMAPS_LOCATE_ADDRESS_BUTTON').'</button></span>';
		$html	.= '</span>';

		$html .= $map->create();
		if($this->params->get('gmaps_showCoordinates',true)){
			$html	.= '<div class="ce-map-lat">';
			$html	.= '<label class="cf-label">'.JText::_('CE_GMAPS_LATITUTE').': </label>' ;
			$html	.= '<span class="ce-map-coord-value">'
			.'<input type="text" name="'.$this->getInputName().'[lat]" class="inputbox ce-coordinates" id="'.$this->getInputName().'lat"
									value="'.(isset($fieldValues) ? $fieldValues['lat'] : $map->get('lat')).'" />'
									.'</span>' ;
									$html	.= '</div>';
									$html	.= '<div class="ce-map-lng">';
									$html	.= '<label class="cf-label">'.JText::_('CE_GMAPS_LONGITUTE').': </label>' ;
									$html	.= '<span class="ce-map-coord-value">'
									.'<input type="text" name="'.$this->getInputName().'[lng]" class="inputbox ce-coordinates" id="'.$this->getInputName().'lng"
									value="'.(isset($fieldValues) ? $fieldValues['lng'] : $map->get('lng')).'" />'
									.'</span>' ;
									$html	.= '</div>';

									$script	.= "
			$('".$this->getInputName()."lat').addEvent('blur', function(e) {
					ceMap.codeAddress();
				});
			$('".$this->getInputName()."lng').addEvent('blur', function(e) {
					ceMap.codeAddress();
				});";

		}else{
			$html	.= '<input name="'.$this->getInputName().'[lat]" id="'.$this->getInputName().'lat"
							type="hidden"
							value="'.(isset($fieldValues) ? $fieldValues['lat'] : $map->get('lat')).'" />';
			$html	.= '<input name="'.$this->getInputName().'[lng]" id="'.$this->getInputName().'lng"
							type="hidden"
							value="'.(isset($fieldValues) ? $fieldValues['lng'] : $map->get('lng')).'" />';
		}

		$html	.= '<input type="hidden" name="'.$this->getInputName().'[zoom]" id="'.$this->getInputName().'zoom"
						value="'.(isset($fieldValues) ? $fieldValues['zoom'] : $map->get('zoom')).'" />';

		$doc =JFactory::getDocument();
		$script	.= "
			$('".$this->getInputId()."').addEvent('blur', function(e) {
					if($('".$this->getInputId()."').get('value') !='' ){
						ceMap.codeAddress();
					}
				});
			$('".$this->getInputId()."').addEvent('keydown', function(e) {
					if(e.key=='enter' && $('".$this->getInputId()."').get('value') !='' ){
						ceMap.codeAddress();
					}
				});";

		$script	= "/* <![CDATA[ */
window.addEvent('domready',function(){".$script."});
/* ]]> */";
		$doc->addScriptDeclaration($script);
		return $html;
	}

	function getInputId(){
		return parent::getInputName().'address';
	}

	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		if($format == 'html'){
			$html	= '<div class="ce-cf-container">';
			$html .= '
			<span class="ce-cf-html-label" style="'.$style['label'].'">'.JText::_($this->getInputFieldName()).'</span>' ;
			$html .= '
			<span class="ce-cf-html-field" style="'.$style['value'].'"> '.JText::sprintf('CE_GMAPS_LOCATION_FROM_MAP',$this->uservalue['address']).'</span>' ;
			$html .= '<br />';
			$link	= JHtml::_('link',
							"http://maps.google.com/maps?q={$this->uservalue['lat']},+{$this->uservalue['lng']}+(".str_replace(' ', '+',$this->uservalue['address']).")"
							.($this->params->get('gmaps_linkToGoogleEarth') == 'gearth' ? '&t=k&z=18&om=1&output=kml&ge_fileext=.kml' : ''),
			JText::sprintf('CE_GMAPS_COORDINATES_FROM_MAP_VALUE',$this->uservalue['lat'],$this->uservalue['lng']),
							'target="_blank"'
							);
							$html .= '<span  class="ce-cf-html-feild" style="'.$style['value'].'"> '. JText::_('CE_GMAPS_COORDINATES_FROM_MAP') .$link.'</span>' ;
							$html	.= '</div>';
							return $html;
		}else{
			return 	$this->getInputFieldName() .": "
			."\n\t\t".JText::sprintf('CE_GMAPS_LOCATION_FROM_MAP',$this->uservalue['address'])
			."\n\t\t".JText::_('CE_GMAPS_COORDINATES_FROM_MAP')
			.JText::sprintf('CE_GMAPS_COORDINATES_FROM_MAP_VALUE',$this->uservalue['lat'],$this->uservalue['lng'])."\n";
		}
	}
}

class ceFieldType_text extends ceFieldType {
	function getInputHTML() {
		$class	= '';
		$alt	= '';
		$js		= '';
		$title	=  $this->name;
		$dataValidators='';
		if($this->params->get('validation') == 'iMask'  ){ //AND $this->params->get('validation-iMask-mask')
			JHTML::_('behavior.framework');
			$class	.= ' iMask';

			$alt	= "{type:'".		$this->params->get('validation-iMask-type','fixed')."',";
			if($this->params->get('validation-iMask-type','fixed') == 'number'){
				if((int)$this->params->get('validation-iMask-decSymbol','.') != 0){
					$alt.="groupDigits:".	$this->pacf_idrams->get('validation-iMask-groupDigits',3).",
						decSymbol:'".	$this->params->get('validation-iMask-decSymbol','.')."',
						decDigits:".	$this->params->get('validation-iMask-decDigits',2).",";
					if($this->params->get('validation-iMask-decSymbol','.') =='.'){
						$alt.="groupSymbol:',',";
					}elseif ($this->params->get('validation-iMask-decSymbol','.') ==','){
						$alt.="groupSymbol:'.',";
					}else{
						$alt.="groupSymbol:'',";
					}
				}else{
					$alt.="groupDigits:3,
						decSymbol:'',
						decDigits:0,
						groupSymbol:'".JText::_('CE_CF_TEXT_MASK_CONFIG_GROUP_SYMBOL')."',";
				}
			}else{
				$alt.="mask:'".		$this->params->get('validation-iMask-mask')."',";
			}
			// stripMask = false is not working
			$alt.="stripMask:".	$this->params->get('validation-iMask-stripMask','true')."}";
			/*$alt	= "{type:'fixed',
			 mask:'99999-999',
			 stripMask: false }";*/
			$doc	=JFactory::getDocument();
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/iMask.js');
			$title	= ' '.JText::sprintf('CE_CF_TEXT_FORMAT',$this->params->get('validation-iMask-mask') );

		}elseif($this->params->get('validation')
				AND $this->params->get('validation') != 'date'
				AND $this->params->get('validation') != 'custom'  ){
			$class	.= ' validate-'.$this->params->get('validation');
		}elseif ($this->params->get('validation') == 'date'){
			$dataValidators="validate-date dateFormat:'{$this->params->get('validation-date-format','%d-%m-%Y')}'";
		}elseif ($this->params->get('validation') == 'custom'
					AND $this->params->get('validation-custom-name')
					AND $this->params->get('validation-custom-errorMsg')
					AND $this->params->get('validation-custom-test')){
			$customValidatorName	= JApplication::stringURLSafe($this->params->get('validation-custom-name'));
			$class	.= ' '.$customValidatorName;

			$doc	=JFactory::getDocument();
			$doc->addScriptDeclaration("
window.addEvent('domready', function(){
	Form.Validator.add('".$customValidatorName."', {
		errorMsg: '".addslashes($this->params->get('validation-custom-errorMsg'))."',
		test: function(element){
			return ".$this->params->get('validation-custom-test')."
		}
	});
});
			");
		}

		if( trim( $this->params->get('minLength') ) ){
			$class	.= ' minLength:'.$this->params->get('minLength',0);
		}
		if( trim( $this->params->get('maxLength') ) ){
			$class	.= ' maxLength:'.$this->params->get('maxLength');
		}

		return '<input
					class="'.$class.' inputbox cf-input-text '.($this->getFieldClass()).'"
					type="'.$this->getFieldType().'"
					name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"
					value="' . htmlspecialchars($this->getValue()) . '" '.$this->attributes.'
					alt="'.$alt.'"
					title="'.$title.'"
					'.($dataValidators ?	' data-validators="'.$dataValidators.'"'	: '' ).'
					'.$js.' />'
				.'<div style="display:none" id="'.$this->getInputId().'-ajax-response"></div>'
				.'<br />';
	}

	function getValue($arg=null) {
		$value = parent::getValue($arg);

		if(!strpos($value, '}')){
			//--The tag is not found in content - abort..
			return $value;
		}

		$doc	= JFactory::getDocument();
		$value = str_ireplace('{current_page_title}', $doc->getTitle(), $value);
		if(JRequest::getVar('content_title')){
			$value = str_ireplace('{referrer_page_title}', ceHelper::decode(JRequest::getVar('content_title')), $value);
		}else{
			$value = str_ireplace('{referrer_page_title}', '', $value);
		}
		return $value;

	}

	function getFieldClass() {
		return parent::getFieldClass()
			. ($this->params->get('check') ? ' validate-'.$this->params->get('check') : '')
			;
	}

	public function jsonExecute() {
		if($this->params->get('check') == 'coupon'
			AND strpos($this->params->get('check-coupon-sql'),'SELECT') !== FALSE){
			$sql	=  $this->params->get('check-coupon-sql');
			$user		= JFactory::getUser();
			$regex = '/{user_id}/i';
			$sql  = preg_replace( $regex, $user->id, $sql );

			$regex = '/{user_email}/i';
			$sql  = preg_replace( $regex, $user->email, $sql );

			$regex = '/{username}/i';
			$sql  = preg_replace( $regex, $user->username, $sql );

			$regex = '/{selectresult}/i';
			$sql  = preg_replace( $regex, JRequest::getVar('q'), $sql);


			$db		= JFactory::getDbo();
			$db->setQuery($sql );
			if(!$db->loadObjectList()){
				return array('action'=> 'error','msg' => $this->params->get('check-coupon-error_no_code'));
			}else{
				$query	= $db->getQuery(true);
				$query->select('value');
				$query->from('#__ce_message_fields');
				$query->where('field_id = '.$db->Quote($this->id));
				$query->where('value = '.$db->Quote(JRequest::getVar('q')));

				if($db->loadObjectList()){
					return array('action'=> 'error','msg' => $this->params->get('check-coupon-error_code_already_used'));
				}else{
					return array('action'=> 'success');
				}
			}
		}
		return true;
	}

	function getValidationScript() {
		$script = parent::getValidationScript();
		if($this->params->get('hide_field_label') == 'overtext'){
				$script	.= "\n\t"
						."new OverText(document.id('".$this->getInputId()."'));"
					."\n";
		}

		if($this->params->get('check') == 'coupon'
			AND strpos($this->params->get('check-coupon-sql'), 'SELECT') !== FALSE){
			$doc	= JFactory::getDocument();
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/json_validate.js');
			$script	.= "\n\t"."jsonvalidate('{$this->id}','');"."\n";
		}
		return $script;
	}
	function validateField() {
		//echo 'here'.ceHelper::print_r($this->type. ' '.$this->uservalue); exit;
		if(parent::validateField()){
			if($this->params->get('check') == 'coupon'
				AND strpos($this->params->get('check-coupon-sql'), 'SELECT') !== FALSE){
				JRequest::setVar('q', $this->uservalue);
				$ret	= $this->jsonExecute();
				//echo '<pre>'; print_r($ret); exit;
				if (is_array($ret) AND isset($ret['action']) AND $ret['action'] == 'error') {
					JFactory::getApplication()->enqueueMessage($ret['msg'],'error');
					return false;
				}
			}
			return true;
		}else{
			return false;
		}
	}
}
class ceFieldType_multitext extends ceFieldType {
	function getInputHTML() {
		$maxlenField	= '';
		$fieldClass		= 'inputbox text_area '.($this->getFieldClass());

		// Limit Characters Box
		if( (int)$this->params->get('maxlen',0) > 1 ){
			$fieldClass	.= ' validate-limited-textarea';
			JText::script('COM_CONTACTENHANCED_CF_MULTITEXT_CHARACTER_LIMIT_REACHED');

			$script	= "
window.addEvent('domready', function(){
	var field		= document.id('".$this->getInputName()."');
	field.addEvent('keyup', function(e) {
		new Event(e).stop();
		var field		= document.id('".$this->getInputName()."');
		var fieldValue	= field.get('value');
		var maxlen		= document.id('".$this->getInputName()."-maxlen');
		maxlen			= maxlen.get('value');
		fieldValue		= fieldValue.substring(0, maxlen);
		field.set('value', fieldValue);
		var fieldLength = document.id('" . $this->getInputName() . "-chars-left');
		fieldLength.set('text', (maxlen - fieldValue.length) );
		ceForm".$this->params->get('contactId')."Validator.test('validate-limited-textarea','".$this->getInputName()."');
	});
});
		";
			$doc	= JFactory::getDocument();
			$doc->addScriptDeclaration($script);
			$doc->addScript(JURI::base(true).'/components/com_contactenhanced/assets/js/mootools.forms_fields.js');

			$maxlenField .= '<div class="ce-cf-multitex-limit-container">
					<input type="hidden" value="'.$this->params->get('maxlen').'"
							name="' . $this->getInputName() . '-maxlen"
							id="' . $this->getInputName() . '-maxlen" />
					<span class="" id="' . $this->getInputName() . '-chars-left">'
						.$this->params->get('maxlen').'</span> ';
				$maxlenField .= JText::_('COM_CONTACTENHANCED_CF_MULTITEXT_CHARACTERS_LEFT');
			$maxlenField .= '</div>';
		}

		$html = '<textarea
						title="'.$this->name.'"
						name="' . $this->getInputName() . '"
						id="' . $this->getInputName() . '"
						class="'.$fieldClass.'" '
						.($this->attributes ? $this->attributes : ' cols="40" rows="8" ' )
					.' >'
							. $this->getValue()
				. '</textarea>
		';
		$html .= $maxlenField;
		return $html;
	}
}

class ceFieldType_weblink extends ceFieldType {
	function getInputHTML() {
		$showGo = $this->getParam('showGo',0);
		$html	= '<input  title="'.$this->name.'"  class="inputbox  cf-input-text  text_area'.($this->getFieldClass()).'"
					type="text" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"
					size="' . $this->getSize() . '" value="' . htmlspecialchars($this->getValue()) . '" />';
		if($showGo){
			$html .= '';
			$html .= ' <input type="button" class="button" onclick=\'';
			$html .= 'javascript:window.open("index2.php?option=com_contactenhanced&amp;task=openurl&amp;url="+escape(document.getElementById("' . $this->getInputName() . '").value))\'';
			$html .= ' value="' . JText::_('Go') . '"  '.$this->attributes.' />';
		}
		return $html;
	}

}
class ceFieldType_selectlist extends ceFieldType{
	function getInputHTML() {
		$javascript	= '';
		if($this->params->get('chain_select') AND $this->params->get('chain_select-enabled-option')){
			JHtml::_('jquery.framework');
			$javascript	= "onchange=\"JsonSelect.updateSelect('".$this->params->get('chain_select-enabled-option')."',this,'".JURI::root()."');\"";
			$doc	=JFactory::getDocument();
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/chainSelectList.js');
		}

		$html = '<select name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"'
		.	$javascript. ' class="inputbox text_area'.($this->getFieldClass()).'" '.$this->attributes.' >';
		if($this->params->get('first_element','option') == 'option'){
			$html .= '<option value="">'.JText::_($this->params->get('first_element-option-text', 'CE_PLEASE_SELECT_ONE')).'</option>';
		}
		foreach($this->arrayFieldElements AS $fieldElement) {
			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
			}else{
				$fieldElement = array($fieldElement,$fieldElement);
			}
			if(substr($fieldElement[0],0,2) == '--'){
				if(substr($fieldElement[1],0,2) == '--'){
					$fieldElement[1]	= substr($fieldElement[1],2);
				}
				$html .= '<optgroup label="'.$fieldElement[1].'"> </optgroup>';
			}else{
				$html .= '<option value="'.JText::_($fieldElement[0]).'"';
				if( $fieldElement[0] == $this->getValue() ) {
					$html .= ' selected';
				}
				$html .= '>' . JText::_($fieldElement[1]) . '</option>';
			}

		}
		$html .= '</select>';
		return $html;
	}
}
class ceFieldType_selectmultiple extends ceFieldType_checkbox {

	function getInputHTML() {
		$javascript	= '';
		$numRows	= $this->params->get('max_number_rows',8);
		$html = '<select name="' . $this->getInputName() . '[]" id="' . $this->getInputName() . '" '
		.	$javascript
		. ' class="inputbox text_area'.($this->getFieldClass()).'" '
		. $this->attributes
		. ' size="'.( count($this->arrayFieldElements) > $numRows ? $numRows : count($this->arrayFieldElements) ).'"'
		. ' multiple >';

		$valueArray	= array();
		if(isset($this->field_value)){
			$valueArray	= (explode(", ", $this->field_value));

		}

		foreach($this->arrayFieldElements AS $fieldElement) {
			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
			}else{
				$fieldElement = array($fieldElement,$fieldElement);
			}
			$html .= '<option value="'.JText::_($fieldElement[0]).'"';
			if( $fieldElement[0] == $this->getValue() OR in_array($fieldElement, $valueArray)  === true) {
				$html .= ' selected';
			}
			$html .= '>' . JText::_($fieldElement[1]) . '</option>';
		}
		$html .= '</select>';
		return $html;
	}
	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}
}
/**
 * @author douglas
 * @deprecated
 */
class ceFieldType_selectrecipient extends ceFieldType_recipient{

}
/**
 * Gets a recipient select list
 * @author douglas
 * @since 1.5.8.1
 */
class ceFieldType_recipient extends ceFieldType_selectlist{
	function getInputName($count=1){
		return parent::getInputName($count);
	}

	function getInputHTML() {
		if($this->params->get('display_type', 'select') == 'select'){
			$html	= $this->getInputHTMLSelect();
		}else{ // if checkbox OR radio
			$html	= $this->getInputHTMLCheckbox();
		}
		return $html;
	}

	private function getContactEmails() {
		if(!$this->params->get('load_contact_emails')){
			return false;
		}
		$db			= JFactory::getDBO();
		$query	= $db->getQuery(true);
		//sqlsrv changes
		$case_when = ' CASE WHEN a.user_id <> 0 THEN (u.email ) ELSE email_to END ';
		$case_when = $query->concatenate(array($case_when, 'a.name'), '::');

		$query->select($case_when.' as recipient');
		$query->from('#__ce_details a');
		$query->where("a.email_to <> '' OR a.user_id > 0");
		$query->join('LEFT','#__users u ON u.id = a.user_id');

		$db->setQuery( $query );
		//echo nl2br(str_replace('#__','dj17_',$query)); exit;
		return $db->loadColumn();
	}

	function getInputHTMLCheckbox() {
		$this->_selectCounter = 0;
		$cols	= $this->params->get('display_type-checkbox-number_of_columns',1);
		$width	= number_format( (99/$cols), 1);
		$displayType	= $this->params->get('display_type');
		$html = '';
		$html .= '<div class="ce-checkbox-container">';


		$valueArray	= array();
		if(isset($this->field_value)){
			$valueArray	= (explode(", ", $this->field_value));

		}

		if (($contactEmails = $this->getContactEmails())) {
			$this->arrayFieldElements	= array_merge($this->arrayFieldElements, $contactEmails);
		}
		$classid =	JApplication::getHash(microtime());
		if($displayType == 'checkbox'){
			$html .=	$this->getSelectAllLink($classid);
		}


		foreach($this->arrayFieldElements AS $fieldElement) {
			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
				$fieldElement = array($fieldElement[1],$fieldElement[1]);
			}else{
				if (!strpos($fieldElement, '@')) {
					continue;
				}
				$fieldElement = array($fieldElement,$fieldElement);
			}


			$html .= '<div style="width:'.$width.'%;float:left">';

			$html .= '<input type="'.$displayType.'" '
					.' class="cf-input-'.$displayType.' check-me-'
							.$classid.$this->getFieldClass()
							.($this->isRequired() ? ' validate-'.$displayType:'').'"'
					.' name="' . $this->getInputName() . '[]" '
					.' id="' . $this->getInputName() . '_' . $this->_selectCounter . '" ';




			if( $fieldElement[1] == $this->getValue()
			OR in_array($fieldElement[1], $valueArray)  === true
			OR ($this->_selectCounter== 0 AND $this->params->get('checkbox_first_selected',0)) )
			{
				$html .= ' checked="checked"  ';
			}
			$html .= ' value="'.strip_tags($fieldElement[1]).'" ';
			$html .= ' '.$this->attributes.' ';
			$html .= '/> <label for="' . $this->getInputId(). '_' . $this->_selectCounter . '">'.JText::_($fieldElement[1]).'</label>';
			$this->_selectCounter++;
			$html .= '</div>';
		}
		$html .= '</div>';
		return $html;
	}

	function getInputHTMLSelect() {
		$javascript	= '';
		if($this->params->get('chain_select') AND $this->params->get('chain_select-enabled-option')){
			JHtml::_('jquery.framework');
			$javascript	= "onchange=\"JsonSelect.updateSelect('".$this->params->get('chain_select-enabled-option')."',this,'".JURI::root()."');\"";
			$doc	=JFactory::getDocument();
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/chainSelectList.js');
		}
		$html = '<select name="' . $this->getInputName() . '" id="' . $this->getInputId() . '"'
		.	$javascript. ' class="inputbox text_area'.($this->getFieldClass()).'" '.$this->attributes.' >';
		$html .= '<option value="">'.JText::_($this->params->get('first_option', 'CE_PLEASE_SELECT_ONE')).'</option>';

		if (($contactEmails = $this->getContactEmails())) {
			$this->arrayFieldElements	= array_merge($this->arrayFieldElements, $contactEmails);
		}

		foreach($this->arrayFieldElements AS $fieldElement) {
			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
				$fieldElement = array($fieldElement[1],$fieldElement[1]);
			}else{
				$fieldElement = array($fieldElement,$fieldElement);
			}
			$html .= '<option value="'.JText::_(trim($fieldElement[0])).'"';
			if( $fieldElement[0] == $this->getValue() ) {
				$html .= ' selected';
			}
			$html .= '>' . JText::_(trim($fieldElement[1])) . '</option>';
		}
		$html .= '</select>';
		return $html;
	}

	function getSelectAllLink($classid){
		if($this->params->get('display_type-checkbox-select_all_button', 0)){
			$doc	=JFactory::getDocument();
			$buttonid	= 'check-all-'.$classid;
			$script = "
window.addEvent('domready', function() {
	$('".$buttonid."').addEvent('click', function() {
		var txtSelect_all	= '".JText::_('CE_CF_CHECKBOX_SELECT_ALL')."';
		var txtSelect_none	= '".JText::_('CE_CF_CHECKBOX_SELECT_NONE')."';
		$$('.check-me-".$classid."').each(function(el) { el.checked = $('".$buttonid."').checked; });
		if($('".$buttonid."').checked){
			$('labelcheckall-".$classid."').setText(txtSelect_none);
		}else{
			$('labelcheckall-".$classid."').setText(txtSelect_all	);
		}
	});
});";
			$doc->addScriptDeclaration($script);
			return '<div class="check-all"><input type="checkbox" class="cf-input-checkbox" name="'.$buttonid.'" id="'.$buttonid.'" />
				<label for="'.$buttonid.'" id="labelcheckall-'.$classid.'">'.JText::_('CE_CF_CHECKBOX_SELECT_ALL').'</label></div>';
		}
		return '';
	}
	/**
	 * Used to get the value of a submitted field
	 * @param string	$text
	 */
	function getSelectedValue($text) {
		$recipient	= array();

		if (($contactEmails = $this->getContactEmails())) {
			$this->arrayFieldElements	= array_merge($this->arrayFieldElements, $contactEmails);
		}

		foreach($this->arrayFieldElements AS $fieldElement) {
			$fieldElement	= explode('::', $fieldElement);
			if(is_array($text)){
				foreach ($text as $email){
					if(isset($fieldElement[1]) AND stristr($fieldElement[1], trim($email))){
						$recipient[]	= trim($fieldElement[0]);
						continue;
					}
				}
			}elseif(isset($fieldElement[1]) AND stristr($fieldElement[1], trim($text))){
				$recipient[]	= trim($fieldElement[0]);
			}
		}
		return implode(',',$recipient);
	}
}

class ceFieldType_radiobutton extends ceFieldType {
	var $_selectCounter	= 0;
	function getInputHTML() {
		$javascript	= '';
		if($this->params->get('chain_select') AND $this->params->get('chain_select-enabled-option')){
			JHtml::_('jquery.framework');
			$javascript	= " onclick=\"JsonSelect.updateSelect('".$this->params->get('chain_select-enabled-option')."',this,'".JURI::root()."');\" ";
			$doc	=JFactory::getDocument();
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/chainSelectList.js');
		}
		$html 	= '';
		$cols	= $this->params->get('number_of_columns',1);
		$width	= number_format( (99/$cols), 1);
		$i = 0;
		$html .= '<div class="ce-radiobox-container">';
		$this->_selectCounter = 0;

		$valueArray	= array();
		if(isset($this->field_value)){
			$valueArray	= (explode(", ", $this->field_value));

		}
		$i		= 0;
		$count	= count($this->arrayFieldElements);
		foreach($this->arrayFieldElements AS $fieldElement) {

			if(!empty($fieldElement)) {
				$html .= '<div style="width:'.$width.'%;float:left">';

				if(strpos($fieldElement, '::') > 0){
					$fieldElement = explode('::', $fieldElement);
				}else{
					$fieldElement = array($fieldElement,$fieldElement);
				}

				$html .= ' <label for="' . $this->getInputId()  . '">'.JText::_($fieldElement[1]).'</label>';

				$html .= '<input type="radio" '
				.' class="cf-input-radio '
						//.($this->isRequired() ? ' validate-radio ':'')
// 						.( ($this->isRequired() AND $i==($count-1)) ? ' validate-one-required ' : '')
						.( ($this->isRequired() AND $i==($count-1)) ? ' validate-boxes ' : '')
						.'" '
					.' name="' . $this->getInputName() . '" '
					.' id="' . $this->getInputId()  . '" ';


				$html .= ' value="'.strip_tags($fieldElement[0]).'" ';

				if( $fieldElement[0] == $this->getValue()
					OR in_array(JText::_($fieldElement[0]), $valueArray)  === true
					OR ($this->_selectCounter== 0 AND $this->params->get('radiobutton_first_selected', 0)) )
				{
					$html .= ' checked="checked"  ';
				}
				$html .= ' '.$this->attributes.' '.$javascript;
				$html .= '/> ';
				$html .= '</div>'; $i++;
				$this->_selectCounter++;
			}
		}
		$html .= '</div>';


		if($this->isRequired() AND !defined('CE_CF_JS_ONE_REQUIRED')){
			define('CE_CF_JS_ONE_REQUIRED',1);
			JHTML::_('behavior.framework');
			$doc	= JFactory::getDocument();
			$doc->addScript(JURI::base(true).'/components/com_contactenhanced/assets/js/mootools.forms_fields.js');
		}

		return $html;
	}
	function getRecordedFieldId(){
		return '<input type="hidden" name="'.parent::getInputName().'_id" value="'.$this->field_id.'" />';
	}
	function getInputId(){
		return parent::getInputName().'_'.$this->_selectCounter;
	}
	function getLabel($output='site'){
		$html	= '';
		if($this->published AND $this->params->get('hide_field_label',0) == 0
			AND $this->params->get('hide_field_label',0) != 'overtext'){
			$label= '<label class="cf-label'.($this->isRequired() ? ' requiredField':'').'"
					id="l'.parent::getInputId().'"
					for="'.parent::getInputId().'">'
					.JText::_( $this->getInputFieldName() )
					.($this->isRequired() ? ' <span class="requiredsign">'.JText::_('CE_FORM_REQUIRED_SIGN').'</span>' : '')
					.'</label>';
			if($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'mouseover'){
				$html .= '<span class="editlinktip hasTip" title="'. JText::_( $this->tooltip ). '">'
				. $label
				. '</span>';
			}elseif($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'inline'){
				$html .= $label;
				$html .= '<div class="ce-tooltip" >'. JText::_( $this->tooltip ). '</div>';
			}else{
				$html .= $label;
			}
		}
		return $html;
	}
}
class ceFieldType_checkbox extends ceFieldType{
	var $_selectCounter	= 0;

	function ceFieldType_checkbox( $data,&$params ) {

		if( !is_null($data) ){
			foreach( $data AS $key => $value ) {
				switch($key){
					case 'value':
						$this->arrayFieldElements = explode("|",$data->$key);
						$this->$key = '';
						break;
					default:
						$this->$key = $value;
						break;
				}
			}
		}
		$this->params	= $params;
		$this->session 	= JFactory::getSession();
		$this->session	= $this->session->get('com_contactenhanced');
	}

	function getFieldClass() {
		return parent::getFieldClass(); //.' validate-one-required';
	}

	function getInputHTML() {

		$this->_selectCounter = 0;
		$cols	= $this->params->get('number_of_columns',1);
		$width	= number_format( (99/$cols), 1);
		$html = '';
		$html .= '<div class="ce-checkbox-container">';

		$valueArray	= array();
		if(isset($this->field_value)){
			$valueArray	= (explode(", ", $this->field_value));

		}

		$classid =	JApplication::getHash(microtime());
		$html	.=	$this->getSelectAllLink($classid);
		$i		= 0;
		$count	= count($this->arrayFieldElements);
		foreach($this->arrayFieldElements AS $fieldElement) {
			$i++;
			$html .= '<div style="width:'.$width.'%;float:left">';

			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
			}else{
				$fieldElement = array($fieldElement,$fieldElement);
			}

			$html .= ' <label for="' . $this->getInputId(). '">'.JText::_($fieldElement[1]).'</label>';

			$html .= '<input type="checkbox" '
			.' class="cf-input-checkbox check-me-'.$classid
				//.$this->getFieldClass()
			//	.($this->isRequired() ? ' validate-checkbox ':'')
// 				.( ($this->isRequired() AND $i==$count) ? ' validate-one-required ' : '')
				.( ($this->isRequired() AND $i==$count) ? ' validate-boxes ' : '')
				.'"'
			.' name="' . $this->getInputName() . '" '
			.' id="' . $this->getInputId() . '" ';



			if( $fieldElement[0] == $this->getValue()
				OR (is_array($valueArray) 		AND in_array($fieldElement[0], $valueArray)  === true)
				OR (is_array($this->getValue()) AND in_array($fieldElement[0], $this->getValue())  === true)
				OR ($this->_selectCounter== 0 AND $this->params->get('checkbox_first_selected',0)) )
			{
				$html .= ' checked="checked"  ';
			}
			$html .= ' value="'.strip_tags($fieldElement[0]).'" ';
			$html .= ' '.$this->attributes.' ';
			$html .= '/> ';
			$this->_selectCounter++;
			$html .= '</div>';
		}
		$html .= '</div>';

		if($this->isRequired() AND !defined('CE_CF_JS_ONE_REQUIRED')){
			define('CE_CF_JS_ONE_REQUIRED',1);
			JHTML::_('behavior.framework');
			$doc	= JFactory::getDocument();
			$doc->addScript(JURI::base(true).'/components/com_contactenhanced/assets/js/mootools.forms_fields.js');
		}
		return $html;
	}
	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}elseif($type=='submission'){
			return parent::getInputName();
		}else{
			return parent::getInputName()."[]";
			return parent::getInputName();//."[".($this->_selectCounter)."]";
		}
	}
	function getInputId(){
		return parent::getInputName().'_'.$this->_selectCounter;
	}
	function getRecordedFieldId(){
		return '<input type="hidden" name="'.parent::getInputName().'_id" value="'.$this->field_id.'" />';
	}
	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}
	function getSelectAllLink($classid){
		if($this->params->get('select_all_button', 0)){
			$doc	=JFactory::getDocument();
			$buttonid	= 'check-all-'.$classid;
			$script = "
window.addEvent('domready', function() {
	$('".$buttonid."').addEvent('click', function() {
		var txtSelect_all	= '".JText::_('CE_CF_CHECKBOX_SELECT_ALL')."';
		var txtSelect_none	= '".JText::_('CE_CF_CHECKBOX_SELECT_NONE')."';
		$$('.check-me-".$classid."').each(function(el) { el.checked = $('".$buttonid."').checked; });
		if($('".$buttonid."').checked){
			$('labelcheckall-".$classid."').setText(txtSelect_none);
		}else{
			$('labelcheckall-".$classid."').setText(txtSelect_all	);
		}
	});
});";
			$doc->addScriptDeclaration($script);
			return '<div class="check-all"><input type="checkbox" class="cf-input-checkbox" name="'.$buttonid.'" id="'.$buttonid.'" />
			<label for="'.$buttonid.'" id="labelcheckall-'.$classid.'">'.JText::_('CE_CF_CHECKBOX_SELECT_ALL').'</label></div>';
		}
		return '';
	}

}


class ceFieldType_constantcontact extends ceFieldType{
	var $_selectCounter = 0;
	var $ctct 			= null;
	var $apiKey			= null;
	var $token			= null;
	var $list			= null;

	function ceFieldType_constantcontact($data,&$params ) {
		parent::ceFieldType( $data,$params );

		// If class was already included by another script
		if(!class_exists('ConstantContact')){
			require_once JPATH_ROOT.'/components/com_contactenhanced/helpers/constant_contact/Ctct/autoload.php';
			require_once(JPATH_ROOT.'/components/com_contactenhanced/helpers/constant_contact/Ctct/ConstantContact.php');
		}

		if (is_string($this->params)) {
			$registry	= new JRegistry();
			$registry->loadString($this->params);
			$this->params = $registry;
		}

		$this->apiKey	= $this->params->get('constantcontact_api_key');
		$this->token	= $this->params->get('constantcontact_token');
		$this->list		= explode(',', $this->params->get('constantcontact_list', null) );

		for ( $i=0; $i < count($this->list); $i++ ){
			$this->list[$i] = trim($this->list[$i]);
			if (empty($this->list[$i])) {
				unset($this->list[$i]); // Remove empty values
			}
		}

		$this->ctct = ctctGetObject( $this->apiKey);

	}

	function getInputHTML() {
		$app	= JFactory::getApplication();
		$this->_selectCounter = 0;

		$html		= '<div class="cf-constantcontact">';

		if(!$this->token OR !$this->apiKey){
			$html	.= '<h1>'.JText::_('You must enter a valid API Key and a Token').'</h1>';
		}
		try {
			$listDetails	= $this->ctct->getLists($this->token);

		} catch (CtctException $ex) {
			foreach ($ex->getErrors() as $error) {
				$app->enqueueMessage($error, 'warning');
			}
			return '';
		}

		$this->getOption($html,$listDetails,'id','name');

		$html		.= '</div>';
		return $html;
	}

	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')) {
		$errors = array();
		$app	= JFactory::getApplication();
		$input = $app->input;

		$this->uservalue	= (array) $this->uservalue;
		if($this->token AND $this->apiKey AND count($this->uservalue) > 0){
			$first_name	= JRequest::getString( 'name', 		null, 'post');
			$last_name	= JRequest::getString( 'surname', 	'', 'post');
			$email		= JRequest::getString( 'email', 	null, 'post');

			try {

				// check to see if a contact with the email addess already exists in the account
				$response = $this->ctct->getContactByEmail($this->token, $email);

				// create a new contact if one does not exist
				if (empty($response->results)) {
					$isNew	= true;
					$action = "Creating Contact";
					$contact = ctctGetContactObject();
					$contact->addEmail((ctctGetContactEmailObject($email, $this->params, $isNew)) );
				} else {
					$isNew	= false;
					$action = "Updating Contact";
					$contact = $response->results[0];
				}
				foreach ($this->uservalue as $list) {
					$contact->addList($list);
				}
				$contact->first_name = $first_name;
				$contact->last_name = $last_name;

				$address_flag		= 0;
				$ctct_corefields	= array('middle_name','last_name', 'prefix_name', 'job_title', 'company_name', 'home_phone', 'work_phone', 'cell_phone', 'fax');
				$ctct_ignorefields	= array('name','surname', 'constantcontact', 'email', 'recipient');
				$ctct_addressfields	= array('address','address_line1', 'line1', 'address_line2', 'line2', 'address_line3', 'line3', 'sub_postal_code', 'postal_code', 'country', 'country_code', 'state', 'state_code', 'address_type', 'city');
				foreach(ceHelper::$submittedfields as $field){
					if(in_array($field->alias, $ctct_corefields)){
						$ctct_corefield	= $field->alias;
						$contact->$ctct_corefield = $input->get($field->alias);
					}elseif($isNew AND in_array($field->alias, $ctct_addressfields) AND !$address_flag){
						$ctct_address = array();
						$ctct_address['line1']			= $input->get('address', $input->get('address_line1', $input->get('line1', '')));
						$ctct_address['line2']			= $input->get('address_line2', $input->get('line2', ''));
						$ctct_address['line3']			= $input->get('address_line3', $input->get('line3', ''));
						$ctct_address['city']			= $input->get('city', '');
						$ctct_address['address_type']	= $input->get('address_type','PERSONAL');
						$ctct_address['state_code']		= strtolower(substr($input->get('state_code', $input->get('state', '')),0,2));
						$ctct_address['country_code']	= strtolower(substr($input->get('country_code', $input->get('country', '')),0,2));
						$ctct_address['postal_code']	= $input->get('postal_code', '');
						$ctct_address['sub_postal_code']= $input->get('sub_postal_code', '');
						++$address_flag;

						$contact->addAddress(ctctCreateAddress($ctct_address));
					}elseif(in_array($field->type, $ctct_ignorefields)){
						// do nothing
					}else{
						//$ctct_cm = ctctCreateCustomField($field);
						//$contact->addCustomField($ctct_cm);
						// Really complicated to edit Custom Fields and sinchronize them
					}
					//notes
				}
				//echo $contact->toJson(); exit;
				if (empty($response->results)) {

					$returnContact = $this->ctct->addContact($this->token, $contact, true);
					// update the existing contact if address already existed
				} else {
					$returnContact = $this->ctct->updateContact($this->token, $contact, true);
				}

				// catch any exceptions thrown during the process and print the errors to screen
			} catch (CtctException $ex) {
				$app->enqueueMessage('Error ' . $action , 'warning');
				foreach ($ex->getErrors() as $error) {
					$app->enqueueMessage($error, 'warning');
				}
				return '';
			}
		}
		return '';
	}

	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}elseif($type=='submission'){
			return parent::getInputName();
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}

	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}

	function getOption(&$html,$list,$value_name='id', $text_name='name') {

		if(is_array($list)){
			foreach ($list as $value){
				$this->getOption($html,$value,$value_name,$text_name);
			}
		}elseif (is_object($list)
				AND isset($list->status)
				AND $list->status == 'ACTIVE'
				AND (count($this->list) == 0 OR in_array(trim($list->name), $this->list))
		){
			$inputType	= $this->params->get('input_type','checkbox');

			$cols	= $this->params->get('number_of_columns',1);
			$width	= number_format( (100/$cols), 1);
			$html .= "\n".'<div style="width:'.$width.'%;float:left">';
			$html .= '<input type="'.$inputType.'" class="cf-input-checkbox'.$this->getFieldClass().($this->isRequired() ? ' required validate-checkbox':'').'" '
			.' name="' . $this->getInputName(). '" '
			.' value="'.$list->$value_name.'" '
			.' id="' . $this->getInputName() . '_' . $this->_selectCounter . '" ';
			if( $list->$value_name == $this->getValue()
					OR ($inputType == 'checkbox' AND $this->params->get('input_type-checkbox-allchecked'))
			){
				$html .= '  checked="checked"  ';
			}
			$html .= ' '.$this->attributes.' ';
			$html .= '/>
			<label for="' . $this->getInputName() . '_' . $this->_selectCounter . '">'
			.JText::_($list->$text_name).'</label>';
			$html .= '</div>';
			$this->_selectCounter++;
		}
	}

}



class ceFieldType_campaignmonitor extends ceFieldType{
	var $_selectCounter = 0;
	var $cm 			= null;
	var $apiKey			= null;
	var $clientID		= null;
	var $list			= null;

	function ceFieldType_campaignmonitor($data,&$params ) {
		parent::ceFieldType( $data,$params );

		// If class was already included by another script
		if(!class_exists('CMBase')){
			require_once JPATH_ROOT.'/components/com_contactenhanced/helpers/CMBase.php';
		}

		if (is_string($this->params)) {
			$this->params = new JParameter($this->params);
		}
		//echo '<pre>'; print_r($this->params); exit;
		$this->apiKey	= $this->params->get('campaignmonitor_api_key');
		$this->clientID	= $this->params->get('campaignmonitor_api_client');
		$this->list		= $this->params->get('campaignmonitorlist');

		$this->cm = new CampaignMonitor( $this->apiKey, $this->clientID );
		//Optional statement to include debugging information in the result
		$this->cm->debug_level = 1;

	}

	function getInputHTML() {
		$this->_selectCounter = 0;

		$html		= '<div class="cf-campaignmonitor">';

		if(!$this->clientID OR !$this->apiKey){
			$html	.= '<h1>'.JText::_('You must enter a valid API Key and a API Client ID').'</h1>';
		}

		if($this->list){
			$listDetails	= $this->cm->listGetDetail($this->list);
			$html			.= $this->getOption($html,$listDetails,'ListID','Title');

		}else{
			$listTypes		= $this->cm->clientGetLists($this->clientID);
			//	testArray($listTypes);
			$this->getOption($html,$listTypes);
			/*foreach ($listTypes as $lists){
				foreach ($lists as $listDetails){
				if(is_array($listDetails)){
				foreach ($listDetails as $value) {
				$html	.= $this->getOption($html,$value);
				}
				}else{
				$html	.= $this->getOption($html,$listDetails);
				}
				}
				}*/
		}

		$html		.= '</div>';
		return $html;
	}

	/**
	 * Subscribes the user to the selected lists. Does not show an email output as the name sugests
	 * @return	error message(s)
	 * 0: Success
	 * 1: Invalid email address
	 * The email value passed in was invalid.
	 * 100: Invalid API Key
	 * The API key pass was not valid or has expired.
	 * 101: Invalid ListID
	 * The ListID value passed in was not valid.
	 * 204: In Suppression List
	 * Address exists in suppression list. Subscriber is not added.
	 * 205: Is Deleted
	 * Email Address exists in deleted list. Subscriber is not added.
	 * 206: Is Unsubscribed
	 * Email Address exists in unsubscribed list. Subscriber is not added.
	 * 207: Is Bounced
	 * Email Address exists in bounced list. Subscriber is not added.
	 * 208: Is Unconfirmed
	 * Email Address exists in unconfirmed list. Subscriber is not added.
	 */
	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')) {
		$errors = array();
		$this->uservalue	= (array) $this->uservalue;
		if($this->clientID AND $this->apiKey AND count($this->uservalue) > 0){
			$name	= JRequest::getString( 'name', null,'post');
			$email	= JRequest::getString( 'email', null,'post');
			/**
			 * @var array Campaign Monitor Custom Fields
			 */
			$cmcf	= array();
			foreach(ceHelper::$submittedfields as $field){
				if(count($field->arrayFieldElements) > 1){
					$cmcf[$field->name]	= explode(', ',$field->uservalue);
				}else{
					$cmcf[$field->name]	= $field->uservalue;
				}

			}
			// Whether to update subscriber or not
			$update	= ($this->params->get('campaignmonitor_always_update',1) ? true : false );
			// Subscribe user in the chosen lists
			foreach ($this->uservalue as $list) {
				$CMAPIReturn = $this->cm->subscriberAddWithCustomFields($email,$name,$cmcf,$list, $update);
				// Was it success full?
				if(isset($CMAPIReturn['code']) AND intval($CMAPIReturn['code']) != 0){
					$errors[] = $CMAPIReturn['code'] .' :: '. $CMAPIReturn['message'];
				}
			}
		}
		//Displays errors if any
		if(count($errors)){
			$html= 'Campaign Monitor Erros: ';
			$html .= '<br />'.ceHelper::print_array($errors);
			return $html;
		}
		return '';
	}

	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}elseif($type=='submission'){
			return parent::getInputName();
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}
	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}
	function getOption(&$html,$list,$value_name='ListID', $text_name='Name') {
		//testArray($list);
		if(!isset($list[$value_name]) AND is_array($list)){
			foreach ($list as $value)
			$this->getOption($html,$value,$value_name,$text_name);
			//return	$this->getOption($html,$value,$value_name,$text_name);
		}elseif (is_array($list)){
			$cols	= $this->params->get('number_of_columns',1);
			$width	= number_format( (100/$cols), 1);
			$html .= '<div style="width:'.$width.'%;float:left">';
			$html .= '<input type="checkbox" class="cf-input-checkbox'.$this->getFieldClass().($this->isRequired() ? ' required validate-checkbox':'').'" '
			.' name="' . $this->getInputName(). '" '
			.' value="'.$list[$value_name].'" '
			.' id="' . $this->getInputName() . '_' . $this->_selectCounter . '" ';
			if( $list[$value_name] == $this->getValue() OR $this->params->get('cm-all-checked')){
				$html .= '  checked="checked"  ';
			}
			$html .= ' '.$this->attributes.' ';
			$html .= '/> <label for="' . $this->getInputName() . '_' . $this->_selectCounter . '">'.JText::_($list[$text_name]).'</label>';
			$html .= '</div>';
			$this->_selectCounter++;
		}

		//return $html;
	}

	public function getLists()
	{
				// Initialize variables
		$app	= JFactory::getApplication();
		$option				= JRequest::getCmd( 'option' );
		$rows	= array();

		require_once JPATH_ROOT.'/components/com_contactenhanced/helpers/CMBase.php';
		//JLoader::register('CMBase', JPATH_ROOT.'/components/com_contact_enhanced/helper/CMBase.php');
		$apiKey	= JRequest::getCmd('elemVar1');
		$clientID= JRequest::getCmd('elemVar2');
		if(!$clientID OR !$apiKey){
			echo '<h1>'.JText::_('You must enter a valid API Key and a API Client ID').'</h1>'; exit;
		}
		$cm = new CampaignMonitor( $apiKey );
		//Optional statement to include debugging information in the result
		$cm->debug_level = 1;
		$listTypes	= $cm->clientGetLists($clientID);
		$this->getListsInfo($rows,$listTypes);
		//testArray($rows);


		return $rows;
	}
	function getListsInfo(&$rows,$list,$value_name='ListID', $text_name='Name') {
		//testArray($list,false);
		if(!isset($list[$value_name]) AND is_array($list)){
			foreach ($list as $value)
			$this->getListsInfo($rows,$value,$value_name,$text_name);
			//return $this->cmGetOption($rows,$value,$value_name,$text_name);
		}elseif (is_array($list)){
			$row		= new JObject();
			$row->id	= $list[$value_name];
			$row->title	= $list[$text_name];
			$rows[]		= $row;
		}

		//return $rows;
	}
}


class ceFieldType_date extends ceFieldType {

	function getInputHTML() {
		$doc	= JFactory::getDocument();
		$lang	= JFactory::getLanguage();
		$js		= '';
		// Perform special operations if Date rage is selected
		if ($this->params->get('datepicker-range')) {
			$this->params->set('datepicker-template','datepicker');
			$this->params->set('datepicker-timePicker',0);
			if ($this->params->get('datepicker-columns') < 2) {
				$this->params->set('datepicker-columns',3);
			}

			$js	.= "$$('.datepicker .footer input').addClass('inputbox');";
			$js	.= "$$('.datepicker .footer button').addClass('button');";
		}

		$this->getJavascript();

		$format	= $this->params->get('date-format',JText::_('CE_CF_DATE_FORMAT'));
		$js_properties	= array();

		if( ($min = trim($this->params->get('datepicker-minDate'))) ){
			if($min == 'today'){
				$min	= time();
				$min	= date('Y-m-d',$min);
			}elseif(strlen($min) > 0 AND strlen($min)< 5){
				$min	= intval($min);
				$min	= time()+($min*86400); // 1 day = 86400 seconds
				$min	= date('Y-m-d',$min);
			}

			$js_properties[]= "minDate: Date.parse('{$min}')";
		}

		if( ($max = trim($this->params->get('datepicker-maxDate'))) ){
			if($max == 'today'){
				$max	= time();
				$max	= date('Y-m-d',$max);
			}elseif(strlen($max) > 0 AND strlen($max)< 5){
				$max	= intval($max);
				$max	= time()+($max*86400); // 1 day = 86400 seconds
				$max	= date('Y-m-d',$max);
			}

			$js_properties[]= "maxDate: Date.parse('{$max}')";
		}

		//$offset = ( (time() - strtotime(JHtml::date(time() , 'Y-m-d H:i:s'))) / 3600);

		$js_properties[]	= "useFadeInOut: !Browser.ie";
		$js_properties[]	= "pickerClass:	'".( $this->params->get('datepicker-columns') > 1 ? 'datepicker' : $this->params->get('datepicker-template','datepicker') )."'";
		$js_properties[]	= "startDay:	".$this->params->get('datepicker-startDay',0);
		$js_properties[]	= "columns:		".$this->params->get('datepicker-columns',1);
		$js_properties[]	= "weeknumbers:	".$this->params->get('datepicker-weeknumbers','false');
		$js_properties[]	= "startView:	'".$this->params->get('datepicker-startView','days')."'";
		$js_properties[]	= "format:		'{$format}".($this->params->get('datepicker-timePicker') ? ' '.$this->params->get('datepicker-timePicker') : '')."'";

		if($this->params->get('datepicker-timePicker',0)){
			$js_properties[]	= "timePicker:	true";
		}

		if($this->params->get('datepicker-pickOnly',0)){
			$js_properties[]	= "pickOnly:	'".$this->params->get('datepicker-pickOnly',0)."'";
		}
		if($lang->isRTL()){
			$js_properties[]	= "rtl:	true";
		}

		$jsDdateClass	= 'Picker.Date'.($this->params->get('datepicker-range') ? '.Range' : '');

		$doc->addScriptDeclaration(
				"window.addEvent('domready', function(){
				".$this->getLocale()."
				new {$jsDdateClass}('".$this->getInputName()."', {
						".implode(",\n\t\t",$js_properties)."
		});
				{$js}
	});
	");


	// Field
	$fieldAttributes	= ' class="inputbox cf-input-date '.($this->getFieldClass()).'" '.$this->attributes;
	$value	= $this->getValue();
	if (is_array($value) ) {
	if(isset($value[$this->_selectCounter])){
		$value	= $value[$this->_selectCounter];
	}
	}
	$html	= '<input
						'.$fieldAttributes.'
						type="text"
						name="' . $this->getInputName() . '"
						id="'	. $this->getInputId() . '"
						value="'. ($this->getValue()) . '"
						title="'.$this->name.'"
					/>
					';


	return $html;
	}

	public function getJavascript() {
		$lang	= JFactory::getLanguage();
		$doc = JFactory::getDocument();

		jimport('joomla.filesystem.file');
		$locale	= "Locale.use('en-GB');";
		if(JFile::exists(CE_SITE_COMPONENT.'assets/datepicker/language/Locale.'.$lang->getTag().'.DatePicker.js')){
			$doc->addScript(JURI::base(true).'/components/com_contactenhanced/assets/datepicker/language/Locale.'.$lang->getTag().'.DatePicker.js');
			$locale	= "Locale.use('".$lang->getTag()."');";
		}
		$doc->addScriptDeclaration($locale);

		$doc->addStyleSheet( JURI::base(true).'/components/com_contactenhanced/assets/datepicker/templates/'
				.( $this->params->get('datepicker-columns') > 1 ? 'datepicker' : $this->params->get('datepicker-template','datepicker') )
				.'/datepicker.css');

		$doc->addScript(JURI::base(true).'/components/com_contactenhanced/assets/datepicker/Picker.js');
		$doc->addScript(JURI::base(true).'/components/com_contactenhanced/assets/datepicker/Picker.Attach.js');
		$doc->addScript(JURI::base(true).'/components/com_contactenhanced/assets/datepicker/Picker.Date.js');
		if ($this->params->get('datepicker-range')) {
			$doc->addScript(JURI::base(true).'/components/com_contactenhanced/assets/datepicker/Picker.Date.Range.js');
		}

	}


	public function getLocale() {
		$lang	= JFactory::getLanguage();
		$doc = JFactory::getDocument();

		jimport('joomla.filesystem.file');
		$locale	= "Locale.use('en-GB');";
		if(JFile::exists(CE_SITE_COMPONENT.'assets/datepicker/language/Locale.'.$lang->getTag().'.DatePicker.js')){
			$doc->addScript(JURI::base(true).'/components/com_contactenhanced/assets/datepicker/language/Locale.'.$lang->getTag().'.DatePicker.js');
			$locale	= "Locale.use('".$lang->getTag()."');";
		}
		return $locale;
	}
}



class ceFieldType_date_deprecated extends ceFieldType {

	function getInputHTML() {
		$fieldAttributes	= ' class="inputbox'.($this->getFieldClass()).'" '.$this->attributes;
		$value	= $this->getValue();
		if (is_array($value) ) {
			if(isset($value[$this->_selectCounter])){
				$value	= $value[$this->_selectCounter];
			}
		}
		if( ($min = trim($this->params->get('date-min'))) ){
			if($min == 'today'){
				$min	= date('Ymd');
			}elseif(strlen($min)< 5){
				$min	= intval($min);
				$min	= mktime(0, 0, 0, date("m")  , date("d")+$min, date("Y"));
				$min	= date('Ymd',$min);
			}
			$min	= ",min: {$min}";
		}

		$html	= JHTML::_('calendar',
						$value,
						$this->getInputName(),
						$this->getInputId(),
						$this->params->get('date-format',JText::_('CE_CF_DATE_FORMAT')),
						$fieldAttributes
					);
		$document = JFactory::getDocument();
		$document->addScriptDeclaration('window.addEvent(\'domready\', function() {Calendar.setup({
		// Id of the input field
		inputField: "'.$this->getInputId().'",
		// Format of the input field
		ifFormat: "'.$this->params->get('date-format',JText::_('CE_CF_DATE_FORMAT')).'",
		// Trigger for the calendar (button ID)
		button: "'.$this->getInputId().'",
		// Alignment (defaults to "Bl")
		align: "Tl",
		singleClick: true,
		firstDay: '.JFactory::getLanguage()->getFirstDay().'
		'.$min.'
		});});');

		return $html;
	}
}

class ceFieldType_daterange extends ceFieldType_date_deprecated {
	var $_selectCounter	= 0;
	function getInputHTML() {
		$this->_selectCounter	= 0;
		$html	= parent::getInputHTML($this->_selectCounter);
		$html	.= ' '.JText::_('CE_CF_DATE_RANGE_TO').' ';
		$this->_selectCounter	= 1;
		$html	.= parent::getInputHTML($this->_selectCounter);
		return $html;
	}

	function getInputId(){
		return parent::getInputName().'_'.$this->_selectCounter;
	}

	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}elseif($type=='submission'){
			return parent::getInputName();
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}
	function getRecordedFieldId(){
		return '<input type="hidden" name="'.parent::getInputName().'_id" value="'.$this->field_id.'" />';
	}
	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		return parent::getEmailOutput(' '.JText::_('CE_CF_DATE_RANGE_TO').' ', $format, $style);
	}


	/**
	 * Client side validation.
	 */
	function getValidationScript() {
		$script = "";
		$this->_selectCounter	= 0;
		$script .= '
			var dateFrom	= $("'.$this->getInputId().'").get("value");
		';
		$this->_selectCounter	= 1;
		$script .= '
			var dateTo		= $("'.$this->getInputId().'").get("value");
		';

		switch ($this->params->get('date-format',JText::_('CF_DATE_FORMAT'))) {
			case '%m-%d-%Y':
				$script .= "
						var month1  = parseInt(dateFrom.substring(0,2),10);
						var day1 	= parseInt(dateFrom.substring(3,5),10);
						var year1	= parseInt(dateFrom.substring(6,10),10);
						var month2  = parseInt(dateTo.substring(0,2),10);
						var day2 	= parseInt(dateTo.substring(3,5),10);
						var year2	= parseInt(dateTo.substring(6,10),10);
				";
				break;
			case '%Y-%m-%d':
				$script .= "
						var year1	= parseInt(dateFrom.substring(0,4),10);
						var month1  = parseInt(dateFrom.substring(5,7),10);
						var day1 	= parseInt(dateFrom.substring(8,10),10);
						var year2	= parseInt(dateTo.substring(0,4),10);
						var month2  = parseInt(dateTo.substring(5,7),10);
						var day2 	= parseInt(dateTo.substring(8,10),10);
				";
				break;
			case '%d-%b-%Y':
			case '%b-%d-%Y':
				return '';
				break;
			case '%d-%m-%Y':
			default:
				$script .= "
						var day1	= parseInt(dateFrom.substring(0,2),10);
						var month1 	= parseInt(dateFrom.substring(3,5),10);
						var year1	= parseInt(dateFrom.substring(6,10),10);
						var day2	= parseInt(dateTo.substring(0,2),10);
						var month2 	= parseInt(dateTo.substring(3,5),10);
						var year2	= parseInt(dateTo.substring(6,10),10);
				";
				break;
		}
		$script	.= '
			var dateFrom = new Date(year1,month1,day1);
			var dateTo	= new Date(year2,month2,day2);
			if(dateTo < dateFrom)
			{
				logObj.addClass("ce-error");
				logObj.set("html","'. JText::_('CE_CF_DATERANGE_ERROR_DATEFROM_GREATER_THAN_DATETO') .'");
				logFx.slideIn();
				return false;
			}
';

		return $script;
	}

	/**
	 * Server side validation
	 */
	function validateField() {
		if(parent::validateField()){
			$dateFrom	= $this->uservalue[0];
			$dateTo		= $this->uservalue[1];
			$dateFrom	= $this->dateToUnix($dateFrom);
			$dateTo		= $this->dateToUnix($dateTo);
			if($dateFrom <= $dateTo){
				return true;
			}else{
				return false;
			}
		}else{
			return false;
		}
	}


	function dateToUnix($date) {
		$date	= explode('-',$date);
		switch ($this->params->get('date-format',JText::_('CE_CF_DATE_FORMAT'))) {
			case '%m-%d-%Y':
				$day	= (int)$date[1];
				$month	= (int)$date[0];
				$year	= (int)$date[2];
				break;
			case '%Y-%m-%d':
				$day	= (int)$date[2];
				$month	= (int)$date[1];
				$year	= (int)$date[0];
				break;
			case '%d-%b-%Y':
				$day	= (int)$date[0];
				$month	= (string)$date[1]; // Abbreviated Month
				$year	= (int)$date[2];
				break;
			case '%b-%d-%Y':
				$day	= (int)$date[1];
				$month	= (string)$date[0]; // Abbreviated Month
				$year	= (int)$date[2];
				break;
			case '%d-%m-%Y':
			default:
				$day	= (int)$date[0];
				$month	= (int)$date[1];
				$year	= (int)$date[2];
				break;
		}

		if(is_string($month)){
			switch ($month) {
				case JText::_('JANUARY_SHORT'):
				case JText::_('JANUARY'):
					$month	= 1;
					break;
				case JText::_('FEBRUARY_SHORT'):
				case JText::_('FEBRUARY'):
					$month	= 2;
					break;
				case JText::_('MARCH_SHORT'):
				case JText::_('MARCH'):
					$month	= 3;
					break;
				case JText::_('APRIL_SHORT'):
				case JText::_('APRIL'):
					$month	= 4;
					break;
				case JText::_('MAY_SHORT'):
				case JText::_('MAY'):
					$month	= 5;
					break;
				case JText::_('JUNE_SHORT'):
				case JText::_('JUNE_SHORT'):
					$month	= 6;
					break;
				case JText::_('JULY_SHORT'):
				case JText::_('JULY'):
					$month	= 7;
					break;
				case JText::_('AUGUST_SHORT'):
				case JText::_('AUGUST'):
					$month	= 8;
					break;
				case JText::_('SEPTEMBER_SHORT'):
				case JText::_('SEPTEMBER'):
					$month	= 9;
					break;
				case JText::_('OCTOBER_SHORT'):
				case JText::_('OCTOBER'):
					$month	= 10;
					break;
				case JText::_('NOVEMBER_SHORT'):
				case JText::_('NOVEMBER'):
					$month	= 11;
					break;
				case JText::_('DECEMBER_SHORT'):
				case JText::_('DECEMBER'):
					$month	= 12;
					break;
			}
		}

		return mktime(0, 0, 0, $month, $day, $year);
	}
}

class ceFieldType_multiplefiles extends ceFieldType {
	function getInputHTML() {

		JHTML::_('behavior.framework');
		$max_file_size	= (int) $this->params->get('max_file_size',300);
		$max_file_size	= $max_file_size * 1024;
		$number_of_files= (int) $this->params->get('mf_number_of_files',3);
		$doc	= JFactory::getDocument();
		//$doc->addScript( JURI::root(). 'components/com_contactenhanced/helpers/multiupload/Stickman.MultiUpload.compressed.js' );
		$doc->addScript( JURI::root(). 'components/com_contactenhanced/helpers/multiupload/Stickman.MultiUpload.js' );
		$doc->addStyleSheet(	 JURI::root(). 'components/com_contactenhanced/helpers/multiupload/Stickman.MultiUpload.css');
		$script	= "window.addEvent('domready', function(){ "
		//. " new MultiUpload( $( 'emailForm' ).".$this->getInputName().", 3, '[{id}]', true, true );"
		. "var multipleUpload = new MultiUpload({
		deleteimg:	'".JURI::base()."components/com_contactenhanced/helpers/multiupload/cross_small.gif',
		input_element: $('".$this->getInputName()."'),
		max:					'".$number_of_files."',
		name_suffix_template:	'[{id}]',
		show_filename_only:		true,
		required:				".($this->isRequired() ? 'true' : 'false').",
		remove_empty_element:	true,
		formID: 'ceForm".$this->params->get('contactId')."',
		language: {
			txtdelete:			'".$this->escapeJSText(JText::_('CF_MULTIPLE_FILES_REMOVE_FILE'))."',
			txtnotfileinput:	'".$this->escapeJSText(JText::_('CF_MULTIPLE_FILES_ERROR_MISSING_INPUT_ELEMENT'))."',
			txtnomorethan:		'".$this->escapeJSText(JText::_('CF_MULTIPLE_FILES_YOU_MAY_NOT_UPLOAD_MORE_THAN'))."',
			txtfiles:			'".$this->escapeJSText(JText::_('CF_MULTIPLE_FILES_FILES'))."',
			txtareyousure:		'".$this->escapeJSText(JText::_('CF_MULTIPLE_FILES_CONFIRM_REMOVE'))."',
			txtfromqueue:		'".$this->escapeJSText(JText::_('CF_MULTIPLE_FILES_FROM_QUEUE'))."',
			filesleft:			'".$this->escapeJSText(JText::_('CF_MULTIPLE_FILES_FILES_LEFT'))."'
			}
  	});"
  	. " });";

	  	$doc->addScriptDeclaration($script);
	  	$html	= '<input type="file"
	  				class="cf-input-file inputbox '.$this->getFieldClass().'"
	  				name="'.$this->getInputName().'"
	  				id="'.$this->getInputName().'" '
	  				.$this->attributes .' />'

	  	.'<input type="hidden" name="MAX_FILE_SIZE" value="'.$max_file_size.'" /><br clear="all" />';
	  	$html	.= '<small>'. JText::_('CF_MULTIPLE_FILES_MAX_FILESIZE_ALLOWED').': '.ceHelper::formatBytes($max_file_size).'</small>';
	  	if ($this->params->get('mf_show_number_of_files_allowed',1)) {
	  		$html	.= ' <small class="mf-number-files">'.JText::_('CF_MULTIPLE_FILES_FILES_LEFT').' '.$number_of_files.'</small> ';
	  	}
	  	return $html;
	}

	function validateFileExtension(&$filename){
		$filter_file_extensions	= $this->params->get('mf_filter_file_extensions');
		$mf_filter_type			= $this->params->get('mf_filter_type');
		$file_extension			= JFile::getExt($filename);

		if($mf_filter_type == 'blacklist' AND stripos($filter_file_extensions,$file_extension) === true){
			$this->errors[]	= JText::_('CF_MULTIPLE_FILES_FILE_TYPE_NOT_ALLOWED');
			return false;
		}else if($mf_filter_type == 'whitelist' AND stripos($filter_file_extensions,$file_extension) === false){
			$this->errors[]	=  JText::_('CF_MULTIPLE_FILES_FILE_TYPE_NOT_ALLOWED');
			return false;
		}
		return true;
	}

	public function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')) {
		return '';
	}
	function getFieldClass() {
		return ($this->isRequired() ? ' ':'').parent::getFieldClass(); //validate-file
	}
}

class ceFieldType_file extends ceFieldType {
	function getInputHTML() {
		$html	= '<input type="file" class="cf-input-file" name="'.$this->getInputName().'" id="'.$this->getInputName().'" '.$this->attributes
		. ' class="inputbox '.$this->getFieldClass().(($this->isRequired()) ? ' validate-file':'').'" '
		.' />'
		.'<input type="hidden" name="MAX_FILE_SIZE" value="102400" /><br/>';
		return $html;
	}
}

class ceFieldType_name extends ceFieldType {
	function getInputHTML() {
		$class	= 'inputbox cf-input-text '.($this->published ? $this->getFieldClass() : '');

		if($this->params->get('validation')
				AND $this->params->get('validation') != 'date'
				AND $this->params->get('validation') != 'custom'  ){
			$class	.= ' validate-'.$this->params->get('validation');
		}elseif ($this->params->get('validation') == 'date'){
			$dataValidators="validate-date dateFormat:'{$this->params->get('validation-date-format','%d-%m-%Y')}'";
		}elseif ($this->params->get('validation') == 'custom'
				AND $this->params->get('validation-custom-name')
				AND $this->params->get('validation-custom-errorMsg')
				AND $this->params->get('validation-custom-test')){
			$customValidatorName	= JApplication::stringURLSafe($this->params->get('validation-custom-name'));
			$class	.= ' '.$customValidatorName;

			$doc	=JFactory::getDocument();
			$doc->addScriptDeclaration("
window.addEvent('domready', function(){
	Form.Validator.add('".$customValidatorName."', {
		errorMsg: '".addslashes($this->params->get('validation-custom-errorMsg'))."',
		test: function(element){
			return ".$this->params->get('validation-custom-test')."
		}
	});
});
			");
		}

		if( trim( $this->params->get('minLength') ) ){
			$class	.= ' minLength:'.$this->params->get('minLength',0);
		}
		if( trim( $this->params->get('maxLength') ) ){
			$class	.= ' maxLength:'.$this->params->get('maxLength');
		}
		$user		= JFactory::getUser();
		$html	= '<input
						title="'.$this->name.'"
						type="'.($this->published ? 'text' : 'hidden').'"
						name="name" id="name" '
		//. ($user->get('name') ? ' readonly ' : '')
		. ' class="'.$class.'" '
		. ' value="'. ($this->getValue() ? $this->getValue() : $user->get('name') ). '"
				'.$this->attributes.' />';
		return $html;
	}
	function getInputName($count=1){
		return 'name';
	}
}
class ceFieldType_surname extends ceFieldType {
	function getInputName($count=1){
		return 'surname';
	}
}
class ceFieldType_email extends ceFieldType {
	function getInputHTML() {
		if($this->getValue() ){
		//if(!$this->params->get('plugin_active') AND $this->getValue() ){
			$value	= $this->getValue();
		}elseif(!$this->params->get('plugin_active')){
			$user		= JFactory::getUser();
			$value	= $user->get('email');
		}else{
			$value	= '';
		}

		$html	= '<input
				title="'.$this->name.'"
				type="'.($this->published ? 'text' : 'hidden').'"
				id="email"
				name="email" '
		//. ($user->get('email') ? ' readonly ' : '')
			. ' class="inputbox cf-input-text'.$this->getFieldClass().($this->isRequired() ? ' validate-email':'').'" '
			. ' value="'.$value. '"
			'.$this->attributes.' />';

		if(	$this->isRequired()
		AND ($this->params->get('email_registration') OR  $this->params->get('email_validation')) )
		{
			$doc =JFactory::getDocument();
			$script = "
window.addEvent('domready', function(){
	$('email').addEvent('blur', function(e) {
		if((/^(?:[a-z0-9!#$%&'*+\/=?^_`{|}~-]\.?){0,63}[a-z0-9!#$%&'*+\/=?^_`{|}~-]@(?:(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)*[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\])$/i).test($('email').get('value'))){
			var log_res = $('email-ajax-response');
			log_res.addClass('ajax-loading');
			log_res.setStyle('display', 'block');
			var url	= '".JURI::root()."index.php?option=com_contactenhanced&task=checkemail&tmpl=raw&registration=".$this->params->get('email_registration')."';
			var jSonRequest = new Request.JSON({url:url, onSuccess: function(response){
				if(response.action == 'success'){
					//email is already in use
					$('email').removeClass('validation-failed');
					$('email').addClass('validation-passed');
					$('email').addClass('success');
					log_res.setStyle('display', 'none');
				}else{
					$('email').removeClass('validation-passed');
					$('email').removeClass('success');
					$('email').addClass('validation-failed');
					log_res.addClass('validation-advice');
				}
				log_res.set('html',response.msg);
				log_res.removeClass('ajax-loading');
				}
			}).get({'email':$('email').value});
		}
	});
});
";
			$doc->addScriptDeclaration($script);
			$html	.= '<div id="email-ajax-response" style="display:none" ></div>';
		}

		return $html;
	}
	function getInputName($count=1){
		return 'email';
	}

	function validateField(){
		JRequest::setVar('registration',$this->params->get('email_registration'));
		$botScoutAPIKey	= ( ($this->params->get('botScout-api-key')) ? $this->params->get('botScout-api-key') : false);
		$ret	= ceHelper::checkEmail(JRequest::getVar('email'), $botScoutAPIKey);

		if( ($ret['action'] == 'error' AND JRequest::getVar('email'))
				OR ($this->isRequired() AND $ret['action'] == 'error'))
		{
			JFactory::getApplication()->enqueueMessage($ret['msg'],'notice');
			return false;
		}

		return parent::validateField();
	}
}

class ceFieldType_email_verify extends ceFieldType {
	function getInputHTML() {
		$fieldAttributes	= ' class="inputbox cf-input-text cf-input-emailverify validate-emailverify '.($this->getFieldClass()).'" '.$this->attributes;
		$html	= '<input  title="'.$this->name.'"  type="text" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"  '
		.$fieldAttributes.  'value="'.htmlspecialchars($this->getValue()).'" />';
		return $html;
	}
	function getInputName($count=1){
		return 'email_verify';
	}
}

class ceFieldType_subject extends ceFieldType {
	function getInputHTML() {
		if(count($this->arrayFieldElements) > 1){
			$html = '<select name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"'
			.	' class="inputbox text_area'.($this->published ? $this->getFieldClass() : '').'" '.$this->attributes.' >';
			$html .= '<option value="">'.JText::_($this->params->get('first_option', 'CE_PLEASE_SELECT_ONE')).'</option>';
			foreach($this->arrayFieldElements AS $fieldElement) {
				if(strpos($fieldElement, '::') > 0){
					$fieldElement = explode('::', $fieldElement);
				}else{
					$fieldElement = array($fieldElement,$fieldElement);
				}
				$html .= '<option value="'.JText::_($fieldElement[0]).'"';
				if( $fieldElement[0] == $this->getValue() ) {
					$html .= ' selected';
				}
				$html .= '>' . JText::_($fieldElement[1]) . '</option>';
			}
			$html .= '</select>';
		}else{
			$html	= '<input  title="'.$this->name.'"  type="'.($this->published ? 'text' : 'hidden').'" name="subject" id="subject" '
			. ' class="inputbox cf-input-text'.($this->published ? $this->getFieldClass() : '').'" '
			. ' value="'. ($this->getValue() ? $this->getValue() : '' ). '" '.$this->attributes.' />';
		}
		return $html;
	}
	function getInputName($count=1){
		return 'subject';
	}
	function getValue($arg=null){

		if($this->value){
			return $this->value;
		}else{
			return parent::getValue($arg);
		}
	}
}
class ceFieldType_numberrange extends ceFieldType{
	var $_selectCounter = 0;
	function getInputHTML() {
		$html = '';
		$this->_selectCounter = 0;
		foreach($this->arrayFieldElements AS $fieldElements) {

			$fieldElement = explode('-',$fieldElements);
			if(!isset($fieldElement[1])){
				$fieldElement[1] = $fieldElement[0];
			}elseif (($fieldElement[0]-$fieldElement[1]) > 200 ){
				return JText::sprintf('CE_CF_NUMBER_ERROR_FIELD_RANGE_TOO_WIDE',($fieldElement[0]-$fieldElement[1]));
			}
			if(count($this->arrayFieldElements) == 1){
				$nrLabel = JText::_('CE_PLEASE_SELECT_ONE');
			}elseif($this->_selectCounter > 0){
				$nrLabel = JText::_('CE_CF_NUMBER_RANGE_TO');
			}else{
				$nrLabel = JText::_('CE_CF_NUMBER_RANGE_FROM');
			}


			$html .= '<select name="' . $this->getInputName() . '" id="' . $this->getInputName() . '" class="inputbox text_area'.($this->getFieldClass()).'" '.$this->attributes.' >';
			$html .= '<option value="">'.$nrLabel.'</option>';
			for($i=$fieldElement[0]; $i <= $fieldElement[1]; $i++){
				$html .= 	'<option value="'.$i.'"';
				if( $i == $this->getValue() ) {
					$html .= ' selected';
				}
				$html .=	' >'.$i.'</option>';
			}
			$html .= '</select> ';
			$this->_selectCounter++;
		}
		return $html;
	}
	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}elseif($type=='submission'){
			return parent::getInputName();
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}
	function getRecordedFieldId(){
		return '<input type="hidden" name="'.parent::getInputName().'_id" value="'.$this->field_id.'" />';
	}

	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		return parent::getEmailOutput(' '.JText::_('CE_CF_NUMBER_RANGE_TO').' ', $format, $style);
	}
}
class ceFieldType_number extends ceFieldType_numberrange{

}

class ceFieldType_freetext extends ceFieldType {
	function getInputHTML() {
		return '<div class="ce-freetext-container" '.$this->attributes.' >'.JText::_($this->getValue()).'</div>';
	}
	function getFieldHTML() {
		if(strpos($this->getValue(), '<fieldset>') !== false
		OR strpos($this->getValue(), '</fieldset>') !== false){
			return $this->getValue();
		}else{
			return parent::getFieldHTML();
		}
	}
	function getValue($arg=null){
		if($this->params->get('parse_content_plugins',0)){
			/*
			 * Handle display events
			 */
			// add full article object to avoid problems with plugins
			$article = new stdClass();
			$article->id	= $this->params->get('contactId',0);
			$article->text	= $this->value;
			$article->event = new stdClass();

			ceHelper::processContentPlugin($this->params, $article);

			return $article->text;
		}else{
			return $this->value;
		}
	}
	public function getLabel($output='site'){
		return '';
	}
}


class ceFieldType_password extends ceFieldType {
	function getInputHTML() {
		$fieldAttributes	= ' class="inputbox cf-input-text cf-input-password password '.($this->getFieldClass()).'" '.$this->attributes;
		$html	= '<input  title="'.$this->name.'"  type="password" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"  '
		.$fieldAttributes.' value="'.htmlspecialchars($this->getValue()).'" />';
		return $html;
	}
	function getInputName($count=1){
		return 'password';
	}
}
class ceFieldType_password_verify extends ceFieldType {
	function getInputHTML() {
		$doc	=JFactory::getDocument();
		$doc->addScript(JURI::base(true).'/components/com_contactenhanced/assets/js/mootools.forms_fields.js');
		JText::script('COM_CONTACTENHANCED_CF_PASSWORD_VERIFY_VALIDATION_ERROR_MSG',true);

		$fieldAttributes	= ' class="inputbox cf-input-text cf-input-password_verify validate-passverify '.($this->getFieldClass()).'" '
								.$this->attributes;
		$html	= '<input  title="'.$this->name.'"
							type="password" name="' . $this->getInputName() . '"
							id="' . $this->getInputName() . '"  '
							.$fieldAttributes.  '
							value="'.htmlspecialchars($this->getValue()).'" />';
		return $html;
	}
	function getInputName($count=1){
		return 'password_verify';
	}

	function validateField() {
		//echo 'here'.ceHelper::print_r($this->type. ' '.$this->uservalue); exit;
		if($this->isRequired() AND (JRequest::getVar('password') != JRequest::getVar('password_verify'))){
			return false;
		}
		return true;
	}
}


class ceFieldType_username extends ceFieldType {
	function getInputHTML() {
		$doc =JFactory::getDocument();
		$logDiv	= 'CElog_res';
		$success	= array();
		$success['class'] = 'success';
		$failure	= array();
		$failure['class'] = 'invalid';
		$script = "
window.addEvent('domready', function(){
	$('ce-username').addEvent('blur', function(e) {
		//e = new Event(e).stop();
		var urlScript	= '".JURI::root()."index.php?option=com_contactenhanced&amp;task=checkusername&amp;tmpl=raw&amp;registration=".$this->params->get('username_registration')."';
		var log_res = '".$logDiv."';
		//build the request
		var jSonRequest = new Request.JSON({url:urlScript, onComplete: function(response){
				$('ce-username').removeClass('invalid');
				$('ce-username').removeClass('success');
				$('ce-username').addClass(response.class);
				//update the response p
				$(log_res).set('html',response.msg);
				$(log_res).setStyle('display', 'block');
				$(log_res).removeClass('ajax-loading');
			}
		}).get(({'username':$('ce-username').value}));
	});

});
";
		$doc->addScriptDeclaration($script);
		$fieldAttributes	= ' class="inputbox cf-input-text cf-input-username'.($this->getFieldClass()).' validate-username"'
		.$this->attributes;
		$html	= '<input  title="'.$this->name.'"  type="text" name="username" id="ce-username"  '.$fieldAttributes.' value="'.htmlspecialchars($this->getValue()).'" />';
		$html	.= '<div id="'.$logDiv.'" ></div>';
		return $html;
	}
	function getInputName($count=1){
		return 'username';
	}
	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		return parent::getEmailOutput($delimiter,$format,$style);
	}
	function validateField(){
		if($this->params->get('username_registration') AND $this->isRequired()){
			$email		= JRequest::getVar('email','');
			$username	= preg_replace( "/^([^@]+)(@.*)$/", "$1", $email);
			$username	= JRequest::getVar('username',$username);
			$db			= JFactory::getDBO();
			$query		= $db->getQuery(true);
			$query->select('count(id)');
			$query->from('#__users');
			$query->where('username = '.$db->Quote($username));
			$db->setQuery( $query );

			// Abort operation if the user is already registered
			if($db->loadResult()){
				JFactory::getApplication()->enqueueMessage(JText::sprintf('USER_REGISTERED_USERNAME_NOT_AVAILABLE',$username),'notice');
				return false;
			}else {
				return parent::validateField();
			}
		}
		return true;
	}
}

class ceFieldType_hidden extends ceFieldType {
	function getInputHTML() {
		$html	= '<input type="hidden" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"  value="' . htmlspecialchars($this->getValue()) . '" '.$this->attributes.' />';
		return $html;
	}
	public function getLabel($output='site'){
		return '';
	}
}

class ceFieldType_sql extends ceFieldType {
	function getInputHTML() {
		JHTML::_('behavior.framework');
		$doc	= JFactory::getDocument();
		$doc->addScript( JURI::root(). 'components/com_contactenhanced/assets/js/addtablerow.js' );
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$isChainSelect	= false;

		$javascript	= '';
		if($this->params->get('chain_select') AND $this->params->get('chain_select-enabled-option')){
			JHtml::_('jquery.framework');
			$javascript	= "onchange=\"JsonSelect.updateSelect('".$this->params->get('chain_select-enabled-option')."',this,'".JURI::root()."');\"";
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/chainSelectList.js');
		}

		if($this->value){

			if($this->params->get('isAdmin')){
				$query->select('m.from_id');
				$query->from('#__ce_messages m');
				$query->join('INNER', '#__ce_message_fields mf ON mf.message_id = m.id');
				$query->where('mf.id = '.$db->Quote($this->field_id));

				$db->setQuery($query);
				$user	= $db->loadResult();
				$user	= JFactory::getUser($user);
			}else{
				$user	= JFactory::getUser();
			}


			$regex = '/{user_id}/i';
			$this->value  = preg_replace( $regex, $user->id, $this->value );

			$regex = '/{user_email}/i';
			$this->value  = preg_replace( $regex, $user->email, $this->value );

			$regex = '/{username}/i';
			$this->value  = preg_replace( $regex, $user->username, $this->value );

			$regex = '/{selectresult}/i';
			$isChainSelect  = preg_match( $regex, $this->value );
			if($isChainSelect){
				$this->value  = preg_replace( $regex, '', $this->value );
			}
			//echo $this->value; exit;
			$db->setQuery( $this->value );
			//echo '<pre>'.$db->getQuery( ).'</pre>'; exit;
			$rows = $db->loadObjectList();
		}else{
			return JText::_($this->params->get('sql_no_result_msg','There is no SQL in the Value field'));
		}
		if(!isset($rows) OR
			(count($rows) <= 0 AND !$this->params->get('hide_field') AND !$isChainSelect)){
			return JText::_($this->params->get('sql_no_result_msg',''));

		}else if($this->params->get('hide_field') ){
			$html	= '';
			$i = 0;
			foreach($rows as $row){
				$html	.= '<input type="hidden" value="'.$row->value.'" name="'.$this->getInputName().'['.$i++.']" />';
			}
			return $html;

		}else if( (is_array($rows) AND count($rows) ) OR $isChainSelect){
			$options	= array();
			$options[]	= JHTML::_('select.option',  '', JText::_($this->params->get('first_option', 'CE_PLEASE_SELECT_ONE')) );
			$fieldClass	= 'inputbox cf-input-text ce-cf-sql '.($this->getFieldClass());
			if(is_array($rows) AND count($rows)){
				foreach($rows as $row){
					if( substr($row->text,0,2) == '--'){
						$options[]	=	JHTML::_('select.optgroup',  str_replace('--','',JText::_( $row->text ) ) );
					}else{
						$options[]	=	JHTML::_('select.option',  $row->value, JText::_( $row->text ) );
					}
				}
			}


			if(isset($this->field_value)){
				$valueArray	= explode("\n", $this->field_value);
			}else{
				$valueArray	= array($this->getValue());
			}


			$html	= '';
			$addButton	='<div>'
			.' <a href="#'.JText::_( 'CF_SQL_ADD' ).'" onclick="inject_row(\''.parent::getInputName().'\')">'.JText::_( 'CF_SQL_ADD_ITEM' ).'</a>'
			.' <a href="#'.JText::_( 'CF_SQL_REMOVE' ).'" onclick="remove_row(\''.parent::getInputName().'\')">'.JText::_( 'CF_SQL_REMOVE_ITEM' ).'</a>'
			.'</div>';


			$html	.= '<div id="'.parent::getInputName().'-container">';
			$html .= '<table id="'.parent::getInputName().'_table">'
			. '<tbody id="'.parent::getInputName().'_table_body">';
			if($this->params->get('sql_show_heading',0)){
				$html	.= '<tr>'
				.'<td class="sectiontableheader">'.JText::_($this->params->get('sql_item_label','CF_SQL_ITEM')).'</td>'
				//.'<th></th>'
				. ($this->params->get('sql_show_quantity',1) ? '<td class="sectiontableheader">'.JText::_($this->params->get('sql_quantity_label','CF_SQL_QUANTITY')).'</th>' : '')
				. '</tr>'
				;
			}
			for($i=1;$i<=count($valueArray);$i++){


				if(is_array($valueArray[($i-1)])){
					if(isset($valueArray[($i-1)]['value']) AND is_array($valueArray[($i-1)]['value'])){
						$value	= explode('::', $valueArray[($i-1)]['value'][0]);
					}else{
						$value	= explode('::', $valueArray[($i-1)]['value']);
					}
				}else{
					$value	= explode('::', $valueArray[($i-1)]);
				}


				$option	= JHTML::_('select.genericlist'
										,   $options, $this->getInputName().'[value][]'
										, $javascript.' size="1" '.$this->attributes. ' class="'.$fieldClass.' ce-cf-field-row"'
										, 'value', 'text', trim($value[0]) );
				$html	.= '<tr id="'.parent::getInputName().'_tr['.$i.']" class="sectiontableentry1">'
				.'<td>'.$option.'</td>'
				//.'<td></td>'
				. ($this->params->get('sql_show_quantity',0) ? '<td>'
				.'<input type="text"
						 name="'.$this->getInputName().'[quantity][]"
						 class="'.$fieldClass.' ce-cf-sql-quantity '
								. ($this->params->get('sql_quantity_validation') ? $this->params->get('sql_quantity_validation') : '').'" '
						. ' value="'.(isset($value[1]) ? trim($value[1]) : '').'" />'

				.'</td>'
				: '')
				. '</tr>'
				;
			}
			$html	.= '</tbody>'
			. '</table> ';
			$html	.='</div>';
			if($this->params->get('sql_allow_multiple_lines',0)){
				$html	.= $addButton;
			}
			$html	.= '<input type="hidden" name="'.parent::getInputName().'_row_count" id="'.parent::getInputName().'_row_count" value="'.($i-1).'" />';
			return $html;
		}else if($isChainSelect > 0){ //

		}

	}


	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		$html	= '';
		if($format == 'html'){
			$html	.= '<div class="ce-cf-container"> ';
			$html .= '
			<span  class="ce-cf-html-label" style="'.$style['label'].'"> '.$this->getInputFieldName().'</span> ' ;
			for($i=0; $i < count($this->uservalue['value']); $i++){
				$html	.= '<br />
						<span class="ce-cf-html-field ce-cf-html-field-sql" style="'.$style['value'].'">'
							.$this->uservalue['value'][$i].''
							.( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '')
						.'</span>';
			}
			$html	.= '</div>';

		}else{
			$html .= $this->getInputFieldName().": ";
			//if there is more than one value, add a break line between the label and values
			if(count($this->uservalue['value']) > 1){
				$html .= "\n ";
			}
			for($i=0; $i < count($this->uservalue['value']); $i++){
				$html	.= $this->uservalue['value'][$i].( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '');
				$html	.= "\n";
			}
		}
		return $html;
	}

	function getMySQLOutput(){
		$html	= '';
		for($i=0; $i < count($this->uservalue['value']); $i++){
			$html	.= $this->uservalue['value'][$i].( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '');
		}
		return $html;
	}
}

class ceFieldType_autocomplete extends ceFieldType {
	var $_selectCounter =0;
	function getInputId(){
		return parent::getInputName().'_value_'.$this->_selectCounter;
	}

	function getInputHTML() {
		JHTML::_('behavior.framework');
		$doc	= JFactory::getDocument();
		$doc->addScript( JURI::root(). 'components/com_contactenhanced/assets/js/addtablerow.js' );
		$doc->addScript( JURI::root(). 'components/com_contactenhanced/assets/js/autocomplete/Meio.Autocomplete.js' );
		$doc->addStyleSheet( JURI::root(). 'components/com_contactenhanced/assets/js/autocomplete/meio.autocomplete.css' );
		$db		=JFactory::getDBO();

		$javascript	= "";


		if($this->params->get('hide_field') ){
			$html	= '';
			$i = 0;
			foreach($rows as $row){
				$html	.= '<input	type="hidden"
									value="'.$row->value.'"
									name="'.$this->getInputName().'['.$i++.']" />';
			}
			return $html;

		}else{


			if(isset($this->field_value)){
				$valueArray	= explode("\n", $this->field_value);
			}else{
				$valueArray	= array('');
			}


			$html	= '';
			$addButton	='<div>'
			.' <a href="#'.JText::_( 'CF_AUTOCOMPLETE_ADD' ).'" onclick="inject_row(\''.parent::getInputName().'\',\'autocomplete\',\''.$this->id.'\',\''.JURI::root().'\')">'.JText::_( 'CF_SQL_ADD_ITEM' ).'</a>'
			.' <a href="#'.JText::_( 'CF_AUTOCOMPLETE_REMOVE' ).'" onclick="remove_row(\''.parent::getInputName().'\')">'.JText::_( 'CF_SQL_REMOVE_ITEM' ).'</a>'
			.'</div>';


			$html	.= '<div id="'.parent::getInputName().'-container">';
			$html .= '<table id="'.parent::getInputName().'_table">'
			. '<tbody id="'.parent::getInputName().'_table_body">';
			if($this->params->get('autocomplete_show_heading',0)){
				$html	.= '<tr>'
				.'<td class="sectiontableheader">'.JText::_($this->params->get('autocomplete_item_label','CF_SQL_ITEM')).'</td>'
				//.'<th></th>'
				. ($this->params->get('autocomplete_show_quantity',1) ? '<td class="sectiontableheader">'.JText::_($this->params->get('autocomplete_quantity_label','CF_SQL_QUANTITY')).'</th>' : '')
				. '</tr>'
				;
			}
			$fieldClass	= 'inputbox cf-input-text ce-cf-autocomplete '.($this->getFieldClass());
			for($i=1;$i<=count($valueArray);$i++){


				if(is_array($valueArray[($i-1)])){
					if(isset($valueArray[($i-1)]['value']) AND is_array($valueArray[($i-1)]['value'])){
						$value	= explode('::', $valueArray[($i-1)]['value'][0]);
					}else{
						$value	= explode('::', $valueArray[($i-1)]['value']);
					}
				}else{
					$value	= explode('::', $valueArray[($i-1)]);
				}


				$this->_selectCounter	= $i;
				$option	= '<input type="text"
								name="'.$this->getInputName().'[value][]"
								id="'.$this->getInputId().'" '
								.$this->attributes.'
								class="'.$fieldClass.' ce-cf-field-row"
								value="'.trim($value[0]).'" />';
				$javascript	.= "
	ceAutocomplete('".$this->getInputName().'_value_'.$i."', '{$this->id}', '".JURI::root()."');
";

				$html	.= '<tr id="'.parent::getInputName().'_tr['.$i.']" class="sectiontableentry1">'
				.'<td>'.$option.'</td>'
				. ($this->params->get('autocomplete_show_quantity',0) ? '<td>'
				.'<input type="text"
						 name="'.$this->getInputName().'[quantity][]"
						 class="'.$fieldClass.' ce-cf-autocomplete-quantity '
								. ($this->params->get('autocomplete_quantity_validation') ? $this->params->get('autocomplete_quantity_validation') : '').'" '
				. ' value="'.(isset($value[1]) ? trim($value[1]) : '').'" '
				.' /></td>'
				: '')
				. '</tr>'
				;
			}
			$html	.= '</tbody>'
			. '</table> ';
			$html	.='</div>';
			if($this->params->get('autocomplete_allow_multiple_lines',0)){
				$html	.= $addButton;
			}
			$html	.= '<input type="hidden" name="'.parent::getInputName().'_row_count" id="'.parent::getInputName().'_row_count" value="'.($i-1).'" />';
			$javascript	= "document.addEvent('domready', function() {{$javascript}});";
			$doc->addScriptDeclaration($javascript);
			return $html;
		}
	}

	public function jsonExecute() {
		if($this->value){
			$user		= JFactory::getUser();
			$regex = '/{user_id}/i';
			$this->value  = preg_replace( $regex, $user->id, $this->value );

			$regex = '/{user_email}/i';
			$this->value  = preg_replace( $regex, $user->email, $this->value );

			$regex = '/{username}/i';
			$this->value  = preg_replace( $regex, $user->username, $this->value );

			$regex = '/{selectresult}/i';
			$this->value  = preg_replace( $regex, JRequest::getVar('q'), $this->value );


			$db		= JFactory::getDbo();
			$db->setQuery($this->value );
			return $db->loadObjectList();
		}
		return '';
	}

	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		$html	= '';
		if($format == 'html'){
			$html	.= '<div class="ce-cf-container"> ';
			$html .= '
			<span  class="ce-cf-html-label" style="'.$style['label'].'"> '.$this->getInputFieldName().'</span> ' ;
			for($i=0; $i < count($this->uservalue['value']); $i++){
				$html	.= '<br />
						<span class="ce-cf-html-field ce-cf-html-field-sql" style="'.$style['value'].'">'
							.$this->uservalue['value'][$i].''
							.( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '')
						.'</span>';
			}
			$html	.= '</div>';

		}else{
			$html .= $this->getInputFieldName().": ";
			//if there is more than one value, add a break line between the label and values
			if(count($this->uservalue['value']) > 1){
				$html .= "\n ";
			}
			for($i=0; $i < count($this->uservalue['value']); $i++){
				$html	.= $this->uservalue['value'][$i].( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '');
				$html	.= "\n";
			}
		}
		return $html;
	}

	function getMySQLOutput(){
		$html	= '';
		for($i=0; $i < count($this->uservalue['value']); $i++){
			$html	.= $this->uservalue['value'][$i].( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '');
		}
		return $html;
	}
}

class ceFieldType_sqlmultiple extends ceFieldType {

	function getInputHTML() {
		JHTML::_('behavior.framework');
		$doc	= JFactory::getDocument();
		$doc->addScript( JURI::root(). 'components/com_contactenhanced/assets/js/addtablerow.js' );


		$row	= array();

		if($this->value){
			$fields	= $this->getFields();
		}
		if(count($fields) < 1 AND !$this->params->get('hide_field') AND !$isChainSelect){
			return JText::_($this->params->get('sql_no_result_msg',''));

		}else if($this->params->get('hide_field') ){
			$html	= '';
			$i = 0;
			foreach($fields as $row){
				$html	.= '<input type="hidden" value="'.$row->value.'" name="'.$this->getInputName().'['.$i++.']" />';
			}
			return $html;

		}else if(count($fields) > 0 OR $isChainSelect){
			$html	= '';
			$addButton	='<div>'
			.' <a href="javascript:void(0);" onclick="inject_row(\''.parent::getInputName().'\')">'.JText::_( 'CF_SQL_ADD_ITEM' ).'</a>'
			.' <a href="javascript:void(0);" onclick="remove_row(\''.parent::getInputName().'\')">'.JText::_( 'CF_SQL_REMOVE_ITEM' ).'</a>'
			.'</div>';


			$html	.= '<div id="'.parent::getInputName().'-container">';
			$html .= '<table id="'.parent::getInputName().'_table">'
			. '<tbody id="'.parent::getInputName().'_table_body">';
			if($this->params->get('sql_show_heading',1)){
				$html	.= '<tr>';
				$html	.= $this->getFieldsHeading($fields);
				$html	.= '</tr>';
			}
			$html	.= $this->getFieldsHTML($fields);

			$html	.= '</tbody>'
			. '</table> ';
			$html	.='</div>';
			if($this->params->get('sql_allow_multiple_lines',1)){
				$html	.= $addButton;
			}
			return $html;
		}else if($isChainSelect > 0){ //

		}

	}

	function getFieldsHeading(&$fields){
		$html	= '';
		foreach ($fields as $field) {
			$html	.= '<th class="sectiontableheader">'.$field[0].'</th>';
		}
		$html	.= ($this->params->get('sql_show_quantity',1) ? '<th class="sectiontableheader">'.JText::_($this->params->get('sql_quantity_label','CF_SQL_QUANTITY')).'</th>' : '');
		return $html;
	}

	function getFieldsHTML(&$fields) {

		$html	= '';
		$db		=JFactory::getDBO();

		$javascript	= '';
		if($this->params->get('chain_select') AND $this->params->get('chain_select-enabled-option')){
			JHtml::_('jquery.framework');
			$javascript	= "onchange=\"JsonSelect.updateSelect('".$this->params->get('chain_select-enabled-option')."',this,'".JURI::root()."');\"";
			$doc->addScript(JURI::root().'components/com_contactenhanced/assets/js/chainSelectList.js');
		}
		$value	= $this->getValue('session');

		if(isset($value[0])){
			$rowCount	= count($value[0]);
		}else{
			$rowCount	= 1;
		}
		$k=1;
		$html	.= '<input type="hidden" name="'.parent::getInputName().'_row_count" id="'.parent::getInputName().'_row_count" value="'.($rowCount).'" />';
		for($i=1;$i<=$rowCount;$i++){
			$html	.= '<tr id="'.parent::getInputName().'_tr['.$i.']" class="sectiontableentry'.($k).'">';

			$fieldClass	= ' class="inputbox cf-input-select ce-cf-sqlmultiple'.($this->isRequired() ? ' required' : '').'" ';

			for($j=0; $j < count($fields); $j++){
				$field	= $fields[$j][1];
				$option	= JHTML::_('select.genericlist',   $field, $this->getInputName().'['.$j.'][]', $javascript.' size="1" '.$this->attributes.$fieldClass.' ce-cf-field-row', 'value', 'text', ( isset($value[$j][$i-1]) ? trim($value[$j][$i-1]) : '' ));
				$html	.=	'<td>'.$option.'</td>';
			}

			$fieldClass	= ' class="inputbox cf-input-text ce-cf-sql-quantity'.($this->isRequired() ? ' required' : '').'" ';
			$html	.= ($this->params->get('sql_show_quantity',1) ? '<td>'
			.'<input type="text" name="'.$this->getInputName().'['.($j).'][]" '.$fieldClass
			. ($this->params->get('sql_quantity_validation') ? ' onkeydown="return '.$this->params->get('sql_quantity_validation').'(event);" ' : '' )
			. ' value="'.(isset($value[$j][$i-1]) ? trim($value[$j][$i-1]) : '').'" '
			.' /></td>'
			: '')
			;

			$html	.= '</tr>';
			$k	= ($k == 2 ? $k =1 : ++$k);
		}

		return $html;
	}

	function getFieldOptions($rows) {

		if(count($rows) > 0){
			$options	= array();
			$options[]	= JHTML::_('select.option',  '', JText::_($this->params->get('first_option', 'CE_PLEASE_SELECT_ONE')) );
			$fieldClass	= ' class="inputbox cf-input-text ce-cf-sql-quantity'.($this->isRequired() ? ' required' : '').'" ';
			foreach($rows as $row){
				if( substr($row->text,0,2) == '--'){
					$options[]	=	JHTML::_('select.optgroup',  str_replace('--','',JText::_( $row->text ) ) );
				}else{
					$options[]	=	JHTML::_('select.option',  $row->value, JText::_( $row->text ) );
				}
			}
		}
	}

	function getFields() {
		$db		=JFactory::getDBO();
		$user	= JFactory::getUser();
		$fields	= array();
		//Cast to make sure it is an array
		$this->arrayFieldElements	= (array) $this->arrayFieldElements;

		foreach ($this->arrayFieldElements as $fieldElement) {
			$regex = '/{user_id}/i';
			$value  = preg_replace( $regex, $user->id, $fieldElement );

			$regex = '/{user_email}/i';
			$value  = preg_replace( $regex, $user->email, $fieldElement );

			$regex = '/{username}/i';
			$value  = preg_replace( $regex, $user->username, $fieldElement );

			$regex = '/{selectresult}/i';
			$isChainSelect  = preg_match( $regex, $fieldElement );

			if(strpos($fieldElement, '::') > 0){
				$fieldElement = explode('::', $fieldElement);
			}else{
				$fieldElement = array('CF_SQL_ITEM',$fieldElement);
			}

			$db->setQuery( $fieldElement[1] );
			//echo '<pre>'.$db->getQuery( ).'</pre>'; exit;
			$rows		= $db->loadObjectList();
			$fields[]	= array(JText::_($fieldElement[0]),$rows);
		}
		return $fields;
	}


	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')){
		$html	= '';
		$k		= 1;
		if($format == 'html'){
			$html	.= '<div class="ce-cf-container"> ';
			$html .= '
			<span class="ce-cf-html-label" style="'.$style['label'].'">'.$this->getInputFieldName().'</span> ' ;
			$html	.= '<div id="'.parent::getInputName().'-container"> ';
			$html .= '<table id="'.parent::getInputName().'_table" cellpadding="3" cellspacing="4">'
			. '<tbody id="'.parent::getInputName().'_table_body">';
			if($this->params->get('sql_show_heading',1)){
				if($this->value){
					$fields	= $this->getFields();
					$html	.= '<tr>';
					$html	.= $this->getFieldsHeading($fields);
					$html	.= '</tr>';
				}

			}
			for($i=0; $i < count($this->uservalue[0]); $i++){
				$html	.= '<tr id="'.parent::getInputName().'_tr['.$i.']" class="sectiontableentry'.($k).'">';
				for($j=0; $j < count($this->uservalue); $j++){
					$html	.=	'<td>'.$this->uservalue[$j][$i].' </td>';
				}
				$html	.= '</tr>';
				$k	= ($k == 2 ? $k =1 : ++$k);
			}

			$html	.= '</tbody>'
			. '</table> ';
			$html	.='</div>';

			$html	.= '</div>';
			///echo $html; exit;
		}else{
			$html .= $this->getInputFieldName().": ";
			//if there is more than one value, add a break line between the label and values
			if(count($this->uservalue[0]) > 1){
				$html .= "\n ";
			}
			for($i=0; $i < count($this->uservalue[0]); $i++){
				$fields	= array();
				for($j=0; $j < count($this->uservalue); $j++){
					if(isset($this->uservalue[$j][$i])){
						$fields[]	= $this->uservalue[$j][$i];
					}else
					echo "<br>this->uservalue[$j][$i]<br>";
				}

				$html	.= implode(" ::\t",$fields)."\n";
			}
		}
		return $html;
	}

	function getMySQLOutput(){
		$html	= '';
		for($i=0; $i < count($this->uservalue['value']); $i++){
			$html	.= $this->uservalue['value'][$i].( isset($this->uservalue['quantity'][$i]) ? ":: \t".$this->uservalue['quantity'][$i] : '');
		}
		return $html;
	}

	function getArrayElem(&$array, $key1=null, $key2=null) {
		if (isset($array[$key1][$key2])) {
			;
		}
	}
}
class ceFieldType_wysiwyg extends ceFieldType {
	function getInputHTML() {
		$this->editor = JFactory::getEditor();
		$html = '';
		// parameters : areaname, content, width, height, cols, rows
		/*$html = '<textarea title="'.$this->name.'" name="' . $this->getInputName() . '" id="' . $this->getInputName() . '"
					class="inputbox text_area'.($this->getFieldClass()).'"
					style="display:none" >'
					. $this->getValue() . '</textarea>'; */
		$html .= $this->editor->display( $this->getInputName().'' ,  $this->getValue(), '90%', '200', '75', '20', false ) ;
		return $html;
	}
	function getValidationScript() {
		$script	= "\n var ".$this->getInputName().'_editor_text = '.$this->editor->getContent( $this->getInputName().'_editor' );
		//$script	.= "\n alert(".$this->getInputName()."_editor_text);";
		$script	.= "\n".'$("'.$this->getInputName().'").setProperty("value",'.$this->getInputName().'_editor_text);';
		return $script;
	}
}

class ceFieldType_mailchimp extends ceFieldType{
	var $_selectCounter = 0;
	var $mcapi 			= null;
	var $apiKey			= null;
	var $clientID		= null;
	var $list			= null;

	function ceFieldType_mailchimp($data,&$params ) {
		parent::ceFieldType( $data,$params );

		require_once JPATH_ROOT.'/components/com_contactenhanced/helpers/MCAPI.class.php';

		if (is_string($this->params)) {
			$registry	= new JRegistry();
			$registry->loadString($this->params);
			$this->params = $registry;
		}
		$this->apiKey	= $this->params->get('mailchimp_api_key');
		$this->mclist	= explode(',',$this->params->get('mailchimplist'));

		for ($i = 0; $i < count($this->mclist); $i++) {
			$this->mclist[$i]	= trim($this->mclist[$i]);
			if(strlen($this->mclist[$i]) < 2 ){
				unset($this->mclist[$i]);
			}
		}

		$this->mcapi = new MCAPI( $this->apiKey);
	}

	function getInputHTML() {
		$this->_selectCounter = 0;

		$html		= '<div class="cf-mailchimp">';

		if(!$this->apiKey){
			$html	.= '<h1>'.JText::_('CE_CF_MAILCHIMP_ERROR_EMPTY_API_KEY').'</h1>';
		}

		$lists = $this->mcapi->lists();


		if ($this->mcapi->errorCode){
			JError::raiseWarning( 0, "Unable to load lists. \nError: ".$this->mcapi->errorCode."\t -".$this->mcapi->errorMessage."\n" );
		} elseif(is_array($lists['data'])) {
			foreach ($lists['data'] as $list){
				$this->getOption($html,$list);
			}
		}
		$html		.= '</div>';
		return $html;
	}

	/**
	 * Subscribes the user to the selected lists. Does not show an email output as the name sugests
	 */
	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')) {
		$errors = array();
		$this->uservalue	= (array) $this->uservalue;
		if($this->apiKey AND count($this->uservalue) > 0){
			$email	= JRequest::getString( 'email', null,'post');
			$fname	= JRequest::getString( 'name', null,'post');
			$lname	= JRequest::getString( 'surname', null,'post');

			/**
			 * @var array MailChimp Custom Fields
			 */
			$cmcf	= array('fname'=>$fname, 'lname'=>$lname);
			foreach(ceHelper::$submittedfields as $field){
				if(count($field->arrayFieldElements) > 1 AND is_string($field->uservalue) ){
					$cmcf[$field->name]	= explode(', ',$field->uservalue);
				}else{
					$cmcf[$field->name]	= $field->uservalue;
				}

			}
			foreach ($this->uservalue as $listID => $list) {
				$cmcf['GROUPINGS']	= array();
				if(is_array($list)){
					foreach ($list as $groupingId => $group) {
						$cmcf['GROUPINGS'][] =	array('id'=>$groupingId, 'groups'=>implode(',',$group));
					}
					$list = $listID;
				}
				$retval = $this->mcapi->listSubscribe(
				$list,
				$email,
				$cmcf,
				$this->params->get('mc-emailType','html'),
				$this->params->get('mc-doubleOptIn',true),
				$this->params->get('mc-update_existing',true),
				true,
				$this->params->get('mc-send_welcome',false)
				);
				if ($this->mcapi->errorCode){
					JError::raiseWarning( 0,
								"Unable to subscribe user. \nError: "
								.$this->mcapi->errorCode."\t -"
								.$this->mcapi->errorMessage."\n" );
				}
				/*else{
					echo "Returned: ".$retval."\n";
					}*/
			}
		}

		return '';
	}

	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}elseif($type=='submission'){
			return parent::getInputName();
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}
	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}
	function getOption(&$html,$list) {
		//testArray($list);
		$inputType	= $this->params->get('input_type','checkbox');
		if(count($this->mclist) < 1 AND !isset($list['id'])){
			foreach ($list as $value)
			$this->getOption($html,$value);
			//return	$this->getOption($html,$value,$value_name,$text_name);
		}elseif (is_array($list)
		AND (count($this->mclist) < 1 OR ( isset($list['id']) AND in_array($list['id'],$this->mclist)) )
		){

			$cols	= $this->params->get('number_of_columns',1);
			$width	= number_format( (100/$cols), 1);
			$html .= '<div style="width:'.$width.'%;float:left">';
			//testArray($list);
			$groupings	= $this->mcapi->listInterestGroupings($list['id']);
			if(!is_array($groupings) OR $this->params->get('display_groupings',1) == 0){
				$html .= '<input type="'.$inputType.'" class="cf-input-'.$inputType.$this->getFieldClass().($this->isRequired() ? ' validate-boxes':'').'" '
				.' name="' . parent::getInputName().'['.$list['id'].']'. '" '
				.' value="'.$list['id'].'" '
				.' id="' . $this->getInputId() . '_' . $this->_selectCounter . '" ';
				if( $list['id'] == $this->getValue() OR $this->params->get('input_type-checkbox-allchecked',0)){
					$html .= '  checked="checked"  ';
				}
				$html .= ' '.$this->attributes.' ';
				$html .= '/>';
				$html .= ' <label for="' . $this->getInputId() . '_' . $this->_selectCounter . '">'.JText::_($list['name']).'</label>';

			}else{
				$html .= '<label class="ce-level-1">'.JText::_($list['name']).'</label><br />';
				foreach ($groupings as $grouping) {
					$html .= '<label class="ce-level-2">'.JText::_($grouping['name']).'</label><br />';
					foreach ($grouping as $groups) {
						if(is_array($groups)){
							foreach ($groups as $group) {
								//testArray($group);
								$fieldType	= ($grouping['form_field'] == 'checkboxes' ? 'checkbox' : 'radio');
								$html .= '<input type="'.$fieldType.'" class="ce-level-3 cf-input-checkbox'.$this->getFieldClass().($this->isRequired() ? ' validate-checkbox':'').'" '
								.' name="' . parent::getInputName().'['.$list['id'].']['.$grouping['id'].']['.$this->_selectCounter.']" '
								.' value="'.$group['name'].'" '
								.' id="' . $this->getInputId() . '_' . $this->_selectCounter . '" ';
								if( $group['name'] == $this->getValue() OR ($this->params->get('input_type-checkbox-allchecked',0) AND $fieldType == 'checkbox' )){
									$html .= '  checked="checked"  ';
								}
								$html .= ' '.$this->attributes.' ';
								$html .= '/>';
								$html .= ' <label for="' . $this->getInputId() . '_' . $this->_selectCounter . '">'.JText::_($group['name']).'</label><br />';
								$this->_selectCounter++;
							}
						}

					}
				}

				//testArray($groupings);
			}
			$html .= '</div>';
			$this->_selectCounter++;
		}

		//return $html;
	}
	function getInputId() {
		return parent::getInputName();
	}

	function getLabel($output='site'){

		$html	= '';
		if($this->published
			AND $this->params->get('hide_field_label',0) == 0
			AND $this->params->get('hide_field_label',0) != 'overtext'){
			$label= '<label class="cf-label'.($this->isRequired() ? ' requiredField':'').'" >'
			.JText::_( $this->getInputFieldName() )
			.($this->isRequired() ? ' '.JText::_('CE_FORM_REQUIRED_SIGN') : '')
			.'</label>';
			if($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'mouseover'){
				$html .= '<span class="editlinktip hasTip" title="'. JText::_( $this->tooltip ). '">'
				. $label
				. '</span>';
			}elseif($this->tooltip AND $this->params->get('tooltip_behavior','mouseover') == 'inline'){
				$html .= $label;
				$html .= '<div class="ce-tooltip" >'. JText::_( $this->tooltip ). '</div>';
			}else{
				$html .= $label;
			}
		}
		return $html;
	}

}

class ceFieldType_acymailing extends ceFieldType{
	var $_selectCounter = 0;
	var $cm 			= null;
	var $apiKey			= null;
	var $clientID		= null;
	var $list			= null;

	function ceFieldType_acymailing($data,&$params ) {
		parent::ceFieldType( $data,$params );

		if(!include_once(rtrim(JPATH_ADMINISTRATOR,DIRECTORY_SEPARATOR).'/components/com_acymailing/helpers/helper.php')){
			JError::raiseWarning( 0, 'The Newsletter AcyMailing <i>Custom Field</i> requires AcyMailing Component in order to work');
			return false;
		};

		$listClass = acymailing_get('class.list');

		$this->lists = $listClass->getLists();

		if (is_string($this->params)) {
			$this->params = new JParameter($this->params);
		}
		$this->acylist		= (array)$this->params->get('acylist',array());

	}

	function getInputHTML() {
		$this->_selectCounter = 0;

		$html		= '<div class="cf-newsletter">';

		//testArray($this->lists);
		if (count($this->lists)){
			foreach ($this->lists as $list){
				$this->getOption($html,$list);
			}
		}else{
			return '';
		}
		$html		.= '</div>';
		return $html;
	}

	/**
	 * Subscribes the user to the selected lists. Does not show an email output as the name suggests
	 */
	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')) {
		$errors = array();
		// Array with selected list IDs
		$this->uservalue	= (array) $this->uservalue;
		if(count($this->uservalue) > 0){
			$subscriberClass = acymailing_get('class.subscriber');

			$member = new JObject();
			$member->email = JRequest::getString('email',	null,'post');
			$member->name = JRequest::getString( 'name',	null,'post');
			/**
			 * Save AcyMailing Custom Fields
			 */
			$db =JFactory::getDBO();
			$acyFields	= $db->getTableFields('#__acymailing_subscriber');
			$acyFields = reset($acyFields);

			foreach(ceHelper::$submittedfields as $field){
				if(is_object($field) AND isset($field->alias)){
					$acy_cf_name	= str_replace('-', '', $field->alias);
					if(array_key_exists($acy_cf_name, $acyFields)){
						if(count($field->arrayFieldElements) > 1 AND is_string($field->uservalue) ){
							$member->$acy_cf_name	= $field->uservalue;
						}elseif(count($field->arrayFieldElements) > 1 AND is_array($field->uservalue) ){
							$member->$acy_cf_name	= implode(', ',$field->uservalue);
						}else{
							$member->$acy_cf_name	= $field->uservalue;
						}
					}
				}
			}

			/**/
			$subid = $subscriberClass->save($member);

			//the user could not be saved for whatever reason
			if(empty($subid)) return '';

			$newSubscription = array();
			if(!empty($this->uservalue)){
				foreach($this->uservalue as $listId){
					$newList = null;
					$newList['status'] = 1;
					$newSubscription[$listId] = $newList;
				}
			}
			//there is nothing to do...
			if(empty($newSubscription)) return '';

			$subscriberClass->saveSubscription($subid,$newSubscription);

			/**
			 * Get List names
			 */
			$db->setQuery('SELECT name FROM #__acymailing_list WHERE listid IN ('.implode(',', $this->uservalue).')');
			$lists	= $db->loadColumn();
			$this->uservalue	= implode(', ', $lists);
			$html	=	parent::getEmailOutput($delimiter, $format ,$style);
			$this->uservalue		= $tempUserValue;
			return $html;
		}

		return '';
	}

	function getInputName($type=''){
		if($type=='cookie'){
			//echo parent::getInputName().'_'.$this->_selectCounter;
			return parent::getInputName().'_'.$this->_selectCounter;
		}elseif($type=='submission'){
			return parent::getInputName();
		}else{
			return parent::getInputName()."[".($this->_selectCounter)."]";
		}
	}
	function getMySQLOutput(){
		return implode(', ',$this->uservalue);
	}
	function getOption(&$html,$list) {
		//testArray($list);
		if((!$this->acylist OR (is_array($this->acylist) AND count($this->acylist)<1) ) AND !isset($list->listid)){
			foreach ($list as $value)
			$this->getOption($html,$value);
			//return	$this->getOption($html,$value,$value_name,$text_name);
		}elseif (is_object($list)
			AND $list->published
			AND (
					!$this->acylist
					OR (isset($list->listid) AND is_array($this->acylist) AND in_array($list->listid, $this->acylist))
				)
		){
			$cols	= $this->params->get('number_of_columns',1);
			$width	= number_format( (100/$cols), 1);
			$html .= '<div style="width:'.$width.'%;float:left">';
			$html .= '<input type="checkbox" class="cf-input-checkbox'.$this->getFieldClass().($this->isRequired() ? ' validate-checkbox':'').'" '
			.' name="' . $this->getInputName(). '" '
			.' value="'.$list->listid.'" '
			.' id="' . $this->getInputName() . '_' . $this->_selectCounter . '" ';
			if( $list->listid == $this->getValue() OR $this->params->get('acy-all-checked',0)){
				$html .= '  checked="checked"  ';
			}
			$html .= ' '.$this->attributes.' ';
			$html .= '/> <label for="' . $this->getInputName() . '_' . $this->_selectCounter . '" title="'.$list->description.'">'
			.JText::_($list->name).'</label>';
			$html .= '</div>';
			$this->_selectCounter++;
		}

		//return $html;
	}

}

class ceFieldType_css extends ceFieldType {
	function getInputHTML() {
		return '';
	}
	function getValue($arg=null) {
		return $this->value;
	}
	function getFieldHTML() {
		$doc = JFactory::getDocument();
		if(trim($this->getValue())){
			$doc->addStyleDeclaration($this->getValue());
		}
	}
	function getLabel($output='site') {
		return '';
	}
	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')) {
		return '';
	}
}


class ceFieldType_js extends ceFieldType {
	function getInputHTML() {
		return '';
	}
	function getValue($arg=null) {
		return $this->value;
	}
	function getFieldHTML() {
		if(trim($this->getValue())){
			$doc = JFactory::getDocument();
			if($this->params->get('position',1)){
				$doc->addScriptDeclaration($this->getValue());
			}else{
				return '<script type="text/javascript">/* <![CDATA[ */
				'.$this->getValue().'
/* ]]> */</script>';
			}

		}
	}
	function getLabel($output='site') {
		return '';
	}
	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')) {
		return '';
	}
}


class ceFieldType_php extends ceFieldType {
	function getInputHTML() {
		$html		= '';

		require_once (JPATH_ROOT.'/components/com_contactenhanced/helpers/safereval.class.php');

		$sEval = new SaferEval();
		$sEval->set('customfield',$this);
		if(($sEval->checkScript($this->getValue(), false) !== false)){
			return $sEval->checkScript($this->getValue(), true);
		}else{
			echo $sEval->htmlErrors(); exit;
		}
		//return self::safe_eval($this->getValue());
	}
	function getValue($arg=null) {
		return $this->value;
	}

}


class ceFieldType_pagination extends ceFieldType {
	function ceFieldType_pagination($data, &$params) {
		// Call parent constructor
		parent::ceFieldType($data, $params);

		require_once (JPATH_ROOT.'/components/com_contactenhanced/helpers/steps.php');

	}

	function getInputHTML() {
		return CEHtmlSteps::step($this->name, 'ceStep'.$this->id, 'ceStepGroup_'.$this->params->get('contactId'));
	}
	function getFieldHTML() {
		if(!class_exists('iBrowser')){
			require_once(JPATH_ROOT.'/components/com_contactenhanced/helpers/browser.php');
		}
		$browser = new iBrowser();
		if($browser->getBrowser() == 'Android' AND version_compare($browser->getVersion(), '2.3.3') <= 0){
			return '';
		}else{
			return $this->getInputHTML();
		}
	}

	function getEmailOutput($delimiter= ', ',$format='text',$style=array('label'=>'','value'=>'')) {
		return '';
	}

	function getLabel($output='site') {
		return '';
	}

	public function start($group = 'steps')
	{
		return CEHtmlSteps::start($group, $this->params);
	}
	public function end()
	{
		return CEHtmlSteps::end();
	}

	public function step($text, $id, $group = 'steps')
	{
		return CEHtmlSteps::step($text, $id, $group);
	}
	public function buttons($group = 'steps')
	{
		return CEHtmlSteps::buttons($group,  $this->params);
	}
	public function status($group = 'steps',$numberSteps)
	{
		return CEHtmlSteps::status($group, $numberSteps, $this->params);
	}
}


class ceFieldType_button extends ceFieldType {
	function ceFieldType_button($data,&$params ) {
		parent::ceFieldType( $data,$params );
		// If button is loaded
		if(is_null($data) ){
			$this->name		= JText::_('CE_FORM_SEND');
			$this->id		= 'ce-submit-button';
			$this->type		= 'button';
			$this->required	= false;
			$this->published= true;

		}
	}

	function getLabel($output='site') {
		return '';
	}

	function getInputHTML() {
		$html	= '';
		//Add class="readon" in order to add compatibility with RocketTheme templates
		//$html	.= '<div class="readon">';
		//$html	.='<span class="readon">';
		$html	.='<span>';
		$html	.='
					<button type="'.$this->params->get('buttonType','submit').'"
							class="button ce-button-'.$this->params->get('buttonType','submit').'"
							id="'.$this->getInputId().'"
					'.$this->attributes.' >'
				//	.'<span class="buttonspan" id="'.$this->getInputId().'-span">'
						.$this->getName()
					//.'</span>'
					.'</button> '
					.'</span>'
					;
		if($this->params->get('buttonType-submit-reset',true) AND $this->params->get('buttonType','submit') == 'submit'){
			//$html	.='<span class="readon">';
			$html	.='<span >';
			$html	.=' <button type="reset" class="button ce-button-reset "  id="'.$this->getInputId().'_reset" >'
					//.'<span class="buttonspan" id="'.$this->getInputId().'_reset-span">'
						.JText::_('CE_FORM_RESET')
					//.'</span>'
					.'</button>'
					.'</span>'
					;
		}
		//$html	.= '</div>';
		return $html;
	}


}