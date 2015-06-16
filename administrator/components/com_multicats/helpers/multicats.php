<?php
/*
 * author Cesky WEB s.r.o.
 * @component Multicats
 * @copyright Copyright (C) Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
 
defined('_JEXEC') or die;

class MulticatsHelper
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
		JSubMenuHelper::addEntry(
			JText::_('COM_MULTICATS_SUBMENU_CPANEL'),
			'index.php?option=com_multicats&view=cpanel',
			$vName == 'cpanel'
		);		
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The category ID.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($categoryId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$assetName = 'com_cweshops';

		$actions = array(
			'core.admin', 'core.manage'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}


	/**
	 * Method to get Multicats Version
	 * @return string Version of Multicats
	 */
	static function getVersion()
	{

		$folder = JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components'.DS.'com_multicats';
    
    // import joomla's filesystem classes
    jimport('joomla.filesystem.folder');
    
		if (JFolder::exists($folder)) {
			$xmlFilesInDir = JFolder::files($folder, '.xml$');
		} else {
			$folder = JPATH_SITE .DS. 'components'.DS.'com_multicats';
			if (JFolder::exists($folder)) {
				$xmlFilesInDir = JFolder::files($folder, '.xml$');
			} else {
				$xmlFilesInDir = null;
			}
		}

		$xml_items = '';
		if (count($xmlFilesInDir))
		{
			foreach ($xmlFilesInDir as $xmlfile)
			{
				if ($data = JApplicationHelper::parseXMLInstallFile($folder.DS.$xmlfile)) {
					foreach($data as $key => $value) {
						$xml_items[$key] = $value;
					}
				}
			}
		}
		
		if (isset($xml_items['version']) && $xml_items['version'] != '' ) {
			return $xml_items['version'];
		} else {
			return '';
		}
	}
}
