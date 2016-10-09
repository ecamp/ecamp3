<?php

namespace EcampLib\Job\Engine;

use EcampLib\Job\JobInterface;

class JobQueue implements JobQueueInterface
{
    private $jobQueue = array();

    /**
     * @param JobInterface $job
     */
    public function enqueue(JobInterface $job)
    {
        array_push($this->jobQueue, $job);
    }

    /**
     * @return JobInterface
     */
    public function dequeue()
    {
        return array_shift($this->jobQueue);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->jobQueue);
    }
}
