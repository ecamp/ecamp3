<?php

namespace eCamp\Core\ContentType;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ContentTypeStrategyProviderFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @return ContentTypeStrategyProvider
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        return new ContentTypeStrategyProvider($container);
    }
}
