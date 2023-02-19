<?php

namespace queue\worker;

use Exception;
use queue\job\Job;
use queue\job\JobOperator;

/**
 * @author fengjunhui
 * @property Job $job
 */
class DefaultWorker implements QueueWorkerInterface
{

    private $task;
    private $job;

    public function main(){
        try {
            $resultData = $this->task->run();
            JobOperator::setJobMessage($this->job, $resultData);
        }catch (Exception $exception){
            JobOperator::setJobStatus($this->job, JobOperator::JOB_FAILURE);
            JobOperator::setJobException($this->job, $exception->getMessage());
            exit(-1);
        }
        JobOperator::setJobStatus($this->job, JobOperator::JOB_FINISHED);
        exit(0);
    }

    public function setJob(Job $job){
        $this->job = $job;
        try {
            $this->task = $job->getTaskInstance();
        }catch (Exception $exception){
            JobOperator::setJobStatus($job, JobOperator::JOB_FAILURE);
            exit(-1);
        }
        $this->task->setArgs($job->args);
    }
}