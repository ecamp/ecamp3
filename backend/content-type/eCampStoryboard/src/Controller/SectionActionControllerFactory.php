<?php

namespace eCamp\ContentType\Storyboard\Controller;

use eCamp\ContentType\Storyboard\Service\SectionService;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SectionActionControllerFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @return SectionActionController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var SectionService $sectionService */
        $sectionService = $container->get(SectionService::class);

        return new SectionActionController($sectionService);
    }
}
