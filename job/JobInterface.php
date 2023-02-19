<?php

namespace queue\job;

/**
 * @author fengjunhui
 */
interface JobInterface
{

    /**
     * 所有任务实现这个接口
     * 可在此方法中return数据，return的数据将会被保存
     * 可在此方法中throw异常，也会被保存
     * @return string message
     */
    public function run();

    /**
     * 可在任务中输入入参
     * @param $args
     * @return boolean
     */
    public function setArgs($args);
}