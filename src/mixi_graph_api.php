<?php

abstract class MixiGraphAPI {

    public static $AUTHORIZE_URL = array(
        "pc" => 'https://mixi.jp/connect_authorize.pl',
        "mobile" => 'http://m.mixi.jp/connect_authorize.pl'
    );

    public static $API_ENDPOINT = 'http://api.mixi-platform.com';
    public static $API_VERSION = '2';

    public static $CURL_OPTIONS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_USERAGENT      => 'mixi-php-sdk',
    );

    protected $consumer_key;
    protected $consumer_secret;
    protected $redirect_uri;
    protected $access_token;
    protected $refresh_token;

    // user id
    protected $user;

    function __construct($config)
    {
        $this->consumer_key = $config["consumer_key"];
        $this->consumer_secret = $config["consumer_secret"];
        $this->scope = $config["scope"];
        $this->display = ($config["display"]) ? $config["display"] : "pc";
        $this->redirect_uri = $config["redirect_uri"];

        session_start();

        /*
         * when changed scope need new authorization
        $scope = $this->getScopeFromAppData();
        if($scope != $this->scope){
            $this->clearAllAppData();
        }
        */
    }

    public function getUser()
    {
        if(!$this->user){
            return $this->getUserFromAppData();
        }
        return $this->user;
    }

    protected function getUserFromAppData()
    {
        $user = $this->getAppData('user_id', $default = 0);
        $app_access_token = $this->getAppData('access_token');
        $access_token = $this->getAccessToken();

        if($app_access_token != $access_token){
            $this->refreshAccessToken();
        }

        if(!$user){
            $user = $this->getUserFromAccessToken();
            if($user){
                $this->setAppData('user_id', $user);
            }
        }
        return $user;
    }

    protected function getUserFromAccessToken()
    {
        $user = $this->api('/people/@me/@self');
        return $user->entry->id;
    }

    protected function getAuthorizeURL($scope)
    {
        $url = self::$AUTHORIZE_URL[$this->display];
        $params = array(
            "client_id" => $this->consumer_key,
            "response_type" => "code",
            "scope" => $scope,
            "display" => $this->display,
            "state" => "",
        );
        $url .= '?' . http_build_query($params, null, '&');
        return $url;
    }

    protected function accessAuthorizeURL()
    {
        $this->clearAllAppData();
        header("Location: " . $this->getAuthorizeURL($this->scope));
    }

    protected function getScopeFromAppData()
    {
        return $this->getAppData('scope');
    }

    /*
     * getTOkenFromeCode
     * @params string authorize code
     * @return array(["access_token"],["refresh_token"]);
     */
    public function getTokenFromCode($code)
    {
        $url = 'https://secure.mixi-platform.com/2/token';
        $result = $this->request('POST', $url, array(
            'grant_type' => 'authorization_code',
            'client_id' => $this->consumer_key,
            'client_secret' => $this->consumer_secret,
            'code' => $code,
            'redirect_uri' => $this->redirect_uri
        ));
        return json_decode($result);
    }

    public function onReceiveAuthorizationCode($code)
    {
        $token = $this->getTokenFromCode($code);
        $this->setAuthenticationData($token);
    }

    public function setAuthenticationData($token){
        $this->setAppData('code', $token->access_token);
        $this->setAppData('access_token', $token->access_token);
        $this->setAppData('refresh_token', $token->refresh_token);

        $user = $this->getUser();
        $this->setAppData('user_id', $user);
        $this->setAppData('scope', $token->scope);
    }

    public function request($method, $url, $params, $headers = null)
    {
        $curl = curl_init();
        $options = self::$CURL_OPTIONS;
        if($method == 'POST' && is_array($params)){
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = http_build_query($params, null, '&');
        }else if($method == 'POST' && $params){
            $options[CURLOPT_POST] = 1;
            $options[CURLOPT_POSTFIELDS] = $params;
        }else if($method == 'GET' && $params){
            $url .= "?" . http_build_query($params, null, '&');
        }
        $options[CURLOPT_URL] = $url;
        if($headers){
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        curl_setopt_array($curl, $options);
        $result = curl_exec($curl);

        $curl_info = curl_getinfo($curl);
        $http_code = $curl_info["http_code"];
        if($http_code == 401){
            preg_match("/WWW-Authenticate: OAuth error='(.*)'/", $result, $match);
            $error_message = ($match && $match[1]) ? $match[1] : null;
            if($error_message == "expired_token"){
                $this->refreshAccessToken();
                $result = curl_exec($curl);
            }else if($error_message == "invalid_token"){
                $this->clearAllAppData();
                return $this->accessAuthorizeURL();
            }else if($error_message){
                return error_log($error_message);
            }else{
                $this->clearAllAppData();
                return $this->accessAuthorizeURL();
            }
        }else if($http_code == 403){
            echo "403: check scope setting";
        }else if($http_code == 404){
            echo $result;
            error_log($result);
            $this->clearAllAppData();
        }

        curl_close($curl);
        return $result;
    }

    public function api($path, $method = 'GET', $params = null, $headers = array())
    {
        if(!$path) return FALSE;
        $url = self::$API_ENDPOINT . "/" . self::$API_VERSION;
        $url .= $path;
        if(is_array($method) && empty($params))
        {
            $params = $method;
            $method = 'GET';
        }

        if(!$headers && $method == "POST"){
            array_push($headers, "Content-type: application/x-www-form-urlencoded");
        }
        array_push($headers, "Authorization: OAuth " . $this->getAccessToken());

        return json_decode($this->request($method, $url, $params, $headers));
    }

    protected function refreshAccessToken()
    {
        if($refresh_token = $this->getRefreshToken()){
            return $this->accessAuthorizeURL();
        }
        $url = 'https://secure.mixi-platform.com/2/token';
        $result = $this->request('POST', $url, array(
            'grant_type' => 'refresh_token',
            'client_id' => $this->consumer_key,
            'client_secret' => $this->consumer_secret,
            'refresh_token' => $refresh_token
        ));
        $token = json_decode($result);
        if(!$token) return $this->accessAuthorizeURL();
        $this->setAuthenticationData($token);
    }

    protected function getAccessTokenFromAppData()
    {
        $app_access_token = $this->getAppData('access_token');
        if($app_access_token){
            $this->setAccessToken($app_access_token);
            return $app_access_token;
        }
        $this->accessAuthorizeURL();
    }

    protected function getRefreshTokenFromAppData()
    {
        $app_refresh_token = $this->getAppData('refresh_token');
        if($app_refresh_token){
            $this->setRefreshToken($app_refresh_token);
            return $app_refresh_token;
        }
        $this->accessAuthorizeURL();
    }

    protected function getAccessToken()
    {
        if($this->access_token)
        {
            return $this->access_token;
        }
        return $this->getAccessTokenFromAppData();
    }

    protected function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    protected function getRefreshToken()
    {
        if($this->refresh_token)
        {
            return $this->refresh_token;
        }
        return $this->getRefreshTokenFromAppData();
    }

    protected function setRefreshToken($refresh_token)
    {
        $this->refresh_token = $refresh_token;
    }

    abstract protected function setAppData($key, $value);

    abstract protected function getAppData($key, $default = false);

    abstract protected function clearAppData($key);

    abstract protected function clearAllAppData();

}

?>
