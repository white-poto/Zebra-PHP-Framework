<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-28
 * Time: 下午9:08
 */

require_once '../Zebra.php';

$manager = new ProcessManager();

for($i=0; $i<100; $i++){
    $manager->fork(new Process('put_message', "My super cool process"));
    echo $i . PHP_EOL;
}


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

echo time() . PHP_EOL;


function put_message(){
    sleep(3);
    $messageQueue = new SystemVMessageQueue('a', __FILE__);
    while(true){
        $messageQueue->put(mt_rand(0, 10000) . getmypid());
    }
}