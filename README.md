Zebra-PHP-Framework
===================
关于Zebra-PHP-Framework
---------------
Zebra-PHP-Framework 是一款轻量级的PHP开发框架，目前处于在开发状态。
开发计划：
+ 常用函数添加
+ 守护进程实现
+ 并发框架
+ System V message queue 消息队列封装
+ 基于Redis链接的消息队列封装
+ 测试单元
+ 基于CURL的HTTP协议封装
+ 单例运行模式实现
+ 简单并发框架，抽离进程控制逻辑，方便并发程序编写

[博客地址:www.huyanping.cn](http://www.huyanping.cn/ "始终不够")

**守护进程实现示例：**
```php
filename:daemon_test.php
<?php
$daemon = new \Zebra\Daemon\Daemon(true, 'nobody', '/tmp/test.log');
$daemon->daemonize();

while(true){
    echo "---------" . PHP_EOL;
    sleep(1);
}
```
以上示例不会在console上打印任何字符，所有数据均会重定向到/tmp/test.log中


**单例程序实现示例：**
```php
<?php
declare(ticks = 1);//注意：一定要在外部调用文件中首部调用该声明，否则程序会无法监听到信号量
$single = new DaemonSingle(__FILE__);
$single->single();
```
以上程序会在/tmp下生成一个pid文件，使用pid文件保证程序单例实例运行

**Redis消息队列使用示例：**
```php
<?php
$server_config = array(
    'IP' => '127.0.0.1',
    'PORT' => '6379',
);

try{
    $queue = new \Zebra\MessageQueue\RedisMessageQueue($server_config, 'test');
    $result = $queue->put('++++++++++++++++');
    var_dump($result);
    $size = $queue->size()
    var_dump($result);
    $message = $queue->get();
    var_dump($message);
}catch(Exception $e){
    echo $e->getMessage();
}
```
以上程序输出：
int(1)
int(1)
string(16) "++++++++++++++++"

**RedisStatus提供队列信息记录的消息队列使用示例：**
```php
<?php
<?php
$message = new \Zebra\MessageQueue\RedisMessageQueueStatus();
for($i=0; $i<100; $i++){
    $message->put(mt_rand(0, 10000));
}

$work = $message->get();
var_dump($work);
echo $message->status_normal();
```
以上程序输出：
string(4) "9971"
Redis Message Queue
-------------------
Message queue name:redis_message_queue
Put position of queue:100
Get position of queue:5 
Number of unread queue:95



