<?php
namespace eCampApi\V1\Rpc\Login;

use eCamp\Core\EntityService\UserService;
use Zend\Authentication\AuthenticationService;

class LoginControllerFactory
{
    public function __invoke($controllers)
    {
        $authenticationService = $controllers->get(AuthenticationService::class);

        /** @var UserService $userService */
        $userService = $controllers->get(UserService::class);

        return new LoginController($authenticationService, $userService);
    }
}
