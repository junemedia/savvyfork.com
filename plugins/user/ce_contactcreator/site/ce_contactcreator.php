<?php
/**
 *
 * Contact Creator
 * A tool to automatically create and synchronise contacts with a user
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Class for Contact Creator
 * @package		Joomla.Plugin
 * @subpackage	User.contactcreator
 * @version		1.6
 */
class plgUserCe_ContactCreator extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();

		$lang =JFactory::getLanguage();
		$lang->load('com_contactenhanced',		JPATH_ADMINISTRATOR.'/components/com_contactenhanced', 'en-GB');
		$lang->load('com_contactenhanced',		JPATH_ADMINISTRATOR.'/components/com_contactenhanced');
		$lang->load('plg_user_ce_contactcreator',dirname(__FILE__), 'en-GB');
		$lang->load('plg_user_ce_contactcreator',dirname(__FILE__));
	}

	function onUserAfterSave($user, $isnew, $success, $msg)
	{
		
		if(!$success) {
			return false; // if the user wasn't stored we don't resync
		}

		if(!$isnew) {
			return false; // if the user isn't new we don't sync
		}

		// ensure the user id is really an int
		$user_id = (int)$user['id'];

		if (empty($user_id)) {
			die('invalid userid');
			return false; // if the user id appears invalid then bail out just in case
		}

		$category = $this->params->get('category', 0);
		if (empty($category)) {
			JError::raiseWarning(41, JText::_('PLG_CE_CONTACTCREATOR_ERR_NO_CATEGORY'));
			return false; // bail out if we don't have a category
		}

		$dbo = JFactory::getDBO();
		// grab the contact ID for this user; note $user_id is cleaned above
		$dbo->setQuery('SELECT id FROM #__ce_details WHERE user_id = '. $user_id );
		$id = $dbo->loadResult();

		require_once(JPATH_ROOT.'/components/com_contactenhanced/helpers/helper.php');
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_contactenhanced/tables');
		$contact = JTable::getInstance('contact', 'ContactenhancedTable');

		if (!$contact) {
			return false;
		}

		if ($id) {
			$contact->load($id);
		}elseif($this->params->get('autopublish', 0)) {
			$contact->published = 1;
		}elseif($this->params->get('autopublish', 0) == 0) {
			$contact->published = -3;
		}
		$lang = JFactory::getLanguage();
		
		$contact->name		= $user['name'];
		$contact->user_id	= $user_id;
		$contact->email_to	= $user['email'];
		$contact->catid		= $category;
		$contact->language	= $lang->getTag();
		
		$autowebpage = $this->params->get('autowebpage', '');

		if (!empty($autowebpage)) {
			// search terms
			$search_array = array('[name]', '[username]', '[userid]', '[email]');
			// replacement terms, urlencoded
			$replace_array = array_map('urlencode', array($user['name'], $user['username'], $user['id'], $user['email']));
			// now replace it in together
			$contact->webpage = str_replace($search_array, $replace_array, $autowebpage);
		}

		if ($contact->check()) {
			$result = $contact->store();
		}

		if (!(isset($result)) || !$result) {
			JError::raiseError(42, JText::sprintf('PLG_CE_CONTACTCREATOR_ERR_FAILED_UPDATE', $contact->getError()));
		}
	}
}
