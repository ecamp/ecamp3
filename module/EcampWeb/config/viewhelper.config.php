<?php

return array(
    'factories' => array(
        'url' => function ($helperPluginManager) {
            $serviceLocator = $helperPluginManager->getServiceLocator();
            $urlHelper =  new \EcampWeb\View\Helper\CampUrl();

            $router = \Zend\Console\Console::isConsole() ? 'HttpRouter' : 'Router';
            $urlHelper->setRouter($serviceLocator->get($router));

            $match = $serviceLocator->get('application')
                ->getMvcEvent()
                ->getRouteMatch();

            if ($match instanceof \Zend\Mvc\Router\Http\RouteMatch) {
                $urlHelper->setRouteMatch($match);
            }

            return $urlHelper;
        }
    )
);
