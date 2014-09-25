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

    protected $controller;

    protected $action;

    protected $callback;

    //URL路由器
    protected $url_rewrite;

    const CONTROLLER_NOT_FOUND = 'CONTROLLER_NOT_FOUND';    //无法找到控制器
    const ILLEGAL_CONTROLLER = 'ILLEGAL_CONTROLLER';        //非法控制器
    const ACTION_NOT_FOUND = 'ACTION_NOT_FOUND';            //无法找到控制器方法
    const ILLEGAL_ACTION = 'ILLEGAL_ACTION';                //非法的控制器方法
    const BEFORE_LOAD_CLASS = 'BEFORE_LOAD_CLASS';          //加载类之前
    const CLASS_LOADED = 'CLASS_LOADED';                    //加载类之后
    const BEFORE_ACTION = 'BEFORE_ACTION';                  //执行控制器方法前
    const AFTER_ACTION = 'AFTER_ACTION';                    //执行控制器方法后

    const VALID_PARAM = '/^[a-zA-Z][a-zA-Z0-9_]{0,255}$/'; //合法的参数名为字母开头的，长度在1-255之间

    public function __construct(\Zebra\Mvc\IUrlRewrite $url_rewrite){
        $this->url_rewrite = $url_rewrite;
        $this->controller = $this->url_rewrite->get_controller();
        $this->action = $this->url_rewrite->get_action();
    }

    public function registerCallback($event, $function){
        $this->callback[$event] = $event;
    }

    public function execute(){
        $this->getController();
        $this->getAction();


    }
}



/**
 * 简单的MVC类，适用于有URLRewrite功能的服务器
 * 需要服务器作如何配置：
 *     1、指定默认文件为包含JetMVC的程序，例如：index.php
 *     index index.php index.html;
 *     2、把不存在的文件调用，都指向包含JetMVC的程序，例如根目录的index.php
 *     if (!-e $request_filename) {
 *       rewrite ^.*$ /index.php last;
 *       break;
 *     }
 */

class JetMVC {

    const CONTROLLER_NOT_FOUND = 'CONTROLLER_NOT_FOUND';
    const ILLEGAL_CONTROLLER = 'ILLEGAL_CONTROLLER';
    const ACTION_NOT_FOUND = 'ACTION_NOT_FOUND';
    const ILLEGAL_ACTION = 'ILLEGAL_ACTION';
    const BEFORE_LOAD_CLASS = 'BEFORE_LOAD_CLASS';
    const CLASS_LOADED = 'CLASS_LOADED';
    const BEFORE_ACTION = 'BEFORE_ACTION';
    const AFTER_ACTION = 'AFTER_ACTION';

    const VALID_PARAM = '/^[a-zA-Z][a-zA-Z0-9_]{0,255}$/'; //合法的参数名为字母开头的，长度在1-255之间

    private $controller;
    private $action;
    private $callBacks;

    private $procceed = false;

    /**
     * 构造函数，对相关参数进行解释
     * @return void
     */
    function __construct() {
    }

    /**
     * 注册回调函数
     * @param  $event 事件名称
     * @param  $functionName 回调函数名称
     * @return void
     */
    public function registerCallBack($event, $functionName) {
        $this->callBacks[$event] = $functionName;
    }

    /**
     * JetMVC执行主函数
     * @throws JetException
     * @return void
     */
    public function execute() {


        $uri = $_SERVER['REQUEST_URI'];

        if (strpos('?', $uri) >= 0) {
            $uri = explode('?', $uri);
            $uri = $uri[0];
        }

        $uriInfo = explode('/', $uri);
        $infoCount = count($uriInfo);
        if ($infoCount >= 2) {

            //获取控制器名称
            $this->controller = $uriInfo[1] ? $uriInfo[1] : 'index';
            //对名称进行校验
            if (preg_match(self::VALID_PARAM, $this->controller)) {
                $this->controller = self::getConvertedName($this->controller);
                $this->procceed = true;
            } else {
                if (!$this->callBack(self::ILLEGAL_CONTROLLER, $this->controller)) throw new JetException("非法的控制器名称：{$this->controller}!");
            }

            //获取Action名称
            if ($infoCount >= 3) {

                $this->action = $uriInfo[2] ? $uriInfo[2] : 'default';
                //对名称进行校验
                if (preg_match(self::VALID_PARAM, $this->action)) {
                    $this->procceed = true;
                } else {
                    if (!$this->callBack(self::ILLEGAL_ACTION, array($this->controller, $this->action))) throw new JetException("控制器[{$this->controller}]：非法的Action：{$this->action}!");
                }

                //把action参数后的参数对传进$_GET数组
                if ($infoCount > 3 && (($infoCount - 3) % 2 == 0)) {
                    for ($i = 3; $i < $infoCount; $i += 2) {
                        $_GET[$uriInfo[$i]] = $uriInfo[$i + 1];
                    }
                }

            } else {
                $this->action = 'default';
            }

        } else {
            die("非法访问！");
        }

        //执行结果
        $success = false;
        if ($this->procceed) {

            try {

                //获取控制器文件路径
                $controllerFile = ROOT . 'controllers' . DIRECTORY_SEPARATOR . $this->controller . '.controller.php';
                //检查文件是否存在
                if (file_exists($controllerFile)) {

                    //如果文件存在

                    //如果存在BEFORE_LOAD_CLASS回调，就调用回调函数
                    $this->callBack(self::BEFORE_LOAD_CLASS, $this->controller);

                    //包含文件
                    require_once($controllerFile);
                    //实例化对象
                    $obj = new $this->controller();

                    //如果存在CLASS_LOADED回调，就调用回调函数
                    $this->callBack(self::CLASS_LOADED, $this->controller);

                    //检查加载的控制器是不是AbstractJetController的子类
                    if (is_subclass_of($obj, "AbstractJetController")) {

                        //处理GET
                        $obj->get = $_GET;
                        //处理POST
                        $obj->post = $_POST;
                        //处理Cookie
                        //处理文件上传
                        if (count($_FILES) > 0) {
                            $obj->files = array();
                            foreach ($_FILES as $name => $file) {
                                $fileItem = new FileItem($file);
                                $obj->files[$name] = $fileItem;
                            }
                        }

                        //循环检查指定的Action是否存在
                        $avaliableActions = array('_' . $this->action, 'onAction' . self::getConvertedName($this->action));
                        $actionFound = false;
                        foreach ($avaliableActions as $action) {
                            if (method_exists($obj, $action)) {

                                //如果存在BEFORE_ACTION回调，就调用回调函数
                                if ($this->callBack(self::BEFORE_ACTION, array($this->controller, $this->action))) {

                                    //调用指定的方法
                                    $obj->$action();

                                    //如果存在AFTER_ACTION回调，就调用回调函数
                                    $this->callBack(self::AFTER_ACTION, array($this->controller, $this->action));

                                }
                                //跳出循环
                                $actionFound = true;
                                break;

                            }
                        }

                        if (!$actionFound)
                            //如果存在ACTION_NOT_FOUND回调，就调用回调函数，否则抛出异常
                            if (!$this->callBack(self::ACTION_NOT_FOUND, array($this->controller, $this->action))) throw new JetException("控制器：[{$this->controller}]不存在的方法：{$this->action}！");

                    } else {

                        //如果不是AbstractJetController的子类
                        //如果存在ILLEGAL_CONTROLLER回调，就调用回调函数，否则抛出异常
                        if (!$this->callBack(self::ILLEGAL_CONTROLLER, $this->controller)) throw new JetException("控制器：[{$this->controller}]不符合规范!");

                    }

                } else {
                    //文件不存在
                    //如果存在CONTROLLER_NOT_FOUND回调，就调用回调函数，否则抛出异常
                    if (!$this->callBack(self::CONTROLLER_NOT_FOUND, $this->controller)) throw new JetException("没有控制器：[{$this->controller}]!");
                }

            } catch (Exception $e) {
                $success = false;
                throw new JetException("发生错误：{$e->getMessage()}!");
            }

        }
        return $success;

    }

    /**
     * 调用回调函数
     * @param  $event
     * @return void
     */
    private function callBack($event, $attatchment) {

        if (array_key_exists($event, $this->callBacks) && function_exists($this->callBacks[$event])) {
            return call_user_func($this->callBacks[$event], $attatchment);
        } else {
            return true;
        }

    }

    /**
     * 把URL地址改成控制类的名字
     * @static
     * @param  $name
     * @return string
     */
    private static function getConvertedName($name) {
        $result = '';
        $upper = true;
        for ($i = 0; $i < strlen($name); $i++) {
            $char = $name[$i];
            if ($upper && ($char >= 'a' && $char <= 'z')) {
                $char = chr(ord($char) - 32);
                $upper = false;
            } else if ($char == '_') {
                $char = '';
                $upper = true;
            }
            $result .= $char;
        }
        return $result;

    }

}
