<?php

namespace EcampCore\Service;

use EcampLib\Job\JobFactoryInterface;
use EcampLib\Job\JobInterface;
use EcampLib\Service\ServiceBase;
use EcampLib\ServiceManager\JobFactoryManager;
use Resque\Job;
use Resque\Redis;

class ResqueJobService extends ServiceBase
{

    /** @var JobFactoryManager */
    private $jobFactoryManager;

    public function __construct(
        JobFactoryManager $jobFactoryManager
    ) {
        $this->jobFactoryManager = $jobFactoryManager;
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
     * @return JobInterface
     */
    public function Create($name, $options = array())
    {
        /** @var JobFactoryInterface $jobFactory */
        $jobFactory = $this->jobFactoryManager->get($name);

        return $jobFactory->create($options);
    }

}
