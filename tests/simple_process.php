<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-23
 * Time: 上午11:41
 */

define('SIMPLE_PROCESS_ROOT', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'core'  . DIRECTORY_SEPARATOR);
require SIMPLE_PROCESS_ROOT .'Process.php';
require SIMPLE_PROCESS_ROOT . 'ProcessManager.php';
require SIMPLE_PROCESS_ROOT . 'Semaphore.php';
require SIMPLE_PROCESS_ROOT . 'SHMCache.php';


declare(ticks=1); // This part is critical, be sure to include it
$manager = new ProcessManager();
$manager->fork(new Process(function() { sleep(5); }, "My super cool process"));
$manager->fork(new Process(function() { sleep(7); }, "My super cool2 process"));
do
{
    foreach($manager->getChildren() as $process)
    {
        $iid = $process->getInternalId();
        if($process->isAlive())
        {
            echo sprintf('Process %s is running', $iid);
        } else if($process->isFinished()) {
            echo sprintf('Process %s is finished', $iid);
        }
        echo "\n";
    }
    sleep(1);
} while($manager->countAliveChildren());