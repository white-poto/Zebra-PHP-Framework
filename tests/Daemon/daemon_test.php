<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-9
 * Time: 上午2:17
 */
require '../../Zebra.php';


$daemon = new \Zebra\Daemon\Daemon(true, 'nobody', '/tmp/test.log');
$daemon->daemonize();

while(true){
    echo "---------" . PHP_EOL;
    sleep(1);
}