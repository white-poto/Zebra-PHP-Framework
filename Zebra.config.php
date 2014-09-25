<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-9-24
 * Time: 上午10:34
 */

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

//项目根目录
if(!defined('ROOT')) define('ROOT', dirname(__FILE__));
//框架目录（Zebar目录位置）
if(!defined('ZEBRA_PATH')) define('ZEBRA_PATH', ROOT);
//控制器目录
if(!defined('CONTROLLER_PATH')) define('CONTROLLER_PATH', ROOT . DS . 'Controller');
//缓存文件目录
if(!defined('CACHE_PATH')) define('CACHE_PATH', ROOT . DS . 'Cache');
//配置文件目录
if(!defined('CONFIG_PATH')) define('CONFIG_PATH', ROOT . DS . 'Config');
//模型文件目录
if(!defined('MODEL_PATH')) define('MODEL_PATH', ROOT . DS . 'Model');

//autoLoad缓存文件
if(!defined('AUTOLOAD_CACHE_FILE')) define('AUTOLOAD_CACHE_FILE', CACHE_PATH . DS .'__autoload.cache.php');

//自动加载目录
if(!defined('CLASS_PATH')){
    $class_path = get_include_path() . PATH_SEPARATOR . CONTROLLER_PATH . PATH_SEPARATOR . MODEL_PATH;
    define('CLASS_PATH', $class_path);
}



