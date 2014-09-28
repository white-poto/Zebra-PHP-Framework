<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-9-24
 * Time: 下午3:11
 */

namespace Zebra\Mvc;


class NormalUrlRewrite implements IUrlRewrite {
    //获取控制器
    public function get_controller(){
        if(isset($_GET['c']) && !empty($_GET['c'])){
            $controller = trim($_GET['c']);
        }else{
            $controller = 'Index';
        }
        return $controller;
    }
    //获取控制器方法
    public function get_action(){
        if(isset($_GET['a']) && !empty($_GET['a'])){
            $action = '_' . trim($_GET['a']);
        }else{
            $action = '_default';
        }
        return $action;
    }

    public function parse_get(){
        
    }
} 