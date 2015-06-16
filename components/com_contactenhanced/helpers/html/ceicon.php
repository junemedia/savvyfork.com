<?php
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Contact Enhanced Component HTML Helper
 *
 * @static
 * @package		com_contactenhanced
 * @since 2.5
 */
class JHTMLCeicon
{

	/**
	 * Display an edit icon for the article.
	 *
	 * This icon will not display in a popup window, nor if the contact is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param	object	$article	The article in question.
	 * @param	object	$params		The article parameters
	 * @param	array	$attribs	Not used??
	 *
	 * @return	string	The HTML for the article edit icon.
	 * @since	2.5
	 */
	static public function edit(&$contact, &$params, $attribs = array())
	{
		// Initialise variables.
		$user	= JFactory::getUser();
		$userId	= $user->get('id');
		$uri	= JFactory::getURI();
	
		// Ignore if the state is negative (trashed).
		if ($contact->published < 0) {
			return;
		}
		
				
		
		JHtml::_('behavior.tooltip');

		// Show checked_out icon if the article is checked out by a different user
		if (property_exists($contact, 'checked_out') && property_exists($contact, 'checked_out_time') && $contact->checked_out > 0 && $contact->checked_out != $user->get('id')) {
			$checkoutUser = JFactory::getUser($contact->checked_out);
			$button = JHtml::_('image','system/checked_out.png', NULL, NULL, true);
			$date = JHtml::_('date',$contact->checked_out_time);
			$tooltip = JText::_('JLIB_HTML_CHECKED_OUT').' :: '
						.JText::sprintf('COM_CONTACTENHANCED_CHECKED_OUT_BY', $checkoutUser->name)
						.' <br /> '.$date;
			return '<span class="hasTip" title="'
					.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
		}

		
		$url	= ContactenchancedHelperRoute::getContactEditRoute($contact->slug, $contact->catid);
		$icon	= $contact->published ? 'edit.png' : 'edit_unpublished.png';
		$text	= JHtml::_('image','system/'.$icon, JText::_('JGLOBAL_EDIT'), NULL, true);

		if ($contact->published == 0) {
			$overlib = JText::_('JUNPUBLISHED');
		}
		else {
			$overlib = JText::_('JPUBLISHED');
		}

		$date = JHtml::_('date',$contact->created);
		$author = $user->get('name'). ' <sup>('.$user->username.')</sup>';

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('COM_CONTACTENHANCED_LINKED_USER'
						, htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

		$button = JHtml::_('link',JRoute::_($url), $text);

		$output = '<span class="hasTip" title="'.JText::_('JGLOBAL_EDIT').' :: '.$overlib.'">'.$button.'</span>';

		return $output;
	}
}
