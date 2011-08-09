<?php

$API_ENDPOINT = 'http://api.mixi-platform.com';
$API_VERSION = '2';

$TOKEN_URL = "https://secure.mixi-platform.com/2/token";
$TOKEN_RESPONSE = '{"refresh_token":"4e8170d1b723a849bdce2c8a60c1686","expires_in":900,"access_token":"4a06e550bb601a7ed3cc454ec7","scope":"r_checkin r_message r_profile r_updates r_voice w_photo w_voice"}';

$PEOPLE_ME_SELF_URL = $API_ENDPOINT . "/" . $API_VERSION . "/people/@me/@self";
$PEOPLE_ME_SELF_RESPONSE = '{"entry":{"thumbnailUrl":"http:\/\/profile.img.mixi.jp\/photo\/member\/p\/56\/f1\/56f195e74fury44c8ff9c751a3b86ff49.jpg","id":"6syksi5ctrp","displayName":"\u3082\u3082","profileUrl":"http:\/\/mixi.jp\/show_friend.pl?uid=6syksi5ctrp"},"startIndex":0,"itemsPerPage":1,"totalResults":1}';

$RESPONSE_DATAS = array(
    $TOKEN_URL => $TOKEN_RESPONSE,
    $PEOPLE_ME_SELF_URL => $PEOPLE_ME_SELF_RESPONSE
);

?>
