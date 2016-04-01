<?php

namespace EcampLib\Job;

use Resque\Job;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractJobBase
    implements JobInterface, ServiceLocatorAwareInterface
{
    public $job;
    public $args;
    public $queue;

    /**
     * @var ServiceLocatorInterface
     */
    private $serviceLocator;

    protected function __construct($defaultQueue = 'php-only')
    {
        $this->args = array();
        $this->queue = $defaultQueue;

        /** @var \Zend\Mvc\Application $app */
        $app = Application::Get();
        if ($app) {
            $this->serviceLocator = $app->getServiceManager();
        }
    }

    /** @param ServiceLocatorInterface $serviceLocator */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /** @return ServiceLocatorInterface */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    public function getService($name)
    {
        return $this->serviceLocator->get($name);
    }

    public function enqueue($queue =  null)
    {
        $q = $queue ?: $this->queue;
        $this->job = \Resque::queue()->push(get_class($this), $this->args, $q);

        return $this->job;
    }

    public function setUp() { }
    public function tearDown() { }

    public function perform($args, $job)
    {
        $this->job = $job;
        $this->args = $args;

        $this->execute();
    }

    abstract public function execute();

    public function getId()
    {
        if ($this->job instanceof Job) {
            return $this->job->getId();
        }

        return null;
    }

    public function &get($name)
    {
        if (isset($this->args[$name])) {
            return $this->args[$name];
        }

        return null;
    }

    public function set($name, $value)
    {
        if (is_object($value)) {
            throw new \Exception("You can not store Objects in a Job-Context: ");
        }

        $this->args[$name] = $value;
    }

    public function &__get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function getStatus()
    {
        if ($this->job instanceof Job) {
            return $this->job->getStatus();
        }

        return 0;
    }

    /**
     * @return \Zend\Mvc\Router\RouteStackInterface
     */
    protected function getRouter()
    {
        return $this->getServiceLocator()->get('HttpRouter');
    }

    /**
     * @param $name
     * @param  array  $params
     * @param  array  $options
     * @return string
     */
    protected function urlFromRoute($name, $params = array(), $options = array())
    {
        $options['name'] = $name;

        return $this->getRouter()->assemble($params, $options);
    }

}
