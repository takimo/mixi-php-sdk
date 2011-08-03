<?php

require_once 'mixi_graph_api.php';

class Mixi extends MixiGraphAPI{

    function __construct($config)
    {
        parent::__construct($config);
    }

    protected function setAppData($key, $value)
    {
        if(!$key) return;
        $name = $this->createKeyname($key);
        $_SESSION[$name] = $value;
    }

    protected function getAppData($key, $default = false)
    {
        $name = $this->createKeyname($key);
        return ($_SESSION && isset($_SESSION[$name])) ? $_SESSION[$name] : $default;
    }

    protected function clearAppData($key)
    {
        $name = $this->createKeyname($key);
        unset($_SESSION[$name]);
    }

    protected static $supportedKeys =
        array('code', 'access_token', 'refresh_token', 'user_id');

    public function clearAllAppData() {
        foreach (self::$supportedKeys as $key) {
            $this->clearAppData($key);
        }
    }

    protected function createKeyname($key) {
    return implode('_', array('mixi',
                              $this->consumer_key,
                              $key));
    }

}

?>
