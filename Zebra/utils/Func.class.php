<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-7-22
 * Time: 上午10:48
 *
 * Example:
 * $func = Func::fromObjectMethod($myArrayObject, "offsetGet");
 * echo $func(3);
 */

class Func {

    /**
     * @param $name
     * @return Func
     */
    public static function fromFunction($name){
        return new Func($name);
    }

    /**
     * @param $class
     * @param $name
     * @return Func
     */
    public static function fromClassMethod($class, $name){
        return new Func(array($class, $name));
    }

    /**
     * @param $object
     * @param $name
     * @return Func
     */
    public static function fromObjectMethod($object, $name){
        return new Func(array($object, $name));
    }

    /**
     * @var
     */
    public $function;

    /**
     * @param $function
     */
    public function  __construct($function) {
        $this->function = $function;
    }

    /**
     * @return mixed
     */
    public function __invoke(){
        return call_user_func_array($this->function, func_get_args());
    }
} 