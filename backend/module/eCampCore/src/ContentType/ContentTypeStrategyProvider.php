<?php

namespace eCamp\Core\ContentType;

use eCamp\Core\Entity\ContentType;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContentTypeStrategyProvider {
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @param $contentTypeOrStrategyClass
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get($contentTypeOrStrategyClass): ?ContentTypeStrategyInterface {
        $strategyClass = $contentTypeOrStrategyClass;

        if ($contentTypeOrStrategyClass instanceof ContentType) {
            $strategyClass = $contentTypeOrStrategyClass->getStrategyClass();
        }

        $strategy = null;
        if (is_string($strategyClass)) {
            if ($this->container->has($strategyClass)) {
                $strategy = $this->container->get($strategyClass);
            } elseif (class_exists($strategyClass)) {
                $strategy = new $strategyClass();
            }
        }

        return $strategy;
    }
}
