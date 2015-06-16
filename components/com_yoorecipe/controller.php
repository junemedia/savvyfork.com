<?php
/*------------------------------------------------------------------------
# com_yoorecipe - YooRecipe! Joomla 1.6 recipe component
# ------------------------------------------------------------------------
# author    YooRock!
# copyright Copyright (C) 2011 yoorock.fr. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://extensions.yoorock.fr
# Technical Support:  Forum - http://extensions.yoorock.fr/
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
jimport( 'joomla.filter.output' );

/**
 * YooRecipe Component Controller
 */
class YooRecipeController extends JControllerLegacy
{

	/**
	 * Proxy for getModel.
	 * @since	1.6
	 */
	public function getModel($name = 'YooRecipe', $prefix = 'YooRecipeModel', $config = array()) 
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}
	
	/**
	*	Task that loads the view of a recipe
	*/
	function viewRecipe()
	{
		$input 	= JFactory::getApplication()->input;
		$input->set( 'view', 'recipe' );
		$input->set( 'layout', 'recipe'  );
		
		parent::display();
	}
	
	/**
	*	Task that loads a list of recipes of a given category
	*/
	function viewCategory()
	{
		$input 	= JFactory::getApplication()->input;
		$input->set( 'view', 'categories' );
		$input->set( 'layout', 'categories' );
		
		parent::display();
	}
	
	/**
	*	Task that loads a list of recipes of a given tag
	*/
	function tags()
	{
		$input 	= JFactory::getApplication()->input;
		$input->set( 'view', 'tags' );
		$input->set( 'layout', 'tags'  );
		
		parent::display();
	}
	
	/**
	*	Task that loads a list of recipes of a given category
	*/
	function viewByUser()
	{
		$input 	= JFactory::getApplication()->input;
		$input->set( 'view', 'user' );
		$input->set( 'layout', 'user'  );
		
		parent::display();
	}
	
	/**
	*	Task that loads a list of recipes associated with a given tag
	*/
	function viewByTag()
	{
		$input 	= JFactory::getApplication()->input;
		$input->set( 'view', 'tags' );
		$input->set( 'layout', 'tags'  );
		
		parent::display();
	}
	
	/**
	 * Task that inserts a comment for a given recipe
	 */
	function addRecipeRating()
	{
		$yooRecipeparams	= JComponentHelper::getParams('com_yoorecipe');
		$showRecapth		= $yooRecipeparams->get('show_recaptch', 'std');

		$json = new stdclass;
		$json->error = false;
		
		$rating = null;
		if ($showRecapth == 'recaptcha') {
		
			require_once JPATH_COMPONENT.'/lib/recaptchalib.php';
			$recaptchaPrivateKey = $yooRecipeparams->get('recaptcha_private_key');
			$resp = recaptcha_check_answer ($recaptchaPrivateKey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

			if ($resp->is_valid) {
				$rating = $this->persistRecipeRating();
			} else {
				$json->error = true;
				echo json_encode($json);
				return;
			}
		}
		else {
			$rating = $this->persistRecipeRating();
		}
		
		// Calculate rights
		$user 	= JFactory::getUser();
		$input 	= JFactory::getApplication()->input;
		$recipeId 	= $input->get('recipeId');
		$authorId 	= $this->getModel()->getAuthorByRecipeId($recipeId);
		$canManageComments	= JHtmlYooRecipeUtils::canManageComments($user, $authorId);
		$canReportComments	= JHtmlYooRecipeUtils::canReportComments($user);

		// Return HTML to ajax caller
		$json->html = JHtml::_('yoorecipeutils.generateRatings', array($rating), $canManageComments, $canReportComments);
		
		echo json_encode($json);
	}
	
	/**
	* Write a recipe comment to database
	*/
	private function persistRecipeRating () {
	
		$yooRecipeparams					= JComponentHelper::getParams('com_yoorecipe');
		$send_email_on_comment				= $yooRecipeparams->get('send_email_on_comment', 0);
		$send_email_on_comment_to_author 	= $yooRecipeparams->get('send_email_on_comment_to_author', 0);
		$auto_publish_comment				= $yooRecipeparams->get('auto_publish_comment', 1);
		$show_author_name					= $yooRecipeparams->get('show_author_name', 'username');
		
		// Get form parameters
		$input 	= JFactory::getApplication()->input;
		$recipeId 	= $input->get('recipeId');
		$comment	= htmlspecialchars($input->get('comment', '', 'STRING'));
		$email		= htmlspecialchars($input->get('email', '', 'STRING'));
		$author		= htmlspecialchars($input->get('author', '', 'STRING'));
		$note	 	= htmlspecialchars($input->get('rating'));
		$userId		= htmlspecialchars($input->get('userId'));
		
		// Build comment object
		$commentObj = new stdClass;
		$commentObj->recipe_id 	= $recipeId;
		$commentObj->note 		= $note;
		$commentObj->author 	= $author;
		$commentObj->user_id 	= $userId;
		$commentObj->email 		= $email;
		$commentObj->comment 	= $comment;
		$commentObj->published 	= $auto_publish_comment;
		$commentObj->abuse	 	= 0;

		// Insert object
		$db = JFactory::getDBO();
		$db->insertObject('#__yoorecipe_rating', $commentObj, 'id');
		
		// Complete object with missing info
		$commentObj->id = $db->insertid();
		$commentObj->creation_date = JFactory::getDate()->toSQL();
		$user = JFactory::getUser($userId);
		switch ($show_author_name) {
			case 'username':
				$commentObj->author_name = $user->username;
				break;
			case 'name':
				$commentObj->author_name = $user->name;
				break;
		}
		
		// Get recipe
		$model = $this->getModel();
		$recipe = $model->getRecipeById($recipeId);
		
		// Notify creation to admin
		if ($send_email_on_comment){
			JHtmlYooRecipeUtils::sendMailToAdminOnSubmitComment($commentObj, $recipe);
		}
		
		// Notify creation to recipe author
		if ($send_email_on_comment_to_author){
			JHtmlYooRecipeUtils::sendMailToAuthorOnSubmitComment($commentObj, $recipe);
		}
		
		// Update recipe global note
		$model->updateRecipeGlobalNote($recipeId);
		
		return $commentObj;
	}
	
	/**
	 * Task that performs search on recipes
	 */
	function initSearch() {
	
		$input 	= JFactory::getApplication()->input;
		$input->set( 'view', 'search' );
		$input->set( 'layout', 'search'  );
		
		parent::display();
	}
	
	/**
	 * Task that performs search on recipes
	 */
	function searchRecipes() {
	
		$input 	= JFactory::getApplication()->input;
		$input->set( 'view', 'search' );
		$input->set( 'layout', 'search_results'  );
		
		parent::display();
	}
	
	/**
	 * Task that performs search on recipes
	 * Used to guarantee ascending compatibility with yoo_recipe modules
	 */
	function search() {
	
		$this->searchRecipes();
	}
	
	/**
	 * Task to edit recipes
	 */
	function editRecipe() {
	
		$input 	= JFactory::getApplication()->input;
		$input->set( 'view', 'form' );
		$input->set( 'layout', 'edit'  );
		
		parent::display();
	}
	
	/**
	 * Task to insert/update recipe
	 */
	function insertRecipe() {
		
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// Get User & Component parameters
		$params = JComponentHelper::getParams('com_yoorecipe');
		$user	= JFactory::getUser();
		
		$use_tags				= $params->get('use_tags', 1);
		$auto_publish 			= $params->get('auto_publish', 0);
		$auto_validate 			= $params->get('auto_validate', 0);
		$notify_on_create		= $params->get('notify_on_create', 0);
		$notify_admin_on_create	= $params->get('notify_admin_on_create', 1);
		$notify_admin_on_update	= $params->get('notify_admin_on_update', 1);
		$use_recipe_settings	= $params->get('use_recipe_settings', 1);
		$show_seasons 			= $use_recipe_settings ? $params->get('show_seasons', 1) : $params->get('show_seasons_fe', 1);

		// Build recipe object from request
		$recipe = JHtmlYooRecipeUtils::buildRecipeFromRequest($params);
	
		if ($recipe != false) {
			
			if ($recipe->title == '' || $recipe->title == null || count($recipe->ingredients) == 0) {
				JError::raiseError(403, JText::_('COM_YOORECIPE_ERROR_FORBIDDEN_OPERATION'));
			} else {
			
				// Get models
				$model 		= $this->getModel();
				$tagsModel	= JModelLegacy::getInstance('tags','YooRecipeModel');
				
				if (empty($recipe->id)) {

					// Create recipe
					$recipe->id = $model->insertRecipe($recipe);
				
					// Notify user the recipe has been created
					if ($notify_on_create) {
					
						$recipe->author_name = $user->name;
						$recipe->author_email = $user->email;
						JHtmlYooRecipeUtils::sendMailToUserOnCreate($recipe);
					}
					
					// Notify admin the recipe has been created
					if ($notify_admin_on_create) {

						$config		= JFactory::getConfig();
						$recipe->author_name	= $user->name;
						$recipe->author_email 	= $config->get('config.mail_from');
						JHtmlYooRecipeUtils::sendRecipeUpdateNotificationToAdmin($recipe);
					}
				}
				else {
				
					// Update recipe
					$model->updateRecipe($recipe);
					
					// Notify admin the recipe has been updated
					if ($notify_admin_on_update) {
					
						$config		= JFactory::getConfig();
						JHtmlYooRecipeUtils::sendRecipeUpdateNotificationToAdmin($recipe);
					}
				}
				
				// Save multiple categories
				$catids = $recipe->category_id;
				$db 	= JFactory::getDBO();
				$query	= $db->getQuery(true);

				// Remove categories affectation
				$query->delete('#__yoorecipe_categories');
				$query->where('recipe_id = ' . $db->quote($recipe->id));
				$db->setQuery($query);
				$db->execute();
					
				// Insert cross categories
				foreach($catids as $catid) :
					
					$query->clear();
					$query->insert('#__yoorecipe_categories');
					$query->set('recipe_id = '. $db->quote($recipe->id));
					$query->set('cat_id = '. $db->quote($catid));
					
					$db->setQuery($query);
					$db->execute();
				endforeach;
				
				if ($show_seasons) {
				
					// Save multiple seasons
					$seasonsids = $recipe->season_id;
					$db 	= JFactory::getDBO();
					$query	= $db->getQuery(true);

					// Remove categories affectation
					$query->delete('#__yoorecipe_seasons');
					$query->where('recipe_id = ' . $db->quote($recipe->id));
					$db->setQuery($query);
					$db->execute();
						
					// Insert cross categories
					foreach($seasonsids as $seasonid) :
						
						$query->clear();
						$query->insert('#__yoorecipe_seasons');
						$query->set('recipe_id = '. $db->quote($recipe->id));
						$query->set('month_id = '. $db->quote($seasonid));
						
						$db->setQuery($query);
						$db->execute();
					endforeach;
				}
				
				if ($use_tags) {
				
					// Remove tags
					$tagsModel->deleteTagsByRecipeId($recipe->id);
					
					// Insert tags
					foreach ($recipe->tags as $tag) {
						$tagsModel->insertTag($recipe->id, $tag->tag_value);
					}
				}
			}
			// Redirect user to recipe page or edit page
			if ($recipe->id != 0) {
				$recipeUrl = JRoute::_('index.php?option=com_yoorecipe&task=viewRecipe&id='. $recipe->id . ":" . $recipe->alias);
				$this->setRedirect($recipeUrl);
			}
			else {
				$this->setRedirect(JUri::current());
			}
		}
	}
	
	/**
	 * Task to upload a recipe picture
	 */
	function uploadRecipePicture() {
	
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		 
		// Get User & Component parameters
		$input 		= JFactory::getApplication()->input;
		$params 	= JComponentHelper::getParams('com_yoorecipe');
		$created_by	= $input->get('created_by', 0, 'INT');
		$user		= ($created_by != 0) ? JFactory::getUser($created_by) : JFactory::getUser();
		$fieldName 	= 'resume_file';
		
		//any errors the server registered on uploading
		$fileError = $_FILES[$fieldName]['error'];
		if ($fileError > 0) 
		{
			switch ($fileError)  {
				case 1: echo JText::_( 'COM_YOORECIPE_ERROR_WARNFILETOOLARGE' );
				return;
		 
				case 2: echo JText::_( 'COM_YOORECIPE_ERROR_WARNFILETOOLARGE' );
				return;
		 
				case 3: echo JText::_( 'COM_YOORECIPE_ERROR_PARTIAL_UPLOAD' );
				return;
		 
				case 4: echo JText::_( 'COM_YOORECIPE_ERROR_FILE_NOT_FOUND' );
				return;
			}
		}
		 
		//check for filesize
		$fileSize = $_FILES[$fieldName]['size'];
		$max_upload_size_bytes = $params->get('max_upload_size', 2000) * 1024;
		if($fileSize > $max_upload_size_bytes)	{
			echo JText::_( 'COM_YOORECIPE_ERROR_WARNFILETOOLARGE' );
		}
		 
		//check the file extension is ok
		$fileName = $_FILES[$fieldName]['name'];
		
		$uploadedFileNameParts = explode('.',$fileName);
		$uploadedFileExtension = array_pop($uploadedFileNameParts);
		$validFileExts = explode(',', $params->get('authorized_extensions', 'jpg,png,gif' ));
		 
		//assume the extension is false until we know its ok
		$extOk = false;
		 
		// Check file extension is ok
		foreach($validFileExts as $key => $value) {
			if( preg_match("/$value/i", $uploadedFileExtension ) ) {
				$extOk = true;
			}
		}
		 
		if ($extOk == false) 
		{
			echo JText::sprintf( 'COM_YOORECIPE_BAD_FILE_EXTENSION', $params->get('authorized_extensions', 'jpg,png,gif'));
			return;
		}
		 
		// the name of the file in PHP's temp directory that we are going to move to our folder
		$fileTemp = $_FILES[$fieldName]['tmp_name'];
		 
		// for security purposes, we will also do a getimagesize on the temp file to check the MIME type of the file, and whether it has a width and height
		$imageinfo = getimagesize($fileTemp);
		 
		// we are going to define what file extensions/MIMEs are ok
		$okMIMETypes = 'image/jpeg,image/pjpeg,image/jpg,image/tiff,image/bmp,image/png,image/x-png,image/gif';
		$validFileTypes = explode(",", $okMIMETypes);		
		 
		// if the temp file does not have a width or a height, or it has a non ok MIME, return
		if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) ||  !in_array($imageinfo['mime'], $validFileTypes) )
		{
			echo JText::sprintf( 'COM_YOORECIPE_BAD_FILE_EXTENSION', $params->get('authorized_extensions', 'jpg,png,gif'));
			return;
		}
		
		//lose any special characters in the filename
		$fileName = preg_replace("/[^A-Za-z0-9]/i", ".", $fileName);

		// prepare image directory
		$componentName 	= 'com_yoorecipe';
		$directory 		= $user->username.'-'.$user->id;

		if(!is_dir(JPATH_ROOT.'/images/'.$componentName)){
			mkdir(JPATH_ROOT.'/images/'.$componentName);
		}
		if(!is_dir(JPATH_ROOT.'/images/'.$componentName.'/'.$directory)){
			mkdir(JPATH_ROOT.'/images/'.$componentName.'/'.$directory);
		}
		$uploadPath = JPATH_ROOT.'/images/'.$componentName.'/'.$directory.'/'.$fileName;
		 
		if(!JFile::upload($fileTemp, $uploadPath)) 
		{
			echo JText::_( 'COM_YOORECIPE_ERROR_PARTIAL_UPLOAD' );
			return;
		}
		else
		{
		   echo JText::_('COM_YOORECIPE_UPLOAD_OK').'##'.JURI::base().'images/'.$componentName.'/'.$directory.'/'.$fileName;
		}
	}
	
	/**
	 * Task to upload a recipe picture
	 */
	function mootoolsUploadRecipePicture() {
	
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		 
		// Get the document object.
		/*$document = JFactory::getDocument();
		 
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		 
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="mootoolsUploadRecipePicture.json"');
		*/
		
		// Json object
		$json = new stdclass;
		$json->errors = array();
		
		// Get User & Component parameters
		$input 		= JFactory::getApplication()->input;
		$params 	= JComponentHelper::getParams('com_yoorecipe');
		$created_by	= $input->get('created_by', 0, 'INT');
		$user		= ($created_by != 0) ? JFactory::getUser($created_by) : JFactory::getUser();
		
		// Debug
		$json->files =$_FILES;
		$fieldName 	= 'file';
		
		// Any errors the server registered on uploading
		if (isset($_FILES[$fieldName]['error'])) {
		
			$fileError = $_FILES[$fieldName]['error'];
			if ($fileError > 0) 
			{
				switch ($fileError)  {
					case 1: $json->error[] = JText::_( 'COM_YOORECIPE_ERROR_WARNFILETOOLARGE' );
					break;
			 
					case 2: $json->error[] = JText::_( 'COM_YOORECIPE_ERROR_WARNFILETOOLARGE' );
					break;
					
					case 3: $json->error[] = JText::_( 'COM_YOORECIPE_ERROR_PARTIAL_UPLOAD' );
					break;
					
					case 4: $json->error[] = JText::_( 'COM_YOORECIPE_ERROR_FILE_NOT_FOUND' );
					break;
				}
			}
		}
		
		// Check the file extension is ok
		$fileName = $_FILES[$fieldName]['name'];
		
		$uploadedFileNameParts = explode('.',$fileName);
		$uploadedFileExtension = array_pop($uploadedFileNameParts);
		$validFileExts = explode(',', $params->get('authorized_extensions', 'jpg,png,gif' ));
		 
		// Assume the extension is false until we know its ok
		$extOk = false;
		 
		// Check file extension is ok
		foreach($validFileExts as $key => $value) {
			if( preg_match("/$value/i", $uploadedFileExtension ) ) {
				$extOk = true;
			}
		}
		 
		if ($extOk == false) 
		{
			$json->errors[] = addslashes(JText::sprintf( 'COM_YOORECIPE_BAD_FILE_EXTENSION', $params->get('authorized_extensions', 'jpg,png,gif')));
		}
		
		// Check for filesize
		$fileSize = $_FILES[$fieldName]['size'];
		$max_upload_size_bytes = $params->get('max_upload_size', 2000) * 1024;
		if($fileSize > $max_upload_size_bytes)	{
			$json->errors[] = addslashes(JText::_( 'COM_YOORECIPE_ERROR_WARNFILETOOLARGE' ));
		}
		
		if (count($json->errors) == 0) {

			// The name of the file in PHP's temp directory that we are going to move to our folder
			$fileTemp = $_FILES[$fieldName]['tmp_name'];
			 
			// For security purposes, we will also do a getimagesize on the temp file to check the MIME type of the file, and whether it has a width and height
			$imageinfo = getimagesize($fileTemp);
			 
			// we are going to define what file extensions/MIMEs are ok
			$okMIMETypes = 'image/jpeg,image/pjpeg,image/jpg,image/tiff,image/bmp,image/png,image/x-png,image/gif';
			$validFileTypes = explode(",", $okMIMETypes);		
			 
			// If the temp file does not have a width or a height, or it has a non ok MIME, return
			if( !is_int($imageinfo[0]) || !is_int($imageinfo[1]) ||  !in_array($imageinfo['mime'], $validFileTypes) )
			{
				$json->errors[] = addslashes(JText::sprintf( 'COM_YOORECIPE_BAD_FILE_EXTENSION', $params->get('authorized_extensions', 'jpg,png,gif')));
			}
			
			// Lose any special characters in the filename
			$fileName = preg_replace("/[^A-Za-z0-9]/i", ".", $fileName);

			// Prepare image directory
			$componentName 	= 'com_yoorecipe';
			$directory 		= $user->username.'-'.$user->id;

			if(!is_dir(JPATH_ROOT.'/images/'.$componentName)){
				mkdir(JPATH_ROOT.'/images/'.$componentName);
			}
			if(!is_dir(JPATH_ROOT.'/images/'.$componentName.'/'.$directory)){
				mkdir(JPATH_ROOT.'/images/'.$componentName.'/'.$directory);
			}
			$uploadPath = JPATH_ROOT.'/images/'.$componentName.'/'.$directory.'/'.$fileName;
			 
			if(!JFile::upload($fileTemp, $uploadPath)) 
			{
				$json->errors[] = addslashes(JText::_( 'COM_YOORECIPE_ERROR_PARTIAL_UPLOAD' ));
			}
			
			// Everything is right!
			$json->picture_path = JURI::base().'images/'.$componentName.'/'.$directory.'/'.$fileName;
		}
		
		// Set json to session
		$session = JFactory::getSession();
		$session->clear('json');
		$session->set('json', json_encode($json));
		
		// Output the JSON data.
		echo json_encode($json);
	}
	
	/**
	 * Task to delete a recipe
	 * Called using Ajax
	 */
	function deleteRecipe() {
	
		// Init variables
		$user 		= JFactory::getUser();
		
		// Get form parameters
		$input 	= JFactory::getApplication()->input;
		$recipeId 	= $input->get('id');
		
		// Check user tries to delete its own recipe
		$model 		= $this->getModel();
		$userId 	= $model->getAuthorByRecipeId($recipeId);
		
		// Check user is connected
		if ($user->guest) {
			echo 'NOK#' . addslashes(JText::_('COM_YOORECIPE_SESSION_TIMED_OUT'));
			return;
		}
		
		// Check recipe is set
		if (!isset($recipeId)) {
			echo 'NOK#' . addslashes(JText::_('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION'));
			return;
		}
		
		// Check user is authorized to perform delete operations 
		$authorised = $user->authorise('core.admin', 'com_yoorecipe') || ($user->authorise('core.edit.own', 'com_yoorecipe') && $user->id == $userId) || $user->authorise('core.edit', 'com_yoorecipe');
		if ($authorised !== true) {
			echo 'NOK#' . addslashes(JText::_('JERROR_ALERTNOAUTHOR'));
			return;
		}
		
		$model->deleteRecipeById($recipeId);
		echo 'OK';
	}
	
	/**
	 * Task to add a recipe
	 */
	function addRecipe() {	
		
		$app		= JFactory::getApplication();
		$user 		= JFactory::getUser();

		// Calculate authorisations
		$authorised 	= $user->authorise('core.create', 'com_yoorecipe') || $user->authorise('core.admin', 'com_yoorecipe');
		$redirectUrl 	= JRoute::_('index.php?option=com_yoorecipe&view=form&layout=edit');

		if ($authorised !== true) {
		
			if ($user->guest == 1) {
				$returnUrl		= base64_encode($redirectUrl); 
				$redirectUrl 	= JRoute::_('index.php?option=com_users&view=login&return='.$returnUrl);
				$app->redirect($redirectUrl);
				
				return;
			}
			
			JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		}
		
		$app->redirect($redirectUrl);
	}
	
	/**
	 * Task to delete a recipe
	 */
	function deleteComment() {
	
		// Get User & Component parameters
		$params 	= JComponentHelper::getParams('com_yoorecipe');
		$user 		= JFactory::getUser();
		
		// Check user is connected
		if (!$user->guest) {
		
			// Get form parameters
			$input 		= JFactory::getApplication()->input;
			$model 		= $this->getModel();
			$post 		= $input->get('post');
			$recipeId	= $input->get('recipeId');
			$commentId	= $input->get('commentId');

			// Check user is authorized to perform delete comment operations 
			$authorised = 	$user->authorise('core.admin', 'com_yoorecipe') || $user->authorise('core.edit', 'com_yoorecipe');
			if ($params->get('comments_manager', 'admin') == 'admin_and_owner') {
				$userId 	= $model->getAuthorByRecipeId($recipeId);
				$authorised |= ($user->authorise('core.edit.own', 'com_yoorecipe') && $userId == $user->id);
			}
			if ($authorised != 1) {
				echo 'NOK#' . addslashes(JText::_('JERROR_ALERTNOAUTHOR'));
				return false;
			}
			
			if (!isset($recipeId) || !isset($commentId) ) {
				echo 'NOK#' . addslashes(JText::_('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION'));
				return false;
			}
			
			// All previous tests are ok, perform delete
			$model->deleteCommentByIdRecipeAndIdComment($recipeId, $commentId);
			$model->updateRecipeGlobalNote($recipeId);
			$recipe = $model->getRecipeById($recipeId);
			echo JHtmlYooRecipeUtils::generateRecipeRatings($recipe, $params->get('use_google_recipe', 1), $params->get('rating_style', 'stars'));
		}
		else {
			echo 'NOK#' . addslahes(JText::_('COM_YOORECIPE_SESSION_TIMED_OUT'));
		}
	}
	
	/**
	 * Get recipes whom name starts with a given letter
	 */
	function getRecipesByLetter() {
		
		$input 	= JFactory::getApplication()->input;
		$input->set( 'view', 'landingpage' );
		$input->set( 'layout', 'letters'  );
		
		parent::display();
	}
	
	/**
	 * Function that retrieve more comments for a given recipe
	 */
	function getMoreComments() {
		
		// Get Params
		$yooRecipeparams	= JComponentHelper::getParams('com_yoorecipe');
		$nbCommentsToFetch 	= $yooRecipeparams->get('nb_comments_to_fetch', '0');
		
		// Get ratings
		$input 	= JFactory::getApplication()->input;
		$user 				= JFactory::getUser();
		$recipeId 			= $input->get('recipeId');
		$model 				= $this->getModel();
		$limit				= $model->getNbRatingsByRecipeId($recipeId);
		$authorId			= $model->getAuthorByRecipeId($recipeId);
		$ratings 			= $model->getRatingsByRecipeIdOrderedByDateDesc($recipeId, $nbCommentsToFetch, $limit);
		
		$canManageComments	= JHtmlYooRecipeUtils::canManageComments($user, $authorId);
		$canReportComments	= JHtmlYooRecipeUtils::canReportComments($user);
		
		echo JHtmlYooRecipeUtils::generateRatings($ratings, $canManageComments, $canReportComments);
	}
	
	/**
	 * Report comment as abusive
	 */
	function reportComment() {
		
		// Get User & Component parameters
		$params 	= JComponentHelper::getParams('com_yoorecipe');
		$user 		= JFactory::getUser();
				
		// Get form parameters
		$input 	= JFactory::getApplication()->input;
		$model 		= $this->getModel();
		$post 		= $input->get('post');
		$recipeId	= $input->get('recipeId');
		$commentId	= $input->get('commentId');

		if (!isset($recipeId) || !isset($commentId) ) {
			JError::raiseError(403, JText::_('COM_YOORECIPE_ERROR_FORBIDDEN_DELETE_OPERATION'));
		}
		
		// All previous tests are ok, perform delete
		$model->reportCommentAsOffensive($recipeId, $commentId);
		echo 'OK';
	}
	
	/**
	 * Add recipe to favourites 
	 */
	function addToFavourites()
	{
		// Get User & Component parameters
		$params 	= JComponentHelper::getParams('com_yoorecipe');
		$user 		= JFactory::getUser();
		$return = $_SERVER['HTTP_REFERER'];
				
		if ($user->get('guest') == 1)
		{
			// Redirect to login page.
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			return;
		}
		// Get parameters
		$input 	= JFactory::getApplication()->input;
		$post 		= $input->get('post');
		$recipeId	= $input->get('recipeId');
		//print_r($recipeId);
		// Perform model operation
		$model 		= $this->getModel();
		$model->addToFavourites($recipeId, $user);
		
		// Refresh screen
		$recipe = new StdClass;
		$recipe->id = $recipeId;
		$recipe->favourite = 1;
		$this->setRedirect($return, false);
		//echo JHtmlYooRecipeIcon::favourites($recipe, $params);
	}
	
	/**
	 * Remove recipe from favourites
	 */
	function removeFromFavourites()
	{
		// Get User & Component parameters
		$params 	= JComponentHelper::getParams('com_yoorecipe');
		$user 		= JFactory::getUser();
		$return = $_SERVER['HTTP_REFERER'];
		
		if ($user->get('guest') == 1)
		{
			// Redirect to login page.
			$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', false));
			return;
		}
		// Get parameters
		$input 	= JFactory::getApplication()->input;
		$post 		= $input->get('post');
		$recipeId	= $input->get('recipeId');
		
		// Perform model operation
		$model 		= $this->getModel();
		$model->deleteFromFavourites($recipeId, $user);
		
		// Refresh screen
		$recipe = new StdClass;
		$recipe->id = $recipeId;
		$recipe->favourite = 0;
		$this->setRedirect($return, false);
		//echo JHtmlYooRecipeIcon::favourites($recipe, $params);
	}
    
    
    function import()
    {
        // Display the import
        $input     = JFactory::getApplication()->input;
        $input->set( 'view', 'import' );
        $input->set( 'layout', 'import'  );
        
        parent::display();
    }
    
    /**
    * We will change the precedure.
    * #1 we preview the recipe first - Here we recall the url.class.php to fetch the recipe content with image
    * #2 Save it in the database - Here we save all the information by the POST vars from the previous page
    * 
    */
    
    function import_preview(){
        //echo "<pre>" . print_r($_POST, true) . "</pre>";

        $app = JFactory::getApplication();
        
        
        
        $yooRecipeparams        = JComponentHelper::getParams('com_yoorecipe'); 
        $user                             = JFactory::getUser();
        $userGroups                  = $user->getAuthorisedGroups();

        $_isAuthorized = false;

        if((array_search('11',$userGroups) !== false) || (array_search('14',$userGroups) !== false)){
            // We found he is the partner
            $_isAuthorized = true;
        }else{
            $message =  "Sorry, only partner can add recipes! If you are a partner. Please <a href='./index.php/login'>click here</a> to login!";
            $app->enqueueMessage($message);    
        }

        $recipes = array();

        if($_isAuthorized){
            // Import the custom lib first
            require_once JPATH_COMPONENT.'/lib/simple_html_dom.php';
            require_once JPATH_COMPONENT.'/lib/url.class.php';

            /**
            * Initialize the urls post vars from the previous page - VIA ulr auto posts
            * This is the "URL" type
            */
            $recipeUrlsInput = array();
            $recipeUrls = isset($_POST["recipeUrls"])?$_POST["recipeUrls"]:array();
            if(is_array($recipeUrls) && count($recipeUrls) > 0 && $recipeUrls[0] != ""){
                
            
                foreach($recipeUrls as $url){
                    $recipe = array();
                    // Let's cleanup the urls first
                    $url = trim($url);   //$url = "http://www.recipe4living.com/recipes/crazy_crab_and_avocado_salad.htm";
                    $googleObj = new UrlGoogleFormat($url);
                    if($googleObj){     
                        if($googleObj->getRecipeType() !== false){
                            // We create the google format ok!
                                $googleObj = new UrlGoogleFormat($url);
                                $recipe["url"] = $url; 
                                $recipe["title"] = $googleObj->getTitle();      // String title
                                $recipe["ingredients"] = $googleObj->getIngredientsArray(); // array ingredients
                                $imageArray = $googleObj->getImageArrayOriginalUrl(); // array image URL
                                
                                /**
                                * OMG, we have to fetch the image and save it locally first!
                                * Anyway, just to change the procedure.
                                * Let's save the image first!         
                                */
                                $recipe["images"] = $this->_saveImage($imageArray,$googleObj->getTitle());        
                        }
                    }
                    $recipeUrlsInput[] = $recipe;
                }
            }

            
            /**
            * Now let's move to the recipe manully input
            */
            $recipeFreeForm = array();
            if(isset($_POST["recipeLink"]) && is_array($_POST["recipeLink"]) && count($_POST["recipeLink"]) > 0 && $_POST["recipeLink"][0] != ""){
                // We will start to save it now
                /**
                * Start to prepare the data structure
                */
                $recipeFreeForm = array();
                for($i=0;$i<count($_POST["recipeLink"]); $i++){
                    $recipeFreeForm[$i]['url'] = $_POST['recipeLink'][$i];
                    $recipeFreeForm[$i]['title'] = $_POST['recipeTitle'][$i];
                    $imageArray = array($_POST['recipeImage'][$i]);

                    $recipeFreeForm[$i]['images'] = $this->_saveImage($imageArray,$_POST['recipeTitle'][$i]);
                    
                    
                    $ing = $_POST['recipeIngredients'][$i];
                    
                    $recipeFreeForm[$i]['ingredients'] = explode("\n", $ing);
                }
            } 
           
            if(count($recipeUrlsInput) > 0){
                foreach($recipeUrlsInput as $i){
                    $recipes[] = $i;
                }
            }
            if(count($recipeFreeForm) > 0){
                foreach($recipeFreeForm as $i){
                    $recipes[] = $i;
                }
            }           
           // We are trying to make up a recipes array we desired
           
        
            // Display the Preview and load the templates
            $input     = JFactory::getApplication()->input;
            $input->set( 'view', 'import' );
            $input->set( 'layout', 'import_preview'  );
            
            $session = JFactory::getSession();
            $session->set( 'previewRecipes', $recipes );
            
            parent::display();            

        }
    }
    
    function import_save_postvars(){
        $app = JFactory::getApplication();
        $session = JFactory::getSession();
        $previewPrepareRecipes = $session->get( 'previewRecipes', array() );

        $yooRecipeparams        = JComponentHelper::getParams('com_yoorecipe'); 
        $user                             = JFactory::getUser();
        $userGroups                  = $user->getAuthorisedGroups();

        $_isAuthorized = false;

        if((array_search('11',$userGroups) !== false) || (array_search('14',$userGroups) !== false)){
            // We found he is the partner
            $_isAuthorized = true;
        }else{
            $message =  "Sorry, only partner can add recipes! If you are a partner. Please <a href='./index.php/login'>click here</a> to login!";
            $app->enqueueMessage($message);    
        }

        $recipes = array();

        if($_isAuthorized){
            // Import the custom lib first
            require_once JPATH_COMPONENT.'/lib/simple_html_dom.php';
            require_once JPATH_COMPONENT.'/lib/url.class.php';

        
            foreach($previewPrepareRecipes as $recipe){
                $dbSaver = new previewSubmission();
                $dbSaver->getDataSource($recipe);
                $yoorecipe_model = new Model_Yoorecipe($dbSaver);
                $yoorecipe_model->save();
                
                $app->enqueueMessage( "<font color='green'>Recipe successfully submitted for Editor Review : " . $dbSaver->getTitle() . "</font>");
            }
        }
        //echo "<pre>" . print_r($previewPrepareRecipes, true) . "</pre>";
        $input     = JFactory::getApplication()->input;
        $input->set( 'view', 'import' );
        $input->set( 'layout', 'import_ok'  );
        
        parent::display();
    }

    private function _saveImage($imageArray, $title){
            /**
            * OMG, we have to fetch the image and save it locally first!
            * Anyway, just to change the procedure.
            * Let's save the image first!         
            */
             
            $title = str_replace("\"", "'", $title);
            //$title = addslashes($title);

            
            $alias = str_replace(" ",'-',$title); 
            $alias = str_replace("'", '_', $alias);
            $alias = str_replace("\"", '_', $alias);
            $num=0;
            
            foreach($imageArray as $image){
                $imageUrl = str_replace(' ', '%20', $image); 
                //echo $imageUrl;
                //$imageFile = md5(time() . $imageUrl) . '.jpg'; 
                 if($num==0)
                {
                    $imageFile = $alias . '.jpg';
                }else{
                    $imageFile = $alias.'_'.$num . '.jpg';
                }
                
                $img = CUSTOM_IMAGE_PATH . $imageFile ;
                if(file_exists($img)){
                    $img = substr($img,0,-4);
                    $img = $img . '-' . md5(time()) . ".jpg";
                }
                
                file_put_contents($img, file_get_contents($imageUrl));
                
                $imageReturnArray[] = basename($img);
                $num++;
            }
            
            return $imageReturnArray;        
    }

    
    function import_ok(){
        //echo "<pre>" . print_r($_POST, true) . "</pre>";exit;
        
        $app = JFactory::getApplication();
        
        
        
        $yooRecipeparams        = JComponentHelper::getParams('com_yoorecipe'); 
        $user                             = JFactory::getUser();
        $userGroups                  = $user->getAuthorisedGroups();

        $_isAuthorized = false;

        if((array_search('11',$userGroups) !== false) || (array_search('14',$userGroups) !== false)){
            // We found he is the partner
            $_isAuthorized = true;
        }else{
            $message =  "Sorry, only partner can add recipes! If you are a partner. Please <a href='./index.php/login'>click here</a> to login!";
            $app->enqueueMessage($message);    
        }        
        
        
        if($_isAuthorized){
            // Import the custom lib first
            require_once JPATH_COMPONENT.'/lib/simple_html_dom.php';
            require_once JPATH_COMPONENT.'/lib/url.class.php'; 
            
                 /**
                 * @desc Add the recipes from link URL arrays            
                 * @author Leon Zhao
                 */

                $messageClickHere = "<a href='./index.php?option=com_yoorecipe&task=import&hideAddRecipeUrls=1'>Click Here to add it manually</a>";
                   
                if(isset($_POST["recipeUrls"])){
                    $recipeUrls = $_POST['recipeUrls'];
                        if(is_array($recipeUrls) && count($recipeUrls) > 0 && $recipeUrls[0] != ""){
                            // We are good to go!                        
                            $countRows = 0;
                            foreach($recipeUrls as $url){
                                if($countRows < 5){
                                    //We are good to move forward
                                    $url = trim($url);
                                    if($url && $this->is_url($url)){
                                        $googleObj = new UrlGoogleFormat($url);
                                        if($googleObj){     
                                            if($googleObj->getRecipeType() !== false){
                                                // We create the google format ok!
                                                $yoorecipe_model = new Model_Yoorecipe($googleObj);
                                                $yoorecipe_model->save(); 
                                                //$app->enqueueMessage( "<font color='green'>Success : " . $url . "</font> Google Recipe Type: " . $googleObj->getRecipeType());
                                                $app->enqueueMessage( "<font color='green'>Recipe successfully submitted for Editor Review : " . $url . "</font>");
                                                
                                                  
                                            }else{
                                                // No matching google formats
                                                $app->enqueueMessage( "<font color='red'>Failed : " . $url . "</font> Google Recipe Type: None. " . $messageClickHere); 
                                            }
                                        }else{
                                            $app->enqueueMessage( "<font color='red'>Failed : " . $url . "</font>. The URL is invalid! " . $messageClickHere);
                                        }           
                                    }else{
                                        $app->enqueueMessage( "<font color='red'>Failed : " . $url . "</font>. The URL is invalid! " . $messageClickHere);
                                    }
                                }else{
                                    // We will discard the rows more than 5
                                }
                                $countRows ++;
                            }
                        }else{
                            /**
                            * Hacking or invalid
                            * But they may add more recipes via manually
                            * So we just comment the information render
                            */
                            //echo "Please make sure you submit at least 1 recipe url !";
                        }
                }
            
                /**
                * @desc Now we are going to try to import manually
                * @author Leon Zhao            
                */
                if(isset($_POST["recipeLink"]) && is_array($_POST["recipeLink"]) && count($_POST["recipeLink"]) > 0 && $_POST["recipeLink"][0] != ""){
                    // We will start to save it now
                    /**
                    * Start to prepare the data structure
                    */
                    for($i=0;$i<count($_POST["recipeLink"]); $i++){
                        $recipes[$i]['recipeLink'] = $_POST['recipeLink'][$i];
                        $recipes[$i]['recipeTitle'] = $_POST['recipeTitle'][$i];
                        $recipes[$i]['recipeImage'] = array($_POST['recipeImage'][$i]);
                        $ing = $_POST['recipeIngredients'][$i];
                        $ing = mb_convert_encoding($ing, 'ISO-8859-1', 'UTF-8');
                        $recipes[$i]['recipeIngredients'] = explode("\n", $ing);
                    }
                    
                    foreach($recipes as $recipe){
                        $dbSaver = new FreeFormSubmission();
                        $dbSaver->getDataSource($recipe);
                        $yoorecipe_model = new Model_Yoorecipe($dbSaver);
                        $yoorecipe_model->save();
                        
                        $app->enqueueMessage( "<font color='green'>Recipe successfully submitted for Editor Review : " . $dbSaver->getTitle() . "</font>");
                    }
                    
                }else{
                    // No recipes manually actually submit. Just discard any changes.
                }
            
            $input     = JFactory::getApplication()->input;
            $input->set( 'view', 'import' );
            $input->set( 'layout', 'import_ok'  );
            
            parent::display();             
        }    
    }
    
    function pre_view_url(){
        $app = JFactory::getApplication();
        $user                             = JFactory::getUser();
        $userGroups                  = $user->getAuthorisedGroups();

        $_isAuthorized = false;

        if((array_search('11',$userGroups) !== false) || (array_search('14',$userGroups) !== false)){
            // We found he is the partner
            $_isAuthorized = true;
        }else{
            $message =  "Sorry, You leave us too long. Please <a href='./index.php/login'>click here</a> to login again!";
            echo $message;    
        }        
        if($_isAuthorized){
            // Import the custom lib first
            require_once JPATH_COMPONENT.'/lib/simple_html_dom.php';
            require_once JPATH_COMPONENT.'/lib/url.class.php';
            //print_r($_POST);
            //echo "<ul><li>sssssss</li><li>ddddddd</li><li>fffffffff</li></ul>";
            $url = trim($_POST["recipe_link"]);
            //$url = "http://www.recipe4living.com/recipes/crazy_crab_and_avocado_salad.htm";
            $googleObj = new UrlGoogleFormat($url);
            if($googleObj){     
                if($googleObj->getRecipeType() !== false){
                    // We create the google format ok!
                        $googleObj = new UrlGoogleFormat($url); 
                        $title = $googleObj->getTitle();      // String title
                        $ingredients = $googleObj->getIngredientsArray(); // array ingredients
                        $image = $googleObj->getImageArrayOriginalUrl(); // array image URL
                        
                        echo "<ul id='li_ul'>";
                        echo "<li class='li_title'>Recipe Title:</li>";
                        echo "<li class='li_content'>$title</li>";
                        echo "<li class='li_title'>Recipe images:</li>";
                        echo "<li class='li_content'>";
                        foreach($image as $img){
                            echo "<img src='$img' />";    
                        }
                        echo "</li>";
                        echo "<li class='li_title'>Recipe Ingredients:</li>";
                        foreach($ingredients as $row){
                            echo "<li class='li_content'>$row</li>";     
                        }
                        echo "</ul>";
                        
                }
            }
        }
    }

    function is_url($str){
        return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/", $str);
    }    
    
}