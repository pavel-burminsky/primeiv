<?php
/**
 * Template Name: Clickfunnel Full Page Iframe
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

$iframe_url = '';
if( function_exists('get_field') ) {
    $iframe_url = get_field('clickfunnel_page_url');
}
?>
<!DOCTYPE html>
<head>
    <style>
        body {
            margin: 0;
        }
        iframe {
            display: block;
            border: none;
            height: 100vh;
            width: 100vw;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>

<script type="text/javascript">
  var updateMeta = function (event) {
    var metatags = event.data.meta;
    if (metatags) {
      document.querySelectorAll('head')[0].insertAdjacentHTML('afterbegin', metatags);
      enforceWPIcon();
    }
  };

  var enforceWPIcon = function () {
    var wpIcon = document.querySelectorAll('.wp_favicon')[0]
    if (wpIcon) {
      var funnelIcon = document.querySelectorAll('[rel="icon"]')[0];
      funnelIcon.parentNode.removeChild(funnelIcon);
    }
  }

  if (window.addEventListener) {
    window.addEventListener("message", updateMeta, false);
  } else if (window.attachEvent) {
    window.attachEvent("onmessage", updateMeta);
  }
</script>
<?php
if( $iframe_url ) {
    ?>
    <iframe width="100%" height="100%" src="<?php echo esc_url($iframe_url); ?>" frameborder="0" allowfullscreen></iframe>
    <?php
}
?>
</body>
</html>