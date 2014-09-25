<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-28
 * Time: 下午8:04
 *
 * 斑马框架入口文件
 */

//加载框架配置文件
require dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Zebra.config.php';
//预先加载文件缓存类，__autoload方法会预先用到
require_once ZEBRA_PATH . DS . 'Cache' . DS . 'FileCache.class.php';
//设置时区
date_default_timezone_set('PRC');
ini_set('date.timezone','Asia/Shanghai');

spl_autoload_register('mvc_subdir_autoload');
//spl_autoload_register('namespace_auto_load');
//spl_autoload_register('mvc_autoload');




//MVC运行模式，支持控制器、模型二级目录
function mvc_subdir_autoload($class_name){
    $cache = new \Zebra\Cache\FileCache(AUTOLOAD_CACHE_FILE);

    //从缓存文件中加载
    $cacheFile = $cache->get($class_name);
    if(!empty($cacheFile)){
        require_once $cacheFile;
        return ;
    }

    //加载框架类
    $zebra_class_file = ZEBRA_ROOT . DS . \str_replace('\\', DS, $class_name) . '.class.php';
    if(\file_exists($zebra_class_file) && \is_readable($zebra_class_file)){
        $cache->set($class_name, $zebra_class_file);
        $cache->save();
        require_once $zebra_class_file;
        return ;
    }

    $class_paths = explode(PATH_SEPARATOR, CLASS_PATH);
    foreach($class_paths as $class_path){
        $dirs = \scandir($class_path);
        foreach($dirs as $dir){
            if($dir=='.' || $dir=='..') continue;
            if(!\is_dir($class_path . DS . $dir)) continue;

            $class_file = $class_path . DS . $dir . DS . $class_name . '.class.php';
            if(\file_exists($class_file) && \is_readable($class_file)){
                $cache->set($class_name, $class_file);
                $cache->save();
                require_once $class_file;
                return ;
            }
        }
    }

}

//命名空间运行模式
function namespace_auto_load($class_name) {
    $cache = new \Zebra\Cache\FileCache(AUTOLOAD_CACHE_FILE);

    //从缓存文件中加载
    $cacheFile = $cache->get($class_name);
    if(!empty($cacheFile)){
        require_once $cacheFile;
        return ;
    }

    //加载框架文件
    $class_file = ROOT . DS . \str_replace('\\', DS, $class_name) . '.class.php';
    if(\file_exists($class_file) && \is_readable($class_file)){
        $cache->set($class_name, $class_file);
        $cache->save();
        require_once $class_file;
        return true;
    }

    return false;
}



//MVC运行模式
function mvc_autoload($class_name){
    $cache = new \Zebra\Cache\FileCache(AUTOLOAD_CACHE_FILE);

    //从缓存文件中加载
    $cacheFile = $cache->get($class_name);
    if(!empty($cacheFile)){
        require_once $cacheFile;
        return ;
    }

    //框架文件
    $class_file[] = ZEBRA_PATH . DS . \str_replace('\\', DS, $class_name) . '.class.php';
    //控制器
    $class_file[] = CONTROLLER_PATH . DS . $class_name . '.class.php';
    //模型
    $class_file[] = MODEL_PATH . DS . $class_name . '.class.php';

    foreach($class_file as $file){
        if(\file_exists($file) && \is_readable($file)){
            $cache->set($class_name, $file);
            $cache->save();
            require_once $file;
            return ;
        }
    }

    return ;
}
