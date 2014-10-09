<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-10-9
 * Time: 下午10:37
 */

namespace Zebra\Utils;


class Input {
    public static function getInt($key, $default=0){
        if(isset($_REQUEST[$key]) && !empty($_REQUEST[$key])){
            return intval($_REQUEST[$key]);
        }
        return $default;
    }

    public static function getString($key, $default=''){
        if(isset($_REQUEST[$key]) && !empty($_REQUEST[$key])){
            return addslashes(trim($_REQUEST[$key]));
        }
        return $default;
    }
} 