<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-9-25
 * Time: 上午9:57
 */

interface ICache {

    /**
     * 保存缓存
     * @return mixed
     */
    public function save();

    /**
     * 设置缓存
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value);

    /**
     * 获取缓存
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * 清理缓存
     * @return mixed
     */
    public function clear();
} 