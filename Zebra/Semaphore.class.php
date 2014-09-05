<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-24
 * Time: 下午14:28
 *
 */

class Semaphore
{

    public static function create($key)
    {
        return new Semaphore($key);
    }
 
    private $lockId;
    private $locked = false;
 
    private function __construct($key)
    {
        if ( ($this->lockId = sem_get($this->_stringToSemKey($key))) === FALSE)
        {
            throw new \Exception('Cannot create semaphore for key: ' . $key);
        }
    }
 
    public function __destruct()
    {
        $this->release();
    }
 
 
    public function acquire()
    {
        if (!sem_acquire($this->lockId))
        {
            throw new \Exception('Cannot acquire semaphore: ' . $this->lockId);
        }
        $this->locked = true;
    }
 
    public function release()
    {
        if ($this->locked)
        {
            if (!sem_release($this->lockId))
            {
                throw new \Exception('Cannot release semaphore: ' . $this->lockId);
            }
            $this->locked = false;
        }
    }
    
    // Semaphore requires a numeric value as the key
    protected function _stringToSemKey($identifier)
    {
        $md5 = md5($identifier);
        $key = 0;
        for ($i = 0; $i < 32; $i++)
        { 
            $key += ord($md5{$i}) * $i;
        }
        return $key;
    }
    
}
 
 