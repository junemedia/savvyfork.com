<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

@error_reporting(E_ALL);

// Help get past php timeouts if we made it that far
// Joomla 1.5 installer can be very slow and this helps avoid timeouts
@set_time_limit(300);
$kn_maxTime = @ini_get('max_execution_time');

$maxMem = trim(@ini_get('memory_limit'));
if ($maxMem) {
	$unit = strtolower($maxMem{strlen($maxMem) - 1});
	switch($unit) {
		case 'g':
			$maxMem	*=	1024;
		case 'm':
			$maxMem	*=	1024;
		case 'k':
			$maxMem	*=	1024;
	}
	if ($maxMem < 16000000) {
		@ini_set('memory_limit', '16M');
	}
	if ($maxMem < 32000000) {
		@ini_set('memory_limit', '32M');
	}
	if ($maxMem < 48000000) {
		@ini_set('memory_limit', '48M');
	}
}
ignore_user_abort(true);

class com_contactenhancedInstallerScript
{
	/*
	 * The release value would ideally be extracted from <version> in the manifest file,
	 * but at preflight, the manifest file exists only in the uploaded temp folder.
	 */
	public $new_version		= null;
	public $oldTablePrefix	= null;

	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		$this->installer	= method_exists($parent, 'getParent') ? $parent->getParent() : $parent->parent;
		$this->manifest		= $this->installer->getManifest();

		// get version from xml file
        if (!$this->manifest) {
            $this->manifest = JApplicationHelper::parseXMLInstallFile($installer->getPath('manifest'));
            if (is_array($this->manifest)) {
                $this->new_version = (string) $this->manifest['version'];
            }
        }else{
			$this->new_version	= ((string) $this->manifest->version);
		}

		// the current version
		$this->current_version	= $this->new_version;

		$xml_file = $this->installer->getPath('extension_administrator') . '/contactenhanced.xml';
		// check for an xml file
		if (is_file($xml_file)) {
			if ($xml = JApplicationHelper::parseXMLInstallFile($xml_file)) {
				$this->current_version = $xml['version'];
			}
		}

		$session 		=JFactory::getSession();
		$session->set('com_contactenhanced.install', $this->current_version);


		// this component does not work with Joomla releases prior to 1.6
		// abort if the current Joomla release is older
		$jversion = new JVersion();
		if( version_compare( $jversion->getShortVersion(), '3.0', 'lt' ) ) {
			JError::raiseWarning(null, 'Cannot install com_contactenhanced 3.x in a Joomla release prior to 3.0');
			return false;
		}


		// if it is a migration from Joomla 1.5 perform migration changes.
		// this migration will only be performed if the the database is MySQL, because MSSQL  was not compatible with Joomla 1.5
		if(
			($this->tableExists('#__contact_enhanced_details') OR $this->tableExists('jos_contact_enhanced_details') )
			AND !$this->tableExists('#__ce_details')
		){
			if($this->tableExists('jos_contact_enhanced_details')){
				$this->oldTablePrefix	= 'jos_';
			}

			JError::raiseNotice('', 'Begin Contact Enhanced Migration');

			if(!$this->_fixBrokenMenu()){
				// @TODO ADD ERROR MESSAGE
			}else{
				JError::raiseNotice('', ' - Fix CE Menus Items');
			}

			if(!$this->_fixBrokenTableReferences()){
				// @TODO ADD ERROR MESSAGE
			}else{
				JError::raiseNotice('', ' - Fix Broken table references');
			}


			if(!$this->_changeTables()){
				// @TODO ADD ERROR MESSAGE
			}else{
				JError::raiseNotice('', ' - Upgrade Database tables');
			}

			if($this->_fixImagePath()){
				JError::raiseNotice('', ' - Image Path fixed');
			}

			JError::raiseNotice('', 'Contact Enhanced Migration FINISHED SUCCESSFULLY');
		}

	}

	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	 /**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{
		$this->loadLanguage();
		$db = JFactory::getDBO();


		// remove Component Menu from previous installations
		//$this->removeComponentMenu();
		$this->installer->set('message', JText::sprintf('COM_CONTACTENHANCED_INSTALL_SUCCESSFULLY', $this->new_version));

		// You can have the backend jump directly to the newly installed component configuration page
		//$parent->getParent()->setRedirectURL('index.php?option=com_contactenhanced');
	}

	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
		$this->loadLanguage();
		$this->updateTables($parent);
		$this->installer->set('message', JText::sprintf('COM_CONTACTENHANCED_INSTALL_UPDATE', $this->new_version));
	}

	private function updateTables( $parent ) {
		$db = JFactory::getDBO();

		if(!$this->tableFieldExists('#__ce_details','skype')){
			$query = 'ALTER TABLE #__ce_details ADD `skype` VARCHAR( 255 ) NOT NULL AFTER `mobile`;';
			if (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
				$query = 'ALTER TABLE #__ce_details ADD `skype` NVARCHAR( 250 ) NOT NULL AFTER `mobile`;';
			}
			$db->setQuery($query);
			$db->query();
		}

		if(!$this->tableFieldExists('#__ce_details','twitter')){
			$query = 'ALTER TABLE #__ce_details
						ADD `twitter` 	VARCHAR( 255 ) NOT NULL AFTER `skype` ,
						ADD `facebook` 	VARCHAR( 255 ) NOT NULL AFTER `twitter` ,
						ADD `linkedin` 	VARCHAR( 255 ) NOT NULL AFTER `facebook`;';
			if (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
				$query = 'ALTER TABLE #__ce_details
						ADD `twitter` 	NVARCHAR( 250 ) NOT NULL AFTER `skype` ,
						ADD `facebook` 	NVARCHAR( 250 ) NOT NULL AFTER `twitter` ,
						ADD `linkedin` 	NVARCHAR( 250 ) NOT NULL AFTER `facebook`;';
			}
			$db->setQuery($query);
			$db->query();
		}

		if(!$this->tableFieldExists('#__ce_messages','catid')){
			$query = 'ALTER TABLE `#__ce_cf` CHANGE `name` `name` VARCHAR( 255 );';
			if (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
				$query = 'ALTER TABLE `#__ce_cf` CHANGE `name` `name` NVARCHAR( 250 );';
			}
			$db->setQuery($query);
			$db->query();

			$query = 'ALTER TABLE `#__ce_messages` CHANGE `category_id` `catid` INT(11) NOT NULL;';
			if (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
				$query = 'ALTER TABLE `#__ce_messages` CHANGE `category_id` `catid` INT NOT NULL;';
			}
			$db->setQuery($query);
			$db->query();

			$query = 'ALTER TABLE `#__ce_messages`
						  ADD `access` INT( 11 ) UNSIGNED NOT NULL
						, ADD `language` CHAR( 7 ) NOT NULL;';
			if (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
				$query = 'ALTER TABLE `#__ce_messages`
						  ADD `access` BIGINT  NOT NULL
						, ADD `language` NVARCHAR( 7 ) NOT NULL;';
			}
			$db->setQuery($query);
			$db->query();

		}
		if(!$this->tableFieldExists('#__ce_messages','published')){
			$query = 'ALTER TABLE `#__ce_messages` ADD `published` TINYINT(3) NOT NULL;';
			if (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
				$query = 'ALTER TABLE `#__ce_messages` ADD `published` SMALLINT NOT NULL;';
			}
			$db->setQuery($query);
			$db->query();
		}

		/**
		 * Update to CE 3.0.2 and 2.5.10
		 */
		if(!$this->tableFieldExists('#__ce_cf','alias')){

			if (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
				$query = 'ALTER TABLE #__ce_cf ADD alias NVARCHAR( 250 ) NOT NULL;';
			}else{
				$query = 'ALTER TABLE `#__ce_cf` ADD `alias` VARCHAR( 255 ) NOT NULL AFTER `label`;';
			}
			$db->setQuery($query);
			$db->query();

			// Update all custom field's alias field
			$query = $db->getQuery(true);
			$query->update('#__ce_cf cf');
			$query->set('alias = '.$query->concatenate(array($db->quote('cf_'), 'cf.id')));
			$db->setQuery($query);
			$db->query();

			$query = $db->getQuery(true);
			$query->update('#__ce_cf cf');
			$query->set('alias = cf.type');
			$query->where(		'type	= '.$db->quote('name')
							. ' OR type	= '.$db->quote('email')
							. ' OR type	= '.$db->quote('email_verify')
							. ' OR type	= '.$db->quote('subject')
							. ' OR type	= '.$db->quote('password')
							. ' OR type	= '.$db->quote('password_verify')
							. ' OR type	= '.$db->quote('username')
							. ' OR type	= '.$db->quote('surname')
					);
			$db->setQuery($query);
			$db->query();
		}

		/**
		 * Update to CE 3.0.3 and 2.5.11
		 */
		if(!$this->tableFieldExists('#__ce_details','birthdate')){
			if (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
				$query = "ALTER TABLE #__ce_details ADD birthdate datetime NOT NULL DEFAULT '1900-01-01 00:00:00' ;";
			}else{
				$query = "ALTER TABLE `#__ce_details` ADD `birthdate` datetime NULL DEFAULT '0000-00-00 00:00:00' AFTER `webpage`;";
			}
			$db->setQuery($query);
			$db->query();
		}

		/**
		 * Update to CE 3.0.4 and 2.5.11
		 */
		if(!$this->tableFieldExists('#__ce_cv','catid')){
			if (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
				$query = "ALTER TABLE `#__ce_cv` CHANGE `category` `catid` BIGINT NOT NULL;";
			}else{
				$query = "ALTER TABLE `#__ce_cv` CHANGE `category` `catid` INT( 11 ) NOT NULL;";
			}
			$db->setQuery($query);
			$db->query();
		}

		/**
		 * Update to CE 3.0.6 and 2.5.13
		 */
		if(!$this->tableFieldExists('#__ce_messages','message_html')){
			if (strtolower($db->name) == 'sqlsrv' || strtolower($db->name) == 'sqlazure') {
				$query = "ALTER TABLE #__ce_messages	ADD message_html NVARCHAR(max) NOT NULL DEFAULT '1900-01-01 00:00:00' ;";
			}else{
				$query = "ALTER TABLE `#__ce_messages`	ADD `message_html`  MEDIUMTEXT NOT NULL AFTER `message`;";
			}
			$db->setQuery($query);
			$db->query();
		}

		/**
		 * Update to CE 3.1: Add Tag compatibility
		 */
		if($this->tableExists('#__content_types')){
			$query = $db->getQuery(true);
			$query->select($db->quoteName('table'));
			$query->from($db->quoteName("#__content_types"));
			$query->where($db->quoteName("table")." = " .$db->quote('#__ce_details'));
			$db->setQuery($query);
			if (!$db->loadResult()) {
				// Table exists but there is no Contact Enhanced is not configured for tagging
				$query = $db->getQuery(true);
				$query->insert($db->quoteName("#__content_types"));
				$query->values('0,'.$db->quote('Contact Enhanced').', '.$db->quote('com_contactenhanced.contact').', '.$db->quote('#__ce_details').', '.$db->quote('').', '.$db->quote('{"common":[{"core_content_item_id":"id","core_title":"name","core_state":"published","core_alias":"alias","core_created_time":"created","core_modified_time":"modified","core_body":"address", "core_hits":"hits","core_publish_up":"publish_up","core_publish_down":"publish_down","core_access":"access", "core_params":"params", "core_featured":"featured", "core_metadata":"metadata", "core_language":"language", "core_images":"image", "core_urls":"webpage", "core_version":"version", "core_ordering":"ordering", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"catid", "core_xreference":"xreference", "asset_id":"null"}], "special": [{"con_position":"con_position","suburb":"suburb","state":"state","country":"country","postcode":"postcode","telephone":"telephone","fax":"fax","misc":"misc","email_to":"email_to","default_con":"default_con","user_id":"user_id","mobile":"mobile","sortname1":"sortname1","sortname2":"sortname2","sortname3":"sortname3"}]}').', '.$db->quote('ContactenhancedHelperRoute::getContactRoute'));
				$db->setQuery($query);
				$db->execute();

				$query = $db->getQuery(true);
				$query->insert($db->quoteName("#__content_types"));
				$query->values('0,'.$db->quote('Contact Enhanced Category').', '.$db->quote('com_contactenhanced.category').', '.$db->quote('#__categories').', '.$db->quote('').', '.$db->quote('{"common":[{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}], "special": [{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}]}').', '.$db->quote('ContactenhancedHelperRoute::getCategoryRoute'));
				$db->setQuery($query);
				$db->execute();
			}
		}

	}

	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
		$db =JFactory::getDBO();
		$query = $db->getQuery(true);


		// Make sure tables are uptodate
		$this->updateTables($parent);

		if($type == 'install'){

			// Insert default values in MySQL
			if((strtolower($db->name) == 'mysql' || strtolower($db->name) == 'mysqli')){
				if (!$this->checkTableContents('#__ce_cf')) {
					$sql	= JFile::read(JPATH_BASE.'/components/com_contactenhanced/install/sql/install.mysql.ce_cf_values.sql');
					$db->setQuery($sql);
					$db->query();
				}
				if (!$this->checkTableContents('#__ce_template')) {
					$sql	= JFile::read(JPATH_BASE.'/components/com_contactenhanced/install/sql/install.mysql.ce_template_values.sql');
					$db->setQuery($sql);
					$db->query();
				}
			}


			$tableExtensions = $db->quoteName("#__extensions");
			$columnElement   = $db->quoteName("element");
			$columnType	 	 = $db->quoteName("type");
			$columnEnabled   = $db->quoteName("enabled");

			$query = $db->getQuery(true);
			$query->update($tableExtensions);
			$query->set($columnEnabled.' = '.$db->quote(1));
			$query->where("$columnElement='contactenhanced'
							OR $columnElement='icaptcha'
							OR $columnElement='mailto2ce'
							OR $columnElement='cefeedback'
							OR $columnElement='isekeywords'");
			$db->setQuery($query);
			$db->query();

			$this->installer->set('message', JText::sprintf('COM_CONTACTENHANCED_INSTALL_ALL_PLUGINS_ENABLED'));

			/* Update module only on install */
			$this->updateModules('mod_ce_alphaindex',	'ce-before-title',1, false);
		}

		$this->updateModules('mod_admin_ce_latest', 	'ce-cpanel');
		$this->updateModules('mod_admin_ce_statistics', 'ce-icon');

	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
		//$this->populate_db('uninstall.sql');
		//echo '<p>' . JText::sprintf('COM_CONTACTENHANCED_UNINSTALL', $this->release) . '</p>';
	}

	public function updateModules($module, $position=null, $published=1, $admin = true){
		$db =JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->update($db->quoteName("#__modules"));
		if($position){ $query->set($db->quoteName("position").' = '.$db->quote($position));}
		$query->set($db->quoteName("published").' = '.$published);
		$query->where($db->quoteName("module")." =" .$db->quote($module));
		$db->setQuery($query);
		$db->query();

		if($admin){
			$query = $db->getQuery(true);
			$query->select('id');
			$query->from($db->quoteName("#__modules"));
			$query->where($db->quoteName("module")." = " .$db->quote($module));
			$db->setQuery($query);
			$moduleId	= $db->loadResult();

			// If module was found, now we look for the corresponding row in the #__modules_menu table
			if($moduleId){
				$query = $db->getQuery(true);
				$query->select('count(moduleid)');
				$query->from($db->quoteName("#__modules_menu"));
				$query->where($db->quoteName("moduleid")." = " .$db->quote($moduleId));
				$db->setQuery($query);

				if(!$db->loadResult()){
					$query = $db->getQuery(true);
					$query->insert($db->quoteName("#__modules_menu"));
					$query->values($moduleId.', 0');
					$db->setQuery($query);
					$db->execute();
				}
			}
		}
	}
	/**
	 * Check is a table exists
	 * @param string	Table name
	 * @return bool
	 */
	private function tableExists($tblval){
		$database 	= JFactory::getDBO();
		$tables		= $database->getTableList();
		$tblval 	= str_replace('#__', $database->getPrefix(),$tblval);
		return in_array($tblval,$tables);
	}

	/**
	 * Check table contents
	 * @return integer
	 * @param string $table Table name
	 */
	private function checkTableContents($table) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		if ($this->tableExists($table)) {
			if (is_object($query)) {
				$query->select('COUNT(id)')->from($table);
			} else {
				$query = 'SELECT COUNT(id) FROM ' . $table;
			}
			$db->setQuery($query);
			return $db->loadResult();
		}
		return false;
	}

	/**
	 * Check is a table field exists
	 * @param string	Table name
	 * @param string	Table field
	 * @return bool
	 */

	private function tableFieldExists($table, $column) {
		$db = JFactory::getDBO();
		if ($this->tableExists($table)) {
			// use built in function
			if (method_exists($db, 'getTableColumns')) {
				$fields = $db->getTableColumns($table);
			} else {
				$db->setQuery('DESCRIBE ' . $table);
				$fields = $db->loadColumn();

				// we need to check keys not values
				$fields = array_flip($fields);
			}

			return array_key_exists($column, $fields);
		}
		return false;
	}

	protected function _changeTables()
	{
		// Initialize Application
		JFactory::getApplication('administrator');
		$db	= JFactory::getDbo();


			$query	= "RENAME TABLE
							#__contact_enhanced_cf			TO #__ce_cf
							,#__contact_enhanced_cv			TO #__ce_cv
							,#__contact_enhanced_details	TO #__ce_details
							,#__contact_enhanced_messages	TO #__ce_messages
							,#__contact_enhanced_message_fields TO #__ce_message_fields
					";

		$db->setQuery ( $query );
    	$db->query ();

    	$query	= "ALTER TABLE `#__ce_cv`
    					ADD `access` TINYINT( 3 ) UNSIGNED NOT NULL AFTER `published`
						, ADD `name` VARCHAR( 255 ) NOT NULL AFTER `text`
						, ADD `language` CHAR( 7 ) NOT NULL DEFAULT '*'
						;";
		$db->setQuery ( $query );
    	$db->query ();


    	$query	= "ALTER TABLE `#__ce_cf`
    					ADD `access`		TINYINT(3)	UNSIGNED NOT NULL AFTER `params`
						, ADD `language`	CHAR(7)		NOT NULL DEFAULT '*'
						, ADD `metakey`		TEXT		NOT NULL
						, ADD `metadesc`	TEXT		NOT NULL
						;";
		$db->setQuery ( $query );
    	$db->query ();




    	$query	= "ALTER TABLE `#__ce_details`
    					ADD `sortname1`			VARCHAR(255)	NOT NULL AFTER `webpage`
    					, ADD `sortname2`		VARCHAR(255)	NOT NULL AFTER `sortname1`
    					, ADD `sortname3`		VARCHAR(255)	NOT NULL AFTER `sortname2`
    					, ADD `language`		CHAR(7)			NOT NULL DEFAULT '*'
    					, ADD `created`			DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `sortname3`
    					, ADD `created_by`		INT(11)			UNSIGNED NOT NULL AFTER `created`
    					, ADD `created_by_alias` VARCHAR(255)	NOT NULL AFTER `created_by`
    					, ADD `modified`		DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `created_by_alias`
    					, ADD `modified_by`		INT(11)			UNSIGNED NOT NULL AFTER `modified`
    					, ADD `metakey`			TEXT			NOT NULL AFTER `modified_by`
    					, ADD `metadesc`		TEXT			NOT NULL AFTER `metakey`
    					, ADD `metadata`		TEXT			NOT NULL AFTER `metadesc`
    					, ADD `featured`		TINYINT(3)		UNSIGNED NOT NULL AFTER `metadata`
    					, ADD `xreference`		DATETIME		NOT NULL AFTER `featured`
    					, ADD `publish_up`		DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER  `xreference`
    					, ADD `publish_down`	DATETIME		NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER  `publish_up`
    					;";
		$db->setQuery ( $query );
    	$db->query ();



    	$query	= "	ALTER TABLE `#__ce_details`
    						CHANGE `published`	`published`		TINYINT(1)		NOT NULL DEFAULT 0
    					,	CHANGE `checked_out` `checked_out`	INT(11)			UNSIGNED NOT NULL DEFAULT 0;
    				";
		$db->setQuery ( $query );
    	$db->query ();


		$query	= "	ALTER TABLE `#__ce_messages` CHANGE `category_id` `catid` INT(11) NOT NULL
						, ADD `access` INT( 11 ) UNSIGNED NOT NULL
						, ADD `language` CHAR( 7 ) NOT NULL ";
		$db->setQuery ( $query );
    	$db->query ();

		return true;
	}

	protected function _fixBrokenTableReferences()
	{
		// Initialize Application
		JFactory::getApplication('administrator');
		$db	= JFactory::getDbo();

		$old_name	= 'com_contact_enhanced';
		$new_name	= 'com_contactenhanced';

		$query	= "UPDATE #__extensions
						SET	element = {$db->quote($new_name)}
						WHERE element = {$db->quote($old_name)}
					";
		$db->setQuery ( $query );
    	$db->query ();


    	$query	= "UPDATE #__extensions
						SET	element = {$db->quote('contactenhanced')}
						WHERE element = {$db->quote('contact_enhanced')}
					";
		$db->setQuery ( $query );
    	$db->query ();



    	$query	= "UPDATE #__categories
						SET	extension = {$db->quote('com_contactenhanced')}
						WHERE extension = {$db->quote('com_contact_enhanced')}
					";
		$db->setQuery ( $query );


		return true;
	}


	protected function _fixBrokenMenu()
	{
		// Initialize Application
		JFactory::getApplication('administrator');
		$db	= JFactory::getDbo();

		$old_name	= 'com_contact_enhanced';
		$new_name	= 'com_contactenhanced';

	    // Get component object
	    $component = JTable::getInstance ( 'extension', 'JTable', array('dbo'=>$db) );
	    $component->load(array('type'=>'component', 'element'=>$old_name));

	    if($component->extension_id){
		     // First fix all broken menu items
		    $query = "UPDATE #__menu
		    	SET component_id={$db->quote($component->extension_id)}
		    	WHERE type = 'component'
		    		AND link LIKE '%option={$old_name}%'";
		    $db->setQuery ( $query );
		    $db->query ();


		    // Get all menu items from the component (JMenu style)
			$query = $db->getQuery(true);
		    $query->select('*');
		    $query->from('#__menu');
		    $query->where("component_id = {$component->extension_id}");
		    $query->where('client_id = 0');
		    $query->order('lft');
		    $db->setQuery($query);
		    $menuitems = $db->loadObjectList('id');
		    foreach ($menuitems as &$menuitem) {
				$menuitem->link = str_replace($old_name, $new_name, $menuitem->link);

				// Save menu object
		        $menu = JTable::getInstance ( 'menu', 'JTable', array('dbo'=>$db) );
		        $menu->bind(get_object_vars($menuitem), array('tree', 'query'));
		        $success = $menu->check();
		        if ($success) {
		          $success = $menu->store();
		        }
		        if (!$success) echo "ERROR to update menu items";
		    }
	    }



	    return true;
	  }

	protected function _fixImagePath()
	{
		// Initialize Application
		$db	= JFactory::getDbo();
	     // First fix all broken menu items
	    $query = "UPDATE  `#__ce_details`
	    SET image = CONCAT('images/stories/',image)
	    WHERE instr(image, 'images/') = 0 AND image  <> ''";
	    $db->setQuery ( $query );
	    $db->query ();
	    return true;
	  }


	function deleteFile($file) {
		if(JFile::exists($file)){
			JFile::delete($file);
		}
	}
	/**
	 * Sometimes when an install or uninstall does not work correctly this menu item is still there
	 * so we want to remove before a brand new install;
	 */
	private function removeComponentMenu() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query = $db->getQuery(true);
		$query->select('id')->from('#__menu')->where(array('link LIKE ' . $db->Quote('%com_contactenhanced%'), 'client_id = ' . $db->Quote(1)));
		$db->setQuery($query);
		$id = $db->loadResult();
		$query->clear();

		if ($id) {
			$table = JTable::getInstance('menu');
			// delete main item
			$table->delete((int) $id);
		}
	}


	private function cleanupInstall() {
		$path = JPATH_ADMINISTRATOR . '/components/com_contactenhanced';

		$db = JFactory::getDBO();

		// cleanup menus
		if (defined('JPATH_PLATFORM')) {
			$query = $db->getQuery(true);
			$query->select('id')->from('#__menu')->where(array('alias = ' . $db->Quote('contactenhanced'), 'menutype = ' . $db->Quote('main')));

			$db->setQuery($query);
			$id = $db->loadResult();

			$query->clear();

			if ($id) {
				$table = JTable::getInstance('menu');

				// delete main item
				$table->delete((int) $id);

				// delete children
				$query->select('id')->from('#__menu')->where('parent_id = ' . $db->Quote($id));

				$db->setQuery($query);
				$ids = $db->loadColumn();

				$query->clear();

				if (!empty($ids)) {
					// Iterate the items to delete each one.
					foreach ($ids as $menuid) {
						$table->delete((int) $menuid);
					}
				}

				// Rebuild the whole tree
				$table->rebuild();
			}
		}
	}
	/**
	 * load languages files
	 */
	private function loadLanguage() {
		$language = JFactory::getLanguage();
		//Load English always, useful if file is partially translated
		$language->load('com_contactenhanced',		JPATH_ADMINISTRATOR.'/components/com_contactenhanced', 'en-GB');
		$language->load('com_contactenhanced.sys',	JPATH_ADMINISTRATOR.'/components/com_contactenhanced', 'en-GB');
		$language->load('com_contactenhanced',		JPATH_ADMINISTRATOR.'/components/com_contactenhanced', null, true);
		$language->load('com_contactenhanced.sys',	JPATH_ADMINISTRATOR.'/components/com_contactenhanced', null, true);
		$language->load('com_contactenhanced',		JPATH_ADMINISTRATOR, null, true);
		$language->load('com_contactenhanced.sys',	JPATH_ADMINISTRATOR, null, true);
	}
}
