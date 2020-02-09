<?php
namespace eCampApi;

use Zend\Mvc\Application;
use Zend\Mvc\MvcEvent;
use ZF\Apigility\Provider\ApigilityProviderInterface;

class Module implements ApigilityProviderInterface {
    public function getConfig() {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return [
            'ZF\Apigility\Autoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }

    public function onBootstrap(MvcEvent $e) {
        /** @var Application $app */
        $app = $e->getApplication();

        $helpers = $app->getServiceManager()->get('ViewHelperManager');
        /** @var \ZF\Hal\Plugin\Hal $hal */
        $hal = $helpers->get('Hal');

        $entityExtractor = new HalEntityExtractor($hal->getEntityHydratorManager());
        $hal->setEntityExtractor($entityExtractor);

        $halResourceFactory = new HalResourceFactory($hal->getEntityHydratorManager(), $hal->getEntityExtractor());
        $hal->setResourceFactory($halResourceFactory);
    }
}
