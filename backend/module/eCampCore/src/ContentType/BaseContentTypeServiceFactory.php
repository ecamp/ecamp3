<?php

namespace eCamp\Core\ContentType;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\Mvc\Application;

abstract class BaseContentTypeServiceFactory {
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return mixed
     */
    protected function getActivityContentId(ContainerInterface $container) {
        /** @var Application $app */
        $app = $container->get('Application');

        $mvcEvent = $app->getMvcEvent();
        $routeMatch = $mvcEvent->getRouteMatch();

        return $routeMatch->getParam('activityContentId');
    }
}
