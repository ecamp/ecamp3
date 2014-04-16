<?php

namespace EcampLib\Job;

use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractServiceJobBase extends AbstractJobBase
{
    /**
     * @var ServiceLocatorInterface
     */
    private static $serviceLocator;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     */
    public static function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        self::$serviceLocator = $serviceLocator;
    }

    protected function __construct($defaultQueue = 'service')
    {
        parent::__construct($defaultQueue);
    }

    /**
     * @return ServiceLocatorInterface
     */
    protected function getServiceLocator()
    {
        return self::$serviceLocator;
    }

    /**
     * @return \Zend\Mvc\Router\RouteStackInterface
     */
    protected function getRouter()
    {
        return self::$serviceLocator->get('HttpRouter');
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
