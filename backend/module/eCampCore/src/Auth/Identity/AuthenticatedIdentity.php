<?php

namespace eCamp\Core\Auth\Identity;

use eCamp\Core\Entity\User;
use ZF\MvcAuth\Identity\AuthenticatedIdentity as BaseIdentity;

class AuthenticatedIdentity extends BaseIdentity {

    /** @var User */
    private $user;

    /** @param User $user */
    public function __construct(User $user) {
        parent::__construct($user->getUsername());
        $this->setName($user->getUsername());
        $this->user = $user;
    }

    /** @return User */
    public function getUser() {
        return $this->user;
    }
}
