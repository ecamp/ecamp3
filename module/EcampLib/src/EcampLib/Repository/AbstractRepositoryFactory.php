<?php

namespace EcampLib\Repository;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;

/**
 * Provides repositories for all doctrine entities
 * Pattern: Ecamp*\Repository\*
 */
class AbstractRepositoryFactory implements AbstractFactoryInterface
{
    const EntityManager = 'entitymanager';
    const EntityName = 'entityname';
    const RepositoryInstance = 'repositoryinstance';

    private $cache = array();

    /** @var \EcampLib\Options\ModuleOptions */
    private $moduleOptions = null;

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (array_key_exists($requestedName, $this->cache)) {
            return true;
        }

        if ($this->moduleOptions == null) {
            $this->moduleOptions = $serviceLocator->get('EcampLib\Options\ModuleOptions');
        }

        $canCreateService = false;

        foreach ($this->moduleOptions->getrepositoryMappings() as $repoPattern => $repositoryMapping) {
            if (preg_match($repoPattern, $requestedName)) {
                $entityPattern = $repositoryMapping[self::EntityName];
                $canCreateService = true;

                $this->cache[$requestedName] = array(
                    self::EntityManager => $repositoryMapping[self::EntityManager],
                    self::EntityName => preg_replace($repoPattern, $entityPattern, $requestedName),
                );

                break;
            }
        }

        return $canCreateService;
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (!$this->canCreateServiceWithName($serviceLocator, $name, $requestedName)) {
            return null;
        }

        $cacheEntry = $this->cache[$requestedName];

        if ($cacheEntry[self::RepositoryInstance] == null) {
            $entityName = $cacheEntry[self::EntityName];
            $entityManagerName = $cacheEntry[self::EntityManager];

            /** @var EntityManager $entityManager */
            $entityManager = $serviceLocator->get('doctrine.entitymanager.' . $entityManagerName);

            $cacheEntry[self::RepositoryInstance] = $entityManager->getRepository($entityName);
        }

        return $cacheEntry[self::RepositoryInstance];
    }
}
