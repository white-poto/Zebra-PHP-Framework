<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-28
 * Time: 下午8:04
 *
 * 斑马框架入口文件
 */

define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

function __autoload($class_name) {
    $class_file = ROOT . \str_replace('\\', DS, $class_name) . '.class.php';
    if(file_exists($class_file)){
        require_once $class_file;
        return true;
    }
    $interface_file = ROOT . \str_replace('\\', DS, $class_name) . '.interface.php';
    if(file_exists($interface_file)){
        require_once $interface_file;
        return true;
    }

    return false;
}