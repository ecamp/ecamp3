<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\RegisterService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RegisterServiceFactory
    implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $services
     * @return RegisterService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $userRepository = $services->get('EcampCore\Repository\User');
        $userService = $services->get('EcampCore\Service\User');
        $loginService = $services->get('EcampCore\Service\Login');
        $resqueJobService = $services->get('EcampCore\Service\ResqueJob');

        return new RegisterService(
            $userRepository,
            $userService,
            $loginService,
            $resqueJobService
        );
    }
}
