<?php

namespace queue\worker;

use queue\job\Job;

/**
 * @author fengjunhui
 */
interface QueueWorkerInterface
{

    public function setJob(Job $job);
    public function main();
}