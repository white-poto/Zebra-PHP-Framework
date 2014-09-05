<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-22
 * Time: 下午2:35
 */

require_once '../Zebra.php';

try{
    $messageQueue = new \Zebra\MessageQueue\SystemVMessageQueue(1, dirname(__FILE__));
    var_dump($messageQueue->queue_remove());
}catch(Exception $e){
    echo $e->getMessage();
}