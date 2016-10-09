<?php

namespace EcampLibTest\Job\Engine;

use Zend\Mvc\ApplicationInterface;

class DummyApplication implements ApplicationInterface
{
    public function getServiceManager()
    {
    }

    public function getRequest()
    {
    }

    public function getResponse()
    {
    }

    public function run()
    {
    }

    public function getEventManager()
    {
    }
}
