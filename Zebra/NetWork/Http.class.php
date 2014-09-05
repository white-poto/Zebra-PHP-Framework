<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-14
 * Time: 下午11:05
 */
namespace Zebra\NetWork;

class Http {

    private $url;
    private $proxy_ip;
    private $proxy_port;
    private $time_out;
    private $transfer_time_out;

    /**
     * @param string $url HTTP协议地址
     * @param int $time_out 请求超时时间
     * @param int $transfer_time_out 执行超时时间
     * @param string $proxy_ip 代理服务器IP
     * @param int $proxy_port 代理服务器端口
     */
    public function __construct($url='', $time_out = 10, $transfer_time_out = 600, $proxy_ip = '', $proxy_port = 0) {

        $this->url = $url;
        $this->time_out = $time_out;
        $this->transfer_time_out = $transfer_time_out;

        //代理服务器I的设置
        if ($proxy_ip) {
            $this->proxy_ip = $proxy_ip;
            $this->proxy_port = $proxy_port ? $proxy_port : 80;
        }

    }

    private function __get($property_name)
    {
        if(isset($this->$property_name))
        {
            return($this->$property_name);
        }
        else
        {
            return(NULL);
        }
    }

    private function __set($property_name, $value)
    {
        $this->$property_name = $value;
    }

    /**
     * 设置代理服务器
     * @param $ip
     * @param int $port
     */
    public function set_proxy($ip, $port = 80) {
        if ($ip) {
            $this->proxy_ip = $ip;
            $this->proxy_port = $port;
        }
    }

    /**
     * 发起GET请求
     * @param null $params
     * @return mixed
     * @throws
     */
    public function GET($params = null) {

        //组合带参数的URL
        $url = $this->url;
        if(empty($url)) throw Exception('url can not be empty!');
        if ($params && is_array($params)) {
            $url .= '?';
            $amp = '';
            foreach ($params as $paramKey => $paramValue) {
                $url .= $amp . $paramKey . '=' . urlencode($paramValue);
                $amp = '&';
            }
        }

        //初始化curl
        $curl = curl_init();
        if ($this->proxy_ip && $this->proxy_port) {
            $proxy = "http://{$this->proxy_ip}:{$this->proxy_port}";
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->time_out);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->transfer_time_out);

        $content = curl_exec($curl);
        curl_close($curl);

        return $content;

    }

    /**
     * 发起POST请求，允许上传文件
     * 上传文件参数格式： array('str'=>'@/filepath/filename')
     * @param null $params
     * @param bool $fileUpload 是否上传文件
     * @return mixed
     */
    public function POST($params = null, $file_upload = false) {

        //初始化curl
        $curl = curl_init();
        if ($this->proxy_ip && $this->proxy_port) {
            $proxy = "http://{$this->proxy_ip}:{$this->proxy_port}";
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->time_out);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->transfer_time_out);

        //设置POST参数
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($params && is_array($params)) {
            if ($file_upload) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            } else {
                $amp = '';
                $postFields = '';
                foreach ($params as $paramKey => $paramValue) {
                    $postFields .= $amp . $paramKey . '=' . urlencode($paramValue);
                    $amp = '&';
                }
                curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
            }
        }

        $content = curl_exec($curl);
        curl_close($curl);

        return $content;

    }

}

?>