<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-22
 * Time: 下午12:13
 */

define('ZEBRA_ROOT', dirname(dirname(dirname(__FILE__))));
require ZEBRA_ROOT . DIRECTORY_SEPARATOR . 'Zebra.php';

try{
    $messageQueue = new \Zebra\MessageQueue\SystemVMessageQueue(1, dirname(__FILE__));
    while(true){
        var_dump($messageQueue->get());
        sleep(1);
    }
}catch(Exception $e){
    echo $e->getMessage();
}
