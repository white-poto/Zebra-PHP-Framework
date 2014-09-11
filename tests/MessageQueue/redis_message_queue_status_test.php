<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-9-5
 * Time: 下午2:43
 */

define('ZEBRA_ROOT', dirname(dirname(dirname(__FILE__))));
require ZEBRA_ROOT . DIRECTORY_SEPARATOR . 'Zebra.php';

$message = new \Zebra\MessageQueue\RedisMessageQueueStatus();
for($i=0; $i<100; $i++){
    $message->put(mt_rand(0, 10000));
}

echo $message->status_normal();
