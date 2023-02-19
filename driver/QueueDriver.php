<?php

namespace queue\driver;

/**
 * @author fengjunhui
 */
class QueueDriver
{
    private static $driver = null;

    public static function getDriver()
    {
        return is_null(static::$driver) ? static::getDefaultDriver() : static::$driver;
    }

    /**
     *
     */
    private static function getDefaultDriver()
    {
        static::$driver = new RedisDriver();
        return static::$driver;
    }
}