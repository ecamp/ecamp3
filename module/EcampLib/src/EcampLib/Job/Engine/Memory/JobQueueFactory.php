<?php

namespace EcampLib\Job\Engine\Memory;

use Zend\Http\Response as HTTPResponse;
use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @codeCoverageIgnore
 */
class JobQueueFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $jobQueue = new JobQueue();

        /** @var Application $application */
        $application = $serviceLocator->get('Application');

        $application->getEventManager()->attach(
            MvcEvent::EVENT_FINISH,
            function(MvcEvent $e) use ($jobQueue) {
                $this->mvcOnFinish($jobQueue, $e);
            },
            -10
        );

        return $jobQueue;
    }
    
    public function mvcOnFinish(JobQueue $jobQueue, MvcEvent $e)
    {
        $statusCode = 200;
        $response = $e->getResponse();

        if($response instanceof HTTPResponse){
            $statusCode = $response->getStatusCode();
        }

        switch (floor($statusCode / 100)) {
            case 2:
            case 3:
                $jobQueue->execute($e->getApplication());
                break;

            case 4:
            case 5:
                break;

            default:
                throw new \Exception('error in onFinish: ' . $statusCode);
        }
    }
}
