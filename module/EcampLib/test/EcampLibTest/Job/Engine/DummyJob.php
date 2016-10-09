<?php

namespace EcampLibTest\Job\Engine;

use EcampLib\Job\AbstractJobBase;
use Zend\Mvc\ApplicationInterface;

class DummyJob extends AbstractJobBase
{
    public $isExecuted = false;

    public function serialize()
    {
        return null;
    }

    public function unserialize($serialized)
    {
    }

    public function doInit(ApplicationInterface $app)
    {
    }

    public function doExecute(ApplicationInterface $app)
    {
        $this->isExecuted = true;
    }
}