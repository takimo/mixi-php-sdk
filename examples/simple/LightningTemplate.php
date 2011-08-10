<?php
/**
 * Lightning Template
 *
 * @author  riaf <ksato@otobank.co.jp>
 * @license New BSD License
 **/
class LightningTemplate
{
    const VERSION = '0.1.1';

    protected $cache;
    protected $filename;
    protected $vars = array();
    protected $filters = array();

    /**
     * init
     *
     * @param string $filename
     * @return void
     **/
    public function __construct($filename=null, $cache=null) {
        if (file_exists($filename) && is_readable($filename)) {
            $this->filename = $filename;
        } else {
            throw new LightningTemplateException("`$filename` is not found");
        }
        if ($cache instanceof LightningTemplateCache) {
            $this->cache = $cache;
        }
    }

    /**
     * template read
     *
     * @param string $filename
     * @return string
     **/
    public function __toString() {
        if (is_null($this->filename)) {
            throw new LightningTemplateException("filename is not defined");
        }
        $_filepath_ = 'php://filter/read=convert.lightning_template_filter/resource='. $this->filename;
        if ($this->cache instanceof LightningTemplateCache) {
            $_filepath_ = $this->cache->get_filepath($this->filename);
        }
        extract($this->vars);
        ob_start();
        include $_filepath_;
        return ob_get_clean();
    }

    /**
     * set template vars
     *
     * @param string $name
     * @param mixed $value
     * @return void
     **/
    public function __set($name, $value) {
        $this->vars[$name] = $value;
    }

    /**
     * set template vars
     *
     * @param string $name
     * @return mixed
     **/
    public function __get($name) {
        return isset($this->vars[$name])? $this->vars[$name]: null;
    }

    /**
     * isset template vars
     *
     * @param string $name
     * @return bool
     **/
    public function __isset($name) {
        return isset($this->vars[$name]);
    }

    /**
     * unset template vars
     *
     * @param string $name
     * @return void
     **/
    public function __unset($name) {
        if (isset($this->vars[$name])) {
            unset($this->vars[$name]);
        }
    }

    public function __call($name, $args) {
        return isset($this->filter[$name])
            ? call_user_func_array($this->filter[$name], $args)
            : array_shift($args);
    }
}

abstract class LightningTemplateCache
{
    abstract public function get_filepath($filename);
}

/**
 * ファイルキャッシュ
 *
 * @author  riaf <ksato@otobank.co.jp>
 **/
class LightningTemplateCache_File extends LightningTemplateCache
{
    protected $cache_dir;
    protected $expire = 0;

    /**
     * init
     *
     * @param string $cache_dir;
     * @param int $expire
     **/
    public function __construct($cache_dir='/tmp', $expire=0) {
        $this->cache_dir = $cache_dir;
        $this->expire = $expire;
    }

    /**
     * キャッシュしたファイルを返す
     *
     * @param string $filename template file
     * @return string $filepath
     **/
    public function get_filepath($filename) {
        if ($filename = realpath($filename)) {
            $cache_file = $this->cache_file($filename);
            if (!file_exists($cache_file) || ($this->expire > 0 && $this->expire < time() - filemtime($cache_file))) {
                file_put_contents(
                    $cache_file,
                    file_get_contents('php://filter/read=convert.lightning_template_filter/resource='. $filename)
                );
            }
            return $cache_file;
        }
        return $filename;
    }
    private function cache_file($filename) {
        return rtrim(realpath($this->cache_dir), '/'). '/'. basename($filename). '.'. md5($filename);
    }
}

class LightningTemplateFilter extends php_user_filter
{
    static public $c_dict = array(
        'instanceof',
    );

    public function filter($in, $out, &$consumed, $closing) {
        while ($bucket = stream_bucket_make_writeable($in)) {
            $patterns = array(
                '/\{\{\s+(.*?)\s+\}\}/e',
                '/\{%\s+for\s+(.*?)\s+in\s+(.*?)\s+%\}/e',
                '/\{%\s+endfor\s+%\}/',
                '/\{%\s+if\s+(.+?)\s+%\}/e',
                '/\{%\s+else\s+%\}/',
                '/\{%\s+endif\s+%\}/',
            );
            $replacements = array(
                "'<?php echo '. \$this->variable('$$1'). ' ?>'",
                "'<?php foreach ('. \$this->variable('$$2', true). ' as \$_key_ => $$1): ?>'",
                '<?php endforeach; ?>',
                "'<?php if ('. \$this->condition('$1'). '): ?>'",
                '<?php else: ?>',
                '<?php endif; ?>',
            );
            $bucket->data = preg_replace($patterns, $replacements, $bucket->data);
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }

    private function variable($varstr, $safe=false) {
        if (substr($varstr, -5) === '|safe') {
            $safe = true;
        }
        if (strpos($varstr, '|')) {
            $_vars = explode('|', $varstr);
            $_varname = array_shift($_vars);
            $_filter = array_shift($_vars);
            $varstr = "\$this->$_filter($_varname)";
            if (!empty($_vars)) {
                $varstr .= '|'. implode('|', $_vars);
                return $this->variable($varstr, $safe);
            }
        }
        return $safe
            ? $varstr
            : "htmlspecialchars($varstr, ENT_QUOTES, 'utf-8')";
    }

    private function condition($str) {
        preg_match_all('/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/', $str, $matches);
        foreach ($matches[1] as $varstr) {
            if (!in_array($varstr, self::$c_dict)) {
                $str = str_replace($varstr, '$'. $varstr, $str);
            }
        }
        return $str;
    }
}
stream_filter_register('convert.lightning_template_filter', 'LightningTemplateFilter');

class LightningTemplateException extends RuntimeException {}

?>
