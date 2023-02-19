<?php

namespace queue\worker;

/**
 * @author fengjunhui
 */
class QueueWorker
{

    public static function getWorker(){
        return new DefaultWorker();
    }
}