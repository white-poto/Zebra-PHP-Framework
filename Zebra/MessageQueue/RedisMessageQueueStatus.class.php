<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-9-5
 * Time: 下午2:16
 *
 * 附加了队列状态信息的RedisMessageQueue
 */

namespace Zebra\MessageQueue;


class RedisMessageQueueStatus extends RedisMessageQueue {

    protected $record_status;

    protected $put_position;

    protected $get_position;

    public function __construct(
        $server_config = array('IP' => '127.0.0.1', 'PORT' => '6379'),
        $key = 'redis_message_queue',
        $p_connect = false,
        $record_status=true
    ){
        parent::__construct($server_config, $key, $p_connect);
        $this->record_status = $record_status;
        $this->put_position = $this->key . '_put_position';
        $this->get_position = $this->key . '_get_position';
    }

    public function get(){
        if($queue = parent::get()){
            $incr_result = $this->redis_server->incr($this->get_position);
            if(!$incr_result) throw new \Exception('can not mark get position,please check the redis server');
            return $queue;
        }else{
            return false;
        }
    }

    public function put($message){
        if(parent::put($message)){
            $incr_result = $this->redis_server->incr($this->put_position);
            if(!$incr_result) throw new \Exception('can not mark put position,please check the redis server');
        }else{
            return false;
        }
    }

    public function status(){
        $status['put_position'] = ($put_position = $this->redis_server->get($this->put_position)) ? $put_position : 0;
        $status['get_position'] = ($get_position = $this->redis_server->get($this->get_position)) ? $get_position : 0;
        $status['unread_queue'] = \intval($status['put_position']) - \intval($status['get_position']);
        $status['queue_name'] = $this->key;
        $status['server'] = $this->server;
        $status['port'] = $this->port;

        return $status;
    }

    public function status_normal(){
        $status = $this->status();
        $message  = 'Redis Message Queue' . PHP_EOL;
        $message .= '-------------------' . PHP_EOL;
        $message .= 'Message queue name:' . $status['queue_name'] . PHP_EOL;
        $message .= 'Put position of queue:' . $status['put_position'] . PHP_EOL;
        $message .= 'Get position of queue:' . $status['get_position'] . PHP_EOL;
        $message .= 'Number of unread queue:' . $status['unread_queue'] . PHP_EOL;

        return $message;
    }

    public function status_json(){
        return \json_encode($this->status());
    }
} 