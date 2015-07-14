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

    }

}
