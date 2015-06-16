<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 *
 * @copyright   Copyright (C) 2005 - 2012 IdealExtensions.com, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.environment.browser');

/**
 * SecurImage Plugin.
 * Based on the Joomla recaptcha plugin
 *
 * @package     Joomla.Plugin
 * @subpackage  Captcha
 * @since       2.5
 */
class plgCaptchaSecurimage extends JPlugin
{
	public function __construct($subject, $config)
	{
		parent::__construct($subject, $config);
		$this->session = JFactory::getSession();

		$lang = JFactory::getLanguage();
		$lang->load('plg_captcha_securimage');
		$lang->load('plg_captcha_securimage',JPATH_SITE.'/plugins/captcha/securimage');
		$lang->load('plg_captcha_securimage',JPATH_ADMINISTRATOR);
	}

	/**
	 * Initialise the captcha
	 *
	 * @param	string	$id	The id of the field.
	 *
	 * @return	Boolean	True on success, false otherwise
	 *
	 * @since  2.5
	 */
	public function onInit($id)
	{
		return true;
	}

	public function canLoad(){
		// @todo Find out why this function is called twice on form submission
		// Avoid this function to be called twice
		$user	= JFactory::getUser();
		$app	= JFactory::getApplication();

		if( ($this->params->get('public_only') AND $user->id)
				OR defined('SECURIMAGE_RAN_ONCE')
		){
			return false;
		}
		return true;
	}
	/**
	 * Gets the challenge HTML
	 *
	 * @return  string  The HTML to be embedded in the form.
	 *
	 * @since  2.5
	 */
	public function onDisplay($name, $id, $class)
	{
		$lang		= JFactory::getLanguage();

		if ($this->canLoad() == false) {
			return '';
		}


		JHtml::_('behavior.framework', true);
		jimport('joomla.error.profiler');
		$doc		= JFactory::getDocument();
		$session	= JFactory::getSession();


		$securImage_path	= '/plugins/captcha/securimage/lib/';
		$urlPath	= JURI::base(true).$securImage_path;

		$namespace	= 'si_'.mt_rand();

		$count	= $session->get('plg_captcha_securimage.count',0);
		$session->set('plg_captcha_securimage.count',(++$count));
		$session->set('plg_captcha_securimage.lang',$lang->getTag());

		$javascript	= '';
		if(!defined('SECURIMAGE_CAPTCHA_INSTANCE'))
		{
			define('SECURIMAGE_CAPTCHA_INSTANCE',1);
			$session->set('plg_captcha_securimage.count',1);
			if($this->params->get('custom-css')){
				$doc->addStyleDeclaration($this->params->get('custom-css'));
			}
			JText::script('PLG_CAPTCHA_SECURIMAGE_VALIDATION_CODE_WRONG');

			if(!class_exists('iBrowser')){
				require_once (JPATH_PLUGINS.'/captcha/securimage/helpers/browser.php');
			}
			$browser = new iBrowser();
			if($browser->getBrowser() == 'Internet Explorer' AND version_compare($browser->getVersion(), '9.0') <= 0){
				$doc->addScript($urlPath.'js/securImage-ie8.js');
			}else{
				$doc->addScript($urlPath.'js/securImage.js');
			}
			/**/
			$javascript	.= "
/* <![CDATA[ */
var icaptchaURI	= '".JURI::root(true)."/';
/* ]]> */
			";

			// This line should only be necesary in the if below, however we were having some problems and decided to always load this script
			$javascript	.= "window.addEvent('domready', function(){
				(function(){updateCaptchas();}).delay(1000);
});
";
		}elseif($count==2 AND $this->params->get('clickMe') ==0){

		}
		$reloadLink	= '';
		//$imageLink	= $urlPath.'show.php?securimage_namespace='.$namespace;
		$imageLink	= $urlPath.'show.php?';
		$linkAttributes['onclick']	= "updateCaptchas('{$namespace}'); return false;";
		$linkAttributes['id']		= 'reloadImage';
		$linkAttributes['title']	= JText::_('PLG_CAPTCHA_SECURIMAGE_RELOAD_IMAGE_TITLE');

		if($this->params->get('reload', 1)){

			$image	= JHTML::_('image','plugins/captcha/securimage/lib/images/reload/'.$this->params->get('reload-image-src', 'sync.png')
					,JText::_('PLG_CAPTCHA_SECURIMAGE_RELOAD_IMAGE')
					,'border="0"'
					, false);

			$reloadLink = JHTML::_('link', JURI::current().'#'.JText::_('PLG_CAPTCHA_SECURIMAGE_RELOAD_IMAGE'),$image,$linkAttributes );
		}
		$html = '<br /><div class="securimage-container">';


		//echo $securImage_path; exit;
		$html .= ' <img '
						//. ' src="'.$imageLink.'&sid='.md5(uniqid(time())).'"'
						. ' src="'.$urlPath.'show.php?firstLoad=1"'
						. ' border="0" '
						.($this->params->get('input-field-location') == 'right' ? ' align="left"' : '')
						. ' id="img-'.$namespace.'"'
						. ' alt="'.JText::_('PLG_CAPTCHA_SECURIMAGE_IMAGE_ALT_TEXT').'"'
						. ' name="captcha" '
						. ' class="img-securimage-captcha" '
						. ' onclick="'.	$linkAttributes['onclick']	.'"'
						. ' title="'.	$linkAttributes['title']	.'"'
					.' />
						';
		//Flash player for sound file
		if($this->params->get('play_sound')){
			$player_size	= $this->params->get('play_sound-sound-playersize','32');
			$bgcolor		= $this->params->get('play_sound-sound-bgcolor','ffffff');
			$playerLink		= $urlPath.'play.swf?&bgcol=#'.$bgcolor.'&amp;icon_file='
									.$urlPath.'images/audio/audio_icon.png&amp;audio_file='
									.$urlPath.'play.php?securimage_namespace='.$namespace;
			$html .= '<object type="application/x-shockwave-flash" data="'.$playerLink.'"
						height="'.$player_size.'"
						width="'.$player_size.'">
			<param name="movie" value="'.$playerLink.'" />
			</object>';
		}

		$html .= $reloadLink;

		$html .= '<div class="securimage-field-container">';
		$html .= '<label for="'.$namespace.'" class="requiredField securimage-label">'
					.JText::_('PLG_CAPTCHA_SECURIMAGE_VALIDATION_CODE_LABEL').': </label>';
		$html .= '<br />';

		$html .=  '<input type="text"
							autocomplete="off"
							name="captcha_code"
							id="'.$namespace.'"
							class="inputbox captchacode required sicaptcha validate-sicaptcha"
							size="10" maxlength="" />';
		$html .=  '<input type="hidden" name="captcha_code-validation" id="'.$namespace.'-validation"  />';
		//$html .=  '<input type="hidden" name="securimage_namespace" value="'.$namespace.'"  />';
		$html .=  '<input type="hidden" name="captcha_code-icaptchaUseAjax" id="icaptchaUseAjax"
							value="'.$this->params->get('ajaxcheck',0).'" />';

		$html .= '</div>';
		$doc->addScriptDeclaration($javascript); //Conflicts with other extensions
		$html .= '<br /></div>';
		return $html;
	}

	/**
	  * Calls an HTTP POST function to verify if the user's guess was correct
	  *
	  * @return  True if the answer is correct, false otherwise
	  *
	  * @since  2.5
	  */
	public function onCheckAnswer($code)
	{
		if ($this->canLoad() == false) {
			return true;
		}
		$input      = JFactory::getApplication()->input;

		require_once(JPATH_PLUGINS.'/captcha/securimage/lib/securimage.php');
		$securimage = new Securimage();
	//	$securimage->namespace	= $input->get('securimage_namespace', 'default', 'string');
		// Is the code was correct?

		if($securimage->check($input->get('captcha_code', '','string')) == false) {
			//@todo use exceptions here
			$this->_subject->setError(JText::_('PLG_CAPTCHA_SECURIMAGE_VALIDATION_CODE_WRONG'));
			return false;
		}
		// @todo Find out why this function is called twice on form submission
		// Avoid this function to be called twice
		define('SECURIMAGE_RAN_ONCE',1);
		return true;
	}
}
