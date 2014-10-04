<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-9-22
 * Time: 下午5:02
 *
 * 配置文件类
 * C::get('key');
 * C::get('key', 'config2');
 * C::get('key', 'field', 'config2');
 */

namespace Zebra\Config;

class C {

    private static $config;

    private function __construct(){}

    /**
     * 根据配置文件名称、配置key，获取配置值
     * @param $key
     * @param string $configFile
     * @return bool
     */
    public static function get($key, $configFile='config'){
        self::initConfig($configFile);
        if(!isset(self::$config[$configFile][$key])){
            return false;
        }
        return self::$config[$configFile][$key];
    }

    /**
     * 根据配置文件名称、配置key、字段名称，获取配置值
     * @param $key
     * @param $field
     * @param string $configFile
     * @return bool
     */
    public function getField($key, $field, $configFile='config'){
        self::initConfig($configFile);
        if(!isset(self::$config[$configFile][$key][$field])){
            return false;
        }
        return self::$config[$configFile][$key][$field];
    }

    /**
     * 根据配置文件名称，配置key，设置配置值，不提供持久化，程序退出后即失效
     * @param $key
     * @param $value
     * @param string $configFile
     */
    public function set($key, $value, $configFile='config'){
        self::$config[$configFile][$key] = $value;
    }

    /**
     * 根据配置文件名称、配置key，字段名称设置配置值，不提供持久化，程序退出后即失效
     * @param $key
     * @param $field
     * @param $value
     * @param string $configFile
     */
    public function setField($key, $field, $value, $configFile='config'){
        self::$config[$configFile][$key][$field] = $value;
    }

    /**
     * 单例模式初始化配置文件
     * @param string $configFile
     * @throws \Exception
     */
    private static function initConfig($configFile='config'){
        if(!isset(self::$config[$configFile])) {
            $realConfigFile = CONFIG_PATH . DS . $configFile . '.ini.php';;
            if(!file_exists($realConfigFile)) throw new \Exception('config file is not exists');
            if(!is_readable($realConfigFile)) throw new \Exception('config file is not readable');
            if(!checkPhpSyntax($realConfigFile)) throw new \Exception('config file contains a syntax error');

            self::$config[$configFile] = include $realConfigFile;
        }
    }
}