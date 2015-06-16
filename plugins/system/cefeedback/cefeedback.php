<?php
/**
 * @package		CEFeedback
 * @author		Douglas Machado {@link http://ideal.fok.com.br}
 * @author		Created on 22-Jan-2011
 * @license		GNU/GPL, see license.txt
 * CE Feedback is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses. 
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Example System Plugin
 *
 * @package		CEFeedback
 * @subpackage	Plugin
 */
class plgSystemCEFeedback extends JPlugin
{
	
	/**
	 * Object Constructor.
	 *
	 * @access	public
	 * @param	object	The object to observe -- event dispatcher.
	 * @param	object	The configuration object for the plugin.
	 * @return	void
	 * @since	1.0
	 */
	function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		
	}
	/**
	 * Add style
	 */
	//function onAfterInitialise(){
	function onAfterDispatch(){
		//CSS
		if (!$this->checkLoadPermissions()) {
			return '';
		}

		$lang =JFactory::getLanguage();
		
		$imagePath		= '/plugins/system/cefeedback/images/';
		$imagePathHover	= $imagePath;
		$image			= $this->params->get('feedback-image',		'feedback-red.png');
		$imageHover		= $this->params->get('feedback-image-hover','feedback-orange.png');
		
		if(is_readable(JPATH_ROOT.$imagePath.$lang->getTag().'/'.$image)){
			$imagePath	.= $lang->getTag().'/';
		}else{
			$imagePath	.= 'en-GB/';
		}
		
		if(is_readable(JPATH_ROOT.$imagePathHover.$lang->getTag().'/'.$imageHover)){
			$imagePathHover	.= $lang->getTag().'/';
		}else{
			$imagePathHover	.= 'en-GB/';
		}
		
		// using JURI::base(true) instead of JURI::root() in order to work correctly with SSL
		$style	= "a.cefeedback{"
					.$this->params->get('position','right').":0;"
					.'background:url("'.JURI::base(true).$imagePath.$image.'") no-repeat scroll 0 50% transparent !important;'
					."outline:medium none;"
					."padding:0 !important;"
					."position:fixed;"
					."z-index:99995;"
					."text-indent:-9999px;"
					."top:".$this->params->get('feedback-link-distance',45)."%;"
					."width:".$this->params->get('feedback-link-width',26)."px;"
					."height:".$this->params->get('feedback-link-height',100)."px;"
					/*
					."-webkit-transform: rotate({$rotation['niceBrowsers']});" 	//
					."-moz-transform: rotate({$rotation['niceBrowsers']});" 	// FF
					."-o-transform: rotate({$rotation['niceBrowsers']});"		// Opera
					."filter: progid:DXImageTransform.Microsoft.BasicImage(rotation={$rotation['ie']});" // IE
					*/
				."}"
				."a.cefeedback:hover,a.cefeedback:focus,a.cefeedback:active{"
					.'background:url("'.JURI::base(true).$imagePathHover.$imageHover.'") no-repeat scroll 0 50% transparent !important;'
				."}";
		$doc	= JFactory::getDocument();
		$doc->addStyleDeclaration($style);
		$doc->addScriptDeclaration("window.addEvent('domready',function(){var ceFeedbackHover=new Asset.image('".JURI::base(true).$imagePathHover.$imageHover."');});");
		
		
		// disabled because it has an error onload
		if(FALSE AND $this->params->get('onfirstpageload') AND !JRequest::getVar('cefeedback-alreadyloaded',0,'cookie')){
			//$doc->addScriptDeclaration("window.addEvent('load',function(){SqueezeBox.fromElement($('cefeedback'));});");
			$doc->addScriptDeclaration("window.addEvent('load',function(){SqueezeBox.fromElement($('cefeedback'));var ceFeedbackCookie=Cookie.write('cefeedback-alreadyloaded','1',{duration:30});});");
		}
	}
	
	public function onAfterRoute(){
		// Artisteer templates did not like this in onAfterDispatch
		if (!$this->checkLoadPermissions()) {
			return '';
		}
		JHtml::_('behavior.framework', true);
		if($this->params->get('modal',1)){
			JHTML::_('behavior.modal', 'a.feedback-modal');
		}
	}
	
	/**
	 * Add Link
	 */
	function onAfterRender()
	{
		if (!$this->checkLoadPermissions()) {
			return '';
		}
		
		$document=JFactory::getDocument();
		$lang =JFactory::getLanguage();
		$lang->load('com_contactenhanced');
		$lang->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
		$lang->load('plg_system_cefeedback',JPATH_ROOT.'/plugin/system/cefeedback');
		
		// get link
		if($this->params->get('media') == 'url' AND $this->params->get('media-url-link')){
			$link	= $this->params->get('media-url-link');
		}elseif ($this->params->get('media') == 'article' AND $this->params->get('media-article-id')){
			$link	= JURI::base(true).'index.php?option=com_content&view=article&id='
						.$this->params->get('media-article-id',1);
			if(!$browser->isMobile() OR $this->params->get('loadonmobile',0) != 'full'){
				$link	.= ($this->params->get('media-article-tmpl') ? '&tmpl='.$this->params->get('media-article-tmpl') : '');
			}
			
			$link	.= ($this->params->get('template') ? '&amp;template='.$this->params->get('template') : '');
		// DEFAULT
		}else{
			require_once (JPATH_ROOT.'/components/com_contactenhanced/helpers/helper.php');
			if($this->params->get('modal',1) OR ($browser->isMobile() AND $this->params->get('loadonmobile',0) != 'full')){
				$tmpl	= '&amp;tmpl=component';
			}else{
				$tmpl	= '';
			}
			$link	= JURI::base(true).'/'.('index.php?option=com_contactenhanced&amp;view=contact&amp;id='
						.$this->params->get('media-ce-id',1)
						.'&amp;content_title='.ceHelper::encode($document->getTitle())
						.'&amp;content_url='.ceHelper::encode(ceHelper::getCurrentURL())
						.$tmpl
						.($this->params->get('template') ? '&amp;template='.$this->params->get('template') : '')
						.'&amp;plugin_load_method=modal'
					);
		}
		
					
		if($this->params->get('useSEF',0)){
		//	$link	= JRoute::_($link);
		}
		$linkRel	= ($this->params->get('modal',1) ? ' rel="{handler: \'iframe\', size: {x:'.$this->params->get('window-size-width',800).', y:'.$this->params->get('window-size-height',480).'}}"' : '');
		$link	= '<a style="text-decoration:none;" 
						href="'.$link.'" 
						class="cefeedback'.($this->params->get('modal',1) ? ' feedback-modal' : '').'" 
						title="'.JText::_('CE_PLUGIN_FEEDBACK_DESC').'"
						'.$linkRel.'
						id="cefeedback">'
						//.$text
						.'&nbsp;'
					.'</a>';
		
		// Add link before the </body> tag
		$body = JResponse::getBody();
		$body = str_replace('</body>', $link.'</body>', $body);
		JResponse::setBody($body);
	}
	
	public function checkLoadPermissions() {
		// Checks if allowed to display in Mobile devices
		if(!class_exists('iBrowser')){
			require_once(JPATH_ROOT.'/components/com_contactenhanced/helpers/browser.php');
		}
		
		if (!$this->params->get('loadonmobile',0)) {
			$browser = new iBrowser();
			if($browser->isMobile() ){
				return false;
			}
		}
		
		$app = JFactory::getApplication();
		if(	$app->isAdmin()
			OR JRequest::getVar('tmpl') == 'component'
			OR JRequest::getVar('tmpl') == 'raw'
			OR (
				is_array($this->params->get('avoid_menuitems',array())) AND
				in_array(JRequest::getInt( 'Itemid'),$this->params->get('avoid_menuitems',array()))
			) 
			OR (
				is_array($this->params->get('avoid_components')) AND
				in_array(JRequest::getVar('option'),$this->params->get('avoid_components',array()))
			)
			OR (
				is_string($this->params->get('avoid_components')) AND
				strlen($this->params->get('avoid_components')) > 4 AND
				JRequest::getCmd('option') ==$this->params->get('avoid_components')
			)
			
			OR (
				is_string($this->params->get('avoid_menuitems')) AND
				strlen($this->params->get('avoid_menuitems')) > 0 AND
				JRequest::getInt( 'Itemid') == $this->params->get('avoid_menuitems')
			)
		){
			return false;
		}
		
		// It seems to be allowed to load, so...
		return true;
	}
}
