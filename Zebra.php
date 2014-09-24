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

//全局加载函数
function __autoload($class_name){
    //根据命名空间加载
    static $autoload_cache;

    if(!is_dir(CONFIG_PATH)){
        mkdir(CONFIG_PATH, 0755);
    }

    if(empty($autoload_cache) && file_exists(AUTOLOAD_CACHE_FILE) && is_readable(AUTOLOAD_CACHE_FILE)){
        $autoload_cache = include AUTOLOAD_CACHE_FILE;
    }
    //检查缓存中是否存在该类的路径
    if(array_key_exists($class_name, $autoload_cache)){
        require_once $autoload_cache[$class_name];
        return ;
    }

    //框架文件
    $class_file[] = ZEBRA_PATH . DS . \str_replace('\\', DS, $class_name) . '.class.php';
    //控制器
    $class_file[] = CONTROLLER_PATH . DS . $class_name . '.class.php';
    //模型
    $class_file[] = MODEL_PATH . DS . $class_name . '.class.php';

    foreach($class_file as $file){
        if(file_exists($file) && is_readable($file)){
            $autoload_cache[$class_name] = $file;
            save_autoload_cache_file($file);
            require_once $file;
            return ;
        }
    }

    return ;
}

//将autoload缓存写入配置文件
function save_autoload_cache_file($autoload_cache){
    if(!empty($autoload_cache) && is_array($autoload_cache)){
        if(is_file(AUTOLOAD_CACHE_FILE) && is_writable(AUTOLOAD_CACHE_FILE)){
            $content = '<?php return ' . var_export($autoload_cache, true);
            return file_put_contents(AUTOLOAD_CACHE_FILE, $content);
        }
    }

    return false;
}


