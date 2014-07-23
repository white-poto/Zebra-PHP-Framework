<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-7-23
 * Time: 上午9:42
 */




/**
 *
 * example: set_error_handler('errorHandler');
 *
 * @param $errno 错误代码
 * @param $errstr 错误信息
 * @param $errfile  发生错误的文件
 * @param $errline  发生错误的行数
 * @param $errcontext
 */
function errorHandler( $errno, $errstr, $errfile, $errline, $errcontext)
{
    $errorMessage = 'Into '.__FUNCTION__.'() at line '.__LINE__.
        "---ERRNO---". print_r( $errno, true).
        "---ERRSTR---". print_r( $errstr, true).
        "---ERRFILE---". print_r( $errfile, true).
        "---ERRLINE---". print_r( $errline, true).
        "---ERRCONTEXT---".print_r( $errcontext, true).
        "---Backtrace of errorHandler()---".print_r( debug_backtrace(), true);

    $errorMessage = str_replace(array("\r\n", "\n"), '', $errorMessage);
    $log_file_name = ROOT . date("Ymd") . "-error.log";
    file_put_contents( $log_file_name, $errorMessage . PHP_EOL, FILE_APPEND);
}