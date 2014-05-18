<?php

namespace EcampCore\Validator\User;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UniqueUsernameFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $fem)
    {
        $serviceLocator = $fem->getServiceLocator();
        $entityManager = $serviceLocator->get('doctrine.entitymanager.orm_default');

        return new UniqueUsername($entityManager);
    }
}
