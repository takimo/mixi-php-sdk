mixi PHP SDK
==========================
[mixi Platform](http://developer.mixi.co.jp/)はソーシャルグラフや各種機能のAPIを提供し、Webサービスやデバイスなどに組み込み、新しいプロダクトを作ることが出来ます。

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

post photo

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

Required
-----
1. curlが動作するPHP環境
2. Consumer Key,Consumer Secretの用意
    [mixiのDeveloper登録、ならびにmixiGraphAPIのサービス追加](http://developer.mixi.co.jp/connect/mixi_graph_api/services/)を行い、

AppData
-----
ユーザーの認証認可の情報を操作するためのmethodはmixi_graph_api.phpで抽象化クラスとして定義されています。
初期では$_SESSIONに保存、読み込みするようになっています。
ですが長期にアプリケーションを利用してもらう場合、データの保存にはデータベースなどに保存・読み込みするように処理をオーバーライドすることが可能です。

fluxflex
-----
このプロジェクトは[fluxflex](https://www.fluxflex.com/)に対応しています。

1.Github importにプロジェクトのURL(or git schema)を入力してください。

2.setup -> Initialize Scriptsに移り、SetEnv Variablesに以下のような記述でconsumer key,consumer secret,redirect urlを記入してください

    CONSUMER_KEY    e8fe2375a8b9ece(your consumer key)
    CONSUMER_SECRET    5a89e11081a384ec0473(your consumer secret)
    REDIRECT_URL    http://hogehoge.fluxflex.com/simple/callback.php

3.利用可能です(アクセストークンの入力を促すような認証画面になるような場合は再度プロジェクトをimportしてみてください)

Author
-----
[@takimo](http://twitter.com/takimo)
