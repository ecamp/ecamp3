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
    private $orm;

    private $pattern = "/^Ecamp(\w+)\\\\Repository\\\\(\w+)$/";

    public function __construct($orm = null)
    {
        $this->orm = $orm ?: 'doctrine.entitymanager.orm_default';
    }

    /**
     * Translate a repository class name into the corresponding entity class name
     * @param  string $repoName
     * @return string
     */
    private function getEntityClassName($repoName)
    {
        return preg_replace($this->pattern,"Ecamp$1\\\\Entity\\\\$2", $repoName);
    }

    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return preg_match($this->pattern,$requestedName) && class_exists($this->getEntityClassName($requestedName));
    }

    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        /* Reository is fetched from Doctrine EntityManager */
        $em = $serviceLocator->get($this->orm);

        return $em->getRepository($this->getEntityClassName($requestedName));
    }
}
