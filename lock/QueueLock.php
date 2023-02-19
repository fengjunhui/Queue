<?php

namespace queue\lock;

/**
 * @author fengjunhui
 */
class QueueLock
{
    private static $lock = null;

    public static function getLock()
    {
        return is_null(static::$lock) ? static::getDefaultDriver() : static::$lock;
    }

    private static function getDefaultDriver()
    {
        static::$lock = new RedisLock();
        return static::$lock;
    }
}