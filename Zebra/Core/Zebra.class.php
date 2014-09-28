<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-9-25
 * Time: 下午2:42
 */

namespace Zebra\Core;


use Zebra\Mvc\Mvc;

class Zebra
{
    public function start()
    {
        //需要预先加载缓存类，autoload会用到
        if(!class_exists('FileCache')){
            if(!defined(ZEBRA_ROOT)) throw new \Exception('const ZEBRA_ROOT not defined');
            require_once ZEBRA_PATH . DS . 'Cache' . DS . 'FileCache.class.php';
        }
        $this->register_autoload();
        $this->build_app();

        //执行MVC路由
        $mvc = new \Zebra\Mvc\Mvc(new \Zebra\Mvc\NormalUrlRewrite());
        $mvc->execute();
    }

    /**
     * 目录、配置初始化
     */
    public function build_app()
    {
        //创建控制器目录
        if(defined(CONTROLLER_PATH) && !\is_dir(CONTROLLER_PATH))  \Zebra\Utils\mkFolder(CONTROLLER_PATH);
        if(defined(CACHE_PATH) && !\is_dir(CACHE_PATH))  \Zebra\Utils\mkFolder(CACHE_PATH);
        if(defined(CONFIG_PATH) && !\is_dir(CONFIG_PATH))  \Zebra\Utils\mkFolder(CONFIG_PATH);
        if(defined(MODEL_PATH) && !\is_dir(MODEL_PATH))  \Zebra\Utils\mkFolder(MODEL_PATH);
    }

    /**
     * 注册autoload函数
     */
    public function register_autoload(){
        switch(RUNNING_MODE){
            case MVC_SUBDIR_MODE :
                \spl_autoload_register('mvc_subdir_autoload');
                break;
            case NAMESPACE_MODE :
                \spl_autoload_register('namespace_auto_load');
                break;
            case MVC_MODE :
                \spl_autoload_register('mvc_autoload');
                break;
        }
    }

    //MVC运行模式，支持控制器、模型二级目录
    public function mvc_subdir_autoload($class_name)
    {
        $cache = new \Zebra\Cache\FileCache(AUTOLOAD_CACHE_FILE);

        //从缓存文件中加载
        $cacheFile = $cache->get($class_name);
        if (!empty($cacheFile)) {
            require_once $cacheFile;
            return;
        }

        //加载框架类
        $zebra_class_file = ZEBRA_ROOT . DS . \str_replace('\\', DS, $class_name) . '.class.php';
        if (\file_exists($zebra_class_file) && \is_readable($zebra_class_file)) {
            $cache->set($class_name, $zebra_class_file);
            $cache->save();
            require_once $zebra_class_file;
            return;
        }

        $class_paths = explode(PATH_SEPARATOR, CLASS_PATH);
        foreach ($class_paths as $class_path) {
            $dirs = \scandir($class_path);
            foreach ($dirs as $dir) {
                if ($dir == '.' || $dir == '..') continue;
                if (!\is_dir($class_path . DS . $dir)) continue;

                $class_file = $class_path . DS . $dir . DS . $class_name . '.class.php';
                if (\file_exists($class_file) && \is_readable($class_file)) {
                    $cache->set($class_name, $class_file);
                    $cache->save();
                    require_once $class_file;
                    return;
                }
            }
        }

    }

//命名空间运行模式
    public function namespace_auto_load($class_name)
    {
        $cache = new \Zebra\Cache\FileCache(AUTOLOAD_CACHE_FILE);

        //从缓存文件中加载
        $cacheFile = $cache->get($class_name);
        if (!empty($cacheFile)) {
            require_once $cacheFile;
            return;
        }

        //加载框架文件
        $class_file = ROOT . DS . \str_replace('\\', DS, $class_name) . '.class.php';
        if (\file_exists($class_file) && \is_readable($class_file)) {
            $cache->set($class_name, $class_file);
            $cache->save();
            require_once $class_file;
            return true;
        }

        return false;
    }


    //MVC运行模式
    public function mvc_autoload($class_name)
    {
        $cache = new \Zebra\Cache\FileCache(AUTOLOAD_CACHE_FILE);

        //从缓存文件中加载
        $cacheFile = $cache->get($class_name);
        if (!empty($cacheFile)) {
            require_once $cacheFile;
            return;
        }

        //框架文件
        $class_file[] = ZEBRA_PATH . DS . \str_replace('\\', DS, $class_name) . '.class.php';
        //控制器
        $class_file[] = CONTROLLER_PATH . DS . $class_name . '.class.php';
        //模型
        $class_file[] = MODEL_PATH . DS . $class_name . '.class.php';

        foreach ($class_file as $file) {
            if (\file_exists($file) && \is_readable($file)) {
                $cache->set($class_name, $file);
                $cache->save();
                require_once $file;
                return;
            }
        }

        return;
    }
} 