<?php
namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return GroupService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $loginRepository = $services->get('EcampCore\Repository\Login');
        $userService = $services->get('EcampCore\Service\User');

        return new LoginService($loginRepository, $userService);
    }
}
