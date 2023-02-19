<?php

namespace queue\job;

use Exception;
use queue\driver\QueueDriver;

/**
 * @author fengjunhui
 */
class JobOperator
{

    const JOB_NOT_FOUND = 0;
    const JOB_WAITING = 1;
    const JOB_RUNNING = 2;
    const JOB_FINISHED = 3;
    const JOB_FAILURE = 4;

    /**
     * @param $id
     * @return int
     */
    public static function getJobStatus($id)
    {
        $job = QueueDriver::getDriver()->getJobInfo($id);

        return is_null($job) ? self::JOB_NOT_FOUND : $job->jobStatus;
    }

    /**
     * @param Job $job
     * @param $status
     * @return boolean
     */
    public static function setJobStatus(Job $job, $status)
    {
        $job->jobStatus = $status;

        switch ($status) {
            case self::JOB_WAITING:
                $job->createTime = microtime(true);
                break;
            case self::JOB_RUNNING:
                $job->runTime = microtime(true);
                break;
            case self::JOB_FINISHED:
            case self::JOB_FAILURE:
                $job->finishTime = microtime(true);
                break;
        }

        return QueueDriver::getDriver()->setJobInfo($job);
    }

    /**
     * @param $id
     * @return string
     * @throws Exception
     */
    public static function getJobMessage($id)
    {
        $job = QueueDriver::getDriver()->getJobInfo($id);
        if (is_null($job)) {
            throw new Exception('job:' . $id . ' not found');
        }
        return $job->message;
    }

    /**
     * @param Job $job
     * @param $message
     * @return boolean
     */
    public static function setJobMessage(Job $job, $message)
    {
        $job->message = $message;
        return QueueDriver::getDriver()->setJobInfo($job);
    }

    /**
     * @param Job $job
     * @param $exception
     * @return boolean
     */
    public static function setJobException(Job $job, $exception)
    {
        $job->exception = $exception;
        return QueueDriver::getDriver()->setJobInfo($job);
    }

    /**
     * @param $id
     * @return boolean
     */
    public static function runAgain($id)
    {
        return QueueDriver::getDriver()->runAgain($id);
    }
}