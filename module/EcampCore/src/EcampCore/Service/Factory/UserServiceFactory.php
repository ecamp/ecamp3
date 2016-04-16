<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\UserService;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class UserServiceFactory
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
        $userRepo = $services->get('EcampCore\Repository\User');
        $resqueJobService = $services->get('EcampCore\Service\ResqueJob');

        return new UserService($userRepo, $resqueJobService);
    }
}
