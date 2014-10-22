<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-10-20
 * Time: 下午2:53
 *
 * 日志记录类，调用方式
 * Log::warn($message, 'dir.dir.filename_no_ext');
 */

namespace vendor\log;


class Log {

    public static function info($message, $filename=null){
        self::log($message, 'INFO', $filename);
    }

    public static function warn($message , $filename=null){
        self::log($message, 'WARN', $filename);
    }

    public static function notice($message , $filename=null){
        self::log($message, 'NOTICE', $filename);
    }

    public static function error($message , $filename=null){
        self::log($message, 'ERROR', $filename);
    }

    public static function event($message , $filename=null){
        self::log($message, 'EVENT', $filename);
    }

    protected static function log($message, $level='LOG', $filename=null){
        if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

        $log_dir = \vendor\config\C::get('log.log_dir');
        if(is_null($filename)) $filename = $log_dir . DS . date('Ymd') . '-debug.log';
        else{
            if(strstr($filename, '.')){
                //格式转换test.test.test => test/test
                $dir = dirname(str_replace('.', '/', $filename));
                //创建目录
                if(!is_dir($log_dir . DS . $dir)) mkdir($log_dir . DS . $dir, 0766, true);
                //获取真实文件名
                $tmp = explode('.', $filename);
                $filename = $log_dir . DS . $dir . DS . date('Ymd') . '-' . $tmp[count($tmp)-1] . '.log';
            }else{
                $filename = $log_dir . DS . date('Ymd') . '-' . $filename . '.log';
            }
        }

        //写入日志
        $content = '[' . date('Y-m-d H:i:s') . ']' . "-[$level]:" . $message . PHP_EOL;
        file_put_contents($filename, $content, FILE_APPEND);
    }

} 