<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-3-5
 * Time: 下午8:30
 */

class MultiProcess {

	//最大队列长度
	private $size;

    //当前生产者数量
	private $curSize;

	//生产者
	private $producer;

	//消费者
	private $worker;


	/**
	 * 构造函数
	 * @param string $worker 需要创建的线程类名
	 * @param int $size 最大线程数量
	 */
	public function __construct($producer, $worker, $size=10){
		$this->producer = new $producer;
		$this->worker = $worker;
		$this->size = $size;
		$this->curSize = 0;
	}

	public function start(){

		$producerPid = pcntl_fork();
		if ($producerPid == -1) {
			die("could not fork");
		} else if ($producerPid) {// parent
			
			while(true){
				$pid = pcntl_fork();
				if ($pid == -1) {
					die("could not fork");
				} else if ($pid) {// parent
					
					$this->curSize++;
					if($this->curSize>=$this->size){
						$sunPid = pcntl_wait($status);
						$this->curSize--;
					}
					
				} else {// worker
				
					$worker = new $this->worker;
					$worker->run();
					exit();
				}
			}
				
		} else {// producer
			$this->producer->run();
			exit();
		}
	}
}









