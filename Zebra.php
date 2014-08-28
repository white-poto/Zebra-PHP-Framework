<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-28
 * Time: 下午8:04
 */

define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

function __autoload($class_name) {
    $class_file = ROOT . DS . 'core' . DS . $class_name . '.class.php';
    echo $class_file . PHP_EOL;
    if(file_exists($class_name)){
        require_once $class_file;
        return true;
    }

    $interface_file = ROOT . DS . 'core' . DS . $class_name . '.interface.php';
    echo $interface_file . PHP_EOL;
    if(file_exists($interface_file)){
        require_once $interface_file;
        return true;
    }

    return false;
}