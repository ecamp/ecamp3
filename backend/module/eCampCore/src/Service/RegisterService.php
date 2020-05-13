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

    public function register($username, $mail, $password) {
        $user = $this->userService->create((object) [
            'username' => $username,
            'mailAddress' => $mail,
            'state' => User::STATE_REGISTERED,
        ]);

        if ($user instanceof User) {
            $login = new Login($user, $password);
            $this->getServiceUtils()->emPersist($login);
        }

        $this->getServiceUtils()->emFlush();

        return $user;
    }
}
