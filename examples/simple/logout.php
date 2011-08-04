<?php
require_once('../../config.php');
require_once('../../src/mixi.php');

$mixi = new Mixi(array(
    'consumer_key' => CONSUMER_KEY,
    'consumer_secret' => CONSUMER_SECRET,
    'scope' => SCOPE,
    'display' => DISPLAY,
    'redirect_uri' => REDIRECT_URL
));

$mixi->clearAllAppData();

echo "logout done.";
?>
