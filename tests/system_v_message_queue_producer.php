<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-22
 * Time: 下午12:13
 */

require_once '../Zebra.php';

try{
    $messageQueue = new \Zebra\MessageQueue\SystemVMessageQueue(1, dirname(__FILE__));
    while(true){
        var_dump($messageQueue->put(mt_rand(0, 1000)));
        echo $messageQueue->size() . PHP_EOL;
        sleep(1);
    }
}catch(Exception $e){
    echo $e->getMessage();
}
