<?php
namespace EcampCore\Service\Factory;

use EcampCore\Service\GroupService;
use Zend\ServiceManager\Exception;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GroupServiceFactory
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
        $groupRepo = $services->get('EcampCore\Repository\Group');

        return new GroupService($groupRepo);
    }
}
