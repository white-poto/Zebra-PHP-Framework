<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-3-5
 * Time: 下午8:30
 *
 */
namespace Zebra\Daemon;

class Daemon
{

    private $info_dir = "/tmp";
    private $pid_file = "";
    private $gc_enabled = null;

    public function __construct($is_sington = false, $user = 'nobody', $output = "/dev/null")
    {
        $this->is_sington = $is_sington; //是否单例运行，单例运行会在tmp目录下建立一个唯一的PID
        $this->user = $user; //设置运行的用户 默认情况下nobody
        $this->output = $output; //设置输出的地方
        $this->check_pcntl();
    }

    //检查环境是否支持pcntl支持
    public function check_pcntl()
    {
        if (!function_exists('pcntl_signal_dispatch')) {
            // PHP < 5.3 uses ticks to handle signals instead of pcntl_signal_dispatch
            // call sighandler only every 10 ticks
            declare(ticks = 1);
        }

        // Make sure PHP has support for pcntl
        if (!function_exists('pcntl_signal')) {
            $message = 'PHP does not appear to be compiled with the PCNTL extension.  This is neccesary for daemonization';
            throw new Exception($message);
        }
        //信号处理
        pcntl_signal(SIGTERM, array(__CLASS__, "signal_handler"), false);
        pcntl_signal(SIGINT, array(__CLASS__, "signal_handler"), false);
        pcntl_signal(SIGQUIT, array(__CLASS__, "signal_handler"), false);

        // Enable PHP 5.3 garbage collection
        if (function_exists('gc_enable')) {
            gc_enable();
            $this->gc_enabled = gc_enabled();
        }
    }

    // daemon化程序
    public function daemonize()
    {
        global $stdin, $stdout, $stderr;
        global $argv;

        set_time_limit(0);

        // 只允许在cli下面运行
        if (php_sapi_name() != "cli") {
            die("only run in command line mode\n");
        }

        // 只能单例运行
        if ($this->is_sington == true) {
            $this->pid_file = $this->info_dir . "/" . __CLASS__ . "_" . substr(basename($argv[0]), 0, -4) . ".pid";
            $this->check_pid_file();
        }

        umask(0); //把文件掩码清0

        if (pcntl_fork() != 0) { //是父进程，父进程退出
            exit();
        }

        posix_setsid(); //设置新会话组长，脱离终端

        if (pcntl_fork() != 0) { //是第一子进程，结束第一子进程
            exit();
        }

        chdir("/"); //改变工作目录

        $this->set_user($this->user) or die("cannot change owner");

        //关闭打开的文件描述符
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);

        $stdin = fopen($this->output, 'r');
        $stdout = fopen($this->output, 'a');
        $stderr = fopen($this->output, 'a');

        if ($this->is_sington == true) {
            $this->create_pid_file();
        }
        pcntl_signal(SIGTERM, SIG_DFL);
        pcntl_signal(SIGINT, SIG_DFL);
        pcntl_signal(SIGQUIT, SIG_DFL);
    }

    //--检测pid是否已经存在
    public function check_pid_file()
    {
        if (file_exists($this->pid_file)) {
            throw new Exception('the process is already be running...');
        }
    }

    //创建pid
    public function create_pid_file()
    {
        if (!is_dir($this->info_dir)) {
            mkdir($this->info_dir);
        }
        $fp = fopen($this->pid_file, 'w') or die("cannot create pid file");
        fwrite($fp, posix_getpid());
        fclose($fp);
    }

    //设置运行的用户
    public function set_user($name)
    {
        if (empty($name))  return true;

        $result = false;
        $user = posix_getpwnam($name);
        if ($user) {
            $uid = $user['uid'];
            $gid = $user['gid'];
            $result = posix_setuid($uid);
            posix_setgid($gid);
        }

        return $result;
    }

    //信号处理函数
    public function signal_handler($signal)
    {
        switch ($signal) {
            case SIGINT :
            case SIGQUIT:
            case SIGTERM:{
                $this->safe_quit();
                break;
            }
        }
    }

    //整个进程退出
    public function safe_quit()
    {
        if (file_exists($this->pid_file)) {
            unlink($this->pid_file);
        }
        posix_kill(0, SIGKILL);
        exit(0);
    }

}