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

// get user id
$user = $mixi->getUser(true);

$update = $mixi->api('/updates/@me/@self', array('count' => 3));

$profile = $mixi->api('/people/@me/@self');

$checkin = $mixi->api('/checkins/@me/@friends', array('count' => 3));

// get voice timeline
$voice = $mixi->api('/voice/statuses/friends_timeline', array('count' => 3));

// get message inbox
$message = $mixi->api('/messages/@me/@inbox/', array('count' => 3));

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

$lt = new LightningTemplate('example.html');
$lt->title = 'Example Simple version.';
$lt->user = json_encode($user);
$lt->profile = json_encode($profile);
$lt->update = json_encode($update);
$lt->voice = json_encode($voice);
$lt->checkin = json_encode($checkin);
$lt->message = json_encode($message);
echo $lt;

?>
