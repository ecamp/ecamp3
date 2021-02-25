<?php

namespace eCamp\Core\ContentType;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class DefaultContentTypeServiceFactory extends BaseContentTypeServiceFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $contentNodeId = $this->getContentNodeId($container);

        return new $requestedName($contentNodeId);
    }
}
