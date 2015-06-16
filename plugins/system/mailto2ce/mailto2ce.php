<?php
/**
 * @package		Mailto2CE
 * @author		Douglas Machado {@link http://ideal.fok.com.br}
 * @author		Created on 22-Jan-2013
 * @license		GNU/GPL, see license.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * System Plugin
 *
 * @package		Mailto2CE
 * @subpackage	Plugin
 */
class plgSystemMailto2ce extends JPlugin
{
	/**
	 * Genarate a search pattern based on link and text.
	 *
	 * @param string The target of an e-mail link.
	 * @param string The text enclosed by the link.
	 * @return string A regular expression that matches a link containing the
	 * parameters.
	 */
	function _email_searchPattern ($link, $text) {
		// <a href="mailto:anyLink">anyText</a>
		$pattern = '~(?:<a [\w "\'=\@\.\-]*href\s*=\s*"(mailto:|https?://(?:[a-z0-9][a-z0-9\-]*[a-z0-9]\.)*(?:[a-z0-9]+)(?::\d+)?[a-z0-9;/\?:\@&=+\$,\-_\.!\~*\'\(\)%]+?%3C)'
			. $link . '(%3E)?"([\w "\'=\@\.\-]*))>' . $text . '</a>~i';
	
		return $pattern;
	}
		
	/**
	 * Changes all email links (mailto:) to a link to a Contact Enhanced form.
	 *
	 * @param string The string to be cloaked.
	 * @param string The mode.
	 * replaces addresses with "mailto:" links if nonzero.
	 * @return boolean True on success.
	 */
	function replacer(&$text)
	{
		
		// any@email.address.com
		$searchEmail = '([\w\.\-]+\@(?:[a-z0-9\.\-]+\.)+(?:[a-z0-9\-]{2,4}))';
		// any@email.address.com?subject=anyText
		$searchEmailLink = $searchEmail . '([?&][\x20-\x7f][^"<>]+)';
		// anyText
		$searchText = '((?:[\x20-\x7f]|[\xA1-\xFF]|[\xC2-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF4][\x80-\xBF]{3})[^<>]+)';
	
		//$searchText = '(+)';
		//Any Image link
		$searchImage	=	"(<img[^>]+>)";
	
		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com"
		 * >email@amail.com</a>
		 */
		$pattern = $this->_email_searchPattern($searchEmail, $searchEmail);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
			$mail = $regs[2][0];
			$mailText = $regs[5][0]; 
			// Check to see if mail text is different from mail addy
			$replacement = $this->createLink( $mail, $mailText);
	
			// Replace the found address with the link to Contact Enhanced
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}
		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com">
		 * anytext</a>
		 */
		$pattern = $this->_email_searchPattern($searchEmail, $searchText);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
			
	        $prefix = $regs[1][0];
			$mail = $regs[2][0];
	        $suffix = $regs[3][0];
	        $attribs = $regs[4][0];
			$mailText = $regs[5][0];
	
			$replacement = $this->createLink( $mail, $mailText, $attribs);
	
			// Replace the found address with the link to Contact Enhanced
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}
	
		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com">
		 * <img anything></a>
		 */
		$pattern = $this->_email_searchPattern($searchEmail, $searchImage);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
			$prefix = $regs[1][0];
			$mail = $regs[2][0];
			$suffix = $regs[3][0];
			$attribs = $regs[4][0];
			$mailText = $regs[5][0];
	
			$replacement = $this->createLink( $mail, $mailText, $attribs);
	
			// Replace the found address with the link to Contact Enhanced
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}
	
		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com?
		 * subject=Text">email@amail.com</a>
		 */
		$pattern = $this->_email_searchPattern($searchEmailLink, $searchEmail);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
			$mail = $regs[2][0] . $regs[3][0];
			$mailText = $regs[6][0];
			
			$queryString = '';
			if(strpos($mail,'?')){
				$queryString = substr($mail, (strpos($mail,'?')+1));
			}
			
			// Needed for handling of Body parameter
			//$mail = str_replace( '&amp;', '&', $mail );
			// This will ignore the subject if it was set in the link
			$mail = substr($mail,0,strpos($mail,'?'));
			// Check to see if mail text is different from mail addy
			$replacement = $this->createLink( $mail, $mailText,'',$queryString);
	
			// Replace the found address with the link to Contact Enhanced
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}
	
		/*
		 * Search for derivatives of link code <a href="mailto:email@amail.com?
		 * subject=Text">anytext</a>
		 */
		$pattern = $this->_email_searchPattern($searchEmailLink, $searchText);
		while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE)) {
			$mail = $regs[2][0] . $regs[3][0];
			$mailText = $regs[6][0];
			
			$queryString = '';
			if(strpos($mail,'?')){
				$queryString = substr($mail, (strpos($mail,'?')+1));
			}
			
			
			// Needed for handling of Body parameter
			//$mail = str_replace( '&amp;', '&', $mail );
			// This will ignore the subject if it was set in the link
			$mail = substr($mail,0,strpos($mail,'?'));
			
			$replacement = $this->createLink( $mail, $mailText,'',$queryString);
	
			// Replace the found address with the link to Contact Enhanced
			$text = substr_replace($text, $replacement, $regs[0][1], strlen($regs[0][0]));
		}
			// Search for plain text email@amail.com
			$pattern = '~' . $searchEmail . '([^a-z0-9]|$)~i';
			while (preg_match($pattern, $text, $regs, PREG_OFFSET_CAPTURE) AND FALSE) {
				$mail = $regs[1][0];
				if($this->params->get('parse-plain-text-emails',0) 
					AND JRequest::getVar('view') != 'contact')
				{
					$replacement = $this->createLink( $mail);
				}else{
					$replacement = $this->_convertEncoding($mail);
				}
				// Replace the found address with the link to Contact Enhanced
				$text = substr_replace($text, $replacement, $regs[1][1], strlen($mail));
			}
		
		return true;
	}
	
	function createLink($mail,$mailText=null, $attribs='',$queryString='') {

		$doc	= JFactory::getDocument();
		$config	= JFactory::getConfig();
		$secret	= $config->get('config.secret');
		$session 	= JFactory::getSession(); // Get the session
		$session->set(JApplication::getHash($secret.$mail), $mail); // Store the emails in the session using a key
		$recipient	= JApplication::getHash($secret.$mail);
		
		require_once (JPATH_ROOT.'/components/com_contactenhanced/helpers/helper.php');
		$link	= ('index.php?option=com_contactenhanced&view=contact&id='
						.$this->params->get('contactid',1)
						.'&content_title='.ceHelper::encode($doc->getTitle())
						.'&content_url='.ceHelper::encode(ceHelper::getCurrentURL())
						.'&encodedrecipient='.$recipient
						.($this->params->get('modal',1) ? '&tmpl=component' : '') 
						.($this->params->get('template') ? '&template='.$this->params->get('template') : '')
						.'&amp;'.$queryString 
						.'&amp;plugin_load_method=modal'
					);
		$mailText = ($mailText ? $mailText : $mail);
		if(strpos($mailText,'@')){
			$mailText	= $this->_convertEncoding($mailText);
		}
		
		if($this->params->get('javascriptLibrary') == 'jquery'){
			$params = array();
			$params['title']  = $mailText;
			$params['url']    = JURI::root().$link;
			$params['height'] = $this->params->get('window-size-height',480);
			$params['width']  = $this->params->get('window-size-width',800);
			$params['remote']  = true;
			$selector				= 'modal-mailto2ce';
			$link	= JHtml::_('bootstrap.renderModal', $selector, $params);
			
			$link	.= JHtml::_('link'
					, '#'.$selector
					, $params['title']
					, $attribs . ' data-toggle="modal" '
			);
		}else{
			$link	= '<a	href="'.JURI::root().$link.'" 
						class="'.($this->params->get('modal',1) ? 'modal-' : '').'mailto2ce" 
						'.$attribs.'
						rel="{handler: \'iframe\', size: {x:'.$this->params->get('window-size-width',800).', y:'.$this->params->get('window-size-height',480).'}}"
						>'
						.$mailText
						//.($mailText ? $mailText : $mail)
					.'</a>';
		}
		
		return $link;
	}

	function onAfterRender()
	{
		$app = JFactory::getApplication();
		
		$lang =JFactory::getLanguage();
		$lang->load('com_contactenhanced');
		$lang->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
		$lang->load('plg_system_mailto2ce',JPATH_ROOT.'/plugin/system/mailto2ce');
		
		if(	$app->isAdmin()
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
						JRequest::getCmd('option') ==$this->params->get('avoid_components')
				)
					
				OR (
						is_string($this->params->get('avoid_menuitems')) AND
						JRequest::getInt( 'Itemid') ==$this->params->get('avoid_menuitems')
				)
				OR (
						JRequest::getInt( 'option') == 'com_content' AND
						JRequest::getVar( 'view')	== 'article' AND
						in_array(JRequest::getInt('id'), $this->params->get('avoid_articles',array()))
				)
		){
			return ;
		}
		
		$body = JResponse::getBody();
		
		// Simple performance check to determine whether bot should process further.
		if (stripos($body, '@') === false 
			OR $app->isAdmin()
			OR JRequest::getVar('format') == 'vcf' 
			) {
				
			if($app->isAdmin()){
				$emailcloakPlugin = JPluginHelper::getPlugin('content', 'emailcloak');
				//echo '<pre>'; print_r($emailcloakPlugin); exit;
				if(is_object($emailcloakPlugin)){
					// disables emailcloak plugin
					$db	= JFactory::getDBO();
					$db->setQuery('UPDATE #__extensions SET enabled = 0 WHERE type= '.$db->Quote('plugin').' AND element = '.$db->Quote('emailcloak'));
					$db->query();
				}
			}
			return true;
		}
		
		$this->replacer($body);
		JResponse::setBody($body);
	}
	
	//function onAfterInitialise(){
	function onAfterDispatch(){
		$app = JFactory::getApplication();
		if(!$app->isAdmin()){
			if($this->params->get('javascriptLibrary') == 'jquery'){
				JHtml::_('bootstrap.framework');
				JHtml::_('bootstrap.loadCss');
			}else{
				JHtml::_('behavior.framework', true);
				JHTML::_('behavior.modal', 'a.modal-mailto2ce');
			}
			
		}
	}
	
	function _convertEncoding( $text )
	{
		// replace vowels with character encoding
		$text 	= str_replace( 'a', '&#97;', $text );
		$text 	= str_replace( 'e', '&#101;', $text );
		$text 	= str_replace( 'i', '&#105;', $text );
		$text 	= str_replace( 'o', '&#111;', $text );
		$text	= str_replace( 'u', '&#117;', $text );
		// replace @ with character encoding
		$text	= str_replace( '@', '&#64;', $text );
		return $text;
	}
}