<?php
require_once('../../config.php');
require_once('../../src/mixi.php');

$consumer_key = (array_key_exists('HTTP_X_FLX_CONSUMER_KEY', $_SERVER))  ? $_SERVER['HTTP_X_FLX_CONSUMER_KEY'] : CONSUMER_KEY;
$consumer_secret = (array_key_exists('HTTP_X_FLX_CONSUMER_SECRET', $_SERVER))  ? $_SERVER['HTTP_X_FLX_CONSUMER_SECRET'] : CONSUMER_SECRET;

var_dump($_SERVER);
var_dump("====");
var_dump($consumer_key);
var_dump($consumer_secret);

$mixi = new Mixi(array(
    'consumer_key' => $consumer_key,
    'consumer_secret' => $consumer_secret,
    'scope' => SCOPE,
    'display' => DISPLAY,
    'redirect_uri' => REDIRECT_URL
));

?>
