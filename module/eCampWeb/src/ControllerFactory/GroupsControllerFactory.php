<?php

namespace eCamp\Web\ControllerFactory;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Entity\Group;
use eCamp\Core\Repository\GroupRepository;
use eCamp\Web\Controller\GroupsController;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class GroupsControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var EntityManager $em */
        $em = $container->get('doctrine.entitymanager.orm_default');

        /** @var GroupRepository $groupRepository */
        $groupRepository = $em->getRepository(Group::class);

        return new GroupsController($groupRepository);
    }
}
