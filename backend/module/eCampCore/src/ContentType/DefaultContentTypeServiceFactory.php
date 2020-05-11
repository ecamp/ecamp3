<?php

namespace eCamp\Core\ContentType;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class DefaultContentTypeServiceFactory extends BaseContentTypeServiceFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $activityContentId = $this->getActivityContentId($container);

        return new $requestedName($activityContentId);
    }
}
