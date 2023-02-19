<?php

namespace queue\listener;

use queue\job\Job;
use queue\job\JobOperator;
use queue\Queue;
use queue\worker\QueueWorker;

/**
 * @author fengjunhui
 */
class Listener
{

    private $queue;
    private $currentWorkers = 0;
    private $maxJobNumber = 100000;
    private $maxWorker;

    public function __construct($queue, $maxWorker = 2)
    {
        $this->queue = $queue;
        $this->maxWorker = $maxWorker;
    }

    public function main()
    {
        $notNum = 0;
        $epoch = 10;
        $jobNum = 0;

        $needWait = true;
        while (true) {
            usleep(1000);

            if ($jobNum >= $this->maxJobNumber) {
                sleep(5);
                exit();
            }
            if ($this->currentWorkers >= $this->maxWorker || $needWait) {
                $needWait = false;
                while (pcntl_wait($status, WNOHANG) > 0) {
                    --$this->currentWorkers;
                }
            } else {
                $job = Queue::deQueue($this->queue);
                if (is_null($job)) {
                    sleep(1);
                    ++$notNum;
                    if ($notNum % $epoch == 0) {
                        $needWait = !$needWait;
                        $notNum = 0;
                    }
                    continue;
                }
                ++$jobNum;
                JobOperator::setJobStatus($job, JobOperator::JOB_RUNNING);
                $this->createWorker($job);
            }
        }
    }

    private function createWorker(Job $job)
    {
        $pid = pcntl_fork();
        switch ($pid) {
            case -1:
                break;
            case 0:
                cli_set_process_title("Queue: worker process job:" . $job->id);
                $worker = QueueWorker::getWorker($this->queue);
                $worker->setJob($job);
                $worker->main();
                exit(0);
            default:
                ++$this->currentWorkers;
        }
    }

    public function runAgain($runAgain)
    {
        if ($runAgain){
            $jobs = Queue::getAllJob($this->queue, JobOperator::JOB_RUNNING);
            array_map(function ($value){
                JobOperator::runAgain($value->jobId);
            }, $jobs);
        }
    }
}