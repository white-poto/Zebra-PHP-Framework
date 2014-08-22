<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-22
 * Time: 下午12:13
 */

define('TEST_ROOT', dirname(__FILE__));

require TEST_ROOT . '/../core/IMessageQueue.interface.php';
require TEST_ROOT . '/../core/SystemVMessageQueue.class.php';

try{
    $messageQueue = new SystemVMessageQueue(1, dirname(__FILE__));
    while(true){
        var_dump($messageQueue->put(mt_rand(0, 1000)));
        echo $messageQueue->size() . PHP_EOL;
        sleep(1);
    }
}catch(Exception $e){
    echo $e->getMessage();
}
