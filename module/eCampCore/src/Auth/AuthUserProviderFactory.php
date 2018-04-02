<?php

namespace eCamp\Core\Auth;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Entity\User;
use eCamp\Core\Repository\UserRepository;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthUserProviderFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AuthUserProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);

        return new AuthUserProvider($userRepository);
    }
}
