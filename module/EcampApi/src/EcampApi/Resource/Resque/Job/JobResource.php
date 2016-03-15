<?php

namespace EcampApi\Resource\Resque\Job;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;
use Resque\Job;

class JobResource extends HalResource
{
    /** @var Job */
    protected $job;

    public function __construct(Job $job)
    {
        $this->job = $job;
        $id = $job->getId();

        $object = array_merge(compact('id'), $job->getPacket());

        parent::__construct($object, $id);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/resque/jobs', array(
            'job' => $id
        ));
        $this->getLinks()->add($selfLink);

        $runLink = new Link('run');
        $runLink->setRoute(
            'api/resque/jobs',
            array('job' => $id),
            array('query' => array('run' => ''))
        );
        $this->getLinks()->add($runLink);

        $class = new \ReflectionClass($job->getClass());
        if ($class->implementsInterface('EcampLib\Job\JobResultInterface')) {
            $resultLink = new Link('result');
            $resultLink->setRoute(
                'api/resque/jobs',
                array('job' => $id),
                array('query' => array('result' => ''))
            );

            $this->getLinks()->add($resultLink);
        }

    }

}
