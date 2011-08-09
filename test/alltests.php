<?php
require_once('../src/mixi.php');
require_once('response_datas.php');

class MixiTest extends Mixi
{
    var $SESSION = array();

    function __construct($config)
    {
        parent::__construct($config);
    }

    public function request($method, $url, $params, $headers = null)
    {
        global $RESPONSE_DATAS;
        if(!$url) return "";
        $response = $RESPONSE_DATAS[$url];
        return $response;
    }

    public function accessAuthorizeURL()
    {
    }

    public function sessionStart()
    {
    }

    public function setAppData($key, $value)
    {
        if(!$key) return;
        $name = $this->createKeyname($key);
        $this->SESSION[$name] = $value;
    }

    public function getAppData($key, $default = false)
    {
        $name = $this->createKeyname($key);
        return ($this->SESSION && isset($this->SESSION[$name])) ? $this->SESSION[$name] : $default;
    }

}

class StackTest extends PHPUnit_Framework_TestCase
{
    public static $AUTHORIZE_URL = array(
        "pc" => 'https://mixi.jp/connect_authorize.pl',
        "mobile" => 'http://m.mixi.jp/connect_authorize.pl'
    );
    private static $TOKEN_URL  = 'https://secure.mixi-platform.com/2/token';
    private static $CONSUMER_KEY = "o983jae9aef09asef23";
    private static $CONSUMER_SECRET = "asfoijwerasdfoij";
    private static $SCOPE = "r_profile w_voice";
    private static $DISPLAY = "pc";
    private static $REDIRECT_URI = "http://exmaple.com/callback";
    
    private static $AUTHORIZE_CODE = "554e79c2f2a93d32367bd6ad43d72350511";

    var $mixi;

    public function getMixiInstance()
    {
        if($this->mixi) return $this->mixi;
        return $this->mixi = new MixiTest(array(
            'consumer_key' => self::$CONSUMER_KEY,
            'consumer_secret' => self::$CONSUMER_SECRET,
            'scope' => self::$SCOPE,
            'display' => self::$DISPLAY,
            'redirect_uri' => self::$REDIRECT_URI
        ));
    }

    public function testCreateMixiInscance()
    {
        $this->assertInstanceOf('Mixi', $this->getMixiInstance());
    }

    public function testAuthorizeURL()
    {
        $this->getMixiInstance();

        $url = self::$AUTHORIZE_URL[$this->mixi->display];
        $params = array(
            "client_id" => self::$CONSUMER_KEY,
            "response_type" => "code",
            "scope" => self::$SCOPE,
            "display" => self::$DISPLAY,
            "state" => "",
        );
        $url .= '?' . http_build_query($params, null, '&');
        $this->assertEquals($url, $this->mixi->getAuthorizeURL(self::$SCOPE));
    }

    public function testRequestAccessToken()
    {
        $this->getMixiInstance();
        global $RESPONSE_DATAS;
        $token = json_decode($RESPONSE_DATAS[self::$TOKEN_URL]);

        $this->mixi->onReceiveAuthorizationCode(self::$AUTHORIZE_CODE);

        $this->assertEquals($token->access_token, $this->mixi->getAppData('access_token'));
        $this->assertEquals($token->refresh_token, $this->mixi->getAppData('refresh_token'));
    }

    public function testApi()
    {
        $this->getMixiInstance();
        global $RESPONSE_DATAS;
        $user_data = json_decode($RESPONSE_DATAS["http://api.mixi-platform.com/2/people/@me/@self"]);

        if(!$this->mixi->getAppData('access_token')){
            $this->mixi->onReceiveAuthorizationCode(self::$AUTHORIZE_CODE);
        }
        $user = $this->mixi->api('/people/@me/@self');
        $this->assertEquals($user_data->entry->id, $user->entry->id);
    }

}

?>
