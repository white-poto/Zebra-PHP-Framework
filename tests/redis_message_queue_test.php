<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-19
 * Time: 下午3:15
 */

define('CUR_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'core');
require CUR_DIR . '/' . 'IMessageQueue.interface.php';
require CUR_DIR . '/' . 'RedisMessageQueue.class.php';

$server_config = array(
    'IP' => '127.0.0.1',
    'PORT' => '6379',
);

try{
    $queue = new RedisMessageQueue($server_config, 'test');
    $result = $queue->put('++++++++++++++++');
    var_dump($result);
    $size = $queue->size();
    var_dump($size);
    $message = $queue->get();
    var_dump($message);
    $message = $queue->get();
    var_dump($message);
}catch(Exception $e){
    echo $e->getMessage();
}