<?php
namespace EcampApi;

use ZF\Apigility\Provider\ApigilityProviderInterface;
use Zend\Config\Factory;

class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
    	return Factory::fromFiles(glob( __DIR__ . '/config/autoload/*.*'));
       // return include __DIR__ . '/config/module.config.php';
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
