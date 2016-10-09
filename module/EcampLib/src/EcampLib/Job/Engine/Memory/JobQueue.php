<?php

namespace EcampLib\Job\Engine\Memory;

use EcampLib\Job\Engine\JobQueue as BaseJobQueue;
use Zend\Mvc\ApplicationInterface;

class JobQueue extends BaseJobQueue
{
    public function execute(ApplicationInterface $app)
    {
        while($this->count() > 0){
            $job = $this->dequeue();
            $job->execute($app);
        }
    }
}
