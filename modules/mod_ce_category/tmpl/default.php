<?php
/**
 * @version		$Id: default.php 21322 2011-05-11 01:10:29Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_ce_category
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
// Get the document object.
$document = JFactory::getDocument();
$document->addStylesheet(JURI::base(true).'/components/com_contactenhanced/assets/css/ce.css');
?>
<ul class="category-module<?php echo $moduleclass_sfx; ?>">
<?php if ($grouped) : ?>
	<?php foreach ($list as $group_name => $group) : ?>
	<li>
		<h<?php echo $item_heading; ?>><?php echo $group_name; ?></h<?php echo $item_heading; ?>>
		<ul>
			<?php foreach ($group as $item) : ?>
				<li>
					<h<?php echo $item_heading+1; ?>>
					   	<?php if ($params->get('link_titles') == 1) : ?>
						<a class="mod-ce-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
						<?php echo $item->title; ?>
				        <?php if ($item->displayHits) :?>
							<span class="mod-ce-category-hits">
				            (<?php echo $item->displayHits; ?>)  </span>
				        <?php endif; ?></a>
				        <?php else :?>
				        <?php echo $item->title; ?>
				        	<?php if ($item->displayHits) :?>
							<span class="mod-ce-category-hits">
				            (<?php echo $item->displayHits; ?>)  </span>
				        <?php endif; ?></a>
				            <?php endif; ?>
			        </h<?php echo $item_heading+1; ?>>
					<?php if ($params->get('show_position') AND $item->con_position) :?>
			       		<span class="mod-ce-category-position">
						<?php echo $item->con_position; ?>
						</span>
					<?php endif;?>
				            
				
				<?php if ($params->get('show_author') AND $item->displayAuthorName) :?>
					<span class="mod-ce-category-writtenby">
					<?php echo $item->displayAuthorName; ?>
					</span>
				<?php endif;?>

				<?php if ($item->displayCategoryTitle) :?>
					<span class="mod-ce-category-category">
					(<?php echo $item->displayCategoryTitle; ?>)
					</span>
				<?php endif; ?>
				<?php if ($item->displayDate) : ?>
					<span class="mod-ce-category-date"><?php echo $item->displayDate; ?></span>
				<?php endif; ?>
				
				 <?php if ($params->get('show_misc') AND $item->displayMisc) :?>
					<p class="mod-ce-category-misc">
					<?php echo $item->displayMisc; ?>
					</p>
				<?php endif; ?>
				
				<?php 
				
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
					 $addressFormat	= explode(',', $params->get('address_format','street_address,suburb,state,postcode,country'));
				
					$item->street_address	= $item->address;
					foreach ($addressFormat as $key):
						if ($item->$key && $params->get('show_'.$key,1)) : ?>
						<span class="contact-<?php echo $key; ?>">
							<?php echo nl2br($item->$key); ?>
						</span>
				<?php 
						endif;
					endforeach;
					 
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
							if($browser->isMobile()){
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
							if($browser->isMobile()){
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
							echo  '<a href="'.$item->webpage.'" target="_blank">';
							 echo  $item->webpage.' </a>';
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
				?>
				


		</li>
			<?php endforeach; ?>
		</ul>
	</li>
	<?php endforeach; ?>
<?php else : ?>
	<?php foreach ($list as $item) : ?>
	    <li>
	   	<h<?php echo $item_heading; ?>>
	   	<?php if ($params->get('link_titles') == 1) : ?>
		<a class="mod-ce-category-title <?php echo $item->active; ?>" href="<?php echo $item->link; ?>">
		<?php echo $item->title; ?>
        <?php if ($item->displayHits) :?>
			<span class="mod-ce-category-hits">
            (<?php echo $item->displayHits; ?>)  </span>
        <?php endif; ?></a>
        <?php else :?>
        <?php echo $item->title; ?>
        	<?php if ($item->displayHits) :?>
			<span class="mod-ce-category-hits">
            (<?php echo $item->displayHits; ?>)  </span>
        <?php endif; ?></a>
            <?php endif; ?>
        </h<?php echo $item_heading; ?>>
        <?php 
        if($params->get('show_image') AND $item->image ){
			echo  '<div class="contact-image">'.
						JHTML::_('image',$item->image, JText::_('COM_CONTACTENHANCED_IMAGE_DETAILS'), array('align' => 'middle')).'
					</div>';
		}
		?>
       <?php if ($params->get('show_position') AND $item->con_position) :?>
       		<span class="mod-ce-category-position">
			<?php echo $item->con_position; ?>
			</span>
		<?php endif;?>
       
       	<?php if ($params->get('show_author')) :?>
       		<span class="mod-ce-category-writtenby">
			<?php echo $item->displayAuthorName; ?>
			</span>
		<?php endif;?>
		<?php if ($item->displayCategoryTitle) :?>
			<span class="mod-ce-category-category">
			(<?php echo $item->displayCategoryTitle; ?>)
			</span>
		<?php endif; ?>
        <?php if ($item->displayDate) : ?>
			<span class="mod-ce-category-date"><?php echo $item->displayDate; ?></span>
		<?php endif; ?>
		
		
		 <?php if ($params->get('show_misc')) :?>
			<p class="mod-ce-category-misc">
			<?php echo $item->displayMisc; ?>
			</p>
		<?php endif; ?>
		
		<?php 
		
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
				$addressFormat	= explode(',', $params->get('address_format','street_address,suburb,state,postcode,country'));
			
				$item->street_address	= $item->address;
				foreach ($addressFormat as $key):
					if ($item->$key && $params->get('show_'.$key,1)) : ?>
					<span class="contact-<?php echo $key; ?>">
						<?php echo nl2br($item->$key); ?>
					</span>
			<?php 
					endif;
				endforeach;
			 
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
					 echo  nl2br($item->telephone); 
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
					 echo  nl2br($item->mobile); 
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
					echo  '<a href="'.$item->webpage.'" target="_blank">';
					 echo  $item->webpage.' </a>';
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
		?>
		
		

		
	</li>
	<?php endforeach; ?>
<?php endif; ?>
</ul>
