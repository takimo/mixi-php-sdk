<?php
require_once('../../config.php.back');
require_once('../../src/mixi.php');

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

$code = $_GET["code"];

if($code){
    $mixi->onReceiveAuthorizationCode($code);
    $script_name = $_SERVER["SCRIPT_NAME"];
    $paths = preg_split('/\//', $script_name);
    $paths[count($paths) - 1] = "index.php";
    $path = join('/', $paths);
    header('Location: http://'.$_SERVER["HTTP_HOST"].$path);
}

?>
