<?php

namespace eCampApi\V1\Rpc\Auth;

use eCamp\Core\EntityService\UserService;
use Zend\Authentication\AuthenticationService;

class AuthControllerFactory {
    public function __invoke($controllers) {
        $authenticationService = $controllers->get(AuthenticationService::class);

        /** @var UserService $userService */
        $userService = $controllers->get(UserService::class);

        return new AuthController($authenticationService, $userService);
    }
}
