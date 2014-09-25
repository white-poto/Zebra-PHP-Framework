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

    /**
     * @var string  控制器类名
     */
    protected $controller;

    /**
     * @var string 控制器方法名称
     */
    protected $action;

    /**
     * 回调函数集合
     * @var
     */
    protected $callback;

    /**
     * @var IUrlRewrite URL路由器
     */
    protected $url_rewrite;

    const CONTROLLER_NOT_FOUND = 'CONTROLLER_NOT_FOUND';    //无法找到控制器
    const ILLEGAL_CONTROLLER = 'ILLEGAL_CONTROLLER';        //非法控制器
    const ACTION_NOT_FOUND = 'ACTION_NOT_FOUND';            //无法找到控制器方法
    const ILLEGAL_ACTION = 'ILLEGAL_ACTION';                //非法的控制器方法
    const BEFORE_ACTION = 'BEFORE_ACTION';                  //执行控制器方法前
    const AFTER_ACTION = 'AFTER_ACTION';                    //执行控制器方法后

    const VALID_PARAM = '/^[a-zA-Z][a-zA-Z0-9_]{0,255}$/'; //合法的参数名为字母开头的，长度在1-255之间

    /**
     * 初始化
     * @param IUrlRewrite $url_rewrite URL_WRITE规则实现对象
     */
    public function __construct(\Zebra\Mvc\IUrlRewrite $url_rewrite = null){
        if(is_null($url_rewrite)) {
            $this->url_rewrite = new \Zebra\Mvc\NormalUrlRewrite();
        }else{
            $this->url_rewrite = $url_rewrite;
        }

        $this->url_rewrite->parse_get();
        $this->controller = $this->url_rewrite->get_controller();
        $this->action = $this->url_rewrite->get_action();
    }

    /**
     * @param $event
     * @param $function
     */
    public function registerCallback($event, $function){
        $this->callback[$event] = $event;
    }

    /**
     * 执行MVC路由
     * @throws JetException
     * @throws \Exception
     */
    public function execute(){
        if(!class_exists($this->controller)) {
            if(!$this->callBack(self::CONTROLLER_NOT_FOUND, $this->controller))
                throw new \Exception('controller not found :' . $this->controller);
        }
        if(!preg_match(self::VALID_PARAM, $this->controller)) {
            if (!$this->callBack(self::ILLEGAL_CONTROLLER, $this->controller))
                throw new JetException('illegal controller :' . $this->controller);
        }
        $controller = new $this->controller;
        if(!method_exists($controller, $this->action)) {
            if(!$this->callBack(self::ACTION_NOT_FOUND, $this->action))
                throw new \Exception('action not found :' . $this->action);
        }
        if(!preg_match(self::VALID_PARAM, $this->action)) {
            if (!$this->callBack(self::ILLEGAL_ACTION, $this->action))
                throw new \Exception('illegal action :' . $this->action);
        }
        if(!empty($this->callback[self::BEFORE_ACTION])){
            if(!method_exists($controller, $this->callback[self::BEFORE_ACTION]))
                throw new \Exception('empty before action' . $this->callback[self::BEFORE_ACTION]);
            if(!$this->callBack(self::BEFORE_ACTION, array($this->controller, $this->action)))
                throw new \Exception('before action error');
        }

        call_user_func(array($controller, $this->action));

        if(!empty($this->callback[self::AFTER_ACTION])){
            if(!method_exists($controller, $this->callback[self::AFTER_ACTION]))
                throw new \Exception('empty before action' . $this->callback[self::AFTER_ACTION]);
            if(!$this->callBack(self::AFTER_ACTION, array($this->controller, $this->action)))
                throw new \Exception('before action error');
        }
    }

    /**
     * 调用回调函数
     * @param  $event
     * @param $attatchment
     * @return void
     */
    private function callBack($event, $attatchment) {

        if (array_key_exists($event, $this->callBacks) && function_exists($this->callBacks[$event])) {
            return call_user_func($this->callBacks[$event], $attatchment);
        } else {
            return true;
        }

    }
}
