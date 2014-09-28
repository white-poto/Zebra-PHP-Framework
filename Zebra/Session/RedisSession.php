<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-9-28
 * Time: 下午3:05
 *
 * <code>
 * <?php
 *    require_once 'RedisSession.php';
 *
 *    RedisSession::init ( array (
 *      'session_name' => 'redis_sess',
 *      'cookie_path' => '/',
 *      'cookie_domain' => '.acme.org',
 *      'lifetime' => 3600,
 *      'server' => array (
 *          'host' => 'redis.acme.org',
 *          'port' => 6379 ) ) );
 * ?>
 * </code>
 */

namespace Zebra\Session;

class RedisSession {
    /**
     * Default config
     * @var array
     */
    private $_config = array (
        'session_name' => 'redis_sess',
        'cookie_path' => '/',
        'cookie_domain' => '.example.com',
        'lifetime' => 0,
        'server' => array (
            'host' => '127.0.0.1',
            'port' => 6379 ) );

    /**
     * Predis Object
     * @var Redis
     */
    private $_redis;

    /**
     * Current session id, for optimization purpose (cf method write())
     * @var string
     */
    private $_id = '';

    /**
     * RedisSession instance, prevent to bind several session manager
     * @var RedisSession
     */
    private static $_instance = null;

    /**
     * Initialize Redis Session (the only method to call)
     *
     * @param array $config Session configuration (cf field definition)
     */
    public static function init ( $config = array() ) {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self ( $config );
        } else {
            throw new \Exception ( 'RedisSession already initialized' );
        }
    }

    /**
     * Default constructor.
     *
     * @param array $config Session configuration (cf field definition)
     *
     * @throws Exception
     */
    private function __construct ( $config = array() ) {
        if (!empty ( $config )) {
            if (!( isset ( $config ['cookie_path'] ) && isset ( $config ['cookie_domain'] )
                && isset ( $config ['session_name'] )
                && isset ( $config ['lifetime'] ) && \is_int ( $config ['lifetime'] )
                && isset ( $config ['server'] ) && \is_array ( $config ['server'] ) && isset ( $config ['server'] ['host'] ) && isset ( $config ['server'] ['port'] ) && \is_int ( $config ['server'] ['port'] )
            )) {
                throw new \Exception ( 'Bad configuration, see documentation' );
            }
            $this->_config = $config;
        }

        if ($this->_init ()) {
            \session_set_save_handler ( array (
                &$this,
                'open' ), array (
                &$this,
                'close' ), array (
                &$this,
                'read' ), array (
                &$this,
                'write' ), array (
                &$this,
                'destroy' ), array (
                &$this,
                'gc' ) );

            \ini_set ( 'session.auto_start', 0 );

            // No garbage collector, Redis exiration do it
            \ini_set ( 'session.gc_probability', 0 );
            \ini_set ( 'session.gc_divisor', 0 );

            \session_cache_limiter ( 'nocache' );
            \session_set_cookie_params ( $this->_config ['lifetime'], $this->_config ['cookie_path'], $this->_config ['cookie_domain'] );

            \session_name ( $this->_config ['session_name'] );
            \session_start ();
        } else {
            throw new \Exception ( 'Cannot initiliaze Redis Session' );
        }
    }

    /**
     * Initialize Redis.
     *
     */
    private function _init () {
        $this->_redis = new \Redis ();
        return $this->_redis->connect ( $this->_config ['server'] ['host'], $this->_config ['server'] ['port'] );
    }

    /**
     * Open just save sessoin name for key prefix as we already have an open connection.
     *
     * @return bool
     */
    public function open ( $savePath, $sessionName ) {
        $this->_config ['keyprefix'] = $sessionName . '::';
        return true;
    }

    /**
     * @return bool
     */
    public function close () {
        return true;
    }

    /**
     * Read the session data.
     *
     * @param string $id
     * @return string
     */
    public function read ( $id ) {
        return $this->_redis->get ( $this->_buildKey ( $id ) );
    }

    /**
     * Write data to the session.
     *
     * @param string  $id
     * @param mixed   $data
     * @return bool
     */
    public function write ( $id, $data ) {
        // This might be the first set for this session key, so we have to check if it's already exists in redis
        if ($this->_id !== $id) {
            // Perform getset to check if session already exists in redis
            if ($this->_redis->getSet ( $this->_buildKey ( $id ), $data ) === false) {
                // session doesn't exists in redis, so we have to set expiration
                $this->_redis->expire ( $this->_buildKey ( $id ), $this->_config ['lifetime'] );
            }
            // Backup id, so we'll know that expiration has already been set, we'll avoid to perform getset on the next write
            $this->_id = $id;
        } else {
            // This is not the first write in script execution, just update value
            $this->_redis->set ( $this->_buildKey ( $id ), $data );
        }
        return true;
    }

    /**
     * Destroys the session by removing the entry.
     *
     * @param string $id
     * @return bool
     */
    public function destroy ( $id ) {
        $this->_redis->delete ( $this->_buildKey ( $id ) );
        \session_destroy ();
        return true;
    }

    /**
     * Garbage collection. Done by redis.
     *
     * @return bool
     */
    public function gc () {
        return true;
    }

    /**
     * Build Redis entry key.
     *
     * @param string $id
     * @return string
     */
    private function _buildKey ( $id ) {
        return $this->_config ['keyprefix'] . $id;
    }
}