<?php

namespace EcampLib\Job;

class JobQueue extends \SplQueue
{
    public function Flush(){
        while($this->count() > 0){
            /** @var $job JobInterface */
            $job = $this->shift();
            $job->enqueue();
        }
    }
}