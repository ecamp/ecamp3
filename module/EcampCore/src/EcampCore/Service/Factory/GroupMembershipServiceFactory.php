<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\GroupMembershipService;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GroupMembershipServiceFactory
    implements FactoryInterface
{
    /**
     *
     * @param  ServiceLocatorInterface              $services
     * @return GroupMembershipService
     * @throws Exception\ServiceNotCreatedException
     */
    public function createService(ServiceLocatorInterface $services)
    {
        $groupMembershipRepo = $services->get('EcampCore\Repository\GroupMembership');

        return new GroupMembershipService($groupMembershipRepo);
    }
}
