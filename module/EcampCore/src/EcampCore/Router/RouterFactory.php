<?php

namespace EcampCore\Router;

use Zend\Mvc\Service\RouterFactory as DefaultRouterFactory;
use Zend\ServiceManager\ServiceLocatorInterface;

class RouterFactory extends DefaultRouterFactory
{
    public function createService(ServiceLocatorInterface $serviceLocator, $cName = null, $rName = null)
    {
        $router = parent::createService($serviceLocator, $cName, $rName);

        //get instance of the RoutePluginManager
        $routePluginManager = $router->getRoutePluginManager();
        
        //set the ServiceLocator for the RoutePluginManager so we can use it in the route
        $routePluginManager->setServiceLocator($serviceLocator);

        return $router;
    }
}
