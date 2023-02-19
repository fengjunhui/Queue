<?php

namespace queue\lock;

use Redis;

/**
 * @author fengjunhui
 */
class RedisLock implements QueueLockInterface
{
    private $expireTime = 3;
    private $keyPrefix = 'queue:';
    private $redisClient;
    private $lockValue = 1;

    public function __construct(){
        $this->redisClient = new Redis();
    }

    public function lock($queue)
    {
        $locked = $this->redisClient->setnx($this->keyPrefix . $queue, $this->lockValue) == 1;
        return $locked ? $this->redisClient->expire($this->keyPrefix . $queue, $this->expireTime): false;
    }

    public function unlock($queue)
    {
        return $this->redisClient->del([$this->keyPrefix . $queue]) == 1;
    }
}