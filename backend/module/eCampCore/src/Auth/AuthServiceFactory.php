<?php

namespace eCamp\Core\Auth;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Entity\User;
use eCamp\Core\Repository\UserRepository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthServiceFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);

        $config = $container->get('config');

        return new AuthService($userRepository, $config['hybridauth']);
    }
}
