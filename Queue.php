<?php
namespace queue;

use queue\driver\QueueDriver;
use queue\job\Job;
use queue\lock\QueueLock;

/**
 * @author fengjunhui
 */
class Queue
{
    const QUEUE_DEFAULT = 0;
    public static function enQueue($className, array $args = [], $queue = self::QUEUE_DEFAULT, array $description = []){
        $job = Job::createJob($className, $args, static::getQueuePrefix() . $queue , $description);
        QueueDriver::getDriver()->push($job);
        return $job->id;
    }

    public static function deQueue($queue){
        $lockStatus = QueueLock::getLock()->lock(static::getQueuePrefix() . $queue);
        $job = $lockStatus ? QueueDriver::getDriver()->pop(static::getQueuePrefix() . $queue) : null;
        QueueLock::getLock()->unlock(static::getQueuePrefix().$queue);
        return $job;
    }

    public static function cleanQueue($queue){
        return QueueDriver::getDriver()->cleanQueue(static::getQueuePrefix().$queue);
    }

    public static function getAllJob($queue, $status){
        return QueueDriver::getDriver()->getAllJob(static::getQueuePrefix().$queue, $status);
    }

    private static function getQueuePrefix(){
        return 'queue:';
    }
}