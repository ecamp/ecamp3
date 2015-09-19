<?php

namespace EcampLib\Job;

interface JobInterface
{
    /**
     * @param  null        $queue
     * @return \Resque\Job
     */
    public function enqueue($queue =  null);
    public function perform($args, $job);
}
