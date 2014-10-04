<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-10-3
 * Time: 下午12:17
 *
 * 单例对象创建工具
 * $object = SingleObject::getInstance('Class_Name');
 */

namespace Zebra\Object;


class SingleObject {

    private static $object;

    public static function getInstance($class_name){
        if(isset(self::$object[$class_name]) && is_object(self::$object[$class_name])) {
            self::$object[$class_name] = new $class_name;
        }
        return self::$object[$class_name];
    }

} 