<?php
/**
 * @package    ISeKeywords
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @author     Created on 22-Sep-2010
 * @license		GNU/GPL, see license.txt
 * ISeKeywords is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses. 
 */

//-- No direct access
defined('_JEXEC') or die('Access Denied - Please do not try to fool me! ;-)');

jimport('joomla.plugin.plugin');

/**
 * System Plugin
 *
 * @package		ISeKeywords
 * @subpackage	Plugin
 */
class plgSystemISeKeywords extends JPlugin
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
		//To test
		//$_SERVER['HTTP_REFERER']= 'http://www.google.com.br/search?q=joomla+Component+improved&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a';
		$app	= JFactory::getApplication();
		$uri	= JURI::getInstance(JURI::base());

		if(	$app->isAdmin()
			OR !isset($_SERVER['HTTP_REFERER'])
			OR strpos($uri->toString(array('host')),$_SERVER['HTTP_REFERER']) // if internal referer
			OR $_SERVER['HTTP_REFERER'] == ''
		){
			return true;
		}
		
		$lang =JFactory::getLanguage(); 
		$lang->load('com_contactenhanced');
		$lang->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
		$lang->load('plg_system_isekeywords',dirname(__FILE__));
		
		

		$this->session = JFactory::getSession();
		
		
		// array containing all values
		$isk	= array();
		$isk['referer'] 		= $_SERVER['HTTP_REFERER'];
		$isk['all_keywords']	= $this->getKeywords();
		$isk['filter_keywords']	= array();
		$isk['params']			= $this->params;
		$isk['queryString']		= '';
		$isk['i']				= 1;
		//List of words to exclude from matching.
		$isk['excludes']		= explode(',',$this->params->get('exclude','a,e,i,o,u,an,the,for,is,in,are,was,and,from,of,on,with,this,that,or,these,those'));
		array_push($isk['excludes'],' ','.',',',';','!','?');
		
		if(!$isk['all_keywords']){
			return false;
		}
		//$input = JFactory::getApplication()->input;
		//$input->set('iskeywords', implode(' ', $isk['all_keywords']));
		JRequest::setVar('iskeywords', implode(' ', $isk['all_keywords']));
		
		if($this->params->get('enable-highlight',0) ){
			
			// Clean the terms array
			$filter = JFilterInput::getInstance();
	
			$cleanTerms = array();
			foreach ($isk['all_keywords'] as $term)
			{
				$cleanTerms[] = $filter->clean($term, 'string');
			}
	
			// Activate the highlighter.
			JHtml::_('behavior.highlighter', $cleanTerms);
	
			// Adjust the component buffer.
			$doc = JFactory::getDocument();
			$buf = $doc->getBuffer('component');
			$buf = '<br id="highlighter-start" />' . $buf . '<br id="highlighter-end" />';
			$doc->setBuffer($buf, 'component');
		
			
			// Add StyleSheet
			//$doc->addStyleSheet(JURI::root().'plugins/system/isekeywords/isekeywords.css');
			if($this->params->get('search-integrate-loader','sameWindow') == 'modal'){
				JHtml::_('behavior.framework', true);
				JHTML::_('behavior.modal', 'a.modal');
			}
		}
		
		if( $this->params->get('integrate-ce',1) ){
			
			$ceSession	= $this->session->get('com_contactenhanced');
			
			if(!is_array($ceSession)){
				$ceSession	= array();
			}
				$ceSession['isekeywords']=$isk;
				$this->session->set('com_contactenhanced',$ceSession);
			
		}
	}
	/**
	 * Add Link
	 */
	function onAfterRender()
	{
		
		$app	= JFactory::getApplication();
		$uri	= JURI::getInstance(JURI::base());
		$lang =JFactory::getLanguage();
		$lang->load('com_contactenhanced');
		$lang->load('com_contactenhanced',JPATH_ROOT.'/components/com_contactenhanced');
		$lang->load('plg_system_isekeywords',JPATH_ROOT.'/plugin/system/isekeywords');
		
		if(	$app->isAdmin()
			OR !isset($_SERVER['HTTP_REFERER'])
			OR strpos($uri->toString(array('host')),$_SERVER['HTTP_REFERER']) // if internal referer
			OR $_SERVER['HTTP_REFERER'] == ''
		){
			return true;
		}
		
		
			
		if($this->params->get('enable-highlight',0) ){
			

			
			
			/*
			$doc = new DOMDocument();
			@$doc->loadHTML($text);
			$content= '';
			$nody	= $doc->getElementsByTagName('body'); // gets NodeList
			$nod	=$nody->item(0);//Node
			
			
			$this->getContent($content,$nod,'replaceTag',$isk);
			
			//echo '<pre>'; print_r($isk); exit;
			//Gets the body tag (it might have attributes)
			$regex = "#<body(.*?)>#s";
			if(preg_match_all($regex, $text, $matches )){
				$bodyOpenTag 	= $matches[0][0];
			}
			
			// Gets entire body tag with all child nodes
			$regex = "#<body(.*?)>(.*?)</body>#s";
			if(preg_match_all($regex, $text, $matches )){
				$text	= preg_replace($regex, $bodyOpenTag.$content.'</body>', $text);
			}
			*/
			
			//$input = JFactory::getApplication()->input;
			//$iskeywords	= $input->get('iskeywords', '', 'string');
			$iskeywords	= JRequest::getVar('iskeywords', '', 'string');
			// Load instructions on the top of the page
			if( $this->params->get('load-instructions',1) AND $iskeywords){
				$text		= JResponse::getBody();
				
				
				$queryString = '<div class="isk-instructions">'
									.JText::sprintf('ISK_INSTRUCTIONS','<span class="isk-key">'
										.urldecode($iskeywords).'<span>')
								.'</div>';
				$regex = "#<body(.*?)>#s";
				
				if(preg_match_all($regex, $text, $matches )){
					$text	= preg_replace($regex, $matches[0][0].$queryString, $text);
				}
				JResponse::setBody($text);
			}
			
			
			
		}
		
		
	}
	
	/**
	 *  Get's the keywords from referer
	 *  @return mixed array of keywords or false if referer was not a known Search Engine
	 */
	function getKeywords() {
		$referer = &$_SERVER['HTTP_REFERER'];
		
		//Did they get here from a search?
		//if((preg_match('/www\.google.*/i',$referer) && !preg_match('/^http:\/\/www\.google\.com\//i', $referer))
		if((preg_match('/www\.google.*/i',$referer) )
			 || preg_match('/www\.bing.*/i',$referer)
			 || preg_match('/search\.atomz.*/i',$referer)
			 || preg_match('/search\.msn.*/i',$referer)
			 || preg_match('/search\.yahoo.*/i',$referer)
			 || preg_match('/msxml\.excite\.com/i', $referer)
			 || preg_match('/search\.lycos\.com/i', $referer)
			 || preg_match('/www\.alltheweb\.com/i', $referer)
			 || preg_match('/search\.aol\.com/i', $referer)
			 || preg_match('/search\.iwon\.com/i', $referer)
			 || preg_match('/ask\.com/i', $referer)
			 || preg_match('/search\.cometsystems\.com/i', $referer)
			 || preg_match('/www\.hotbot\.com/i', $referer)
			 || preg_match('/www\.overture\.com/i', $referer)
			 || preg_match('/www\.metacrawler\.com/i', $referer)
			 || preg_match('/search\.netscape\.com/i', $referer)
			 || preg_match('/www\.looksmart\.com/i', $referer)
			 || preg_match('/go\.google\.com/i', $referer)
			 || preg_match('/dpxml\.webcrawler\.com/i', $referer)
			 || preg_match('/search\.earthlink\.net/i', $referer)
			 || preg_match('/search\.viewpoint\.com/i', $referer)
			 || preg_match('/www\.mamma\.com/i', $referer)
			 || preg_match('/home\.bellsouth\.net\/s\/s\.dll/i', $referer)
			 || preg_match('/www\.ask\.co\.uk/i', $referer)) {
	
			//Figure out which search and get the part of its URL which contains the search terms.
			if(preg_match('/(www\.google.*)|(www\.bing.*)|(search\.msn.*)|(www\.alltheweb\.com)|(ask\.com)|(go\.google\.com)|(search\.earthlink\.net)/i',$referer))
				$delimiter = "q";
			elseif(preg_match('/www\.ask\.co\.uk/i', $referer))
				$delimiter = "ask";
			elseif(preg_match('/search\.atomz.*/i',$referer))
				$delimiter = "sp-q";
			elseif(preg_match('/search\.yahoo.*/i',$referer))
				$delimiter = "p";
			elseif(preg_match('/(msxml\.excite\.com)|(www\.metacrawler\.com)|(dpxml\.webcrawler\.com)/i', $referer))
				$delimiter = "qkw";
			elseif(preg_match('/(search\.lycos\.com)|(search\.aol\.com)|(www\.hotbot\.com)|(search\.netscape\.com)|(search\.mamma\.com)/i', $referer))
				$delimiter = "query";
			elseif(preg_match('/search\.iwon\.com/i', $referer))
				$delimiter = "searchfor";
			elseif(preg_match('/search\.cometsystems\.com/i', $referer))
				$delimiter = "qry";
			elseif(preg_match('/www\.overture\.com/i', $referer))
				$delimiter = "Keywords";
			elseif(preg_match('/www\.looksmart\.com/i', $referer))
				$delimiter = "key";
			elseif(preg_match('/search\.viewpoint\.com/i', $referer))
				$delimiter = "k";
			elseif(preg_match('/home\.bellsouth\.net\/s\/s\.dll/i', $referer))
				$delimiter = "string";
			
			$query = parse_url($referer, PHP_URL_QUERY);
			parse_str($query, $params);
			$query = $params[$delimiter];
			$query	= str_replace(array('/',':'),' ', $query);
			// Return Query array
			return explode(' ',$query);
		}else{
			return false;
		}
	}
	
	
	public function replaceTag(&$content,&$arg) {
		foreach ($arg['all_keywords'] as $term) {
			if(in_array($term, $arg['excludes']) OR !trim($term)) {
					if(!defined('ISK_TAG_REPLACER')){
						$arg['queryString']	.= ' <span>'.$term.'</span>';
					}
					continue;
				}
				//array_push($arg['filter_keywords'],$term);
				
				if (!preg_match('/<.+>/',$content)) {
					$content = preg_replace('/(\b'.$term.'\b)/i','<span class="isk-term isk-term-'.$arg['i'].'">$1</span>',$content);	
				} else {
					$content = preg_replace('/(?<=>)([^<]+)?(\b'.$term.'\b)/i','$1<span class="isk-term isk-term-'.$arg['i'].'">$2</span>',$content);
				}
				if(!defined('ISK_TAG_REPLACER')){
					$term	= htmlspecialchars($term);
					if($arg['params']->get('search','integrate')){
						$attributes	= array();
						$link = ('index.php?option=com_search&searchword='.$term.'&ordering=newest&searchphrase=any');
						
						if($arg['params']->get('search-integrate-loader','sameWindow') == 'modal'){
							$attributes['class']	= 'modal';
							$link.= '&tmpl=component';
						}elseif($arg['params']->get('search-integrate-loader','sameWindow') == 'newWindow'){
							$attributes['target']	= '_blank';
						}
						
						$term = JHTML::_('link',JRoute::_($link),$term,$attributes);
					}
					$arg['queryString']	.= ' <span class="isk-term-'.$arg['i'].'">'
												.($term)
											.'</span>';
				}
				$arg['i']++;
			}
		// $arg is passed by reference, so we have to reset it
		$arg['i']	= 1;
		if(!defined('ISK_TAG_REPLACER')){
			define('ISK_TAG_REPLACER',1);
		}
		return $content;
	}
	
	/**
	 * 
	 * Get the Content of a XHTML node
	 * @param string $NodeContent the node's content 
	 * @param object $nod The node object
	 * @param string $callback A callback method
	 * @param array	$cb_arg Callback arguments
	 */
	function getContent(&$NodeContent="",$nod,$callback,&$cb_arg){
		$NodList=$nod->childNodes;
		if(is_object($NodList)){
			for( $j=0 ;  $j < $NodList->length; $j++ ){
				$nod2=$NodList->item($j);//Node j
				$nodemane=$nod2->nodeName;
				$nodevalue=$nod2->nodeValue;
				if($nod2->nodeType == XML_TEXT_NODE){
					if (method_exists($this,$callback)) {
						$nodevalue = $this->$callback($nodevalue,$cb_arg);
					}
					$NodeContent .=  $nodevalue;
				}elseif($nodemane == '#comment'){
					$NodeContent .="<!-- $nodevalue -->";
				}elseif($nodemane == '#cdata-section'){
					$NodeContent .="$nodevalue"; 
					// was getting inline javascript on the way, so I commented for testing purposes
					//$NodeContent .="<![CDATA[$nodevalue]]>";
				}else{
					$NodeContent .= "<$nodemane ";
					$attAre=$nod2->attributes;
					
					foreach ($attAre as $value){
						$NodeContent .=' '. $value->nodeName.'="'.$value->nodeValue.'"' ;
					}
					$NodeContent .=">";					
					$this->getContent($NodeContent,$nod2,$callback,$cb_arg);					
					$NodeContent .= "</$nodemane>";
				}
			}
		}
		
	   
	}
}
