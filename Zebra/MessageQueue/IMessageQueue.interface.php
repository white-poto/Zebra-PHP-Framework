<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-8-19
 * Time: 下午12:12
 */
namespace Zebra\MessageQueue;

interface IMessageQueue {
    public function get();
    public function put($message);
    public function size();
} 