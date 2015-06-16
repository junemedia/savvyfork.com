<?php
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


//echo $_GET['item'];

define( '_JEXEC', 1 );
define( 'DS', DIRECTORY_SEPARATOR );
define('JPATH_BASE', dirname(__FILE__) . '/../../');

require_once ( JPATH_BASE .DS.'includes'.DS.'defines.php' );

require_once ( JPATH_BASE .DS.'includes'.DS.'framework.php' );

$mainframe = JFactory::getApplication($_GET['client']);
$mainframe->initialise();

//$session =& JFactory::getSession();
//$data = $session->get("catz");

$data = $mainframe->getUserState( "com_content.mcats", '' );

$data = json_decode($data);
if(!is_array($data)) { $data = array(); }
/* FUNKCE NA OBSLUHU $data: object <-> array */
// Function Object a Array
function object_to_array($object)
{
  if(is_array($object) || is_object($object))
  {
    $array = array();
    foreach($object as $key => $value)
    {
      $array[$key] = object_to_array($value);
    }
    return $array;
  }
  return $object;
}
 
// Function Array a Object
function array_to_object($array = array())
{
	return (object) $array;
}
// Funkce na projití objectu - obdoba in_array pro pole
function property_value_in_array($array, $property, $value) {
    $flag = false;

    foreach($array as $object) {
        if(!is_object($object) || !property_exists($object, $property)) {
            return false;        
        }

        if($object->$property == $value) {
            $flag = true;
        }
    }
    
    return $flag;
}
/* END */


// control  
/*$catarray = explode(',',$_GET['catz']);

if(isset($_GET['catz']) AND $_GET['catz'] != ''){
  $titlesarray = explode(',',$_GET['catztitles']);
  foreach($catarray AS $key => $cat){
    $title = $titlesarray[$key];
    $data[] = array("id" => $cat, "title" => $title);
  }
}
*/
// zaručení typu $data jako object
//$data = array_to_object($data);

// uncheck
if($_GET['chck'] == 'false'){
  //$data = object_to_array($data); 
  foreach($data as $key => $cat){
    if($cat->id == $_GET['item']){
    //if($cat['id'] == $_GET['item']){
      unset($data[$key]);
    }
  }
  $data = object_to_array($data);
  $data = array_values($data);
  foreach($data as $key => $cat){
    $data[$key] = (object) $data[$key];
  }
}
// check
if(!property_value_in_array($data, 'id', $_GET['item'], $data) AND $_GET['chck'] == 'true'){
  $data = object_to_array($data);
  $data[] = array("id" => $_GET['item'], "title" => $_GET['title']);
  //echo $_GET['item']."+";
}

// uncheck all
if($_GET['item'] == 0){
  $data = object_to_array($data);
  foreach($data as $key => $cat){
    unset($data[$key]);
  }
  
}

$catz = json_encode($data);
//$session->set("catz",$catz);
$mainframe->setUserState( "com_content.mcats", $catz );

// Send the result
echo $catz; 
?>