<?php
/**
 * @author     Douglas Machado {@link http://ideal.fok.com.br}
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;


/**
 * @package		com_contactenhanced
*/
class ContactenhancedTableCustomfield extends JTable
{
	/**
	 * Constructor
	 *
	 * @param object Database connector object
	 * @since 1.0
	 */
	public function __construct(& $db)
	{
		parent::__construct('#__ce_cf', 'id', $db);
	}

	/**
	 * Overloaded bind function
	 *
	 * @param	array		Named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @since	1.6
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
		$jform	= JRequest::getVar('jform'	, '', 'POST', 'none', JREQUEST_ALLOWRAW);
		if (isset($jform['value'])) {
			$array['value']	= $jform['value'];
		}
		if (isset($jform['tooltip'])) {
			$array['tooltip']	= $jform['tooltip'];
		}
		if (isset($jform['attributes'])) {
			$array['attributes']	= $jform['attributes'];
		}
	
		return parent::bind($array, $ignore);
	}

	/**
	 * Stores a custom field
	 *
	 * @param	boolean	True to update fields even if they are null.
	 * @return	boolean	True on success, false on failure.
	 * @since	1.6
	 */
	public function store($updateNulls = false)
	{
		$this->_getOrdering();
		
		// Transform the params field
		if (is_array($this->params)) {
			$registry = new JRegistry();
			$registry->loadArray($this->params);
			$this->params = (string)$registry;
		}
		//echo '<pre>'; print_r($this); echo '<pre>'; exit;
		
		// Attempt to store the data.
		return parent::store($updateNulls);
	}

	/**
	 * Overloaded check function
	 *
	 * @return boolean
	 * @see JTable::check
	 * @since 1.5
	 */
	function check()
	{
		
		/** check for valid category */
		if (trim($this->catid) == '') {
			$this->setError(JText::_('COM_CONTACTENHANCED_WARNING_CATEGORY'));
			return false;
		}
		
		if (empty($this->alias)) {
			$this->alias = $this->name;
		}
		
		switch ($this->type){
			case 'name':
			case 'email':
			case 'email_verify':
			case 'subject':
			case 'password':
			case 'password_verify':
			case 'username':
			case 'surname':
				$this->alias = $this->type;
			break;
		}
		$this->alias = str_replace('-','_',JApplication::stringURLSafe($this->alias));
		
		return $this->canInsert();
	}
	
	/**
	 * 
	 */
	function canInsert(){
		// Check Alias
		
		$query	= $this->_db->getQuery(true);
		//
		if($this->id){
			$query->where('id <> '.$this->_db->Quote($this->id));
		}
		
		//ignore unpublished items:
		$query->where('published	 > 0');
		
		if($this->language == '*' AND $this->catid == '0'){
			$query->where('alias		= '.$this->_db->Quote($this->alias));
		}else{
			$query->where('alias		= '.$this->_db->Quote($this->alias));
			$query->where('(catid		= '.$this->_db->Quote($this->catid) 	.' OR catid	= '.$this->_db->Quote('0'). ')');
			$query->where('(language	= '.$this->_db->Quote($this->language)	.' OR language = '.$this->_db->Quote('*'). ')');
		}
		
		$query->select('count(id)');
		$query->from($this->_tbl);
		$this->_db->setQuery($query);
		if( ($this->_db->loadResult()) > 0 ){
			$this->setError(JText::sprintf('COM_CONTACTENHANCED_CF_ERROR_UNIQUE_ALIAS',($this->alias)));
			return false;
		}
		
		// Check for Multiple Unique fields
				$denyMultiple	= array('subject','email','email_verify','name','username','surname','password','password_verify','password_verify');
		
		if( !in_array($this->type, $denyMultiple) ){
			return true;
		}
		
		$query	= $this->_db->getQuery(true);
		//
		if($this->id){
			$query->where('id <> '.$this->_db->Quote($this->id));
		}
		
		//ignore unpublished items:
		$where[]= 'published	 > 0';
		
		if($this->language == '*' AND $this->catid == '0'){
			$query->where('type		= '.$this->_db->Quote($this->type));
		}else{
			$query->where('type		= '.$this->_db->Quote($this->type));
			$query->where('(catid	= '.$this->_db->Quote($this->catid) 	.' OR catid	= '.$this->_db->Quote('0'). ')');
			$query->where('(language= '.$this->_db->Quote($this->language)	.' OR language = '.$this->_db->Quote('*'). ')');
		}
		
		$query->select('count(id)');
		$query->from($this->_tbl);
		
		$this->_db->setQuery($query);
		// echo $this->_db->getQuery($query); exit;
		if( ($this->_db->loadResult()) > 0 ){
			$this->setError(JText::sprintf('CE_CF_WARNING_ONE_FIELD_PER_CATEGORY',ucfirst($this->type)));
			return false;
		}
		
		return true;	
	}
	private function _getOrdering() {
		if($this->ordering == 999){
			$query	= $this->_db->getQuery(true);
			$query->select('ordering');
			$query->from('#__ce_cf');
			$query->where("catid =".$this->_db->Quote($this->catid));
			$query->order('ordering DESC');
			$this->_db->setQuery($query);
			if (($ordering	= $this->_db->loadResult()) ) {
				$this->ordering = (++$ordering);
			}
		}
	}
}
