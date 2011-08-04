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
