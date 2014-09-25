<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-9-25
 * Time: 上午10:09
 */

namespace Zebra\Cache;

class FileCache implements ICache {

    protected static $cache;

    private $cache_file;

    public function __construct($cache_file=''){
        if(!is_dir(CONFIG_PATH)){
            mkdir(CONFIG_PATH, 0755);
        }

        if(empty($cache_file)) $cache_file = CACHE_PATH . DS . '__zebar.cahce.php';
        if(!\file_exists($cache_file)) \touch($cache_file);
        if(!\is_readable($cache_file)) throw new \Exception('cache file is not readable');
        if(!\is_writable($cache_file)) throw new \Exception('cache file is not writable');

        $this->cache_file = $cache_file;
        $this->init();
    }

    public function init(){
        if(!isset(self::$cache[$this->cache_file]) || empty(self::$cache[$this->cache_file])){
            if($this->checkPhpSyntax($this->cache_file)){
                throw new \Exception($this->cache_file . 'has syntax error, please check the cache file.');
            }
            self::$cache[$this->cache_file] = include $this->cache_file;
        }
    }

    public function checkPhpSyntax($php_file){
        $command = PHP_COMMAND_PATH . ' -l ' . $php_file;
        exec($command, $output, $status);
        if($status===0){
            return true;
        }
        return false;
    }

    /**
     * 保存缓存
     * @return mixed
     * @throws \Exception empty cache
     */
    public function save(){
        if(empty(self::$cache)){
            throw new \Exception('cache is empty! You can not save a empty cache');
        }
        $content = '<?php return ' . \var_export(self::$cache[$this->cache_file], true);
        $result = \file_put_contents($this->cache_file, $content);
        return $result;
    }

    /**
     * 设置缓存
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value){
        self::$cache[$this->cache_file][$key] = $value;
    }

    /**
     * 获取缓存
     * @param $key
     * @return mixed
     */
    public function get($key){
        return self::$cache[$this->cache_file][$key];
    }

    /**
     * 清理缓存
     * @return mixed
     */
    public function clear(){
        unset(self::$cache[$this->cache_file]);
        \unlink($this->cache_file);
    }
} 