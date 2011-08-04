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
