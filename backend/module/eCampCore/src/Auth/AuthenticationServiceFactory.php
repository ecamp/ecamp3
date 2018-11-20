<?php

namespace eCamp\Core\Auth;

use Carnage\JwtZendAuth\Authentication\Storage\Jwt;
use Carnage\JwtZendAuth\Service\Jwt as JwtService;
use eCamp\Lib\Auth\Storage\AuthHeaderAndRedirectQueryParam;
use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationService;
use Zend\ServiceManager\Factory\FactoryInterface;

class AuthenticationServiceFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {

        $config = $container->get('config');

        /** @var Jwt $jwtStorage */
        $jwtStorage = new Jwt(
            $container->get(JwtService::class),
            $container->get(AuthHeaderAndRedirectQueryParam::class),
            $config['jwt_zend_auth']['expiry']
        );

        $authenticationService = new AuthenticationService($jwtStorage);
        return $authenticationService;
    }
}
