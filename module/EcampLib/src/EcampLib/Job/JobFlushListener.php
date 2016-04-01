<?php

namespace EcampLib\Job;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface as Events;
use Zend\Mvc\MvcEvent;

class JobFlushListener extends AbstractListenerAggregate
{
    private $jobQueue;

    public function __construct(JobQueue $jobQueue){
        $this->jobQueue = $jobQueue;
    }


    public function attach(Events $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onFinish'), -10);
    }

    public function onFinish(MvcEvent $e){
        $response = $e->getResponse();

        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;

        switch(floor($statusCode / 100)){
            case 2:
            case 3:
                $this->flushJobQueue();
                break;

            case 4:
            case 5:
                break;

            default:
                throw new \Exception('error in onFinish: ' . $statusCode);
        }
    }

    public function flushJobQueue(){
        $this->jobQueue->Flush();
    }
}