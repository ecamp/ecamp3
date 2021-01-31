<?php

namespace eCamp\Core\ContentType;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class ContentTypeStrategyProviderFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContentTypeStrategyProvider {
        return new ContentTypeStrategyProvider($container);
    }
}
