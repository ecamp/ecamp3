<?php
namespace EcampCore;

use Zend\Stdlib\ArrayUtils;
use Zend\Mvc\MvcEvent;
use Zend\ModuleManager\Feature\ServiceProviderInterface;

use EcampCore\EntityUtil\ServiceLocatorAwareEventListener;

class Module implements 
	ServiceProviderInterface
{
    public function getConfig(){
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig(){
    	
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig(){
    	return include __DIR__ . '/config/service.config.php';
    	
//     	$config = array();
//     	$configFiles = array(
// 	    	__DIR__ . '/config/service.config.php',
// 	    	__DIR__ . '/config/service.config.repos.php', 
// 	    	__DIR__ . '/config/service.config.services.php',
//     	);
    	 
//     	// Merge all module config options
//     	foreach($configFiles as $configFile) {
//     		$config = ArrayUtils::merge($config, include $configFile);
//     	}
    	
//     	return $config;
    }
    
    public function getControllerConfig(){
    	return include __DIR__ . '/config/controller.config.php';
    }
    
}
