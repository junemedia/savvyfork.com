<?php
/*
 * author Cesky WEB s.r.o.
 * @component Multicats
 * @copyright Copyright (C) Cesky WEB s.r.o. extensions.cesky-web.eu
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.controller' );


/**
 * Multi category Controller
 *
 * @package Joomla
 * @subpackage Multi category
 */


class MulticatsController extends JControllerLegacy
{
	/**
	 * display task
	 *
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false)
	{	// set default view if not set
		JRequest::setVar('view', JRequest::getCmd('view', 'default'));

		// call parent behavior
		parent::display($cachable);
	}
}
