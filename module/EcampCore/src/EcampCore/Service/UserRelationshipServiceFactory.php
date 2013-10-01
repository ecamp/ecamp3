<?php
namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserRelationshipServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return UserService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $userRelationshipRepo = $services->get('EcampCore\Service\UserRelationship');

        return new UserRelationshipService($userRelationshipRepo);
    }
}
