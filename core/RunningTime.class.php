<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-28
 * Time: 下午8:42
 */

class RunningTime {

    private $times;

    public function begin(){
        $this->times['begin'] = $this->getCurrentTime();
    }

    public function end(){
        $this->times['end'] = $this->getCurrentTime();
    }

    public function mark($key){
        $this->times[$key] = $this->getCurrentTime();
    }

    public function getReport(){
        $report = '';

        $pre_time = -1;
        foreach($this->times as $key=>$time){
            if($pre_time<0){
                $report .= $key . ':' . $time . PHP_EOL;
            }else{
                $taken_time = $time - $pre_time;
                $report .= $key . ':' . $time . '. taken time:' . $taken_time . PHP_EOL;
            }
            $pre_time = $time;
        }

        return $report;
    }

    public function getCurrentTime ()
    {
        list ($msec, $sec) = explode(" ", microtime());
        return (float)$msec + (float)$sec;
    }
}