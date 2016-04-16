<?php

namespace EcampLib\Service;

use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ServiceWrapper implements ServiceLocatorAwareInterface, EventManagerAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    private $service;
    private $serviceFactoryName;

    protected $defaultListeners = array(
        'EcampLib\Service\ServiceInitializer',
    );

    public function __construct($serviceFactoryName)
    {
        $this->service = null;
        $this->serviceFactoryName = $serviceFactoryName;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Inject an EventManager instance
     *
     * @param  EventManagerInterface $events
     * @return ServiceWrapper
     */
    public function setEventManager(EventManagerInterface $events)
    {
        $events->setIdentifiers(array(
            __CLASS__,
            get_class($this),
        ));
        foreach ($this->defaultListeners as $listener) {
            $events->attach($this->getServiceLocator()->get($listener));
        }

        $this->events = $events;

        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }

        return $this->events;
    }

    public function setService(ServiceBase $service)
    {
        $this->service = $service;
    }

    public function getService()
    {
        if (null === $this->service) {
            $serviceFactoryName = $this->serviceFactoryName;

            /** @var $serviceFactory \Zend\ServiceManager\FactoryInterface */
            $serviceFactory = new $serviceFactoryName;
            $service = $serviceFactory->createService($this->getServiceLocator());

            $this->getEventManager()->trigger(
                ServiceEvent::SERVICE_CREATED,
                $this,
                array(
                    'service' => $service
                )
            );

            $this->service = $service;
        }

        return $this->service;
    }

    public function __call($method, $args)
    {
        $service = $this->getService();

        $this->getEventManager()->trigger(
            ServiceEvent::SERVICE_CALL_PRE,
            $this,
            array(
                'service' => $service,
                'method' => $method,
                'args' => $args
            )
        );

        try {
            $result = call_user_func_array(array($service, $method), $args);

            $this->getEventManager()->trigger(
                ServiceEvent::SERVICE_CALL_SUCCESS,
                $this,
                array(
                    'service' => $service,
                    'method' => $method,
                    'args' => $args,
                    'result' => $result,
                )
            );

            return $result;

        } catch (\Exception $ex) {
            $this->getEventManager()->trigger(
                ServiceEvent::SERVICE_CALL_ERROR,
                $this,
                array(
                    'service' => $service,
                    'method' => $method,
                    'args' => $args,
                    'exception' => $ex
                )
            );

            throw $ex;

        } finally {
            $this->getEventManager()->trigger(
                ServiceEvent::SERVICE_CALL_POST,
                $this,
                array(
                    'service' => $service,
                    'method' => $method,
                    'args' => $args
                )
            );
        }
    }
}
