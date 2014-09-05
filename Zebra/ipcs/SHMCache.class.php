<?php
/**
 * Created by PhpStorm.
 * User: huyanping
 * Date: 14-8-24
 * Time: 下午14:28
 *
 */
namespace Zebra\Ipcs;

class SHMCache
{

    static protected $nsKeyCache = array();

    protected $shmId;
    protected $keyCache = [];

    public function __construct($ns, $memSize=null)
    {
        if(!$memSize) {
            $memSize = 1000;
        }
        if(\array_key_exists($ns, self::$nsKeyCache))
        {
            $this->shmId = self::$nsKeyCache[$ns];
        } else {
            $tmp = \tempnam('/tmp', $this->forgeKey($ns));
            self::$nsKeyCache[$ns] = $this->shmId = \shm_attach(\ftok($tmp, 'a'), $memSize);
        }
    }

    protected function forgeKey($str)
    {
        if(empty($this->keyCache[$str]))
        {
            $hex_str = \md5($str);

            $arr = \str_split($hex_str, 4);
            foreach ($arr as $grp) {
                $dec[] = \str_pad(\hexdec($grp), 5, '0', STR_PAD_LEFT);
            }
            $numeric = \trim(\implode('', $dec), '0');
            while($numeric >= PHP_INT_MAX)
            {
                $numeric = \bcdiv($numeric, 2, 0);
            }
            $this->keyCache[$str] = (int)(\substr($numeric, 0, 7));
        }
        return $this->keyCache[$str];
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id cache id The id of the cache entry to fetch.
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch($id)
    {
        if($this->contains($id))
        {
            return \shm_get_var($this->shmId, $this->forgeKey($id));
        }
        return false;
    }

    /**
     * Test if an entry exists in the cache.
     *
     * @param string $id cache id The cache id of the entry to check for.
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    public function contains($id)
    {
        return \shm_has_var($this->shmId, $this->forgeKey($id));
    }

    /**
     * Puts data into the cache.
     *
     * @param string $id The cache id.
     * @param mixed $data The cache entry/data.
     * @param int $lifeTime The lifetime. Not handled by this driver
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return \shm_put_var($this->shmId, $this->forgeKey($id), $data);
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id cache id
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    public function delete($id)
    {
        return \shm_remove_var($this->shmId, $this->forgeKey($id));
    }

    public function destroy()
    {
        \shm_remove($this->shmId);
    }

}

