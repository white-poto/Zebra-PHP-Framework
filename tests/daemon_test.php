<?php
//全局变量
define('API_KEY', 'P_369_#$*(^5_2010');
define('LOG_SERVER', '14.18.204.171'); //日志处理服务器
define('LOG_SERVER_URL', 'http://log.369.com/api/online_log_receive_sync.php'); //日志处理服务器请求URL
define('ONE_SECOND', 1); //一秒钟
define('LOG_QUEUE', 1); //日志队列
define('LOG_DIR', "/data0/OnLineLogServer/"); //备份目录

if (!is_dir(LOG_DIR . date("Y-m-d"))) $r = mkdir(LOG_DIR . date("Y-m-d"), 0755, true);

$logFileName = $argv[1];

if ($logFileName) {

    if (file_exists($logFileName)) {
        $lastModify = date('YmdHi', filemtime($logFileName));
        if (date("YmdHi") > $lastModify) {
            //生成API校验
            $sign = md5(API_KEY . basename($logFileName));

            //把文件上传到日志处理服务器
            $uploader = new JetHttp(LOG_SERVER_URL);
            $uploader->setProxy(LOG_SERVER);
            $params = array('log_file' => "@" . $logFileName, 'sign' => $sign);
            //echo LOG_DIR."/".basename($logFileName);
            //echo "\r\n";
            if ($result = $uploader->POST($params, true)) {
                $targetdir = LOG_DIR . substr(basename($logFileName), 0, 4) . '-' . substr(basename($logFileName), 4, 2) . '-' . substr(basename($logFileName), 6, 2);
                if (!rename($logFileName, $targetdir . "/" . basename($logFileName))) {
                    unlink($logFileName);
                    echo "delete:{$logFileName}\r\n";
                };
            } else {
                var_dump($result);
            }
        } else {
            echo "time out!\r\n";
        }
    } else {
        echo "Error: $logFileName not exists!\r\n";
    }

} else {
    echo "Usage: php {$argv[0]} log_file_name\r\n";
}

function parseQueryString($str)
{
    $op = array();
    $pairs = explode("&", $str);
    foreach ($pairs as $pair) {
        list($k, $v) = array_map("urldecode", explode("=", $pair));
        $op[$k] = $v;
    }
    return $op;
}

/**
 *  Http类
 */
class JetHttp
{

    private $url;
    private $proxyIp;
    private $proxyPort;


    public function __construct($url, $proxyIp = '', $proxyPort = 0)
    {

        //URL地址
        if (!$url) die("必须指定URL地址！");
        $this->url = $url;

        //代理服务器I的设置
        if ($proxyIp) {
            $this->proxyIp = $proxyIp;
            $this->proxyPort = $proxyPort ? $proxyPort : 80;
        }

    }

    public function setProxy($ip, $port = 80)
    {
        if ($ip) {
            $this->proxyIp = $ip;
            $this->proxyPort = $port;
        }
    }

    public function GET($params = null)
    {

        //组合带参数的URL
        $url = $this->url;
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
        if ($this->proxyIp && $this->proxyPort) {
            $proxy = "http://{$this->proxyIp}:{$this->proxyPort}";
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $content = curl_exec($curl);
        curl_close($curl);

        return $content;

    }

    public function POST($params = null, $fileUpload = false)
    {

        //初始化curl
        $curl = curl_init();
        if ($this->proxyIp && $this->proxyPort) {
            $proxy = "http://{$this->proxyIp}:{$this->proxyPort}";
            curl_setopt($curl, CURLOPT_PROXY, $proxy);
        }
        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        //设置POST参数
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($params && is_array($params)) {
            if ($fileUpload) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
            } else {
                $postFields = '';
                $amp = '';
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
