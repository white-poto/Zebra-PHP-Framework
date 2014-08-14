<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-3-5
 * Time: 下午8:30
 */

class MultiProcess {

	//最大队列长度
	private $work_count;

	//生产者
	private $producer;

	//消费者
	private $worker;

    //消费者运行次数
    private $runtime;

	public function __construct($producer, $worker, $work_count=10, $runtime=100){
		$this->producer = $producer;
		$this->worker = $worker;
		$this->work_count = $work_count;
        $this->runtime = $runtime;
	}

	public function start(){

		$producerPid = pcntl_fork();
		if ($producerPid == -1) {
			die("could not fork");
		} else if ($producerPid) {// parent
			$cur_size = 0;
			while(true){
				$pid = pcntl_fork();
				if ($pid == -1) {
					die("could not fork");
				} else if ($pid) {// parent

                    $cur_size++;
					if($cur_size>=$this->work_count){
						$sunPid = pcntl_wait($status);
                        $cur_size--;
					}
					
				} else {// worker
				
					$worker = new $this->worker($this->runtime);
					$worker->run();
					exit();
				}
			}
				
		} else {// producer
            while(true){
                $producer = new $this->producer($this->runtime);
                $producer->run();
                unset($producer);
            }
		}
	}
}

class BaseWorkerProducer{

    //运行次数，用于避免内存溢出
    private $runtime;

    public function __construct($runtime=100){
        $this->runtime = $runtime;
    }

    public function run(){
        while($this->runtime!=0){
            $this->go();
            $this->runtime--;
        }
    }

    //业务代码
    protected function go(){}

    public function get($queue_id){

    }

    public function set($queue_id){

    }
}









