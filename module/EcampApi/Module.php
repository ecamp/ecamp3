<?php
namespace EcampApi;

use Zend\Mvc\MvcEvent;

use EcampApi\Listener\JsonExceptionStrategy;
use EcampApi\Listener\AuthenticationRequiredExceptionStrategy;
use EcampApi\Camp\CampResourceListener;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $event)
    {
        $application = $event->getTarget();
        $serviceManager = $application->getServiceManager();
        $config = $serviceManager->get('Config');

        // Config json enabled exceptionStrategy
        $exceptionStrategy = new JsonExceptionStrategy();

        $displayExceptions = false;

        if (isset($config['view_manager']['display_exceptions'])) {
            $displayExceptions = $config['view_manager']['display_exceptions'];
        }

        $exceptionStrategy->setDisplayExceptions($displayExceptions);
        $exceptionStrategy->attach($application->getEventManager());

        $authenticationRequiredStrategy = new AuthenticationRequiredExceptionStrategy();
        $authenticationRequiredStrategy->attach($application->getEventManager());
    }
    
    public function getServiceConfig()
    {
    	return array('factories' => array(
    			'EcampApi\Camp\CampResourceListener' => function ($services) {
    				$repository = $services->get('EcampCore\Repository\Camp');
    				return new CampResourceListener($repository);
    				
    			},
    	));
    }
}
