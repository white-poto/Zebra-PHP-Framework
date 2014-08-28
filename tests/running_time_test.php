<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-28
 * Time: 下午8:49
 */

require_once '../Zebra.php';

$runningTime = new RunningTime();
$runningTime->begin();
sleep(10);
$runningTime->mark('test');
$runningTime->end();
echo $runningTime->getReport();