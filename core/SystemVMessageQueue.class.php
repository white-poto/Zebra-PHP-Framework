<?php
/**
 * Created by PhpStorm.
 * User: Jenner
 * Date: 14-8-9
 * Time: 上午3:44
 */

class SystemVMessageQueue {

    //消息分组类型，用于将一个消息队列中的信息进行分组
    private $msg_type;

    //队列标志
    private $queue;

    //是否序列化
    private $serialize_needed;

    //无法写入队列时，是否阻塞
    private $block_send;

    //设置位MSG_IPC_NOWAIT，如果无法获取到一个消息，则不等待；如果设置位NULL，则会等待消息到来
    private $option_receive;

    //希望接收到的最大消息大小
    private $maxsize;

    /**
     * @param $ipc_filename IPC通信标志文件，用于获取唯一IPC KEY
     * @param $msg_type 消息类型
     * @param bool $serialize_needed 是否序列化
     * @param bool $block_send 无法写入队列时，是否阻塞
     * @param int $option_receive 设置位MSG_IPC_NOWAIT，如果无法获取到一个消息，则不等待；如果设置位NULL，则会等待消息到来
     * @param int $maxsize 希望接收到的最大消息
     */
    public function __construct($ipc_filename, $msg_type, $serialize_needed=true, $block_send=false, $option_receive=MSG_IPC_NOWAIT, $maxsize=100000){
        $this->msg_type = $msg_type;
        $this->serialize_needed = $serialize_needed;
        $this->block_send = $block_send;
        $this->option_receive = $option_receive;
        $this->maxsize = $maxsize;
        $key_t = ftok($ipc_filename, $msg_type);
        $this->queue = msg_get_queue($key_t);
    }

    public function get_queue(){
        $queue_status = $this->status_queue();
        if ($queue_status['msg_qnum']>0) {
            if (msg_receive($this->queue,$this->msg_type ,$msgtype_erhalten,$this->maxsize,$data,$this->serialize_needed, $this->option_receive, $err)===true) {
                return $data;
            } else {
                throw new Exception($err);
            }
        }
    }

    /**
     * @param $message
     * @throws Exception
     */
    public function set_queue($message){
        if(!msg_send($this->queue,$this->msg_type, $message,$this->serialize_needed, $this->block_send,$err)===true){
            throw new Exception($err);
        }
    }

    /*
     * 返回值数组下标如下：
     * msg_perm.uid	 The uid of the owner of the queue. 用户ID
     * msg_perm.gid	 The gid of the owner of the queue. 用户组ID
     * msg_perm.mode	 The file access mode of the queue. 访问模式
     * msg_stime	 The time that the last message was sent to the queue. 最后一次队列写入时间
     * msg_rtime	 The time that the last message was received from the queue.  最后一次队列接收时间
     * msg_ctime	 The time that the queue was last changed. 最后一次修改时间
     * msg_qnum	 The number of messages waiting to be read from the queue. 当前等待被读取的数据
     * msg_qbytes	 The maximum number of bytes allowed in one message queue.  一个消息队列中允许接收的最大消息总大小
     *               On Linux, this value may be read and modified via /proc/sys/kernel/msgmnb.
     * msg_lspid	 The pid of the process that sent the last message to the queue. 最后发送消息的进程ID
     * msg_lrpid	 The pid of the process that received the last message from the queue. 最后接收消息的进程ID
     */
    public function status_queue(){
        $queue_status = msg_stat_queue($this->queue);
        return $queue_status;
    }


    /**
     * 修改队列状态信息，下标可以是status_queue返回值中下标的任意一个
     * 可以用来修改队列运行接收的最大读取的数据
     * @param $key 状态下标
     * @param $value 状态值
     * @return bool
     */
    public function set_status($key, $value){
        $queue_status = $this->status_queue();
        $queue_status[$key] = $value;
        return msg_set_queue($this->queue, $queue_status);
    }
} 