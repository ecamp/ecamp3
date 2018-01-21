<?php

namespace eCamp\Plugin\Textarea;

use eCamp\Plugin\Textarea\Service\TextareaService;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class StrategyFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Strategy
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var TextareaService $textareaService */
        $textareaService = $container->get(TextareaService::class);

        return new Strategy($textareaService);
    }
}