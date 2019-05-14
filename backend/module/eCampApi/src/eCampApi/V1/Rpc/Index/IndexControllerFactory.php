<?php
namespace eCampApi\V1\Rpc\Index;

use eCamp\Core\EntityService\UserService;
use Zend\Authentication\AuthenticationService;

class IndexControllerFactory
{
    public function __invoke($controllers)
    {
        $authenticationService = $controllers->get(AuthenticationService::class);

        /** @var UserService $userService */
        $userService = $controllers->get(UserService::class);

        return new IndexController($authenticationService, $userService);
    }
}
