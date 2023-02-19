<?php

namespace queue\lock;

interface QueueLockInterface
{

    public function lock($queue);

    public function unlock($queue);
}