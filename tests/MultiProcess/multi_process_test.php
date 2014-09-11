<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-9
 * Time: 上午2:53
 */
declare(ticks = 1);
define('ZEBRA_ROOT', dirname(dirname(dirname(__FILE__))));
require ZEBRA_ROOT . DIRECTORY_SEPARATOR . 'Zebra.php';



class producer extends BaseWorkerProducer {
    public function go(){
        echo "Producer ..." . PHP_EOL;
        sleep(1);
    }
}

class worker extends BaseWorkerProducer {
    public function go(){
        echo "Worker ..." . PHP_EOL;
        sleep(1);
    }
}

$multi = new MultiProcess('producer', 'worker', 2, 10);
$multi->start();