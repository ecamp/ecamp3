<?php

namespace EcampCore\Service;

use EcampLib\Job\JobFactoryInterface;
use EcampLib\Job\JobInterface;
use EcampLib\Job\JobQueue;
use EcampLib\ServiceManager\JobFactoryManager;
use Resque\Job;
use Resque\Redis;

class ResqueJobService extends Base\ServiceBase
{

    /** @var JobFactoryManager */
    private $jobFactoryManager;

    /** @var JobQueue */
    private $jobQueue;

    public function __construct(
        JobFactoryManager $jobFactoryManager,
        JobQueue $jobQueue
    ) {
        $this->jobFactoryManager = $jobFactoryManager;
        $this->jobQueue = $jobQueue;
    }

    /**
     * @param $id
     * @return Job
     */
    public function Get($id)
    {
        return Job::load($id);
    }

    /**
     * @return array
     */
    public function GetAll()
    {
        $redisKey = 'resque:job:';

        $jobs = array();
        $jobNodes = Redis::instance()->keys($redisKey . '*');

        foreach ($jobNodes as $jobNode) {
            $jobs[] = $this->Get(substr($jobNode, strlen($redisKey)));
        }

        return $jobs;
    }

    /**
     * @param $name
     * @param  array        $options
     * @param  bool         $enqueue
     * @return JobInterface
     */
    public function Create($name, $options = array(), $enqueue = false)
    {
        /** @var JobFactoryInterface $jobFactory */
        $jobFactory = $this->jobFactoryManager->get($name);

        $job = $jobFactory->create($options);

        if ($enqueue) {
            $this->Enqueue($job);
        }

        return $job;
    }

    /**
     * @param JobInterface $job
     */
    public function Enqueue(JobInterface $job)
    {
        $this->jobQueue->push($job);
    }

    public function Flush()
    {
        $this->jobQueue->Flush();
    }

    /**
     * @return \Resque\Job|null
     */
    public function GetNextJob()
    {
        /** @var \Resque\Job $job */
        $job = \Resque::pop(array('php-only'));

        return $job;
    }

}
