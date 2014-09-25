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
        if(empty($cache_file)) $cache_file = CACHE_PATH . DS . '__zebar.cahce.php';
        if(!\file_exists($cache_file)) \touch($cache_file);
        if(!\is_readable($cache_file)) throw new \Exception('cache file is not readable');
        if(!\is_writable($cache_file)) throw new \Exception('cache file is not writable');

        $this->cache_file = $cache_file;
    }

    public function init(){
        
    }

    /**
     * 保存缓存
     * @return mixed
     */
    public function save(){
        if(empty(self::$cache)){
            throw new \Exception('cache is empty! You can not save a empty cache');
        }

    }

    /**
     * 设置缓存
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value){

    }

    /**
     * 获取缓存
     * @param $key
     * @return mixed
     */
    public function get($key){

    }

    /**
     * 清理缓存
     * @return mixed
     */
    public function clear(){

    }
} 