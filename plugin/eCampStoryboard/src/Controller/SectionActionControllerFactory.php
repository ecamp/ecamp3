<?php

namespace eCamp\Plugin\Storyboard\Controller;

use eCamp\Plugin\Storyboard\Service\SectionService;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class SectionActionControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return SectionActionController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var SectionService $sectionService */
        $sectionService = $container->get(SectionService::class);

        return new SectionActionController($sectionService);
    }

}
