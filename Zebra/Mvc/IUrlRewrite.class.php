<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-9-24
 * Time: 下午3:04
 */

namespace Zebra\Mvc;


interface IUrlRewrite {
    //获取控制器
    public function get_controller();
    //获取控制器方法
    public function get_action();
    //编译$_GET参数
    public function parse_get();
} 