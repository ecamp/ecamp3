<?php

namespace eCamp\Core\Plugin;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\Mvc\Application;

abstract class BasePluginServiceFactory {
    /**
     * @param ContainerInterface $container
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getEventPluginId(ContainerInterface $container) {
        /** @var Application $app */
        $app = $container->get('Application');

        $mvcEvent = $app->getMvcEvent();
        $routeMatch = $mvcEvent->getRouteMatch();

        return $routeMatch->getParam('event_plugin_id');
    }
}
