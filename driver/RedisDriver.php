<?php

namespace queue\driver;

use queue\job\Job;
use Redis;

class RedisDriver implements QueueDriverInterface
{

    private $redisClient;

    private $keyExpire = 60 * 60 * 1;

    public function __construct()
    {
        $this->redisClient = new Redis();
    }

    public function push(Job $job)
    {
        $res = $this->redisClient->rPush($job->queue, [serialize($job)]);
        return $res == 1;
    }

    public function pop($queue)
    {
        $jobString = $this->redisClient->rPop($queue);
        $job = @unserialize($jobString);
        return !($job instanceof Job) ? null : $job;
    }

    public function cleanQueue($queue)
    {
        // TODO: Implement cleanQueue() method.
    }

    public function getAllJob($queue, $status)
    {
        // TODO: Implement getAllJob() method.
    }

    public function getJobInfo($id)
    {
        $jobString = $this->redisClient->get($this->getJobKey($id));
        $job = @unserialize($jobString);
        return !($job instanceof Job) ? null : $job;
    }

    public function resetJobInfo(Job $job)
    {
        $setKeyResult = $this->setKeyExpire($job->id);
        $setJobResult = $this->redisClient->set($this->getJobKey($job->id), serialize($job));
        $setRunJobResult = $this->redisClient->sAdd($this->getRunJobSetKey(), [$job->id]);
        return $setJobResult == 1 && $setKeyResult && $setRunJobResult == 1;
    }

    public function getJobKey($id){
        return 'queue' . ':job' . $id;
    }

    public function getRunJobSetKey(){
        return 'queue' . ':run:jobs';
    }

    public function runAgain($id)
    {
        // TODO: Implement runAgain() method.
    }

    public function setKeyExpire($id){
        $res = $this->redisClient->expire($this->getJobKey($id), $this->keyExpire);
        return $res == 1;
    }
}