<?php

namespace EcampLib\Job\Engine\Resque;

use EcampLib\Job\Engine\Application;
use EcampLib\Job\JobInterface;
use Resque\Job as ResqueJob;

class JobQueueItem
{
    const CLASS_NAME = 'class_name';
    const SERIALIZED_DATA = 'serialized_data';


    public function setUp()
    {
    }

    public function perform($args, $job)
    {
        $application = Application::Instance();
        JobQueueItem::Create($args, $job)->execute($application);
    }

    public function tearDown()
    {
    }


    /**
     * @param JobInterface $job
     * @return array
     */
    public static function Data(JobInterface $job)
    {
        return array(
            self::CLASS_NAME => get_class($job),
            self::SERIALIZED_DATA => $job->serialize()
        );
    }

    /**
     * @param array $data
     * @param $resqueJob
     * @return JobInterface
     */
    public static function Create(array $data, $resqueJob)
    {
        $id = null;
        if ($resqueJob instanceof ResqueJob) {
            $id = $resqueJob->getId();
        }

        $jobClass = $data[self::CLASS_NAME];
        $jobSerialized = $data[self::SERIALIZED_DATA];

        /** @var JobInterface $job */
        $job = new $jobClass();
        $job->id($id);
        $job->unserialize($jobSerialized);

        return $job;
    }
}
