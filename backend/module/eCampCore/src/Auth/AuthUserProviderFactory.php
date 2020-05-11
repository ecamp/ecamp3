<?php

namespace eCamp\Core\Auth;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Entity\User;
use eCamp\Core\Repository\UserRepository;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AuthUserProviderFactory implements FactoryInterface {
    /**
     * @param string $requestedName
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     *
     * @return AuthUserProvider
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);

        /** @var AuthenticationService $authenticationService */
        $authenticationService = $container->get(AuthenticationService::class);

        return new AuthUserProvider($userRepository, $authenticationService);
    }
}
