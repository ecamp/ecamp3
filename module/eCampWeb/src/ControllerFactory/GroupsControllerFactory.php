<?php

namespace eCamp\Web\ControllerFactory;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Entity\Group;
use eCamp\Core\Repository\GroupRepository;
use eCamp\Core\Service\GroupService;
use eCamp\Web\Controller\GroupsController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class GroupsControllerFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var GroupService $groupService */
        $groupService = $container->get(GroupService::class);

        return new GroupsController($groupService);
    }
}
