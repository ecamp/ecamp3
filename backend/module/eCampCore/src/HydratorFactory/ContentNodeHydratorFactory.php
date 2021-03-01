<?php

namespace eCamp\Core\HydratorFactory;

use eCamp\Core\ContentType\ContentTypeStrategyProvider;
use eCamp\Core\Hydrator\ContentNodeHydrator;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContentNodeHydratorFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ContentNodeHydrator {
        /** @var ContentTypeStrategyProvider $contentTypeStrategyProvider */
        $contentTypeStrategyProvider = $container->get(ContentTypeStrategyProvider::class);

        return new ContentNodeHydrator($contentTypeStrategyProvider);
    }
}
