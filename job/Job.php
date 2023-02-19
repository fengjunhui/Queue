<?php

namespace queue\job;

use Exception;

/**
 * @author fengjunhui
 */
class Job
{

    public $id;
    private $task;
    public $createTime;
    public $runTime = null;
    public $finishTime = null;
    public $description;
    public $args;
    public $jobStatus = JobOperator::JOB_WAITING;
    public $message = null;
    public $queue;
    public $exception;

    public function __construct($task, array $args, $queue, array $description = []){
        $this->id = uniqid();
        $this->task = $task;
        $args['job_id'] = $this->id;
        $this->args = $args;
        $this->queue = $queue;
        $this->createTime = microtime(true);
        $this->description = $description;
    }

    public static function createJob($className, array $args, $queue, array $description = []){
        return new Job($className, $args, $queue, $description);
    }

    public function getTaskInstance(){
        if (!class_exists($this->task)){
            throw new Exception('task file is not exists');
        }
        return new $this->task;
    }

    public function jobToArray(){
        return [
            'id' => $this->id,
            'task' => $this->task,
            'create_time' => $this->createTime,
            'run_time' => $this->runTime,
            'finish_time' => $this->finishTime,
            'description' => $this->description,
            'args' => $this->args,
            'job_status' => $this->jobStatus,
            'queue' => $this->queue,
            'exception' => $this->exception
        ];
    }
}