<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.beez3
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

JLoader::import('joomla.filesystem.file');

// Check modules
$showRightColumn = ($this->countModules('position-3') or $this->countModules('position-6') or $this->countModules('position-8'));
$showbottom      = ($this->countModules('position-9') or $this->countModules('position-10') or $this->countModules('position-11'));
$showleft        = ($this->countModules('position-4') or $this->countModules('position-7') or $this->countModules('position-5'));

if ($showRightColumn == 0 and $showleft == 0) {
  $showno = 0;
}

JHtml::_('behavior.framework', true);

// Get params
$color = $this->params->get('templatecolor');
$logo = $this->params->get('logo');
$navposition = $this->params->get('navposition');
$headerImage = $this->params->get('headerImage');
$app = JFactory::getApplication();
$doc = JFactory::getDocument();
$user = JFactory::getUser();
$templateparams = $app->getTemplate(true)->params;
$config = JFactory::getConfig();

$bootstrap = explode(',', $templateparams->get('bootstrap'));
$jinput = JFactory::getApplication()->input;
$option = $jinput->get('option', '', 'cmd');
$view = $jinput->get('view', '', 'cmd');
$itemId = $jinput->get('id', '', 'STRING');
if ($itemId =='') { $itemId = $jinput->get('catid', '', 'STRING'); }  // if itemid is not set, get category id

if (in_array($option, $bootstrap)) {
  // Load optional rtl Bootstrap css and Bootstrap bugfixes
  JHtmlBootstrap::loadCss($includeMaincss = true, $this->direction);
}

$doc->addStyleSheet(JURI::base() . 'templates/system/css/system.css');
$doc->addStyleSheet(JURI::base() . 'templates/' . $this->template . '/css/position.css', $type = 'text/css', $media = 'screen,projection');
$doc->addStyleSheet(JURI::base() . 'templates/' . $this->template . '/css/layout.css', $type = 'text/css', $media = 'screen,projection');
$doc->addStyleSheet(JURI::base() . 'templates/' . $this->template . '/css/print.css', $type = 'text/css', $media = 'print');
$doc->addStyleSheet(JURI::base() . 'templates/' . $this->template . '/css/general.css', $type = 'text/css', $media = 'screen,projection');
$doc->addStyleSheet(JURI::base() . 'templates/' . $this->template . '/css/' . htmlspecialchars($color) . '.css', $type = 'text/css', $media = 'screen,projection');

if ($this->direction == 'rtl') {
  $doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/template_rtl.css');
  if (file_exists(JPATH_SITE . '/templates/' . $this->template . '/css/' . $color . '_rtl.css')) {
    $doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/' . htmlspecialchars($color) . '_rtl.css');
  }
}

JHtml::_('bootstrap.framework');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/md_stylechanger.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/hide.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/respond.src.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/logactivity.js', 'text/javascript');
$doc->addScript($this->baseurl . '/templates/' . $this->template . '/javascript/docready.js', 'text/javascript');

$menu = $app->getMenu();

$viewArray = array('userdetail','recipe','partner','profile');
if ($menu->getActive() != $menu->getDefault() && !in_array($view,$viewArray)) {
  $doc->setTitle($doc->getTitle(). ' | SavvyFork | The Best Looking Recipes On The Web');
}
?>

<!DOCTYPE html>
<html class="no-touch csstransitions" lang="en-US" xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >
<head>

  <?php include 'partials/ads/adthrive_js.php'; ?>

  <?php require __DIR__ . '/jsstrings.php';?>
  <meta name="google-site-verification" content="Icf4sVy0OPI9T4b9qcOV9e9wC7ofSMZImJ3l_SJCnBM" />

  <jdoc:include type="head" />
  <link href='http://fonts.googleapis.com/css?family=PT+Sans' rel='stylesheet' type='text/css'>
  <!--[if IE 7]>
  <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template; ?>/css/ie7only.css" rel="stylesheet" type="text/css" />
  <![endif]-->
  <style>
    .ipad_table {
      width:300px !important;
      border: none !important;
    }
    .ipad_table tr {
      border:none !important;
      line-height:12px !important;
    }
    .ipad_table td {
      border:none !important;
      height:20px !important;
      padding:0px !important;
    }
    .ipad_logo {
      margin-left:2px !important;
    }
  </style>

  <script>
    function onHover(img,hover) {
      document.getElementById(img).src= '<?php echo $this->baseurl; ?>/images/'+hover;
    }

    function offHover(img,hover) {
      document.getElementById(img).src = '<?php echo $this->baseurl; ?>/images/'+hover;
    }
    document.ready(function() {
      $images = document.getElementsByTagName("img");

      for (var i = 0; i < $images.length; i++) {
        var attribute = document.createAttribute("Itemprop");
        attribute.nodeValue = "Image";
        $images[i].setAttributeNode(attribute);
      }
      var mobile = (/iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()));
      if (mobile) {
        var userAgent = navigator.userAgent.toLowerCase();

        if(userAgent.search("ipad") > -1) {
          document.getElementById('top_table').className = "ipad_table";
          document.getElementById('logo').className = "ipad_logo";
        }
      }
    });
  </script>

  <?php
  if (false != strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) { ?>
  <style type="text/css">
    @media only screen and (max-width: 1366px),(min-device-width: 768px) and (max-device-width: 1024px) {
      .header_right_top td:last-child {
        width:31% !important;
      }
    }
  </style>
  <?php }?>

</head>

<body id="shadow">
  <!-- Google Tag Manager -->
  <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PFRPS8"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','dataLayer','GTM-PFRPS8');</script>
  <!-- End Google Tag Manager -->

  <iframe height="0" width="0" frameborder="0"></iframe>
  <?php if ($color == 'image'): ?>
    <style type="text/css">
      .logoheader {
        background:url('<?php echo $this->baseurl . '/' . htmlspecialchars($headerImage); ?>') no-repeat right;
      }
      body {
        background: <?php echo $templateparams->get('backgroundcolor'); ?>;
      }
    </style>
  <?php endif; ?>

  <div id="all">
    <div id="back">
      <div id="header">
        <div class="logoheader">
          <div id="logo">
            <?php if ($logo) : ?>
            <a href='<?php echo JURI::base(); ?>'><img src="<?php echo $this->baseurl ?>/<?php echo htmlspecialchars($logo); ?>"  alt="<?php echo htmlspecialchars($templateparams->get('sitetitle'));?>" /></a>
            <?php endif;?>

            <?php if (!$logo AND $templateparams->get('sitetitle')) : ?>
              <?php echo htmlspecialchars($templateparams->get('sitetitle'));?>
            <?php elseif (!$logo AND $config->get('sitename')) : ?>
              <?php echo htmlspecialchars($config->get('sitename'));?>
            <?php endif; ?>
          </div>

          <table class="header_right" id="top_table">
            <tr class="header_right_top">
              <td>
                <div class="partner">
                  <a href="<?php echo $this->baseurl ?>/index.php/partner-list">OUR PARTNERS<!--<img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/partner.png" />--></a>
                </div>

                <div class="partner" style="left:130px;width:157px;width:162px\0;left:134px\0;">
                  <a href="<?php echo $this->baseurl ?>/partner-register">BECOME A PARTNER<!--<img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/become_a_partner.png" />--></a>
                </div>
              </td>
              <td style="width:34%;width:26%\0;">
                <div id="login">
                  <?php if ($user->id == 0 || empty($user->username)) { ?>
                    <a href="./index.php/register" title="Save your favorite recipes!">Register</a>
                    <span class="hspline"></span>
                    <a href="./index.php/login">Sign In</a>
                  <?php } else {
                    $return = base64_encode($this->baseurl);
                    $url = JRoute::_('index.php?option=com_users&task=user.logout&' . JSession::getFormToken() . '=1&return=' . $return, false);
                    ?>
                      Welcome <a href="<?php echo JRoute::_('index.php?option=com_users&view=profile');?>"><?php echo $user->name;?></a>
                      <span class="hspline"></span>
                      <a href="<?php echo $url; ?>">Sign Out</a>
                  <?php } ?>
                </div>
              </td>
            </tr><!--header_right_top end-->

            <tr class="header_right_bottom">
              <td>
                <div id="navmenu">
                  <jdoc:include type="modules" name="position-categories" />

                  <?php if ($_SERVER['PHP_SELF'] == '/index.php/latest' && $itemId !='') { ?>
                  <div id="latest_btn" style="background-color:#cacaca;">
                  <?php } else { ?>
                  <div id="latest_btn">
                  <?php } ?>
                    <a href="/index.php/latest<?php if ($option=="com_yoorecipe") { echo '?catid='/*.$itemId*/; } ?>">
                      <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/personal/latest.png"/>

                      <?php if ($_SERVER['PHP_SELF'] == '/index.php/latest' && $itemId !='') { ?>
                      <span style="color:#ffffff;">Latest</span>
                      <?php } else { ?>
                      <span>Latest</span>
                      <?php } ?>
                    </a>
                  </div>

                  <?php if ($_SERVER['PHP_SELF'] == '/index.php/most-popular') { ?>
                  <div id="popular_btn" style="background-color:#cacaca;">
                  <?php } else { ?>
                  <div id="popular_btn">
                  <?php } ?>
                    <a href="/index.php/most-popular<?php if ($option=="com_yoorecipe") { echo '?catid=' ;} ?>">
                      <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/personal/mostpopular.png"/>

                      <?php if ($_SERVER['PHP_SELF'] == '/index.php/most-popular') { ?>
                      <span style="color:#ffffff;">Most Shared</span>
                      <?php } else { ?>
                      <span>Most Shared</span>
                      <?php } ?>
                    </a>
                  </div>

                  <?php if ($_SERVER['PHP_SELF'] == '/index.php/editor-rating') { ?>
                  <div id="editorrating_btn" style="background-color:#cacaca;">
                  <?php } else { ?>
                  <div id="editorrating_btn">
                  <?php } ?>
                    <a href="/index.php/editor-rating<?php if($option=="com_yoorecipe") {echo '?catid=' ;} ?>">
                      <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/personal/mostpopular.png"/>

                      <?php if ($_SERVER['PHP_SELF'] == '/index.php/editor-rating') { ?>
                      <span style="color:#ffffff;">Editor Rating</span>
                      <?php } else { ?>
                      <span>Editor Rating</span>
                      <?php } ?>
                    </a>
                  </div>

                  <?php if ($_SERVER['PHP_SELF'] == '/index.php/favorites') { ?>
                  <div id="favorite_btn" style="background-color:#cacaca;">
                  <?php } else { ?>
                  <div id="favorite_btn">
                  <?php } ?>
                    <a href="/index.php/favorites">
                      <img src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/images/personal/favstar.png"/>

                      <?php if ($_SERVER['PHP_SELF'] == '/index.php/favorites') { ?>
                      <span style="color:#ffffff;">Favorites</span>
                      <?php } else { ?>
                      <span>Favorites</span>
                      <?php } ?>
                    </a>
                  </div>
                </div> <!--Nav menu end-->
              </td>

              <td>
                <jdoc:include type="modules" name="position-0" />
              </td>
            </tr><!--header_right_bottom end-->
          </table> <!--header_right end-->
        </div><!-- end logoheader -->
      </div><!-- end header -->

      <div id="<?php echo $showRightColumn ? 'contentarea2' : 'contentarea'; ?>">
        <div id="breadcrumbs">
          <jdoc:include type="modules" name="position-2" />
        </div>

        <?php if ($navposition == 'left' and $showleft) : ?>
          <nav class="left1 <?php if ($showRightColumn == null){ echo 'leftbigger';} ?>" id="nav">
            <jdoc:include type="modules" name="position-7" style="beezDivision" headerLevel="3" />
            <jdoc:include type="modules" name="position-4" style="beezHide" headerLevel="3" state="0 " />
            <jdoc:include type="modules" name="position-5" style="beezTabs" headerLevel="2"  id="3" />
          </nav><!-- end navi -->
        <?php endif; ?>

        <div id="<?php echo $showRightColumn ? 'wrapper' : 'wrapper2'; ?>" <?php if (isset($showno)) { echo 'class="shownocolumns"'; } ?>>

        <?php
        $mainClass = "";

        if ($menu->getActive() != $menu->getDefault() || $view=='recipe') {
          $mainClass = 'class = "lockedmain"';
        } ?>
          <div id="main" <?php echo $mainClass; ?>>

            <?php if ($this->countModules('position-12')) : ?>
            <div id="top"><jdoc:include type="modules" name="position-12" /></div>
            <?php endif; ?>

            <?php if ($this->countModules('position-1')) : ?>
            <jdoc:include type="modules" name="position-1" />
            <?php endif; ?>

            <jdoc:include type="message" />

            <jdoc:include type="component" />

            <?php if ($this->countModules('position-partner')) : ?>
            <div id="top"><jdoc:include type="modules" name="position-partner" /> </div>
            <?php endif; ?>
          </div><!-- end main -->

        </div><!-- end wrapper -->

        <?php if ($showRightColumn) : ?>
        <aside id="right">
          <a id="additional"></a>
          <jdoc:include type="modules" name="position-6" style="beezDivision" headerLevel="3" />
          <jdoc:include type="modules" name="position-8" style="beezDivision" headerLevel="3" />
          <jdoc:include type="modules" name="position-3" style="beezDivision" headerLevel="3" />
        </aside><!-- end right -->
        <?php endif; ?>

        <?php if ($navposition == 'center' and $showleft) : ?>
        <nav class="left <?php if ($showRightColumn == null){ echo 'leftbigger';} ?>" id="nav" >
          <jdoc:include type="modules" name="position-7"  style="beezDivision" headerLevel="3" />
          <jdoc:include type="modules" name="position-4" style="beezHide" headerLevel="3" state="0 " />
          <jdoc:include type="modules" name="position-5" style="beezTabs" headerLevel="2"  id="3" />
        </nav><!-- end navi -->
        <?php endif; ?>

        <?php if ($option=="com_yoorecipe" && $view == "recipe") {
          $db = JFactory::getDBO ();
          $sql = "SELECT created_by FROM #__yoorecipe WHERE id =".(int)$itemId;
          $db->setQuery($sql);
          $userId = $db->loadResult();
          $recipe_author = JFactory::getUser($userId);
          $author_profile = JUserHelper::getProfile($userId);

        } ?>

          <div style="clear: both;margin-bottom: 20px"></div>

        <div class="wrap"></div>
      </div> <!-- end contentarea -->
    </div><!-- back -->
  </div><!-- all -->

  <div id="footer-outer">
    <div id="footer-inner" >
      <div id="footer-left">
        <div id="aboutus">
        <?php
          $userGroups = $user->getAuthorisedGroups();
          if ($user->id != 0 && array_search('11',$userGroups) !== false) {
            $add_link = '/index.php?option=com_yoorecipe&task=import';
          } else {
            $add_link = JRoute::_('index.php?option=com_contactenhanced&view=contact&id=31');
          }
        ?>
          <a href="<?php echo $add_link; ?>" style="color:#0A8E36">Add your recipe!</a>
          <a href="/index.php/about-us">About us</a>
          <a href="/SavvyForkMediaKit102013.pdf">Media Kit</a>
          <a href="<?php echo JRoute::_('index.php?option=com_contactenhanced&view=contact&id=31'); ?>">Contact us</a>
        </div>

        <div id="followus">
          <span>Follow us on:</span>
          <a title="Share" href="http://on.fb.me/13QG1Cw"  onmouseover="onHover('fb_img','fb-green.png');" onmouseout="offHover('fb_img','fb-grey.png');" target="_blank">
            <img id="fb_img" src="<?php echo $this->baseurl; ?>/images/fb-grey.png" alt="logo"/>
            <span>Facebook</span>
          </a>
          <a title="Tweet" href="http://bit.ly/133UR9X" onmouseover="onHover('tw_img','tw-green.png');" onmouseout="offHover('tw_img','tw-grey.png');" target="_blank">
            <img id="tw_img" src="<?php echo $this->baseurl; ?>/images/tw-grey.png" alt="logo" />
            <span>Twitter</span>
          </a>
          <a title="Pin" href="http://bit.ly/10Y7vss" onmouseover="onHover('pin_img','pin-green.png');" onmouseout="offHover('pin_img','pin-grey.png');" target="_blank">
            <img id="pin_img" src="<?php echo $this->baseurl; ?>/images/pin-grey.png" alt="logo" />
            <span>Pinterest</span>
          </a>
          <a title="Google+" href="http://bit.ly/17Shlvw" onmouseover="onHover('google_img','google-green.png');" onmouseout="offHover('google_img','google-grey.png');" target="_blank">
            <img id="google_img" src="<?php echo $this->baseurl; ?>/images/google-grey.png" alt="logo" />
            <span>Google+</span>
          </a>
        </div>

        <div id="footer-sub" style="font-size:12px;"><br>
          <span>&copy; <?php echo date('Y'); ?>
            <a href="http://www.junemedia.com/" target="_blank" style="font-size:12px;">June Media Inc.</a> All rights reserved. All third party content &copy; their respective owners.
            <a href="/index.php/terms-of-use" style="font-size:12px;">Terms of Use</a> |
            <a href="/index.php/privacy-policy" style="font-size:12px;">Privacy Policy</a> |
            <!--<a href='/subctr' style="font-size12px;">Manage My Newsletters</a>-->
          </span><br><br><br><br>
        </div>
      </div>
    </div>
  </div>

  <jdoc:include type="modules" name="debug" />
  <?php if ($menu->getActive() != $menu->getDefault() || $view=='recipe') { ?>
  <script>
    if (screen.width>=1280) {
      var main = document.getElementById("main");
      main.className = main.className + " lockmainwidth";
    }
  </script>
  <?php }?>

  <script>
    function hideItem(divID) {
      refID = document.getElementById(divID);
      refID.style.display = "none";
      if (readCookie('nlcookie') != true) {
        createCookie('nlcookie', true, 7);
      }
    }
    function createCookie(name,value,days) {
      var date = new Date();
      date.setTime(date.getTime() + (days*24*60*60*1000));
      var expires = "; expires=" + date.toGMTString();
      document.cookie = name + "=" + value+expires + "; path=/";
    }
    function readCookie(name) {
      var nameEQ = name + "=";
      var ca = document.cookie.split(';');
      for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
      }
      return ca;
    }
  </script>

  <style>
    #newsletter-signup-tray{
      font-family:'Open Sans', sans-serif;
      background-color:green;
      position:fixed;
      left:0px;
      right:0px;
      bottom:0px;
      width:100%;
      height:50px;
      z-index:12345;
      -webkit-box-shadow:0px -3px 8px 0px #777;
          -moz-box-shadow:0px -3px 8px 0px #777;
              box-shadow:0px -3px 8px 0px #777;
    }
  </style>

  <div id="newsletter-signup-tray" align="center">
    <table align="center" width="80%" border="0">
      <tr>
        <td width="80%" style="text-align:right;" nowrap="nowrap">
          <iframe src="/mcapi/index.php" width="950px" frameborder="0" scrolling="No" align="center" height="50px"></iframe>
        </td>
        <td width="20px" style="text-align:left;">
          <input type="button" value="Close" onclick="hideItem('newsletter-signup-tray');" id="newsletter_close" style="border:0;">
        </td>
      </tr>
    </table>
  </div>

  <script>
    if (readCookie('nlcookie') == 'true') { hideItem('newsletter-signup-tray'); }
  </script>

  <?php if (isset($_GET['dhtml']) && strtoupper(trim($_GET['dhtml'])) == 'Y') { include_once("dhtml.php"); } ?>

  <?php include 'partials/ads/underdog.php'; ?>
  <?php include 'partials/ads/liveconnect.php'; ?>

</body>
</html>
