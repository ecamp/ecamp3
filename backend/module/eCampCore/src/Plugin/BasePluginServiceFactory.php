<?php

namespace eCamp\Core\Plugin;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\Mvc\Application;

abstract class BasePluginServiceFactory {
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return mixed
     */
    protected function getEventPluginId(ContainerInterface $container) {
        /** @var Application $app */
        $app = $container->get('Application');

        $mvcEvent = $app->getMvcEvent();
        $routeMatch = $mvcEvent->getRouteMatch();

        return $routeMatch->getParam('eventPluginId');
    }
}
