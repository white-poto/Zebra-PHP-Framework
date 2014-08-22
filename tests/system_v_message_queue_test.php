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
    $messageQueue = new SystemVMessageQueue(1, __FILE__);
    var_dump($messageQueue->put('test'));
    var_dump($messageQueue->get());
    var_dump($messageQueue->status());
}catch(Exception $e){
    echo $e->getMessage();
}
