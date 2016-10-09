<?php
namespace EcampCore\Service\Factory;

use EcampCore\Repository\AutologinRepository;
use EcampCore\Repository\LoginRepository;
use EcampCore\Repository\UserRepository;
use EcampCore\Service\LoginService;
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
        /** @var LoginRepository $loginRepository */
        $loginRepository = $services->get('EcampCore\Repository\Login');

        /** @var AutologinRepository $autologinRepository */
        $autologinRepository = $services->get('EcampCore\Repository\Autologin');

        /** @var UserRepository $userRepository */
        $userRepository = $services->get('EcampCore\Repository\User');

        return new LoginService($loginRepository, $autologinRepository, $userRepository);
    }
}
