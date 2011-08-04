<?php

require_once('../../config.php.back');
require_once('../../src/mixi.php');
require_once('OtokomaeTemplate.php');
$TEMPLATE_DIR    = 'templates';
$LAYOUT_TEMPLATE = 'layout.php';
$context = array('list'=>array(10,'<A&amp;B>',NULL));
include_template('template.php', $context);

/*

$consumer_key = (array_key_exists('HTTP_X_FLX_CONSUMER_KEY', $_SERVER)) ? $_SERVER['HTTP_X_FLX_CONSUMER_KEY'] : CONSUMER_KEY;
$consumer_secret = (array_key_exists('HTTP_X_FLX_CONSUMER_SECRET', $_SERVER)) ? $_SERVER['HTTP_X_FLX_CONSUMER_SECRET'] : CONSUMER_SECRET;
$redirect_url = (array_key_exists('HTTP_X_FLX_REDIRECT_URL', $_SERVER)) ? $_SERVER['HTTP_X_FLX_REDIRECT_URL'] : REDIRECT_URL;

$mixi = new Mixi(array(
    'consumer_key' => $consumer_key,
    'consumer_secret' => $consumer_secret,
    'scope' => SCOPE,
    'display' => DISPLAY,
    'redirect_uri' => $redirect_url
));
*/
var_dump($mixi->api('/checkins/@me/@friends'));

  var image = 'beachflag.png';
  var myLatLng = new google.maps.LatLng(-33.890542, 151.274856);
  var beachMarker = new google.maps.Marker({
      position: myLatLng,
      map: map,
      icon: image
  });

echo '<html>
<head>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=set_to_true_or_false"></script>
<script type="text/javascript">
  function initialize() {
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  }

</script>
</head>
<body onload="initialize()">
  <div id="map_canvas" style="width:100%; height:100%"></div>
</body>
</html>';

?>
