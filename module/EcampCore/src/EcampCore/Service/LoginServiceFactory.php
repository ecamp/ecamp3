<?php
namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginServiceFactory
    implements FactoryInterface
{
    /**
     * @param  ServiceLocatorInterface $services
     * @return LoginService
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $loginRepository = $services->get('EcampCore\Repository\Login');
        $autoLoginRepository = $services->get('EcampCore\Repository\AutoLogin');
        $userRepository = $services->get('EcampCore\Repository\User');

        return new LoginService($loginRepository, $autoLoginRepository, $userRepository);
    }
}
