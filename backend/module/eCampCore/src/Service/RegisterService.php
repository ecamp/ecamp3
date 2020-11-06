<?php

namespace eCamp\Core\Service;

use eCamp\Core\Entity\Login;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityService\UserService;
use eCamp\Lib\Service\ServiceUtils;
use Laminas\Authentication\AuthenticationService;

class RegisterService extends AbstractService {
    /** @var UserService */
    protected $userService;

    public function __construct(
        ServiceUtils $serviceUtils,
        AuthenticationService $authenticationService,
        UserService $userService
    ) {
        parent::__construct($serviceUtils, $authenticationService);

        $this->userService = $userService;
    }

    public function register($data) {
        $data->state = User::STATE_REGISTERED;
        $user = $this->userService->create($data);

        if ($user instanceof User) {
            $login = new Login($user, $data->password);
            $this->getServiceUtils()->emPersist($login);
        }

        $this->getServiceUtils()->emFlush();

        return $user;
    }
}
