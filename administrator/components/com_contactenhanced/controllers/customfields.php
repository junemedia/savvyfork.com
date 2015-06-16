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
class ContactenhancedControllerCustomfields extends JControllerAdmin
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
		$link = 'index.php?option=com_contactenhanced&view=customfields';
		$content	= ceImport::getFileContent('',$sql_file['tmp_name']);
		
		if( $content ){
			$db		=JFactory::getDBO();
			$table	= $db->replacePrefix('#__ce_cf');
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
	public function getModel($name = 'Customfields', $prefix = 'ContactenhancedModel', $config = array())
	{
		$tasks = array('saveOrderAjax','saveorder','publish','unpublish','archive', 'trash','report', 'orderup', 'orderdown', 'delete');
		if( in_array($this->getTask(), $tasks) ){
			$model = parent::getModel('Customfield', $prefix, array('ignore_request' => true));
		}else{
			$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		}
		
		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return	void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$pks   = $this->input->post->get('cid', array(), 'array');
		$order = $this->input->post->get('order', array(), 'array');

		// Sanitize the input
		JArrayHelper::toInteger($pks);
		JArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		JFactory::getApplication()->close();
	}
	
}