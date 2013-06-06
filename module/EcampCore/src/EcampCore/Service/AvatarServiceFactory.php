<?php
namespace EcampCore\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AvatarServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return AvatarService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $userService = $services->get('EcampCore\Service\User\Internal');
        $groupService = $services->get('EcampCore\Service\Group\Internal');

        return new AvatarService($userService, $groupService);
    }
}
