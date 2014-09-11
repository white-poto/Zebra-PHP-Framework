<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-9
 * Time: 上午2:17
 */
define('ZEBRA_ROOT', dirname(dirname(dirname(__FILE__))));
require ZEBRA_ROOT . DIRECTORY_SEPARATOR . 'Zebra.php';

$daemon = new \Zebra\Daemon\Daemon(true, 'nobody', '/tmp/test.log');
$daemon->daemonize();

while(true){
    echo "---------" . PHP_EOL;
    sleep(1);
}