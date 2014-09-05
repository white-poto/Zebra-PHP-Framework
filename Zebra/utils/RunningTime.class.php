<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-28
 * Time: 下午8:42
 *
 * PHP脚本运行计时器
 * 调用方法：
 * $runningTime = new RunningTime();
 * $runningTime->begin();
 * sleep(10);
 * $runningTime->mark('test');
 * $runningTime->end();
 * echo $runningTime->getReport();
 *
 */
namespace Zebra\Utils;

class RunningTime {

    /**
     * 运行时间记录器
     */
    private $times;

    /**
     * 程序开始计时
     */
    public function begin(){
        $this->times['begin_time'] = $this->getCurrentTime();
    }

    /**
     * 程序结束计时
     */
    public function end(){
        $this->times['end_time'] = $this->getCurrentTime();
    }

    /**
     * 程序中间运行计时
     * @param $key 运行标志，例如一个函数名称
     */
    public function mark($key){
        $this->times[$key] = $this->getCurrentTime();
    }

    /**
     * 获取运行计时器
     * @return mixed
     */
    public function getTimes(){
        return $this->times;
    }

    /**
     * 获取运行报告
     * @return string
     */
    public function getReport(){
        $report = 'Running Time Report:' . PHP_EOL;

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

    /**
     * 获取当前时间
     * @return float
     */
    public function getCurrentTime ()
    {
        list ($msec, $sec) = explode(" ", microtime());
        return (float)$msec + (float)$sec;
    }
}