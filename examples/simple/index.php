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

// get profile
$profile_images = $mixi->api('/people/images/@me/@self', array('privacy' => 'everyone'));

/*
// post diary (type application/json)
$post_diary =  $mixi->api('/diary/articles/@me/@self',
    'POST',
    json_encode(array(
        "title" => "日記のタイトル",
        "body" => "日記の本文",
        "privacy" => array(
            "visibility" => "self",
        )
    )),
    array('Content-Type' => 'application/json')
);
*/

/*
// post diary (type multipart/form-data)
$post_diary = $mixi->api('/diary/articles/@me/@self',
    'POST',
    array(
        "request" => json_encode(array(
            "title" => "日記のタイトル",
            "body" => "日記の本文",
            "privacy" => array(
                "visibility" => "self",
            )
        )),
        "photo1" => "@sample.jpeg"
    ),
    array('Content-Type' => 'multipart/form-data')
);
*/

/*
// change profile image
$profile_image_change = $mixi->api(
    '/people/images/@me/@self/' . $profile_images->entry[0]->id,
    'PUT',
    json_encode(
        array(
            "privacy" => "everyone",
            "primary" => "true"
        )
    ),
    array('Content-Type' => 'application/json')
);
var_dump($profile_image_change);
*/

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
$lt->profile_image = json_encode($profile_images);
echo $lt;

?>
