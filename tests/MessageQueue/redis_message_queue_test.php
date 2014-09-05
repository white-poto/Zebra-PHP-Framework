<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-19
 * Time: 下午3:15
 */

require '../../Zebra.php';

$server_config = array(
    'IP' => '127.0.0.1',
    'PORT' => '6379',
);

try{
    $queue = new \Zebra\MessageQueue\RedisMessageQueue($server_config, 'test');
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