<?php

namespace eCamp\Core\Controller\Auth;

use Doctrine\ORM\EntityManager;
use eCamp\Core\Auth\AuthService;
use eCamp\Core\EntityService\UserIdentityService;
use eCamp\Core\EntityService\UserService;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class GoogleControllerFactory implements FactoryInterface {
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return GoogleController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        /** @var UserIdentityService $userIdentityService */
        $userIdentityService = $container->get(UserIdentityService::class);

        /** @var UserService $userService */
        $userService = $container->get(UserService::class);

        /** @var AuthService $authService */
        $authService = $container->get(AuthService::class);

        return new GoogleController(
            $entityManager,
            $userIdentityService,
            $userService,
            $authService
        );
    }
}
