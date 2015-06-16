<?php 
/**
 * @version		2.5.0
 * @package		com_contactenhanced
 * @subpackage	mod_ce_contactslider
 * @copyright	Copyright (C) 2005 - 2013 IdealExtensions.com. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


if ($params->get('show_position') AND $item->con_position) :?>
	<span class="mod-ce-contactslider-position">
		<?php echo $item->con_position; ?>
	</span>
<?php endif;?>
           

<?php if ($item->displayCategoryTitle) :?>
	<span class="mod-ce-contactslider-category">
	(<?php echo $item->displayCategoryTitle; ?>)
	</span>
<?php endif; ?>
<?php if ($item->displayDate) : ?>
	<span class="mod-ce-contactslider-date"><?php echo $item->displayDate; ?></span>
<?php endif; ?>

 <?php if ($params->get('show_misc')) :?>
	<p class="mod-ce-contactslider-misc">
	<?php echo $item->displayMisc; ?>
	</p>
<?php endif;

if (	($item->address && $params->get('show_street_address',1)) 
	||	($item->suburb && $params->get('show_suburb',1))
	||	($item->state && $params->get('show_state',1))
	||	($item->country && $params->get('show_country',1))
	||	($item->postcode && $params->get('show_postcode',1))
	) : 
	echo '<div class="contact-address">';
		echo  '<span class="'. $params->get('marker_class').'" >';
			 echo  $params->get('marker_address'); 
		echo  '</span>';
		echo  '<address>';
	 if ($item->address && $params->get('show_street_address',1)) : 
		echo  '<span class="contact-street"> ';
			 echo  nl2br($item->address); 
		echo  '</span>';
	 endif; 
	 if ($item->suburb && $params->get('show_suburb',1)) : 
		echo  '<span class="contact-suburb"> ';
			 echo  $item->suburb; 
		echo  '</span>';
	 endif; 
	 if ($item->state && $params->get('show_state',1)) : 
		echo  '<span class="contact-state"> ';
			 echo  $item->state; 
		echo  '</span>';
	 endif; 
	 if ($item->postcode && $params->get('show_postcode',1)) : 
		echo  '<span class="contact-postcode"> ';
			 echo  $item->postcode; 
		echo  '</span>';
	 endif; 
	 if ($item->country && $params->get('show_country',1)) : 
		echo  '<span class="contact-country"> ';
			 echo  $item->country; 
		echo  '</span>';
	 endif; 
	 
	echo  '</address>';
	echo  '</div>';
endif; 
	
	
		
	
 if($params->get('show_email') || $params->get('show_telephone')||$params->get('show_fax')||$params->get('show_mobile')|| $params->get('show_webpage') ) : 
	echo  '<div class="contact-contactinfo">';
 endif; 
 if ($item->email_to && $params->get('show_email')) : 
	echo  '<p>';
		echo  '<span class="'.$params->get('marker_class').'" >';
			 echo  $params->get('marker_email'); 
		echo  '</span>';
		echo  '<span class="contact-emailto">';
			 echo  $item->email_to; 
		echo  '</span>';
	echo  '</p>';
 endif; 

 if ($item->telephone && $params->get('show_telephone',1)) : 
	echo  '<p>';
		echo  '<span class="'.$params->get('marker_class').'" >';
			 echo  $params->get('marker_telephone'); 
		echo  '</span>';
		echo  '<span class="contact-telephone">';
			$tel	= nl2br($item->telephone);
			if($this->browser->isMobile()){
				$telLink= 'tel:'.preg_replace('[(?!\+\b)\D]', '', $tel);
				$tel	= JHtml::_('link'
									,$telLink
									,$tel
									, array('title' => JText::sprintf('COM_CONTACTENHANCED_CALL_USING_YOUR_PHONE',$tel))
									);
			}
			echo ($tel); 
		echo  '</span>';
	echo  '</p>';
 endif; 
 if ($item->fax && $params->get('show_fax',1)) : 
	echo  '<p>';
		echo  '<span class="'.$params->get('marker_class').'" >';
			 echo  $params->get('marker_fax'); 
		echo  '</span>';
		echo  '<span class="contact-fax">';
		 echo  nl2br($item->fax); 
		echo  '</span>';
	echo  '</p>';
 endif; 
 if ($item->mobile && $params->get('show_mobile',1)) :
	echo  '<p>';
		echo  '<span class="'.$params->get('marker_class').'">';
			 echo  $params->get('marker_mobile'); 
		echo  '</span>';
		echo  '<span class="contact-mobile">';
			$tel	= nl2br($item->mobile);
			if($this->browser->isMobile()){
				$telLink= 'tel:'.preg_replace('[(?!\+\b)\D]', '', $tel);
				$tel	= JHtml::_('link'
									,$telLink
									,$tel
									, array('title' => JText::sprintf('COM_CONTACTENHANCED_CALL_USING_YOUR_PHONE',$tel))
									);
			}
			echo ($tel);
		echo  '</span>';
	echo  '</p>';
 endif; 
 if ($item->skype && $params->get('show_skype',1)) : 
	echo  '<p>';
		echo  '<span class="'.$params->get('marker_class').'" >';
			 echo  $params->get('marker_skype'); 
		echo  '</span>';
		echo  '<span class="contact-skype">
			<a href="skype:'.$item->skype.'?call" 
				title="'.JText::_('COM_CONTACTENHANCED_SKYPE_MAKE_A_CALL').'" 
				target="_blank" rel="nofollow">';
			 echo  $item->skype.'</a>';
		echo  '</span>';
	echo  '</p>';
 endif; 

 if ($item->twitter && $params->get('show_twitter',1)) : 
	echo  '<p>';
		echo  '<span class="'.$params->get('marker_class').'" >';
			 echo  $params->get('marker_twitter'); 
		echo  '</span>';
		echo  '<span class="contact-twitter">
			<a href="http://twitter.com/#!/'.$item->twitter.'" 
				title="'.JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_TWITTER_LABEL').'" 
				target="_blank" rel="nofollow">@'.$item->twitter.'</a>';
		echo  '</span>';
	echo  '</p>';
 endif; 

 if ($item->facebook && $params->get('show_facebook',1)) : 
	echo  '<p>';
		echo  '<span class="'.$params->get('marker_class').'" >';
			 echo  $params->get('marker_facebook'); 
		echo  '</span>';
		echo  '<span class="contact-facebook">
			<a href="'.$item->facebook.' " 
				title="'.JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_FACEBOOK_LABEL').'" 
				target="_blank" rel="nofollow">';
			 echo  $item->facebook.'</a>';
		echo  '</span>';
	echo  '</p>';
 endif; 

 if ($item->linkedin && $params->get('show_linkedin',1)) : 
	echo  '<p>';
		echo  '<span class="'.$params->get('marker_class').'" >';
			 echo  $params->get('marker_linkedin'); 
		echo  '</span>';
		echo  '<span class="contact-linkedin">
			<a href="'.$item->linkedin.'" 
				title="'.JText::_('COM_CONTACTENHANCED_FIELD_INFORMATION_LINKEDIN_LABEL').'" 
				target="_blank" rel="nofollow">';
			 echo  $item->linkedin.'</a>';
		echo  '</span>';
	echo  '</p>';
 endif; 

 if ($item->webpage && $params->get('show_webpage',1)) : 
	echo  '<p>';
		echo  '<span class="'.$params->get('marker_class').'" >';
			 echo  $params->get('marker_website'); 
		echo  '</span>';
		echo  '<span class="contact-webpage">';
			echo  ' <a href="'.$item->webpage.'" title="'.$item->webpage.'" target="_blank">';
			 	if($params->get('show_webpage') == 'trim'){
					 echo ceHelper::trimURL($item->webpage); 
				}elseif($params->get('show_webpage') == 'label'){
					 echo JText::_('COM_CONTACTENHANCED_WEBPAGE_LABEL'); 
				}else{
					echo $item->webpage;
				}
			echo  ' </a>';
		echo  '</span>';
	echo  '</p>';
 endif; 
 if($params->get('show_email',1) || $params->get('show_telephone',1)||$params->get('show_fax',1)||$params->get('show_mobile',1)|| $params->get('show_webpage',1) ) : 
	echo  '</div>';
 endif; 

 
 

	
if(		$params->get('show_extrafield_1',0) OR $params->get('show_extrafield_2',0)
	OR	$params->get('show_extrafield_3',0) OR $params->get('show_extrafield_4',0) OR $params->get('show_extrafield_5',0) ) : 
	echo  '<div class="contact-extrafields">';


	
	if ($item->extra_field_1 && $params->get('show_extrafield_1',0)) : 
		echo  '<p>';
			echo  '<span class="'.$params->get('marker_class').'" >';
				 echo  $params->get('marker_extrafield_1'); 
			echo  '</span>';
			echo  '<span class="contact-extrafield_1">';
				 echo  $item->extra_field_1;
			echo  '</span>';
		echo  '</p>';
	 endif; 
	 
	 if ($item->extra_field_2 && $params->get('show_extrafield_2',0)) : 
		echo  '<p>';
			echo  '<span class="'.$params->get('marker_class').'" >';
				 echo  $params->get('marker_extrafield_2'); 
			echo  '</span>';
			echo  '<span class="contact-extrafield_2">';
				 echo  $item->extra_field_2;
			echo  '</span>';
		echo  '</p>';
	 endif; 
	 
	 if ($item->extra_field_3 && $params->get('show_extrafield_3',0)) : 
		echo  '<p>';
			echo  '<span class="'.$params->get('marker_class').'" >';
				 echo  $params->get('marker_extrafield_3'); 
			echo  '</span>';
			echo  '<span class="contact-extrafield_3">';
				 echo  $item->extra_field_3;
			echo  '</span>';
		echo  '</p>';
	 endif; 
	 
	 if ($item->extra_field_4 && $params->get('show_extrafield_4',0)) : 
		echo  '<p>';
			echo  '<span class="'.$params->get('marker_class').'" >';
				 echo  $params->get('marker_extrafield_4'); 
			echo  '</span>';
			echo  '<span class="contact-extrafield_4">';
				 echo  $item->extra_field_4;
			echo  '</span>';
		echo  '</p>';
	 endif; 
	 
	 if ($item->extra_field_5 && $params->get('show_extrafield_5',0)) : 
		echo  '<p>';
			echo  '<span class="'.$params->get('marker_class').'" >';
				 echo  $params->get('marker_extrafield_5'); 
			echo  '</span>';
			echo  '<span class="contact-extrafield_5">';
				 echo  $item->extra_field_5;
			echo  '</span>';
		echo  '</p>';
	 endif; 

	
	echo  '</div>';
endif; 
