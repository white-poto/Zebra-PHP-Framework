<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-28
 * Time: 下午9:08
 */

require '../../Zebra.php';

$manager = new \Zebra\MultiProcess\ProcessManager();

for ($i = 0; $i < 100; $i++) {
    $manager->fork(new \Zebra\MultiProcess\Process('put_message', "My super cool process"));
    echo $i . PHP_EOL;
}


do {
    foreach ($manager->getChildren() as $process) {
        $iid = $process->getInternalId();
        if ($process->isAlive()) {
            echo sprintf('Process %s is running', $iid);
        } else if ($process->isFinished()) {
            echo sprintf('Process %s is finished', $iid);
        }
        echo "\n";
    }
    sleep(1);
} while ($manager->countAliveChildren());

echo time() . PHP_EOL;


function put_message()
{
    sleep(3);
    try {
        $messageQueue = new \Zebra\MessageQueue\SystemVMessageQueue(1, __FILE__);
        while (true) {
            $messageQueue->put(mt_rand(0, 10000) . getmypid());
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}