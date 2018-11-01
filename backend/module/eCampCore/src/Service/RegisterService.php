<?php

namespace eCamp\Core\Service;

use eCamp\Core\Entity\Login;
use eCamp\Core\Entity\User;
use eCamp\Core\EntityServiceAware\UserServiceAware;
use eCamp\Core\EntityServiceTrait\UserServiceTrait;

class RegisterService extends AbstractService {

    public function register($username, $mail, $password) {
        $user = $this->getUserService()->create((object)[
            'username' => $username,
            'mailAddress' => $mail,
            'state' => User::STATE_REGISTERED
        ]);

        $login = new Login($user, $password);

        $this->getEntityManager()->persist($login);
    }
}
