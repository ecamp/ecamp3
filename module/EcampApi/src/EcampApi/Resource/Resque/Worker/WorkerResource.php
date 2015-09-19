<?php

namespace EcampApi\Resource\Resque\Worker;

use PhlyRestfully\HalResource;
use PhlyRestfully\Link;

class WorkerResource extends HalResource
{
    /** @var \Resque\Worker */
    protected $worker;

    public function __construct(\Resque\Worker $worker)
    {
        $this->worker = $worker;
        $id = $worker->getId();

        $object = array_merge(compact('id'), $worker->getPacket());
        parent::__construct($object, $id);

        $selfLink = new Link('self');
        $selfLink->setRoute('api/resque/workers', array('worker' => $id));
        $this->getLinks()->add($selfLink);
    }

}
