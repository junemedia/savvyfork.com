<?php
/**
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * @package		com_contactenhanced
* @since	1.6
 */
class ContactenhancedControllerTemplates extends JControllerAdmin
{
	/**
	 * Exports the custom fields.
	 *
	 * @return	none
	 * @since	1.5
	 */
	public function export(){
		require_once(JPATH_COMPONENT.'/helpers/export.php');
		$cid	= (array)JRequest::getVar('cid');
		ceExport::customFields($cid);
	}
	
	/**
	 * Imports custom fields.
	 *
	 * @return	none
	 * @since	1.5
	 */
	public function import(){
		$app		= JFactory::getApplication();
		require_once(JPATH_COMPONENT.'/helpers/import.php');
		//echo '<pre>'; print_r($_POST); exit;
		$sql_file	= JRequest::getVar( 'sql_file', false, 'FILES' );
		$link = 'index.php?option=com_contactenhanced&view=templates';
		$content	= ceImport::getFileContent('',$sql_file['tmp_name']);
		
		if( $content ){
			$db		=JFactory::getDBO();
			$table	= $db->replacePrefix('#__ce_template');
			$result	= ceImport::executeQuery($content, '','', 'INSERT INTO '.$table);
			if($result){
				$app->redirect($link,JText::_( 'CE_CF_IMPORT_SUCCESS' ));
			}
		}else{
			jimport('joomla.application.helper');
			JError::raiseWarning(100, JText::_('CE_CF_IMPORT_NO_VALID_FILE' ));
			$app->redirect($link);
		}
	}

	/**
	 * Proxy for getModel
	 * @since	1.6
	 */
	public function getModel($name = 'Templates', $prefix = 'ContactenhancedModel', $config = array())
	{
		$tasks = array('saveorder','publish','unpublish','archive', 'trash','report', 'orderup', 'orderdown', 'delete');
		if( in_array($this->getTask(), $tasks) ){
			$model = parent::getModel('Template', $prefix, array('ignore_request' => true));
		}else{
			$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		}
		
		return $model;
	}
	
	
}