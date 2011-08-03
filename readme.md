mixi PHP SDK
==========================
[mixi Platform](http://developer.mixi.co.jp/)はソーシャルグラフや各種機能のAPIを提供し、Webサービスやデバイスなどに組み込み、新しいプラダクトを作ることが出来ます。

mixi PHP SDKはウェブサーバーフローを使った認証認可の処理やリフレッシュトークンを使用したアクセストークンの再発行の処理をSDK側で吸収してくれます。

mixi Graph API
-----
各種APIのドキュメントは下記のURLを御覧ください。
http://developer.mixi.co.jp/connect/mixi_graph_api/mixi_io_spec_top/

Usage
-----

create mixi instance

    $mixi = new Mixi(array(
        'consumer_key' => 'e8fe2375a8b9ece',
        'consumer_secret' => '5a89e11081a384ec0473',
        'scope' => 'r_profile w_voice r_voice',
        'display' => 'pc',
        'redirect_uri' => 'http://example.com/callback.php'
    ));

mixiGraphAPIは認証フローとして[ウェブサーバーフロー][webserverflow]を採用しています。
ユーザーが認可をすると事前に指定したURLにリダイレクトされます。
その際、クエリパラメータとしてcodeパラメータが付与されるのでサーバー側にリダイレクトを受け取る処理を準備する必要があります。

    // example.com/callback.php
    $code = $_GET["code"];
    if($code) $mixi->onReceiveAuthorizationCode($code);

[webserverflow]: http://openid-foundation-japan.github.com/draft-ietf-oauth-v2.ja.html#anchor6

get user id

    $mixi->getUser();

get user(viewer) data

    $mixi->api('/people/@me/@self');

get voice friends timeline (recnt_voice.pl)

    $mixi->api('/voice/statuses/friends_timeline');

post voice

    $mixi->api('/voice/statuses', 'POST', array(
        "status" => "こんにちわ、世界"
    ));
