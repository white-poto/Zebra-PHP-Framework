<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-9-24
 * Time: 上午10:08
 *
 * Mvc路由类
 */

namespace Zebra\Mvc;

class Mvc {

    private $controller;

    private $action;

    private $namespace;

    public function __construct(){}

    public function execute(){
        $this->getNamespace();
        $this->getController();
        $this->getAction();

    }

    private function getNamespace(){
        if(isset($_GET['n']) && !empty($_GET['n'])){
            $this->namespace = trim($_GET['n']);
        }else{
            $this->namespace = 'Index';
        }
    }

    private function getController(){
        if(isset($_GET['c']) && !empty($_GET['c'])){
            $this->controller = trim($_GET['c']);
        }else{
            $this->controller = 'Index';
        }
    }

    private function getAction(){
        if(isset($_GET['a']) && !empty($_GET['a'])){
            $this->action = '_' . trim($_GET['a']);
        }else{
            $this->action = '_default';
        }
    }

} 