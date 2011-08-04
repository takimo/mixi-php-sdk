<?php
require_once('../../config.php');
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

// get user id
var_dump($mixi->getUser(true));

// get voice timeline
var_dump($mixi->api('/voice/statuses/friends_timeline'));

// get message inbox
var_dump($mixi->api('/messages/@me/@inbox/'));

/*
// post voice
var_dump($mixi->api('/voice/statuses', 'POST', array(
    "status" => "こんにちは、世界!"
)));
*/

/*
// post photo
function loadImage($file_path)
{
    $fp = fopen($file_path,'rb');
    $size = filesize($file_path);
    $img  = fread($fp, $size);
    fclose($fp);
    return $img;
}

$file_path = "sample.jpeg";
$info = GetImageSize("sample.jpeg");
$result = $mixi->api('/photo/mediaItems/@me/@self/@default', 'POST',
    loadImage('sample.jpeg'),
    array(
        'Content-type: '.$info["mime"]
    )
);
*/

?>
