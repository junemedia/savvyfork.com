<?php
/**
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Supports a modal article picker.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @since		1.6
 */
class JFormFieldModal_Cats extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'Modal_Cats';

	/**
	 * Method to get the field input markup.
	 *
	 * @return	string	The field input markup.
	 * @since	1.6
	 */
	protected function getInput()
	{
    $document = JFactory::getDocument();
    //$document->addScript('http://code.jquery.com/jquery-latest.js');
    
    $script = "
    if (typeof jQuery == 'undefined') {
       var script = document.createElement('script');
       script.type = 'text/javascript';
       script.src = '".JUri::base()."components/com_cwtags/helpers/jquery1.7.2.js';
       document.getElementsByTagName('head')[0].appendChild(script);
    }";
    $document->addScriptDeclaration($script);    
    
    //$document->addScript(JUri::base().'components/com_content/helpers/jquery1.7.2.js');
    
    $document->addStyleDeclaration('
    .catz { display: inline-block; padding: 5px; margin: 3px; -webkit-border-radius: 5px; border-radius: 5px; color: #555; border: 2px solid #AADE66; }
    .catz.red { border: 2px solid #FF8080;}
    
    .catz img { padding: 0px; margin: 0px; width: 16px; height: 16px; margin-right: 5px; cursor: pointer;}
    #catmask {width: 100%; height: 100%; position: absolute; display: none; background: rgba(255,255,255,0.7) url('.JURI::root().'administrator/components/com_multicats/assets/images/loading.gif) center no-repeat; }   
    
    ');
 
    $language = JFactory::getLanguage();
    $extension = 'com_content';
    $language_tag = $language->getTag(); // loads the current language-tag
    $language->load('com_multicats', JPATH_SITE, $language_tag, true);
       
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal');

		// Build the script.
		$script = array();
	  $script[] = '    function jSelectCategory_'.$this->id.'(id, title, object) {';
	  $script[] = '        var txt=title.replace(/,/g,"</span><span style=\"display: inline-block; padding: 5px; margin: 3px; -webkit-border-radius: 5px; border-radius: 5px; color: #555; border: 2px solid #adadad\">");';
    $script[] = '        document.id("'.$this->id.'_title").innerText = "";
                          if(document.all){
                               document.id("'.$this->id.'_title").innerText = "";
                          } else{
                              document.id("'.$this->id.'_title").textContent = "";

                          }
                ';
    $script[] = '
    var $cjq = jQuery.noConflict();
    $cjq(document).ready(function($) {
      $cjq().ready(function() {            
          ids = id.split(",");
          arr = title.split(";");
          if(arr[0] != ""){
            $cjq.each(arr, function(key, value) { 
              var spanTag = document.createElement("span"); 
              spanTag.className = "catz"; 
              icon = "<img src=\"'.JURI::root().'administrator/components/com_multicats/assets/images/del.png\" onclick=\"uncheckCat(id, "+ids[key]+", &quot;"+value+"&quot;, false );\" />";
              spanTag.innerHTML = icon+value;  
              document.id("'.$this->id.'_title").appendChild(spanTag);
            });
          } else {
              var spanTag = document.createElement("span"); 
              spanTag.className = "catz red"; 
              spanTag.innerHTML = "'.JText::_('COM_CONTENT_NO_CATEGORY').'"; 
              document.id("'.$this->id.'_title").appendChild(spanTag);            
          }
          link	= "index.php?option=com_categories&view=categories&extension=com_content&layout=modal&tmpl=component&catz="+id+"&function=jSelectCategory_'.$this->id.'&'.JSession::getFormToken().'=1";
          $cjq(".catmodal a.modal").attr({"href": link });          
          
          $cjq(\'<div>\', {id:"catmask" } ).appendTo("#'.$this->id.'_title");  //recreate catmask
      });
    });
    ';
        
    $script[] = '        document.id("'.$this->id.'_id").value = id;';
	  //$script[] = '        document.id("'.$this->id.'_title").innerText = txt;';
	  $script[] = '        SqueezeBox.close();';
	  $script[] = '    }';

		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


    $delscript[] = '
   
function uncheckCat(id, item, title, chck) {
    var $cjq = jQuery.noConflict();
    $cjq(document).ready(function($) {
      $cjq().ready(function() {  

        var rand = \'rand=\'+Math.random();
        $cjq("#catmask").css("display" , "block");
  
        $cjq.ajax({
          type: "GET",                                                                             
          url:"'.JUri::root().'components/com_multicats/mcats.php",
          data: rand+"&item="+item+"&title="+title+"&chck="+chck+"&client=administrator&rand2="+Math.random(),
          success:function(results){
              //alert(results);
              var obj=$cjq.parseJSON(results); // now obj is a json object
              var cattitles = \'\';
              var catids = \'\';
              var i = 1;
              var j = 1;
              $cjq.each( obj, function(key){  
                $cjq.each( obj[key], function(k,v){
                 if(k == \'id\'){
                  if(i == 1) { catids = v; }
                  else { catids = catids + \',\' + v; }
                  i = i + 1;
                 }
                 if(k == \'title\'){
                  if(j == 1) { cattitles = v; }
                  else { cattitles = cattitles + \';\' + v; }
                  j = j + 1;
                 }
                });
              });
    
              $cjq("'.$this->id.'_title").remove(\'.catz\');
              document.getElementById("'.$this->id.'_id").value=catids;
              $cjq("#'.$this->id.'_title span.catz").remove();
              
              //create new span structure
              arr = cattitles.split(";"); //titles
              ids = catids.split(","); //ids
              
              if(arr[0] != ""){
                $cjq.each(arr, function(key, value) { 
                  var spanTag = document.createElement("span"); 
                  spanTag.className = "catz"; 
                  icon = "<img src=\"'.JURI::root().'administrator/components/com_multicats/assets/images/del.png\" onclick=\"uncheckCat(id, "+ids[key]+", &quot;"+value+"&quot;, false );\" />";
                  spanTag.innerHTML = icon+value; 
                  document.id("'.$this->id.'_title").appendChild(spanTag);
                  
                });
              } else { /*
                  var spanTag = document.createElement("span"); 
                  spanTag.className = "catz red"; 
                  spanTag.innerHTML = "'.JText::_('COM_CONTENT_NO_TAG').'"; 
                  document.id("'.$this->id.'_title").appendChild(spanTag);*/            
              }
              $cjq("#catmask").css("display" , "none");
                       
          } // end success
        });
    });
  });
}
';
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $delscript));

		// Setup variables for display.
		$html	= array();
		//$link	= 'index.php?option=com_categories&amp;view=categories&amp;extension=com_content&amp;layout=modal&amp;tmpl=component&amp;function=jSelectCategory_'.$this->id;
   	$clink	= 'index.php?option=com_categories&amp;view=categories&amp;extension=com_content&amp;layout=modal&amp;tmpl=component&amp;catz='.$this->value.'&amp;function=jSelectCategory_'.$this->id;

    //tvorba řetězce názvů kategorií
		$titles = '';
    $arr = explode(',',$this->value);
    $i = 1;
    $i = 1;
    foreach($arr as $key => $value){
      $db	= JFactory::getDBO();
  		$db->setQuery(
  			'SELECT title' .
  			' FROM #__categories' .
  			' WHERE id = '.(int) $value.' AND id > 1'
  		);
      if($name = $db->loadResult()){        
        $titles .= "<span class='catz'><img src='".JURI::root()."administrator/components/com_multicats/assets/images/del.png' onclick=\"uncheckCat(id, ".$value.", &quot;".$name."&quot;, false);\"/>".$db->loadResult()."</span>";
        $data[] =  array("id" => $value, "title" =>  $db->loadResult());
      } else {
        $titles .= "<span class='catz red'>".JText::_('COM_CONTENT_NO_CATEGORY')."</span>";
      }
      //if($i < count($arr)){ $titles .= '<br /> ';}
      $i++;
    }

		if ($error = $db->getErrorMsg()) {
			JError::raiseWarning(500, $error);
		}

		if (empty($title)) {
			$title = JText::_('COM_CONTENT_SELECT_AN_ARTICLE');
		}
		$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

    //set session
    $session = JFactory::getSession();
    
    //set session
    //$session =& JFactory::getSession();
    
    /* SESSION */
    $catz = '';
    if(isset($data)) {$catz = json_encode($data);}
    //print_r($catz);
    //$_SESSION['mcatz'] = $catz;
    //$session->set("catz", $catz);
    
    $mainframe = JFactory::getApplication();
    $data = $mainframe->setUserState( "com_content.mcats", $catz );

		// The user select button.
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank catmodal">';
		$html[] = '	<a class="modal" title="'.JText::_('COM_CONTENT_CHANGE_CATEGORY').'"  href="'.$clink.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'.JText::_('COM_CONTENT_CHANGE_CATEGORY_BUTTON').'</a>';
		$html[] = '  </div>';
		$html[] = '</div>';
		// The current user display field.
		$html[] = '<div class="fltlft">';
		//$html[] = '  <input type="text" id="'.$this->id.'_name" value="'.$titles.'" disabled="disabled" size="100" />';
    //if($titles != '') {} 
    $html[] = '  <div style="position: relative; margin-top: 5px; -webkit-border-radius: 5px; border-radius: 5px; display: block; border: 1px solid #d5d5d5; padding: 5px 10px 5px 10px; margin-bottom: 10px; " id="'.$this->id.'_title"><div id="catmask"></div>'.$titles.'</div>';
		$html[] = '</div>';


		// The active article id field.
		if (0 == (int)$this->value) {
			$value = '';
		} else {
			$value = (int)$this->value;
		}

		// class='required' for client side validation
		$class = '';
		if ($this->required) {
			$class = ' class="required modal-value"';
		}

		$html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$this->value.'" />';

		return implode("\n", $html);
	}
}
