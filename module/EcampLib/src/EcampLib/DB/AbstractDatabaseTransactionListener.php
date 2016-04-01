<?php

namespace EcampLib\DB;

use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface as Events;
use Zend\Mvc\MvcEvent;

abstract class AbstractDatabaseTransactionListener extends AbstractListenerAggregate
{

    public function attach(Events $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_BOOTSTRAP, array($this, 'onBootstrap'), 100);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_FINISH, array($this, 'onFinish'), -10);
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 10);
    }

    public function onBootstrap(){
        $this->beginTransaction();
    }

    public function onFinish(MvcEvent $e){
        $response = $e->getResponse();

        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;

        switch(floor($statusCode / 100)){
            case 2:
            case 3:
                $this->commitTransaction();
                break;

            case 4:
            case 5:
                $this->rollbackTransaction();
                break;

            default:
                throw new \Exception('error in onFinish: ' . $statusCode);
        }
    }

    public function onDispatchError(MvcEvent $e){
        $this->rollbackTransaction();
    }

    abstract function beginTransaction();
    abstract function commitTransaction();
    abstract function rollbackTransaction();
}