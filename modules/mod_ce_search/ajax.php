<?php
/**
 * @package		mod_ce_search
 * @author		Douglas Machado {@link http://idealextensions.com}
 * @license		GNU/GPL, see license.txt in Joomla root directory
 */

error_reporting(1);


// Set flag that this is a parent file
define( '_JEXEC', 1 );

define('JPATH_BASE', '../../' );
define( 'DS', DIRECTORY_SEPARATOR );

require_once ( JPATH_BASE .'/includes/defines.php' );
require_once ( JPATH_BASE .'/includes/framework.php' );

// Instantiate the application.
$app	= JFactory::getApplication('site');
$input 	= $app->input;

//set tmpl = component in order to avoid some problems
$input->set("tmpl", 'component');

error_reporting(E_ALL);


// Load language files
$lang 	= JFactory::getLanguage();
$lang->load('mod_ce_search', dirname(__FILE__), $input->get('lang'), true);
$lang->load('mod_ce_search', JPATH_BASE, 		$input->get('lang'), true);



class idealExtensions extends JObject{

	static function getSelect(){
		//require_once(JPATH_BASE.'/modules/mod_ce_search/helper.php');
		$app	= JFactory::getApplication('site');
		$input 	= $app->input;
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);

		$field	= $input->get('update');
		$catids	= $input->getString('catids');

		$firstOption	= new stdClass();
		$firstOption->value = '';
		$firstOption->text = JText::_('MOD_CE_SEARCH_SELECT_'.$field);

		$result	= array();
		if ($field == 'state' AND $input->getString('country') == '') {

		}else{
			$query->select('DISTINCT '.$db->quoteName($field, 'value'). ', '.$db->quoteName($field, 'text') );
			$query->from('#__ce_details');
			$query->where('published = 1');
			$query->where($field.' <> '.$db->quote(''));
			if(($catids)){
				if (is_numeric($catids)){
					$query->where('catid = ' . (int) $catids);
				}else{
					$query->where('catid  IN ('.$catids.')');
				}
			}
			if ($input->getString('country')) {
				$query->where('country ='.$db->quote($input->getString('country')));
			}
			if ($field == 'suburb' AND $input->getString('state')) {
				$query->where('state ='.$db->quote($input->getString('state')));
			}
			$query->where('language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');

			$query->order($field.' ASC');

			//echo $query; exit;

			$db->setQuery($query);
			$result		= $db->loadObjectList();
		}

		array_unshift($result,$firstOption);
		$data	= JHtml::_('select.options',$result);
		echo $data; exit;
	}
}

switch($input->getCmd('task'))
{
	case 'setSession':
		idealExtensions::setSession();
		break;
	case 'getSelect':
		idealExtensions::getSelect();
		break;
	case 'autocomplete':
		idealExtensions::autocomplete();
		break;

}