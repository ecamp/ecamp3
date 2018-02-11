<?php

namespace eCamp\Web\Route;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Entity\Camp;
use eCamp\Core\Entity\Group;
use eCamp\Core\Entity\User;
use eCamp\Core\Repository\CampRepository;
use eCamp\Core\Repository\GroupRepository;
use eCamp\Core\Repository\UserRepository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class FluentRouterFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var EntityManager $em */
        $em = $container->get('doctrine.entitymanager.orm_default');

        /** @var UserRepository $userRepository */
        $userRepository = $em->getRepository(User::class);

        /** @var GroupRepository $groupRepository */
        $groupRepository = $em->getRepository(Group::class);

        /** @var CampRepository $campRepository */
        $campRepository = $em->getRepository(Camp::class);

        return new FluentRouter($userRepository, $groupRepository, $campRepository, $options);
    }
}