<?php
/**
 * @subpackage Helpers
 * @license		GNU/GPL, see license.txt */

/**
 * @package		com_contactenhanced
 * @since		1.6
 */
class ContactenhancedHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
	public static function addSubmenu($vName)
	{
		require_once (JPATH_ROOT.'/components/com_contactenhanced/helpers/helper.php');
		ceHelper::addSubmenu('categories');
	}


}
