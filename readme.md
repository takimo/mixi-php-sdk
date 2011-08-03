mixi PHP SDK
==========================
http://developer.mixi.co.jp/connect/mixi_graph_api/mixi_io_spec_top/

Usage
-----
create mixi instance
    $mixi = new Mixi(array(
        'consumer_key' => 'e8fe2375a8b9ece',
        'consumer_secret' => '5a89e11081a384ec0473',
        'scope' => 'r_profile w_voice r_voice',
        'display' => 'pc',
        'redirect_uri' => 'http://example.com/callback'
    ));

get user id
    $mixi->getUser();

get user(viewer) data
    $mixi->api('/people/@me/@self');

get voice friends timeline (recnt_voice.pl)
    $mixi->api('/voice/statuses/friends_timeline');

post voice
    $mixi->api('/voice/statuses', 'POST, array(
        "status" => "こんにちわ、世界"
    ));
