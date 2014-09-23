<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-9-23
 * Time: 下午3:50
 *
 * 
 * $config = array(
 * 'HOST' => 'master.db.adv:3307', //数据库主机
 * 'DB' => 'w37_ad_referer', //数据库名
 * 'USER' => 'root', //数据库用户
 * 'PASSWORD' => '', //密码
 * 'CHARSET' => 'utf8', //数据库字符集
 * 'PERSIST' => FALSE, //是否使用长连接
 * 'DATA_SOURCE_INDEX' => 0, //用于与其他数据库链接配置相区分
 * ),
 *
 */

namespace Zebra\Database;

class MySQL {

    //单例实现多个数据库链接
    protected static $connection;

    //当前对象所保持的数据库链接KEY
    protected $dataSourceIndex;

    public $metaData;

    protected $lastSql;

    //事务启动标志，0表示未启动，1表示等待递交或回滚
    protected $transTimes;

    /**
     * 构造函数
     * @param int $dataSourceIndex 数据源索引
     * @return void
     */
    public function __construct($config) {
        $this->dataSourceIndex = $config['DATA_SOURCE_INDEX'];
        $this->initConnection($config);
    }

    /**
     * 销毁函数
     * @return void
     */
    public function __destruct() {

        foreach($this->dataSourceIndex as $index){

        }

    }


    /**
     * 初始化数据库访问
     * @throws Exception
     */
    protected function initConnection($config) {
        if(empty($config)) throw new \Exception('mysql connect config can not be empty');

        if(is_resource(self::$connection[$this->dataSourceIndex])) return true;

        self::$connection[$this->dataSourceIndex] = $config['PERSIST'] ?
            mysql_pconnect($config['HOST'], $config['USER'], $config['PASSWORD']) :
            mysql_connect($config['HOST'], $config['USER'], $config['PASSWORD']);
        if(!is_resource(self::$connection[$this->dataSourceIndex])){
            throw new \Exception('could not connect the mysql server');
        }
        $select_result = mysql_select_db($config['DB'], self::$connection[$this->dataSourceIndex]);
        if(!$select_result){
            throw new \Exception('could not select database');
        }
        if ($config['CHARSET']) {
            mysql_set_charset($config['CHARSET'], self::$connection[$this->dataSourceIndex]);
        }

        return true;
    }

    public function getLastSql(){
        return $this->lastSql;
    }

    /**
     * 执行一条SQL语句
     * @param  $sql
     * @return resource
     */
    public function execute($sql) {
        //保证数据库连接是开启的
        $this->lastSql = $sql;
        $this->initConnection($this->dataSourceIndex);
        return mysql_query($sql, self::$connection[$this->dataSourceIndex]);
    }

    /**
     * 执行查询，返回一行
     * @throws JetException
     * @param  $sql string SQL语句
     * @return array 一行数据
     */
    public function fetchRow($sql) {

        $this->lastSql = $sql;
        $rs = $this->execute($sql);
        if (is_resource($rs)) {
            $this->getMetaData($rs);
            return mysql_fetch_assoc($rs);
        } else {
            throw new JetException("不能正确返回数据集！SQL: $sql");
        }

    }
    /**
     * 执行查询，返回多行
     * @throws JetException
     * @param  $sql string SQL语句
     * @return array 多行数据集
     */
    public function fetchRows($sql) {
        $this->lastSql = $sql;
        $rs = $this->execute($sql);
        if (is_resource($rs)) {
            $this->getMetaData($rs);
            $rows = array();
            while ($row = mysql_fetch_assoc($rs)) {
                $rows[] = $row;
            }
            return $rows;
        } else {
            throw new JetException("不能正确返回数据集！SQL: $sql");
        }

    }

    /**
     * 分页获取数据
     * @param  $sql string
     * @param  $start int
     * @param  $limit int
     * @return data totalRecords
     */
    public function fetchRowsByPage($sql, $start, $limit) {
        $countSQL = $this->getCountSQL($sql);

        $totalRecords = 0;
        $result = null;
        $resultSet = $this->fetchRow($countSQL);

        if ($resultSet) {

            $totalRecords = $resultSet['CNT'];
            $limitation = " LIMIT $start,$limit";
            $sql .= $limitation;
            $this->lastSql = $sql;
            $result = $this->fetchRows($sql);

        }
        return array($result, $totalRecords);

    }

    /**
     * 获取数据库产生的自增ID
     * @return int
     */
    public function insertId() {
        return mysql_insert_id(self::$connection[$this->dataSourceIndex]);
    }


    /**
     * 返回更新影响的行数
     * @return int
     */
    public function affectedRows() {
        return mysql_affected_rows(self::$connection[$this->dataSourceIndex]);
    }

    /**
     * 启动事务
     * @access public
     * @return boolean
     */
    public function startTrans(){
        if ($this->transTimes == 0) {
            mysql_query('START TRANSACTION', self::$connection[$this->dataSourceIndex]);
            $this->transTimes ++;
            return true;
        }

        return false;
    }

    /**
     * 用于非自动提交状态下面的查询提交
     * @access public
     * @return boolean
     */
    public function commit(){
        if ($this->transTimes > 0) {
            $result = mysql_query('COMMIT', self::$connection[$this->dataSourceIndex]);
            $this->transTimes = 0;
            if(!$result){
                return false;
            }
        }
        return true;
    }

    /**
     * 事务回滚
     * @access public
     * @return boolean
     */
    public function rollback(){
        if ($this->transTimes > 0) {
            $result = mysql_query('ROLLBACK', self::$connection[$this->dataSourceIndex]);
            $this->transTimes = 0;
            if(!$result){
                return false;
            }
        }
        return true;
    }


    /**
     * 字符串转义，推荐的转义方法，考虑了连接的字符集
     * @param  $str
     * @return string
     */
    public function escapeString($str) {
        return mysql_real_escape_string($str, self::$connection[$this->dataSourceIndex]);
    }


    /**
     * 获取错误代码
     * @return int
     */
    public function errorNumber() {
        return mysql_errno(self::$connection[$this->dataSourceIndex]);
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function errorMessage() {
        return mysql_error(self::$connection[$this->dataSourceIndex]);
    }

    /*
     * 获取数据元信息
     */
    private function getMetaData($rs) {

        $this->metaData = array();
        $fieldCount = mysql_num_fields($rs);
        for ($i = 0; $i < $fieldCount; $i++) {
            $field = mysql_fetch_field($rs, $i);
            $this->metaData[] = $field;
        }

    }

    /*
     * 生成统计语句
     */
    private function getCountSQL($sql) {

        $countSQL = "";

        if ($sql) {
            $parsingSQL = strtoupper($sql);
            $groupPos = strpos($parsingSQL, 'GROUP');
            if (!$groupPos) {
                $fromPos = strpos($parsingSQL, 'FROM') + 4;
                $orderPos = strpos($parsingSQL, 'ORDER BY');
                if (!$orderPos) $orderPos = strlen($parsingSQL);
                $countSQL = "SELECT COUNT(*) AS CNT FROM" . substr($sql, $fromPos, $orderPos - $fromPos);
            }else{
                $fromPos = strpos($parsingSQL, 'FROM') + 4;
                $orderPos = strpos($parsingSQL, 'ORDER BY');
                if (!$orderPos) $orderPos = strlen($parsingSQL);
                $countSQL = "SElECT COUNT(A.CNT) CNT FROM (SELECT COUNT(*) AS CNT FROM" . substr($sql, $fromPos, $orderPos - $fromPos).") A";
            }
        }

        return $countSQL;

    }

}

?>