<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-9
 * Time: 上午2:17
 */

define('ROOT', dirname(dirname(__FILE__)));
require ROOT . 'Daemon.class.php';

$daemon = new Daemon(true, 'nobody', '/tmp/test.log');
$daemon->daemonize();

while(true){
    echo "---------" . PHP_EOL;
    sleep(1);
}