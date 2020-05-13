<?php

namespace eCamp\ContentType\Textarea;

use eCamp\ContentType\Textarea\Service\TextareaService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class StrategyFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return Strategy
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var TextareaService $textareaService */
        $textareaService = $container->get(TextareaService::class);

        return new Strategy($textareaService);
    }
}
