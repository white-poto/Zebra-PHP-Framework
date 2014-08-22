<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-22
 * Time: 下午2:35
 */

define('TEST_ROOT', dirname(__FILE__));

require TEST_ROOT . '/../core/IMessageQueue.interface.php';
require TEST_ROOT . '/../core/SystemVMessageQueue.class.php';

try{
    $messageQueue = new SystemVMessageQueue(1, dirname(__FILE__));
    var_dump($messageQueue->queue_remove());
}catch(Exception $e){
    echo $e->getMessage();
}