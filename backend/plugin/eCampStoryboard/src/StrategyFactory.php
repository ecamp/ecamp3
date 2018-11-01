<?php

namespace eCamp\Plugin\Storyboard;

use eCamp\Plugin\Storyboard\Service\SectionService;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class StrategyFactory implements FactoryInterface {
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return Strategy
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var SectionService $sectionService */
        $sectionService = $container->get(SectionService::class);

        return new Strategy($sectionService);
    }
}
