<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-9-24
 * Time: 上午10:34
 */

define('DS', DIRECTORY_SEPARATOR);

//项目根目录
define('ROOT', dirname(__FILE__));
//框架目录（Zebar目录位置）
define('ZEBRA_PATH', ROOT);
//控制器目录
define('CONTROLLER_PATH', ROOT . DS . 'Controller');
//缓存文件目录
define('CACHE_PATH', ROOT . DS . 'Cache');
//配置文件目录
define('CONFIG_PATH', ROOT . DS . 'Config');
//模型文件目录
define('MODEL_PATH', ROOT . DS . 'Model');

//autoLoad缓存文件
define('AUTOLOAD_CACHE_FILE', CACHE_PATH . DS .'__autoload.cache.php');

