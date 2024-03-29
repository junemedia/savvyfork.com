<?php

$creative = strtolower(trim($_GET['c']));
$email = strtolower(trim($_GET['e']));

if (!eregi("^[A-Za-z0-9\._-]+[@]{1,1}[A-Za-z0-9-]+[\.]{1}[A-Za-z0-9\.-]+[A-Za-z]$", $email)) { $email = ''; }
list($prefix, $domain) = split("@",$email);
if (!getmxrr($domain, $mxhosts)) { $email = ''; }

$querystring = "?email=$email";


switch ($creative) {
	case 'bread':
		$url = '/mcapi/bread.php';
		break;
	case 'chocolate':
		$url = '/mcapi/chocolate.php';
		break;
	default:
		$rand = rand(1, 2);
		if ($rand == 1) {
			$url = '/mcapi/bread.php';
		}
		if ($rand == 2) {
			$url = '/mcapi/chocolate.php';
		}
}

?>
<?php if ($email != '') { ?>
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/jquery.fancybox-1.3.4.pack.js"></script>
<script type="text/javascript" src="/js/jquery.cookie.js"></script>
<script type="text/javascript">
	var SFdhtml = jQuery.noConflict();
</script>
<style>
#fancybox-overlay { position:absolute;top:0;left:0;width:100%;z-index:1100;display:none; }
#fancybox-wrap { position:absolute;z-index:1101;outline:none;display:none;position:absolute;top:230px !important; }
#fancybox-outer { position:relative;width:100%;height:100%;background:#fff;border-top-left-radius:15px;border-bottom-right-radius:15px;border-top-right-radius:15px;border-bottom-left-radius:15px; }
#fancybox-frame { width:100%;height:100%;border:none;display:block; }
#fancybox-close { position:absolute;top:-15px;right:-15px;width:30px;height:30px;cursor:pointer;z-index:1103;display:none;background:transparent url('/images/fancy_close.png'); }
</style>
<script>
function closethis() {
	SFdhtml.fancybox.close();
}
</script>
<script type="text/javascript">
	function callFancyBoxiFrame() {
		SFdhtml(document).ready(function() {
			SFdhtml.fancybox({
				'width'					: '43',
				'height'				: '41',
				'autoScale'				: false,
				'transitionIn'			: 'elastic',
				'transitionOut'			: 'elastic',
				'type'					: 'iframe',
				'scrolling'				: 'no',
				'padding'				: 0,
				'hideOnOverlayClick'	: false,
				'href'					: '<?php echo $url; ?><?php echo $querystring; ?>',
				'overlayColor'			: '#000',
				'overlayOpacity'		: '.30',
				'showCloseButton'		: true
			});
		});
	}
	window.setTimeout("callFancyBoxiFrame();", 10000);
</script>
<?php } ?>
