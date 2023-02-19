<?php

namespace queue\driver;

use queue\job\Job;

/**
 * @author fengjunhui
 */
interface QueueDriverInterface
{
    /**
     * @param Job $job
     * @return boolean
     */
    public function push(Job $job);

    /**
     * @param string $queue
     * @return Job|null
     */
    public function pop($queue);

    /**
     * @param $queue
     * @return boolean
     */
    public function cleanQueue($queue);

    /**
     * @param string $queue
     * @param int $status
     * @return array(QueueTaskListModel)
     */
    public function getAllJob($queue, $status);

    /**
     * @param $id
     * @return Job|null
     */
    public function getJobInfo($id);

    /**
     * @param Job $job
     * @return boolean
     */
    public function resetJobInfo(Job $job);

    /**
     * @param string $id
     * @return boolean
     */
    public function runAgain($id);
}