<?php

namespace eCamp\Core\ContentType;

use eCamp\Core\Entity\ContentType;
use Interop\Container\ContainerInterface;
use Laminas\Crypt\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class ContentTypeStrategyProvider {
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(ContentType $contentType): ContentTypeStrategyInterface {
        $strategyClass = $contentType->getStrategyClass();

        if (is_string($strategyClass)) {
            if ($this->container->has($strategyClass)) {
                return $this->container->get($strategyClass);
            }
            if (class_exists($strategyClass)) {
                return new $strategyClass();
            }
        }

        throw new NotFoundException('No Strategy found for ContentType '.$contentType->getName());
    }
}
