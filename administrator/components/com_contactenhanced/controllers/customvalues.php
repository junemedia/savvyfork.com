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
class ContactenhancedControllerCustomvalues extends JControllerAdmin
{
	
	/**
	 * Proxy for getModel
	 * @since	1.6
	 */
	public function getModel($name = 'Customvalues', $prefix = 'ContactenhancedModel', $config = array())
	{
		$tasks = array('saveOrderAjax','saveorder','publish','unpublish','archive', 'trash','report', 'orderup', 'orderdown', 'delete');
		if( in_array($this->getTask(), $tasks) ){
			$model = parent::getModel('Customvalue', $prefix, array('ignore_request' => true));
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