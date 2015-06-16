<?php
 /**
 * @package		com_contactenhanced
 * @copyright	Copyright (C) 2006 - 2013 IdealExtensions.com. All rights reserved.reserved.Development
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


$canDo		= CEHelper::getActions();
$jversion	= new JVersion();
$user		= JFactory::getUser();

$cparams = JComponentHelper::getParams ('com_media');
if (JRequest::getVar('submitted')) {
	$doc	= JFactory::getDocument();
	$script	= '';
	$script	.= "
		if(typeof(pageTracker) != 'undefined'){
			pageTracker._trackPageview('/".JText::_("CE_FORM_GA_CONTACT_FORM")."/".$this->contact->alias."');
			console.log('pageTracker :: Google Analytics Page tracked');
		}else if(typeof(_gaq) != 'undefined'){
			_gaq.push(['_trackPageview','/".JText::_("CE_FORM_GA_CONTACT_FORM")."/".$this->contact->alias."']);
			console.log('_gaq.push :: Google Analytics Page tracked');
		}
	";

	if ( $this->params->get( 'thankyoupageType' ) == 'alert' ) {
		$script	.= "alert('".JText::_('COM_CONTACTENHANCED_EMAIL_THANKS')."');";
	}

	$doc->addScriptDeclaration("
window.addEvent('domready', function(){
{$script}
});
	");
}
$containerClass	= '';
if(JRequest::getVar('tmpl') == 'component'){
	$containerClass	= 'ce-contact-modal';
	$this->document->setMetaData('robots', 'noindex, nofollow');
}

?>
<div itemscope itemtype="http://data-vocabulary.org/Person"
	id="ce-contact-<?php echo $this->contact->id; ?>"
	class="ce-container contact<?php echo $this->params->get('pageclass_sfx'). ' '.$containerClass; ?>">
<?php echo ceHelper::loadModulePosition('ce-before-title');  ?>
<?php if ($this->params->get('show_page_heading', 0)) : ?>
<?php // class="title" was added in order to try add compaibility with Gantry based templates?>
<h1 class="title ">
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
	<?php if ($this->contact->name && $this->params->get('show_name',1)) : ?>
		<h2>
			<span itemprop="name" class="contact-name"><?php echo $this->contact->name; ?></span>
		</h2>
	<?php endif; ?>

	<?php echo ceHelper::loadModulePosition('ce-after-title');  ?>

	<?php if ($this->params->get('show_contact_category','hide') == 'show_no_link') : ?>
		<h3>
			<span class="contact-category"><?php echo $this->contact->category_title; ?></span>
		</h3>
	<?php endif; ?>
	<?php if ($this->params->get('show_contact_category','hide') == 'show_with_link') : ?>
		<?php $contactLink = ContactenchancedHelperRoute::getCategoryRoute($this->contact->catid);?>
		<h3>
			<span class="contact-category"><a href="<?php echo $contactLink; ?>">
				<?php echo $this->escape($this->contact->category_title); ?></a>
			</span>
		</h3>
	<?php endif; ?>

	<?php
	if(version_compare( $jversion->getShortVersion(), '3.1') >= 0):
		if ($this->params->get('show_tags', 1) && !empty($this->item->tags)) :
	?>
		<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
		<?php //echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
	<?php
		endif;
	endif; ?>

	<?php
	if (
		($canDo->get('core.edit') OR ($canDo->get('core.edit.own')
			AND $this->contact->user_id == $this->user->get('id'))
			AND $this->contact->user_id > 0
			AND $this->user->get('id') > 1
		)
	):
	?>
		<ul class="actions">
			<li class="edit-icon">
			<?php echo JHtml::_('ceicon.edit', $this->contact, $this->params); ?>
			</li>
		</ul>
	<?php
	endif;?>

	<?php echo $this->loadTemplate('introtext'); ?>


	<?php if ($this->params->get('show_contact_list') && count($this->contacts) > 1) : ?>
		<form action="#" method="get" name="selectForm" id="selectForm">
			<?php echo JText::_('COM_CONTACTENHANCED_SELECT_CONTACT'); ?>:
			<?php echo JHtml::_('select.genericlist',  $this->contacts, 'id', 'class="inputbox" onchange="document.location.href = this.value"', 'link', 'name', $this->contact->link);?>
		</form>
	<?php endif;
	if ($this->params->get('presentation_style','plain')!='plain'){
		echo  JHtml::_($this->params->get('presentation_style','plain').'.start', 'contact-slider');
	}
	if ($this->params->get('show_contact_details','beforeform') == 'beforeform'){
		echo $this->loadTemplate('details');
	}

	?>


	<?php

		/**
		 * MAP: Before Form
		 */
		if ($this->params->get('show_map','beforeform') == 'beforeform') : ?>
		<?php if ($this->params->get('presentation_style','plain')!='plain'):?>
			<?php  echo JHtml::_($this->params->get('presentation_style','plain').'.panel', JText::_('CE_MAP'), 'form-map');  ?>
		<?php endif; ?>
		<?php  echo $this->loadTemplate('map');  ?>
	<?php endif; ?>

	<?php

	/**
	 * MISC: Before Form
	 */

	if($this->params->get('show_misc') == 'before_form'){
		echo $this->loadTemplate('misc');
	}
	?>

	<?php
		/**
		 * FORM
		 */
	?>
	<?php if ($this->params->get('show_email_form',1) && ($this->contact->email_to || $this->contact->user_id)) : ?>
		<?php if ($this->params->get('presentation_style','plain')!='plain'):?>
			<?php  echo JHtml::_($this->params->get('presentation_style','plain').'.panel', JText::_('COM_CONTACTENHANCED_EMAIL_FORM'), 'display-form');  ?>
		<?php elseif ($this->params->get('presentation_style','plain')=='plain'):?>
			<?php  //echo '<h3>'. JText::_('COM_CONTACTENHANCED_EMAIL_FORM').'</h3>';  ?>
		<?php endif; ?>

		<?php  echo $this->loadTemplate('form');  ?>
	<?php endif; ?>

	<?php
		/**
		 * MAP: After Form
		 */
	?>
	<?php if ($this->params->get('show_map','beforeform') == 'afterform') : ?>
		<?php if ($this->params->get('presentation_style','plain')!='plain'):?>
			<?php  echo JHtml::_($this->params->get('presentation_style','plain').'.panel', JText::_('CE_MAP'), 'form-map');  ?>
		<?php endif; ?>
		<?php  echo $this->loadTemplate('map');  ?>
	<?php endif; ?>
	<?php

	if ($this->params->get('show_contact_details','beforeform') == 'afterform'){
		echo $this->loadTemplate('details');
	}

	?>
	<?php if ($this->params->get('show_links',1)) : ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>
	<?php if ($this->params->get('show_articles',0) && $this->contact->user_id && $this->contact->articles) : ?>
		<?php if ($this->params->get('presentation_style','plain')!='plain'):?>
				<?php echo JHtml::_($this->params->get('presentation_style','plain').'.panel', JText::_('JGLOBAL_ARTICLES'), 'display-articles'); ?>
			<?php endif; ?>
			<?php if  ($this->params->get('presentation_style','plain')=='plain'):?>
				<?php echo '<h3>'. JText::_('JGLOBAL_ARTICLES').'</h3>'; ?>
			<?php endif; ?>
			<?php echo $this->loadTemplate('articles'); ?>
	<?php endif; ?>
	<?php if ($this->params->get('show_profile') && $this->contact->user_id && JPluginHelper::isEnabled('user', 'profile')) : ?>
		<?php if ($this->params->get('presentation_style','plain')!='plain'):?>
			<?php echo JHtml::_($this->params->get('presentation_style','plain').'.panel', JText::_('COM_CONTACTENHANCED_PROFILE'), 'display-profile'); ?>
		<?php endif; ?>
		<?php if ($this->params->get('presentation_style','plain')=='plain'):?>
			<?php echo '<h3>'. JText::_('COM_CONTACTENHANCED_PROFILE').'</h3>'; ?>
		<?php endif; ?>
		<?php echo $this->loadTemplate('profile'); ?>
	<?php endif; ?>

	<?php
		// @todo:  added the option in the xml
		if($this->params->get('show_misc') == 'end' OR $this->params->get('show_misc',1) == 1){
			echo $this->loadTemplate('misc');
		}
	?>

	<?php if ($this->params->get('presentation_style','plain')!='plain'){
			echo JHtml::_($this->params->get('presentation_style','plain').'.end');
	} ?>
</div>
