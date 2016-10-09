<?php

namespace EcampLib\Job\Engine;

use EcampLib\Job\JobInterface;

/**
 * Interface JobQueueInterface
 * @package EcampLib\JobEngine
 */
interface JobQueueInterface
{
    /**
     * @param JobInterface $job
     */
    public function enqueue(JobInterface $job);

    /**
     * @return JobInterface
     */
    public function dequeue();
}
