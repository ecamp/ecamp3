<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\UserRelationshipService;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserRelationshipServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return UserRelationshipService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $userRelationshipRepo = $services->get('EcampCore\Service\UserRelationship');

        return new UserRelationshipService($userRelationshipRepo);
    }
}
