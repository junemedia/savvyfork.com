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

JHTML::_('behavior.tooltip');
JHTML::_('behavior.modal');
JHtmlBehavior::framework();
jimport('mosets.profilepicture.profilepicture');
JHtml::addIncludePath(JPATH_COMPONENT.'/lib');

$document = JFactory::getDocument();
$document->addStyleSheet('media/com_yoorecipe/styles/yoorecipe_single.css');
$document->addStyleSheet('media/com_yoorecipe/styles/bluecurve/bluecurve.css');
$document->addScript('media/com_yoorecipe/js/range.js');
$document->addScript('media/com_yoorecipe/js/timer.js');
$document->addScript('media/com_yoorecipe/js/slider.js');

// Init variables
$input    = JFactory::getApplication()->input;
$user     = JFactory::getUser();
$recipe   = $this->recipe;
$recipe_author = JFactory::getUser($recipe->created_by);
$author_profile = JUserHelper::getProfile($recipe->created_by);
$picture = new ProfilePicture($recipe->created_by);
$headimage = $picture->getURL('original');
if (!$headimage) {
  $headimage="images/headimg_reserve.jpg";
}

$date = new DateTime($recipe->creation_date);
$publish_date = $date->format('F d, Y');

$isPrinting = $input->get('print', '0', 'INT');

// Image count
$recipe_images = array();

if ($recipe->picture != "") {
  $recipe_images [] = $recipe->picture;
}
if ($recipe->picture2 != "") {
  $recipe_images [] = $recipe->picture2;
}
if ($recipe->picture3 != "") {
  $recipe_images [] = $recipe->picture3;
}

//Recipe ingredients
$ingredients = array();
if (!empty($recipe->ingredients)) {
  foreach ($recipe->ingredients as $ingredient) {
    $ingredients[] = $ingredient->description;
  }
}

// Add scripts
$document->addScriptDeclaration(JHtml::_('yoorecipejsutils.getSmoothScrollScript'));
$document->addScriptDeclaration(JHtml::_('yoorecipejsutils.getReportAbusiveCommentScript'));
if (!$user->guest) {
  $document->addScriptDeclaration(JHtml::_('yoorecipejsutils.getAddToFavouritesScript'));
  $document->addScriptDeclaration(JHtml::_('yoorecipejsutils.removeFromFavouritesScript'));
}
if (isset($this->canManageComments) && $this->canManageComments) {
  $document->addScriptDeclaration(JHtml::_('yoorecipejsutils.getDeleteCommentScript'));
}

// Anti spam code generation
$int1 = rand ( 0 , 5 );
$int2 = rand ( 0 , 4 );

// Component Parameters
$yooRecipeparams             = JComponentHelper::getParams('com_yoorecipe');
$thumbnail_width             = $yooRecipeparams->get('thumbnail_width', 250);

$canShowPrice                = $yooRecipeparams->get('show_price', 0);
$currency                    = $yooRecipeparams->get('currency', '&euro;');

// Menu Parameters also defined in Component Settings
$enable_comments             = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'enable_comments', 1);
$use_automatic_numbering     = $yooRecipeparams->get('use_automatic_numbering', 1);
$use_quantity_converter      = $yooRecipeparams->get('use_quantity_converter', 1);
$show_author                 = $yooRecipeparams->get('show_author', 1);
$use_default_picture         = $yooRecipeparams->get('use_default_picture', 1);
$use_watermark               = $yooRecipeparams->get('use_watermark', 1);

$register_to_comment         = $yooRecipeparams->get('register_to_comment', 0);
$show_recaptch               = $yooRecipeparams->get('show_recaptch', 'std');
$show_email                  = $yooRecipeparams->get('show_email', 1) && $user->guest;
$use_google_recipe           = $yooRecipeparams->get('use_google_recipe', 1);
$show_people_icons           = $yooRecipeparams->get('show_people_icons', 1);
$use_fractions               = $yooRecipeparams->get('use_fractions', 0);
$use_video                   = $yooRecipeparams->get('use_video', 1);
$use_nutrition_facts         = $yooRecipeparams->get('use_nutrition_facts', 1);
$show_slider_tooltip         = $yooRecipeparams->get('show_slider_tooltip', 0);
$use_prices                  = $yooRecipeparams->get('use_prices', 0);
$currency                    = $yooRecipeparams->get('currency', '&euro;');
$max_servings                = $yooRecipeparams->get('max_servings', 10);
$publickey                   = $yooRecipeparams->get('recaptcha_public_key');
$use_tags                    = $yooRecipeparams->get('use_tags', 1);
$show_seasons                = $yooRecipeparams->get('show_seasons', 1);

$canShowCategory             = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_category', 1);

$canShowDescription          = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_description', 1);
$canShowDifficulty           = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_difficulty', 1);
$canShowCost                 = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_cost', 1);
$canShowRatings              = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_rating', 1);
$ratingStyle                 = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'rating_style', 'stars');

$canShowPreparationTime      = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_preparation_time', 1);
$canShowCookTime             = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_cook_time', 1);
$canShowWaitTime             = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_wait_time', 1);

$canShowPrintIcon            = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_print_icon', 1);
$show_email_icon             = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_email_icon', 1);

$useSocialSharing            = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'use_social_sharing', 1);
$showSocialBookmarksOnTop    = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_on_top', 1);
$showSocialBookmarksOnBottom = JHtml::_('yoorecipeutils.getParamValue', $this->menuParams, $yooRecipeparams, 'show_on_bottom', 0);


function pluralTosingular($word) {
  $rules = array(
    'ss' => false,
    'os' => 'o',
    'ies' => 'y',
    'xes' => 'x',
    'oes' => 'o',
    'ies' => 'y',
    'ves' => 'f',
    'hes' => 'h',
    's' => ''
  );
  $excludeArr = array('cuisines');
  if (in_array($word,$excludeArr)) {
    return $word;
  }

  if (strtolower($word) == 'breakfast recipes') {
    return 'Breakfast';
  }

  foreach (array_keys($rules) as $key) {
    if (substr($word, (strlen($key) * -1)) != $key) {
      continue;
    }
    if ($key === false) {
      return $word;
    }
    return substr($word, 0, strlen($word) - strlen($key)) . $rules[$key];
  }
  return $word;
}
?>

<link rel="stylesheet" href="media/com_yoorecipe/styles/magnific-popup.css" type="text/css" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script src="media/com_yoorecipe/js/popup.js"></script>
<style>
  .white-popup {
    position: relative;
    background: #FFF;
    padding: 20px;
    width:auto;
    max-width: 500px;
    margin: 20px auto;
  }
</style>
<script type="text/javascript">
  function switchLogin(showObject,hideObject) {
    $('#'+showObject).show();
    $('#'+hideObject).hide();
  }
  $(document).ready(function() {
    $('.open-popup-link').magnificPopup({
      type:'inline',
      midClick: true,
      closeBtnInside: true
    });
  });
</script>

<!-- BEGIN Tynt Script -->
<script type="text/javascript">
  if(document.location.protocol=='http:'){
  var Tynt=Tynt||[];Tynt.push('cJujl2gy0r5j-Tacwqm_6r');
  (function(){var s=document.createElement('script');s.async="async";s.type="text/javascript";s.src='http://tcr.tynt.com/ti.js';var h=document.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})();
  }
</script>
<!-- END Tynt Script -->

<?php
if (isset($recipe) && $recipe->published && $recipe->validated) {

  // Add FB opengraph tags
  $openGraphTags   = array();
  $uri             = JURI::getInstance();
  $lang            = JFactory::getLanguage();
  $config          = JFactory::getConfig();
  $openGraphTags[] = '<meta property="og:url" content="'.$uri->toString().'"/>';
  $openGraphTags[] = '<meta property="og:title" content="'.htmlspecialchars($this->recipe->title).'"/>';
  $openGraphTags[] = '<meta property="og:description" content="'.strip_tags($this->recipe->description).'"/>';
  $openGraphTags[] = '<meta property="og:type" content="recipebox:recipe"/>';
  $openGraphTags[] = '<meta property="og:locale" content="en-US"/>';
  $openGraphTags[] = '<meta property="og:site_name" content="savvyfork.com"/>';
  $openGraphTags[] = '<meta property="og:image" content="http://'.$_SERVER['SERVER_NAME'].'/'.$recipe_images[0].'" />';

  $document->addCustomTag(implode("\n", $openGraphTags));

  // For the social media:
  $details_url = JRoute::_('index.php?option=com_yoorecipe&task=viewRecipe&id=' . $recipe->slug);
  $db = JFactory::getDBO();
  $sql = "SELECT recipe_id FROM #__yoorecipe_favourites WHERE user_id = ".$db->quote($user->id);
  $db->setQuery ($sql);
  $fav_recipes = $db->loadColumn();
  $fav_url = JRoute::_('index.php?option=com_yoorecipe&task=addToFavourites&recipeId=' . $recipe->id);
  $fav_class = "";

  if ($user->get('guest') == 1) {
    $fav_url = "#login-popup";
    $fav_class="open-popup-link";
  }

  $remove_fav = JRoute::_('index.php?option=com_yoorecipe&task=removeFromFavourites&recipeId=' . $recipe->id);
  $cat_url = JRoute::_(JHtml::_('YooRecipeHelperRoute.getcategoryroute', $recipe->categories[0]->id.':'.$recipe->categories[0]->alias) , false);
  ?>

<div id="div-yoorecipe">
  <div id="backhome">
    <a href="./">Savvy Fork Home</a> &gt;
    <?php if (!empty($recipe->categories)) { ?>
      <a href="<?php echo $cat_url;?>"><?php echo $recipe->categories[0]->title?></a> &gt;
    <?php }?>
    <a href="#"><?php echo htmlspecialchars($recipe->title);?></a>
  </div>

  <div class="email_to">
    <script type="text/javascript">var switchTo5x = false;</script>
    <script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>
    <script type="text/javascript">
      stLight.options({
        publisher: "ur-ff7f3b14-df2c-9a06-45d5-350cacb1861c",
        doNotHash: false,
        doNotCopy: false,
        hashAddressBar: false
      });
    </script>
    <span class='st_email' displayText='Email this recipe to a friend'></span>
  </div>

  <div id="div-recipe-detail">
    <div class="left-detail">
      <div id="recipe-part" itemscope itemtype ="http://schema.org/Recipe">
        <?php if ($use_google_recipe) : ?>
        <div class="hrecipe">
        <?php endif; ?>
          <div id="div-recipe-title">
            <?php
              $editUrl = JRoute::_('index.php?option=com_yoorecipe&view=form&layout=edit&id=' . $recipe->slug);
            ?>
            <span class="item">
              <h1 class="recipe-title <?php if ($use_google_recipe) : echo 'fn'; endif; ?>" itemprop="title">
                <?php echo htmlspecialchars($recipe->title); if($canShowPrice==1 && $recipe->price!=null && $recipe->price > 0){ echo ' '.$recipe->price . $currency;} ?>
              </h1>
            </span>

            <div id="recipe-img">
              <?php if (count($recipe_images) == 1) { ?>
              <div id="imageContainer">
                <div id="big-img">
                  <img style="width:300px;height:300px;" src="<?php if(file_exists($recipe_images[0])){echo $recipe_images[0];}else{echo "/images/default.jpg";}?>" title="<?php echo $recipe->title;?>" alt="<?php echo $recipe->title;?>" class="photo" />
                  <div id="cardStar">

                    <?php if (in_array($recipe->id,$fav_recipes)) { ?>
                    <a class="isfav" href="<?php echo $remove_fav;?>"></a>
                    <?php } else { ?>
                    <a class="fav <?php echo $fav_class;?>" title="Save as Favorite" onclick="logActivity('<?php echo $user->id;?>','<?php echo $recipe->id;?>','Favorite');" href="<?php echo $fav_url;?>"></a>
                    <?php }?>
                  </div>
                </div>

                <div id="editorComment">
                  <div class="summary"><?php echo $recipe->description;?></div>
                </div>
              </div>

              <?php } else if (count($recipe_images) == 2) { ?>
              <div id="big-img"><img style="width:400px;" src="<?php echo $recipe_images[0]?>" /></div>
              <div id="small-img">
                <div class="small" style="width:230px;height:250px;">
                  <img src="<?php echo $recipe_images[1]?>" style="height:250px;"/>
                </div>
              </div>

              <?php } else if (count($recipe_images) == 3) { ?>
              <div id="big-img"><img src="<?php echo $recipe_images[0]?>" style="height:250px;"/></div>
              <div id="small-img">
                <div class="small">
                  <img src="<?php echo $recipe_images[1]?>" style="width:230px;"/>
                </div>
                <div class="small" style="margin-top:5px;">
                  <img src="<?php echo $recipe_images[2]?>" style="width:230px;"/>
                </div>
              </div>
              <?php } ?>

            </div><!-- /#recipe-img -->
          </div><!-- /#div-recipe-title -->

          <div class="middleContent">
            <div class="cookingTips">
            <?php if ($recipe->cookingtips != "") { ?>
              <h3>Cooking Tips</h3>
              <div><?php echo $recipe->cookingtips;?></div>
            <?php }?>
            </div>
          </div>

          <div id="social_stats">
            <table>
              <tr class="share_tr">
                <td><div><h3 class="share_recipe">Share Recipe</h3></div></td>
                <td>
                  <div class="share_recipe"><a title="Share" target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $recipe->id;?>','Facebook')" href="http://www.facebook.com/sharer.php?u=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>"><img src="/images/icons/color/Facebook.png" /></a></div>
                </td>
                <td>
                  <div class="share_recipe"><a title="Tweet" target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $recipe->id;?>','Twitter')" href="https://twitter.com/share?text=<?php echo $recipe->title;?>&url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>"><img src="/images/icons/color/Twitter.png" /></a></div>
                </td>
                <td>
                  <div class="share_recipe"><a title="Pin" target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $recipe->id;?>','Pinterest')" href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>&media=http://www.savvyfork.com/<?php echo urlencode($recipe_image); ?>&description=<?php echo urlencode($recipe_title); ?>"><img src="/images/icons/color/Pinterest.png" /></a></div>
                </td>
                <td>
                  <div class="share_recipe"><a title="Plus" target="_blank" onclick="logActivity('<?php echo $user->id;?>','<?php echo $recipe->id;?>','Google+')" href="https://plus.google.com/share?url=<?php echo urlencode('http://www.savvyfork.com'.$details_url); ?>"><img src="/images/icons/color/Google+.png" /></a></div>
                </td>
              </tr>
              <tr>
                <td><div class="div_stats"><span><?php echo $recipe->rating;?></span></div></td>
                <td><div class="div_stats"><span><?php echo isset($this->socialStats[1])? $this->socialStats[1]['sharecount']:0; ?></span></div></td>
                <td><div class="div_stats"><span><?php echo isset($this->socialStats[2])? $this->socialStats[2]['sharecount']:0; ?></span></div></td>
                <td><div class="div_stats"><span><?php echo isset($this->socialStats[3])? $this->socialStats[3]['sharecount']:0; ?></span></div></td>
                <td><div class="div_stats"><span><?php echo isset($this->socialStats[4])? $this->socialStats[4]['sharecount']:0; ?></span></div></td>
              </tr>
              <tr>
                <td><div class="stats_desc">Editor<br/>Rating</div></td>
                <td><div class="stats_desc">Facebook<br/>Shares</div></td>
                <td><div class="stats_desc">Tweets</div></td>
                <td><div class="stats_desc">Pinterest<br/>Pins</div></td>
                <td><div class="stats_desc">Google+</div></td>
              </tr>
            </table>

            <div class="sp_line2"></div>
          </div> <!-- /#social_stats -->

          <div class="clear"></div>

          <div class="div-recipe-container-ingredients">
            <div id="div-recipe-ingredients-single">
              <h3><?php echo JText::_('COM_YOORECIPE_RECIPES_INGREDIENTS'); ?></h3>
              <div class="ingredients">
                <ul>
                <?php
                if (!empty($ingredients)) {
                  foreach ($ingredients as $ingredient) { ?>
                  <span class="ingredient"><li itemprop="ingredient"><?php echo $ingredient?></li></span>
                  <?php }
                }?>
                </ul>
              </div>
            </div>
          </div>

          <div style="border-bottom: 1px solid #E2E2E2;height: 20px;margin-bottom:20px;"></div>

          <div id="directions">
            <h3>Directions</h3>
            <img src="media/com_yoorecipe/images/chef-hat-icon.png"/>
            <span> Want to see the entire recipe? </span>
            <a href="<?php echo $recipe->preparation;?>" onclick="logActivity('<?php echo $user->id;?>','<?php echo $recipe->id;?>','Directions');" target="_blank"> Click here </a>

            <span style="float: right;">Published <?php echo $publish_date;?>.</span>

            <?php /* We need to track full recipe view by date so we will just log this using existing logActivity */ ?>
            <script>logActivity('<?php echo $user->id;?>','<?php echo $recipe->id;?>','FullRecipeView');</script>
          </div>
        <?php if ($use_google_recipe) : ?>
        </div><!-- /.hrecipe -->
        <?php endif; ?>
      </div><!-- /#recipe-part -->

<?php
} // End if (isset($recipe) && isset($recipe->published) && isset($recipe->validated)) {

else {
  if (isset($recipe) && ($recipe->published == 0 || $recipe->validated == 0)) { ?>
<div class="yoorecipe-ok-message"><?php echo JText::_('COM_YOORECIPE_AWAITING_VALIDATION'); ?></div>
  <?php
  }
  else {
    echo JHtml::_('yoorecipeutils.generateCategoriesList', $this->categories);
  }
}
?>
    </div><!-- /.left-detail -->

    <div id="user-part">
      <div id="partner-info">
        <div id="partner-head"><img src="<?php echo $headimage;?>" /></div>
        <div class="d_info">
          <h2 class="author"><?php echo $recipe_author->name;?></h2>
          <div class="partner_info_link">
          <?php if (isset($author_profile->profile["companyurl"]) && $author_profile->profile["companyurl"] != "") {
            $link = $author_profile->profile["companyurl"]; ?>
            <a href="http://<?php echo $link;?>" target="_blank">
              <img src="media/com_yoorecipe/images/earth.png" />
              <span><?php echo $link;?></span>
            </a>
          <?php } ?>
            <br/>

            <a href="<?php echo JRoute::_('index.php?option=com_users&view=partner&user_name='.$recipe_author->username); ?>">
              <img src="media/com_yoorecipe/images/search_small.png" />
              <span>view all <?php $recipe_count = count($this->user_recipes); echo $recipe_count;?> recipes</span>
            </a>
          </div>

          <?php
          // Sponsored Label'ID on the stg site is 15
          if (array_search(13, $recipe_author->groups) || array_search(19, $recipe_author->groups)) { ?>
          <font size="1">Sponsored</font><?php } ?>
        </div>
      </div><!-- /#partner-info -->

      <div id="right_banner">
        <br>
        <?php if (array_search(13,$recipe_author->groups)) {
          echo $author_profile->profile["rightbanner"];
        } else {
          include "templates/beez3/right_banner.php";
        }?>
      </div><!-- /#right_banner -->

      <div id="more_like">
        <h2>More <?php echo pluralTosingular($recipe->categories[0]->title);?> Recipes:</h2>
        <div id="more_list">
        <?php if (!empty($this->relevanceRecipes)) {
          foreach ($this->relevanceRecipes as $r_recipe) {
            $recipeUrl = JRoute::_('index.php?option=com_yoorecipe&task=viewRecipe&id=' . $r_recipe["slug"]); ?>
          <div><a href="<?php echo $recipeUrl;?>"><img src="<?php echo $r_recipe["picture"];?>" /></a></div>
          <?php }
        } else { ?>
          <span>No more recipes like this.</span>
        <?php }?>
          <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br> <!-- quick fix ;) -->
        </div>
      </div><!-- /#more-like -->
    </div><!-- /#user-part -->
  </div><!-- /#div-recipe-detail -->
</div><!-- /#div-yoorecipe -->

<pre>
  <div id="login-popup" class="white-popup mfp-hide" style="width:300px;margin:0 auto;background:#E8E8E8;">
    <form method="post" action="/register.html?task=registration.popupRegister" id="pop-member-registration">
      <div class="prompt">
        Already registered? <a href="" onclick="switchLogin('pop-login-form','pop-member-registration');return false;">Log in</a>
      </div>

      <fieldset>
        <legend>Join the cool table...</legend>
        <div class="subtitle">(You can save and share your favorite recipes!)</div>
        <div class="control-group">
          <div class="controls">
            <input type="email" size="30" placeholder="Email Address" value="" id="jform_email1" class="validate-email required" name="jform[email1]" aria-required="true" required="required">
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <input type="text" size="30" placeholder="Username" class="validate-username required" value="" id="jform_username" name="jform[username]" aria-required="true" required="required">
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <input type="password" size="30" placeholder="Password" class="validate-password required" autocomplete="off" value="" id="jform_password1" name="jform[password1]" aria-required="true" required="required">
          </div>
        </div>
        <div class="control-group">
          <div class="controls">
            <input type="password" size="30" placeholder="Password Confirm" class="validate-password required" autocomplete="off" value="" id="jform_password2" name="jform[password2]" aria-required="true" required="required">
          </div>
        </div>
      </fieldset>

      <div class="terms">
        <input type="checkbox" id="pop-terms" name="pop-terms" checked/><span>Yes, I agree to the SavvyFork's <a href="/index.php/terms-of-use">terms of use</a>!</span>
      </div>
      <div class="form-actions">
        <input class="btn btn-primary validate" type="submit" value="Submit">
        <input type="hidden" value="com_users" name="option">
        <input type="hidden" value="registration.popupRegister" name="task">
        <input type="hidden" value="popup" name="where">
        <?php echo JHtml::_('form.token');?>
      </div>
    </form>

    <form class="form-inline" id="pop-login-form" method="post" action="/login.html" style="display:none;">
      <div class="prompt">
        New user? Please <a href="" onclick="switchLogin('pop-member-registration','pop-login-form');return false;">Register</a>
      </div>
      <fieldset>
        <legend>Login</legend>
        <div class="control-group" id="form-login-username">
          <div class="controls" style="margin-top:18px;">
            <input type="text" placeholder="Username" size="18" tabindex="1" class="input-small validate-username required" name="username" id="modlgn-username" aria-required="true" required="required">
          </div>
        </div>
        <div class="control-group" id="form-login-password">
          <div class="controls">
            <input type="password" placeholder="Password" size="18" tabindex="2" class="input-small validate-password required" name="password" id="modlgn-passwd" aria-required="true" required="required">
          </div>
        </div>

        <div class="control-group checkbox" id="form-login-remember">
          <label class="control-label" for="modlgn-remember">Remember Me</label>
          <input type="checkbox" value="yes" class="inputbox" name="remember" id="modlgn-remember">
          <input class="btn btn-primary btn" name="Submit" tabindex="3" type="submit" value="Login">
        </div>
      </fieldset>
      <input type="hidden" value="com_users" name="option">
      <input type="hidden" value="user.login" name="task">
      <input type="hidden" value="aW5kZXgucGhwP0l0ZW1pZD0yMDE=" name="return">
      <?php echo JHtml::_('form.token'); ?>
    </form>
  </div>
</pre>
