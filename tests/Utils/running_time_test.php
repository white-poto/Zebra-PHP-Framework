<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-28
 * Time: 下午8:49
 */

define('ZEBRA_ROOT', dirname(dirname(dirname(__FILE__))));
require ZEBRA_ROOT . DIRECTORY_SEPARATOR . 'Zebra.php';

$runningTime = new \Zebra\Utils\RunningTime();
$runningTime->begin();
sleep(10);
$runningTime->mark('test');
$runningTime->end();
echo $runningTime->getReport();