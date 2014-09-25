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

    public static function get($key, $configFile='config'){
        self::initConfig($configFile);
        if(!isset(self::$config[$configFile][$key])){
            return false;
        }
        return self::$config[$configFile][$key];
    }

    public function getField($key, $field, $configFile='config'){
        self::initConfig($configFile);
        if(!isset(self::$config[$configFile][$key][$field])){
            return false;
        }
        return self::$config[$configFile][$key][$field];
    }

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