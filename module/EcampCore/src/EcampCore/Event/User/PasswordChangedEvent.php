<?php

namespace EcampCore\Event\User;

use EcampCore\Entity\User;
use EcampCore\Event\UserEvent;

class PasswordChangedEvent extends UserEvent {

    const PasswordChanged = 'password-changed';

    public function __construct($target, User $user){
        parent::__construct(self::PasswordChanged, $target, $user);
    }
}