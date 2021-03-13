<?php

namespace eCampApi;

use Laminas\ApiTools\Provider\ApiToolsProviderInterface;
use Laminas\Config\Factory as ConfigFactory;
use Laminas\Mvc\Application;
use Laminas\Mvc\MvcEvent;
use Laminas\Paginator\Paginator;

class Module implements ApiToolsProviderInterface {
    public function getConfig() {
        return ConfigFactory::fromFiles(
            array_merge(
                glob(__DIR__.'/../../config/Rpc/*.config.php'),
                glob(__DIR__.'/../../config/Rest/*.config.php')
            )
        );
    }

    public function getAutoloaderConfig() {
        return [
            'Laminas\ApiTools\Autoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }

    public function onBootstrap(MvcEvent $e): void {
        Paginator::setDefaultItemCountPerPage(PHP_INT_MAX);

        /** @var Application $app */
        $app = $e->getApplication();

        $helpers = $app->getServiceManager()->get('ViewHelperManager');
        /** @var \Laminas\ApiTools\Hal\Plugin\Hal $hal */
        $hal = $helpers->get('Hal');

        $entityExtractor = new HalEntityExtractor($hal->getEntityHydratorManager());
        $hal->setEntityExtractor($entityExtractor);

        $halResourceFactory = new HalResourceFactory($hal->getEntityHydratorManager(), $hal->getEntityExtractor());
        $hal->setResourceFactory($halResourceFactory);
    }
}
