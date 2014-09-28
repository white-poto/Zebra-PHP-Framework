<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-13
 * Time: 下午2:25
 *
 * 实现程序单例运行，调用方式：
 * declare(ticks = 1);//注意：一定要在外部调用文件中首部调用该声明，否则程序会无法监听到信号量
 * $single = new DaemonSingle(__FILE__);
 * $single->single();
 *
 */
namespace Zebra\Daemon;

class DaemonSingle {

    //PID文件路径
    private $pid_dir;

    //PID文件名称
    private $filename;

    //PID文件完整路径名称
    private $pid_file;

    /**
     * 构造函数
     * @param $filename
     * @param string $pid_dir
     */
    public function __construct($filename, $pid_dir='/tmp/'){
        if(empty($filename)) throw new JetException('filename cannot be empty...');
        $this->filename = $filename;
        $this->pid_dir = $pid_dir;
        $this->pid_file = $this->pid_dir . DIRECTORY_SEPARATOR . substr(basename($this->filename), 0, -4) . '.pid';
    }

    /**
     * 单例模式启动接口
     * @throws JetException
     */
    public function single(){
        $this->check_pcntl();
        if(file_exists($this->pid_file)) {
            throw new Exception('the process is already running...');
        }
        $this->create_pid_file();
    }

    /**
     * @throws JetException
     */
    private function create_pid_file()
    {
        if (!is_dir($this->pid_dir)) {
            mkdir($this->pid_dir);
        }
        $fp = fopen($this->pid_file, 'w');
        if(!$fp){
            throw new Exception('cannot create pid file...');
        }
        fwrite($fp, posix_getpid());
        fclose($fp);
        $this->pid_create = true;
    }

    /**
     * 环境检查
     * @throws Exception
     */
    public function check_pcntl()
    {
        // Make sure PHP has support for pcntl
        if (!function_exists('pcntl_signal')) {
            $message = 'PHP does not appear to be compiled with the PCNTL extension.  This is neccesary for daemonization';
            throw new Exception($message);
        }
        //信号处理
        pcntl_signal(SIGTERM, array(&$this, "signal_handler"));
        pcntl_signal(SIGINT, array(&$this, "signal_handler"));
        pcntl_signal(SIGQUIT, array(&$this, "signal_handler"));

        // Enable PHP 5.3 garbage collection
        if (function_exists('gc_enable')) {
            gc_enable();
            $this->gc_enabled = gc_enabled();
        }
    }

    /**
     * 信号处理函数，程序异常退出时，安全删除PID文件
     * @param $signal
     */
    public function signal_handler($signal)
    {
        switch ($signal) {
            case SIGINT :
            case SIGQUIT:
            case SIGTERM:{
                self::safe_quit();
                break;
            }
        }
    }

    /**
     * 安全退出，删除PID文件
     */
    public function safe_quit()
    {
        if (file_exists($this->pid_file)) {
            $pid = intval(posix_getpid());
            $file_pid = intval(file_get_contents($this->pid_file));
            if($pid == $file_pid){
                unlink($this->pid_file);
            }
        }
        posix_kill(0, SIGKILL);
        exit(0);
    }

    /**
     * 析构函数，删除PID文件
     */
    public function __destruct(){
            $this->safe_quit();
    }
} 