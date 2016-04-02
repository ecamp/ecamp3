<?php

namespace EcampLib\DB;

use EcampLib\Service\ServiceEvent;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractDatabaseFlushListener
    implements  SharedListenerAggregateInterface, ServiceLocatorAwareInterface
{
    const IDENTIFIER = 'EcampLib\Service\ServiceWrapper';

    /** @var ServiceLocatorInterface */
    private $serviceLocator;

    private $listeners;

    private $serviceNestingCounter = 0;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(self::IDENTIFIER, ServiceEvent::SERVICE_CALL_PRE, array($this, 'onServicePre'));
        $this->listeners[] = $events->attach(self::IDENTIFIER, ServiceEvent::SERVICE_CALL_SUCCESS, array($this, 'onServiceSuccess'));
        $this->listeners[] = $events->attach(self::IDENTIFIER, ServiceEvent::SERVICE_CALL_ERROR, array($this, 'onServiceError'));
        $this->listeners[] = $events->attach(self::IDENTIFIER, ServiceEvent::SERVICE_CALL_POST, array($this, 'onServicePost'));
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $listener) {
            $events->detach(self::IDENTIFIER, $listener);
        }
    }

    public function onServicePre()
    {
        $this->serviceNestingCounter++;
    }

    public function onServicePost()
    {
        $this->serviceNestingCounter--;
    }

    public function onServiceSuccess()
    {
        if ($this->serviceNestingCounter == 1) {
            $this->flush();
        }
    }

    public function onServiceError()
    {
        if ($this->serviceNestingCounter == 1) {
            $this->rollback();
        }
    }

    abstract public function flush();
    abstract public function rollback();

}
