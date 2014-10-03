<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-10-3
 * Time: 下午12:17
 *
 * 单例对象创建工具
 */

namespace Zebra\Object;


class SingleObject {

    private static $object;

    public static function make($class_name){
        if(isset(self::$object[$class_name]) && is_object(self::$object[$class_name])) {
            self::$object[$class_name] = new $class_name;
        }
        return self::$object[$class_name];
    }

} 