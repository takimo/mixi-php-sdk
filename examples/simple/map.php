<?php

require_once('../../config.php');
require_once('../../src/mixi.php');
require_once('LightningTemplate.php');

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

$data = $mixi->api('/checkins/@me/@friends');

$lt = new LightningTemplate('map.html');
$lt->title = 'Example Simple version.';
$lt->data = json_encode($data);
echo $lt;

?>
