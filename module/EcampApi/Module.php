<?php
namespace EcampApi;

use ZF\Apigility\Provider\ApigilityProviderInterface;
use Zend\Config\Factory;

class Module implements ApigilityProviderInterface
{
	
	
    public function getConfig()
    {
    	return Factory::fromFiles(array_merge(
    		[ __DIR__ . '/config/module.config.php' ],
    		glob( __DIR__ . '/config/autoload/*.*'),
    		glob( __DIR__ . '/config/autoload/V1/*.*')
    	));
    }
    
    public function getAutoloaderConfig()
    {
        return [
            'ZF\Apigility\Autoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src',
                ],
            ],
        ];
    }
}
