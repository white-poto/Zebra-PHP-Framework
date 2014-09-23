<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-9-23
 * Time: 上午9:40
 *
 * 日志记录类
 */

namespace Zebra\Log;

class Logger {

    private $log_file;

    public function set_log_file($log_file){
        $dir = dirname($log_file);
        if(!is_dir($dir)){
            if(!mkdir($dir, 0755)){
                return false;
            }
        }
        if(!is_file($log_file)){
            touch($log_file);
        }
        if(!is_readable($log_file)){
            return false;
        }
        $this->log_file = $log_file;
    }

    /**
     * TRACE级别日志记录
     *
     * @param mixed $message message
     * @param $throwable 是否抛出异常
     */
    public function trace($message, $throwable = false) {
        $this->log('TRACE', $message, $throwable);
    }

    /**
     * DEBUG级别日志记录
     *
     * @param mixed $message message
     * @param $throwable 是否抛出异常
     */
    public function debug($message, $throwable = false) {
        $this->log('DEBUG', $message, $throwable);
    }

    /**
     * INFO级别日志记录
     *
     * @param mixed $message message
     * @param $throwable 是否抛出异常
     */
    public function info($message, $throwable = false) {
        $this->log('INFO', $message, $throwable);
    }

    /**
     * WARN级别日志记录
     *
     * @param mixed $message message
     * @param $throwable 是否抛出异常
     */
    public function warn($message, $throwable = false) {
        $this->log('WARN', $message, $throwable);
    }

    /**
     * ERROR级别日志记录
     *
     * @param mixed $message message
     * @param $throwable 是否抛出异常
     */
    public function error($message, $throwable = false) {
        $this->log('ERROR', $message, $throwable);
    }

    /**
     * FATAL级别日志记录
     *
     * @param mixed $message message
     * @param $throwable 是否抛出异常
     */
    public function fatal($message, $throwable = false) {
        $this->log('FATAL', $message, $throwable);
    }

    /**
     * FATAL级别日志记录
     *
     * @param mixed $message message
     * @param $event_name 时间名称
     */
    public function event($message, $event_name) {
        $this->log("EVENT[$event_name]", $message);
    }


    public function log($level, $message, $throwable = false) {
        $content = $level . ':' . $message . PHP_EOL;
        \file_put_contents($this->log_file, $message, FILE_APPEND);
        if($throwable){
            throw new \Exception($content);
        }
    }

} 