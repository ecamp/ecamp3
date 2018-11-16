<?php

namespace eCamp\Core\Auth;

use Carnage\JwtZendAuth\Authentication\Storage\Jwt;
use Carnage\JwtZendAuth\Service\Jwt as JwtService;
use Doctrine\ORM\EntityManager;
use eCamp\Core\Entity\User;
use eCamp\Core\Repository\UserRepository;
use eCamp\Lib\Auth\Storage\AuthHeaderAndRedirectQueryParam;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthServiceFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        /** @var UserRepository $userRepository */
        $userRepository = $entityManager->getRepository(User::class);

        $config = $container->get('config');

        /** @var Jwt $jwtStorage */
        $jwtStorage = new Jwt(
            $container->get(JwtService::class),
            $container->get(AuthHeaderAndRedirectQueryParam::class),
            $config['jwt_zend_auth']['expiry']
        );

        $authService = new AuthService($userRepository, $config['hybridauth']);
        $authService->setStorage($jwtStorage);
        return $authService;
    }
}
