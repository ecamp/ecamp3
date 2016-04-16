<?php

namespace EcampLib\Service;

use EcampLib\Acl\Acl;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\Event;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceInitializer extends AbstractListenerAggregate
    implements ServiceLocatorAwareInterface
{
    /** @var ServiceLocatorInterface */
    private $serviceLocator;

    /** @var  array */
    private $config;

    /** @var Acl */
    private $acl;

    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(ServiceEvent::SERVICE_CREATED, array($this, 'onCreated'));
    }

    public function onCreated(Event $e)
    {
        if ($this->config == null) {
            $this->config = $this->serviceLocator->get('Config');
        }

        if ($this->acl == null) {
            $this->acl = $this->serviceLocator->get('EcampLib\Acl');
        }

        /** @var ServiceBase $service */
        $service = $e->getParam('service');
        $service->setConfigArray($this->config);
        $service->setAcl($this->acl);
    }

}
