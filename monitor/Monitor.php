<?php

namespace queue\monitor;

use queue\listener\Listener;

/**
 * @author fengjunhui
 */
class Monitor
{

    private $queue;

    private $listenersPath = '.' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'listeners';

    private $listeners;

    public function __construct()
    {
        touch($this->listenersPath);
        $this->queue = $this->loadQueues();
        $currentListeners = $this->getCurrentListeners();
        $queueDefault = array_fill(0, count($this->queue), null);
        $this->listeners = empty($currentListeners) ? $queueDefault : $currentListeners;
    }

    public function startMonitor()
    {
        foreach ($this->queue as $queue => $value) {
            $this->startListener($queue);
        }
    }

    public function stopMonitor()
    {
        foreach ($this->queue as $queue => $value) {
            $this->stopListener($queue);
        }
    }

    public function startListener($queue)
    {
        $maxWorkers = $this->queue[$queue['max_workers']];
        $runAgain = $this->queue[$queue['run_again']];
        if (!$this->checkProcess($this->listeners[$queue])) {
            $this->createListener($queue, $maxWorkers, $runAgain);
        }
    }

    public function stopListener($queue)
    {
        $listener = $this->listeners[$queue];
        if (!is_null($listener) && $this->checkProcess($listener)) {
            posix_kill($listener, SIGTERM);
            $this->updateListenerStatus($queue, $listener, true);
        }
    }

    public function checkProcess($pid)
    {
        if (file_exists('/proc/' . $pid . '/status')) {
            $file = fopen('/proc/' . $pid . '/status', 'r');
            $nameString = fgets($file);
            fclose($file);
            $nameArray = explode(':', $nameString);
            if (trim($nameArray[1] == 'php')) {
                return true;
            }
        }
        return false;
    }

    public function getCurrentListeners()
    {
        $currentListeners = file_get_contents($this->listenersPath);
        return !$currentListeners ? [] : json_decode($currentListeners, true);
    }

    public function updateListenerStatus($queue, $pid, $kill = false)
    {
        $status = $kill ? `kill ` . $pid : ` use process` . $pid;
        $this->listeners[$queue] = $status ? null : $pid;
        file_put_contents($this->listenersPath, json_encode($this->listeners));
    }

    public function createListener($queue, $maxWorker, $runAgain)
    {
        $pid = pcntl_fork();
        switch ($pid) {
            case -1:
                exit('can not fork');
            case 0:
                cli_set_process_title("Queue: master process queue:" . $queue);
                if (posix_setsid() == -1) {
                    exit('set session id failure');
                }
                $listener = new Listener($queue, $maxWorker);
                $listener->runAgain($runAgain);
                $listener->main();
                exit(0);
            default:
                $this->updateListenerStatus($queue, $pid);
        }
    }

    public function loadQueues()
    {
        return [['max_workers' => 4, 'run_again' => false]];
    }
}