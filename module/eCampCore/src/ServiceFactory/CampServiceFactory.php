<?php

namespace eCamp\Core\ServiceFactory;

use eCamp\Core\Service\CampService;
use eCamp\Core\Service\EventCategoryService;
use eCamp\Core\Service\JobService;
use eCamp\Core\Service\PeriodService;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class CampServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return CampService|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $acl = $container->get(\Zend\Permissions\Acl\AclInterface::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $jobService = $container->get(JobService::class);
        $eventCategoryService = $container->get(EventCategoryService::class);
        $periodService = $container->get(PeriodService::class);

        return new CampService
        ( $acl
        , $entityManager
        , $jobService
        , $eventCategoryService
        , $periodService
        );
    }
}
