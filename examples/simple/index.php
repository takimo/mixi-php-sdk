<?php
require_once('../../config.php');
require_once('../../src/mixi.php');

$consumer_key = $_SERVER['HTTP_X_FLX_CONSUMER_KEY'],
$consumer_secret = $_SERVER['HTTP_X_FLX_CONSUMER_SECRET'],

$mixi = new Mixi(array(
    'consumer_key' => (array_key_exists('HTTP_X_FLX_CONSUMER_KEY', $_SERVER))  ? $_SERVER['HTTP_X_FLX_CONSUMER_KEY'] : CONSUMER_KEY,
    'consumer_secret' => (array_key_exists('HTTP_X_FLX_CONSUMER_SECRET', $_SERVER))  ? $_SERVER['HTTP_X_FLX_CONSUMER_SECRET'] : CONSUMER_SECRET,
    'scope' => SCOPE,
    'display' => DISPLAY,
    'redirect_uri' => REDIRECT_URL
));

var_dump($_SERVER);


?>
